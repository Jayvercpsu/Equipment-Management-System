<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ReservationModel;
use App\Models\EquipmentModel;
use App\Models\UserModel;

class ReservationController extends BaseController
{
    protected $reservationModel;
    protected $equipmentModel;
    
    public function __construct()
    {
        $this->reservationModel = new ReservationModel();
        $this->equipmentModel = new EquipmentModel();
        helper(['form', 'url']);
    }
    
    public function index()
    {
        if (session()->get('role_name') === 'itso_personnel') {
            $reservations = $this->reservationModel->getReservationsWithDetails();
        } else {
            $reservations = $this->reservationModel->getUserReservations(session()->get('user_id'));
        }
        
        $data = [
            'title' => 'Reservation Management',
            'reservations' => $reservations
        ];
        
        return view('admin/reservation/index', $data);
    }
    
    public function create()
    {
        if (session()->get('role_name') === 'student') {
            return redirect()->to('/admin/dashboard')->with('error', 'Students cannot make reservations');
        }
        
        $equipment = $this->equipmentModel->getAvailableEquipment();
        
        $data = [
            'title' => 'Create Reservation',
            'equipment' => $equipment,
            'next_reservation_code' => $this->reservationModel->getNextReservationCode()
        ];
        
        return view('admin/reservation/form', $data);
    }
    
    public function store()
    {
        if (session()->get('role_name') === 'student') {
            return redirect()->to('/admin/dashboard')->with('error', 'Students cannot make reservations');
        }
        
        $validation = \Config\Services::validation();
        
        $validation->setRules([
            'equipment_id' => 'required|integer',
            'start_datetime' => 'required|valid_date',
            'end_datetime' => 'required|valid_date'
        ]);
        
        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }
        
        $startDatetime = $this->request->getPost('start_datetime');
        $endDatetime = $this->request->getPost('end_datetime');
        $equipmentId = $this->request->getPost('equipment_id');
        
        $tomorrow = date('Y-m-d', strtotime('+1 day'));
        if (date('Y-m-d', strtotime($startDatetime)) < $tomorrow) {
            return redirect()->back()->withInput()->with('error', 'Reservation must be made at least 1 day in advance');
        }
        
        if (strtotime($endDatetime) <= strtotime($startDatetime)) {
            return redirect()->back()->withInput()->with('error', 'End date must be after start date');
        }
        
        if (!$this->reservationModel->checkAvailability($equipmentId, $startDatetime, $endDatetime)) {
            return redirect()->back()->withInput()->with('error', 'Equipment is not available for the selected dates');
        }
        
        $data = [
            'reservation_code' => $this->reservationModel->getNextReservationCode(),
            'user_id' => session()->get('user_id'),
            'equipment_id' => $equipmentId,
            'start_datetime' => $startDatetime,
            'end_datetime' => $endDatetime,
            'status' => 'active'
        ];
        
        if ($this->reservationModel->insert($data)) {
            $userModel = new UserModel();
            $user = $userModel->find(session()->get('user_id'));
            $equipment = $this->equipmentModel->find($equipmentId);
            
            $email = \Config\Services::email();
            $email->setTo($user['email']);
            $email->setSubject('Reservation Confirmation - ITSO Equipment');
            
            $message = "Hello " . $user['first_name'] . ",\n\n";
            $message .= "Your reservation has been confirmed:\n\n";
            $message .= "Reservation Code: " . $data['reservation_code'] . "\n";
            $message .= "Equipment: " . $equipment['name'] . "\n";
            $message .= "Start: " . date('M d, Y H:i', strtotime($startDatetime)) . "\n";
            $message .= "End: " . date('M d, Y H:i', strtotime($endDatetime)) . "\n\n";
            $message .= "Please pick up the equipment at the scheduled time.";
            
            $email->setMessage($message);
            $email->send();
            
            return redirect()->to('/admin/reservation')->with('success', 'Reservation created successfully');
        }
        
        return redirect()->back()->withInput()->with('error', 'Failed to create reservation');
    }
    
    public function cancel($id)
    {
        $reservation = $this->reservationModel->find($id);
        
        if (!$reservation) {
            return redirect()->to('/admin/reservation')->with('error', 'Reservation not found');
        }
        
        if ($reservation['user_id'] != session()->get('user_id') && session()->get('role_name') !== 'itso_personnel') {
            return redirect()->to('/admin/reservation')->with('error', 'You can only cancel your own reservations');
        }
        
        if ($reservation['status'] !== 'active') {
            return redirect()->to('/admin/reservation')->with('error', 'Only active reservations can be cancelled');
        }
        
        if ($this->reservationModel->update($id, ['status' => 'cancelled'])) {
            $userModel = new UserModel();
            $user = $userModel->find($reservation['user_id']);
            
            $email = \Config\Services::email();
            $email->setTo($user['email']);
            $email->setSubject('Reservation Cancelled - ITSO Equipment');
            
            $message = "Hello " . $user['first_name'] . ",\n\n";
            $message .= "Your reservation has been cancelled:\n\n";
            $message .= "Reservation Code: " . $reservation['reservation_code'] . "\n\n";
            $message .= "If you have any questions, please contact us.";
            
            $email->setMessage($message);
            $email->send();
            
            return redirect()->to('/admin/reservation')->with('success', 'Reservation cancelled successfully');
        }
        
        return redirect()->to('/admin/reservation')->with('error', 'Failed to cancel reservation');
    }
    
    public function reschedule($id)
    {
        $reservation = $this->reservationModel->select('reservations.*, equipment.name as equipment_name')
            ->join('equipment', 'equipment.id = reservations.equipment_id')
            ->where('reservations.id', $id)
            ->first();
        
        if (!$reservation) {
            return redirect()->to('/admin/reservation')->with('error', 'Reservation not found');
        }
        
        if ($reservation['user_id'] != session()->get('user_id') && session()->get('role_name') !== 'itso_personnel') {
            return redirect()->to('/admin/reservation')->with('error', 'You can only reschedule your own reservations');
        }
        
        $data = [
            'title' => 'Reschedule Reservation',
            'reservation' => $reservation
        ];
        
        return view('admin/reservation/reschedule', $data);
    }
    
    public function updateSchedule($id)
    {
        $reservation = $this->reservationModel->find($id);
        
        if (!$reservation) {
            return redirect()->to('/admin/reservation')->with('error', 'Reservation not found');
        }
        
        $validation = \Config\Services::validation();
        
        $validation->setRules([
            'start_datetime' => 'required|valid_date',
            'end_datetime' => 'required|valid_date'
        ]);
        
        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }
        
        $startDatetime = $this->request->getPost('start_datetime');
        $endDatetime = $this->request->getPost('end_datetime');
        
        $tomorrow = date('Y-m-d', strtotime('+1 day'));
        if (date('Y-m-d', strtotime($startDatetime)) < $tomorrow) {
            return redirect()->back()->withInput()->with('error', 'Reservation must be made at least 1 day in advance');
        }
        
        if (!$this->reservationModel->checkAvailability($reservation['equipment_id'], $startDatetime, $endDatetime, $id)) {
            return redirect()->back()->withInput()->with('error', 'Equipment is not available for the selected dates');
        }
        
        if ($this->reservationModel->update($id, [
            'start_datetime' => $startDatetime,
            'end_datetime' => $endDatetime
        ])) {
            return redirect()->to('/admin/reservation')->with('success', 'Reservation rescheduled successfully');
        }
        
        return redirect()->back()->withInput()->with('error', 'Failed to reschedule reservation');
    }
    
    public function view($id)
    {
        $reservation = $this->reservationModel->select('reservations.*, equipment.name as equipment_name,
                                                        equipment.item_id, users.first_name, users.last_name')
            ->join('equipment', 'equipment.id = reservations.equipment_id')
            ->join('users', 'users.id = reservations.user_id')
            ->where('reservations.id', $id)
            ->first();
        
        if (!$reservation) {
            return redirect()->to('/admin/reservation')->with('error', 'Reservation not found');
        }
        
        $data = [
            'title' => 'Reservation Details',
            'reservation' => $reservation
        ];
        
        return view('admin/reservation/view', $data);
    }
}