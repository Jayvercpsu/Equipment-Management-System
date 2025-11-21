<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BorrowModel;
use App\Models\EquipmentModel;
use App\Models\AccessoryModel;
use App\Models\UserModel;

class BorrowController extends BaseController
{
    protected $borrowModel;
    protected $equipmentModel;
    protected $accessoryModel;
    
    public function __construct()
    {
        $this->borrowModel = new BorrowModel();
        $this->equipmentModel = new EquipmentModel();
        $this->accessoryModel = new AccessoryModel();
        helper(['form', 'url']);
    }
    
    public function index()
    {
        $perPage = 10;
        
        if (session()->get('role_name') === 'itso_personnel') {
            $borrows = $this->borrowModel->getBorrowsWithDetails();
        } else {
            $borrows = $this->borrowModel->getUserBorrows(session()->get('user_id'));
        }
        
        $data = [
            'title' => 'Borrow Management',
            'borrows' => array_slice($borrows, 0, $perPage)
        ];
        
        return view('admin/borrow/index', $data);
    }
    
    public function create()
    {
        $equipment = $this->equipmentModel->getAvailableEquipment();
        
        $data = [
            'title' => 'Borrow Equipment',
            'equipment' => $equipment,
            'next_borrow_code' => $this->borrowModel->getNextBorrowCode()
        ];
        
        return view('admin/borrow/form', $data);
    }
    
    public function store()
    {
        $validation = \Config\Services::validation();
        
        $validation->setRules([
            'equipment_id' => 'required|integer',
            'borrow_date' => 'required|valid_date',
            'due_date' => 'required|valid_date',
            'notes' => 'permit_empty'
        ]);
        
        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }
        
        $equipmentId = $this->request->getPost('equipment_id');
        $equipment = $this->equipmentModel->find($equipmentId);
        
        if (!$equipment || $equipment['stock_count'] <= 0) {
            return redirect()->back()->withInput()->with('error', 'Equipment not available for borrowing');
        }
        
        $accessories = $this->accessoryModel->getRequiredAccessories($equipmentId);
        $accessoryData = [];
        
        foreach ($accessories as $acc) {
            $accessoryData[] = [
                'name' => $acc['name'],
                'quantity' => $acc['quantity']
            ];
        }
        
        $data = [
            'borrow_code' => $this->borrowModel->getNextBorrowCode(),
            'user_id' => session()->get('user_id'),
            'equipment_id' => $equipmentId,
            'accessories' => json_encode($accessoryData),
            'borrow_date' => $this->request->getPost('borrow_date'),
            'due_date' => $this->request->getPost('due_date'),
            'status' => 'pending',
            'notes' => $this->request->getPost('notes'),
            'condition_on_borrow' => 'good'
        ];
        
        if ($this->borrowModel->insert($data)) {
            $email = \Config\Services::email();
            $userModel = new UserModel();
            $user = $userModel->find(session()->get('user_id'));
            
            $email->setTo($user['email']);
            $email->setSubject('Borrow Request Submitted - ITSO Equipment');
            
            $message = "Hello " . $user['first_name'] . ",\n\n";
            $message .= "Your borrow request has been submitted:\n\n";
            $message .= "Borrow Code: " . $data['borrow_code'] . "\n";
            $message .= "Equipment: " . $equipment['name'] . "\n";
            $message .= "Borrow Date: " . $data['borrow_date'] . "\n";
            $message .= "Due Date: " . $data['due_date'] . "\n\n";
            $message .= "Your request is pending approval by ITSO personnel.";
            
            $email->setMessage($message);
            $email->send();
            
            return redirect()->to('/admin/borrow')->with('success', 'Borrow request submitted successfully');
        }
        
        return redirect()->back()->withInput()->with('error', 'Failed to submit borrow request');
    }
    
    public function approve($id)
    {
        $borrow = $this->borrowModel->find($id);
        
        if (!$borrow) {
            return redirect()->to('/admin/borrow')->with('error', 'Borrow record not found');
        }
        
        if ($borrow['status'] !== 'pending') {
            return redirect()->to('/admin/borrow')->with('error', 'This borrow request has already been processed');
        }
        
        $this->borrowModel->update($id, ['status' => 'approved']);
        $this->equipmentModel->updateStock($borrow['equipment_id'], false);
        
        $userModel = new UserModel();
        $user = $userModel->find($borrow['user_id']);
        $equipment = $this->equipmentModel->find($borrow['equipment_id']);
        
        $email = \Config\Services::email();
        $email->setTo($user['email']);
        $email->setSubject('Borrow Request Approved - ITSO Equipment');
        
        $message = "Hello " . $user['first_name'] . ",\n\n";
        $message .= "Your borrow request has been approved!\n\n";
        $message .= "Borrow Code: " . $borrow['borrow_code'] . "\n";
        $message .= "Equipment: " . $equipment['name'] . "\n";
        $message .= "Due Date: " . $borrow['due_date'] . "\n\n";
        $message .= "Please return the equipment on or before the due date.";
        
        $email->setMessage($message);
        $email->send();
        
        return redirect()->to('/admin/borrow')->with('success', 'Borrow request approved successfully');
    }
    
    public function cancel($id)
    {
        $borrow = $this->borrowModel->find($id);
        
        if (!$borrow) {
            return redirect()->to('/admin/borrow')->with('error', 'Borrow record not found');
        }
        
        if ($borrow['status'] !== 'pending') {
            return redirect()->to('/admin/borrow')->with('error', 'Only pending requests can be cancelled');
        }
        
        if ($borrow['user_id'] != session()->get('user_id') && session()->get('role_name') !== 'itso_personnel') {
            return redirect()->to('/admin/borrow')->with('error', 'You can only cancel your own requests');
        }
        
        if ($this->borrowModel->delete($id)) {
            return redirect()->to('/admin/borrow')->with('success', 'Borrow request cancelled successfully');
        }
        
        return redirect()->to('/admin/borrow')->with('error', 'Failed to cancel borrow request');
    }
    
    public function view($id)
    {
        $borrow = $this->borrowModel->select('borrows.*, equipment.name as equipment_name, 
                                              equipment.item_id, equipment.category,
                                              users.first_name, users.last_name, users.email')
            ->join('equipment', 'equipment.id = borrows.equipment_id')
            ->join('users', 'users.id = borrows.user_id')
            ->where('borrows.id', $id)
            ->first();
        
        if (!$borrow) {
            return redirect()->to('/admin/borrow')->with('error', 'Borrow record not found');
        }
        
        $data = [
            'title' => 'Borrow Details',
            'borrow' => $borrow
        ];
        
        return view('admin/borrow/view', $data);
    }
}