<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ReturnModel;
use App\Models\BorrowModel;
use App\Models\EquipmentModel;
use App\Models\UserModel;

class ReturnController extends BaseController
{
    protected $returnModel;
    protected $borrowModel;
    protected $equipmentModel;
    
    public function __construct()
    {
        $this->returnModel = new ReturnModel();
        $this->borrowModel = new BorrowModel();
        $this->equipmentModel = new EquipmentModel();
        helper(['form', 'url']);
    }
    
    public function index()
    {
        $returns = $this->returnModel->getReturnsWithDetails();
        $activeBorrows = $this->borrowModel->getActiveBorrows();
        
        $data = [
            'title' => 'Return Management',
            'returns' => $returns,
            'active_borrows' => $activeBorrows
        ];
        
        return view('admin/return/index', $data);
    }
    
    public function create($borrowId)
    {
        $borrow = $this->borrowModel->select('borrows.*, equipment.name as equipment_name, 
                                              equipment.item_id, users.first_name, users.last_name')
            ->join('equipment', 'equipment.id = borrows.equipment_id')
            ->join('users', 'users.id = borrows.user_id')
            ->where('borrows.id', $borrowId)
            ->first();
        
        if (!$borrow) {
            return redirect()->to('/admin/return')->with('error', 'Borrow record not found');
        }
        
        if ($borrow['status'] === 'returned') {
            return redirect()->to('/admin/return')->with('error', 'This equipment has already been returned');
        }
        
        $existingReturn = $this->returnModel->where('borrow_id', $borrowId)->first();
        if ($existingReturn) {
            return redirect()->to('/admin/return')->with('error', 'Return record already exists');
        }
        
        $data = [
            'title' => 'Process Return',
            'borrow' => $borrow
        ];
        
        return view('admin/return/form', $data);
    }
    
    public function store()
    {
        try {
            $borrowId = $this->request->getPost('borrow_id');
            $borrow = $this->borrowModel->find($borrowId);
            
            if (!$borrow) {
                return redirect()->to('/admin/return')->with('error', 'Borrow record not found');
            }
            
            $existingReturn = $this->returnModel->where('borrow_id', $borrowId)->first();
            if ($existingReturn) {
                return redirect()->to('/admin/return')->with('error', 'Return record already exists');
            }
            
            $condition = $this->request->getPost('condition_on_return');
            $returnDate = $this->request->getPost('return_date');
            $returnDate = date('Y-m-d H:i:s', strtotime($returnDate));
            
            $returnData = [
                'borrow_id' => $borrowId,
                'returned_by' => session()->get('user_id'),
                'return_date' => $returnDate,
                'condition_on_return' => $condition,
                'fines' => $this->request->getPost('fines') ? floatval($this->request->getPost('fines')) : 0,
                'notes' => $this->request->getPost('notes'),
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            $db = \Config\Database::connect();
            $db->transStart();
            
            // Insert return record
            $this->returnModel->insert($returnData);
            
            // Update borrow status
            $this->borrowModel->update($borrowId, ['status' => 'returned']);
            
            // Update equipment stock
            $equipment = $this->equipmentModel->find($borrow['equipment_id']);
            
            if ($condition === 'good') {
                $this->equipmentModel->update($borrow['equipment_id'], [
                    'stock_count' => $equipment['stock_count'] + 1
                ]);
            } elseif ($condition === 'damaged') {
                $this->equipmentModel->update($borrow['equipment_id'], [
                    'status' => 'unusable',
                    'stock_count' => $equipment['stock_count'] + 1
                ]);
            }
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                return redirect()->to('/admin/return')->with('error', 'Failed to process return');
            }
            
            // Send email notification
            try {
                $userModel = new UserModel();
                $user = $userModel->find($borrow['user_id']);
                
                $email = \Config\Services::email();
                $email->setTo($user['email']);
                $email->setSubject('Equipment Return Confirmation - ITSO');
                
                $message = "Hello " . $user['first_name'] . ",\n\n";
                $message .= "Equipment return has been processed:\n\n";
                $message .= "Borrow Code: " . $borrow['borrow_code'] . "\n";
                $message .= "Equipment: " . $equipment['name'] . "\n";
                $message .= "Return Date: " . $returnDate . "\n";
                $message .= "Condition: " . ucfirst($condition) . "\n";
                
                if ($returnData['fines'] > 0) {
                    $message .= "Fines: PHP " . number_format($returnData['fines'], 2) . "\n";
                }
                
                $message .= "\nThank you for returning the equipment.";
                
                $email->setMessage($message);
                $email->send();
            } catch (\Exception $e) {
                log_message('error', 'Email error: ' . $e->getMessage());
            }
            
            return redirect()->to('/admin/return')->with('success', 'Return processed successfully');
            
        } catch (\Exception $e) {
            log_message('error', 'Return error: ' . $e->getMessage());
            return redirect()->to('/admin/return')->with('error', 'Error: ' . $e->getMessage());
        }
    }
}