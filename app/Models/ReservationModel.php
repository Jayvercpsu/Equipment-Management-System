<?php

namespace App\Models;

use CodeIgniter\Model;

class ReservationModel extends Model
{
    protected $table = 'reservations';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'reservation_code', 'user_id', 'equipment_id', 'start_datetime',
        'end_datetime', 'status'
    ];
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    public function getReservationsWithDetails()
    {
        return $this->select('reservations.*, equipment.name as equipment_name,
                             equipment.item_id, users.first_name, users.last_name, users.email')
            ->join('equipment', 'equipment.id = reservations.equipment_id')
            ->join('users', 'users.id = reservations.user_id')
            ->orderBy('reservations.start_datetime', 'DESC')
            ->findAll();
    }
    
    public function getUserReservations($userId)
    {
        return $this->select('reservations.*, equipment.name as equipment_name, equipment.item_id')
            ->join('equipment', 'equipment.id = reservations.equipment_id')
            ->where('reservations.user_id', $userId)
            ->orderBy('reservations.start_datetime', 'DESC')
            ->findAll();
    }
    
    public function checkAvailability($equipmentId, $startDate, $endDate, $excludeId = null)
    {
        $query = $this->where('equipment_id', $equipmentId)
            ->where('status', 'active')
            ->groupStart()
                ->where('start_datetime <=', $endDate)
                ->where('end_datetime >=', $startDate)
            ->groupEnd();
        
        if ($excludeId) {
            $query->where('id !=', $excludeId);
        }
        
        return $query->countAllResults() === 0;
    }
    
    public function getNextReservationCode()
    {
        $last = $this->orderBy('id', 'DESC')->first();
        if ($last) {
            $num = (int)substr($last['reservation_code'], 3) + 1;
        } else {
            $num = 1;
        }
        return 'RS-' . str_pad($num, 6, '0', STR_PAD_LEFT);
    }
}