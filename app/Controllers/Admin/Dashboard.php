<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\EquipmentModel;
use App\Models\BorrowModel;
use App\Models\ReturnModel;
use App\Models\ReservationModel;
use App\Models\UserModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $equipmentModel = new EquipmentModel();
        $borrowModel = new BorrowModel();
        $returnModel = new ReturnModel();
        $reservationModel = new ReservationModel();
        $userModel = new UserModel();
        
        $borrowModel->checkOverdue();
        
        $data = [
            'title' => 'Dashboard',
            'total_equipment' => $equipmentModel->countAllResults(),
            'available_equipment' => $equipmentModel->where('status', 'available')->countAllResults(),
            'borrowed_equipment' => $equipmentModel->where('status', 'borrowed')->countAllResults(),
            'unusable_equipment' => $equipmentModel->where('status', 'unusable')->countAllResults(),
            'total_users' => $userModel->countAllResults(),
            'active_borrows' => $borrowModel->whereIn('status', ['pending', 'approved'])->countAllResults(),
            'overdue_borrows' => $borrowModel->where('status', 'overdue')->countAllResults(),
            'active_reservations' => $reservationModel->where('status', 'active')->countAllResults(),
            'recent_borrows' => $borrowModel->getBorrowsWithDetails(),
            'recent_returns' => $returnModel->getReturnsWithDetails()
        ];
        
        if (session()->get('role_name') !== 'itso_personnel') {
            $data['my_borrows'] = $borrowModel->getUserBorrows(session()->get('user_id'));
            $data['my_reservations'] = $reservationModel->getUserReservations(session()->get('user_id'));
        }
        
        return view('admin/dashboard', $data);
    }
}