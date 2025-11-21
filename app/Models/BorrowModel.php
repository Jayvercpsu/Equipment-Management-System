<?php

namespace App\Models;

use CodeIgniter\Model;

class BorrowModel extends Model
{
    protected $table = 'borrows';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'borrow_code', 'user_id', 'equipment_id', 'accessories',
        'borrow_date', 'due_date', 'expected_return_date', 'status',
        'notes', 'condition_on_borrow'
    ];
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    public function getBorrowsWithDetails()
    {
        return $this->select('borrows.*, equipment.name as equipment_name, equipment.item_id, 
                             users.first_name, users.last_name, users.email')
            ->join('equipment', 'equipment.id = borrows.equipment_id')
            ->join('users', 'users.id = borrows.user_id')
            ->orderBy('borrows.created_at', 'DESC')
            ->findAll();
    }
    
    public function getUserBorrows($userId)
    {
        return $this->select('borrows.*, equipment.name as equipment_name, equipment.item_id')
            ->join('equipment', 'equipment.id = borrows.equipment_id')
            ->where('borrows.user_id', $userId)
            ->orderBy('borrows.created_at', 'DESC')
            ->findAll();
    }
    
    public function getActiveBorrows()
    {
        return $this->select('borrows.*, equipment.name as equipment_name, equipment.item_id,
                             users.first_name, users.last_name')
            ->join('equipment', 'equipment.id = borrows.equipment_id')
            ->join('users', 'users.id = borrows.user_id')
            ->whereIn('borrows.status', ['pending', 'approved'])
            ->findAll();
    }
    
    public function getNextBorrowCode()
    {
        $last = $this->orderBy('id', 'DESC')->first();
        if ($last) {
            $num = (int)substr($last['borrow_code'], 3) + 1;
        } else {
            $num = 1;
        }
        return 'BR-' . str_pad($num, 6, '0', STR_PAD_LEFT);
    }
    
    public function checkOverdue()
    {
        $now = date('Y-m-d H:i:s');
        return $this->where('status', 'approved')
            ->where('due_date <', $now)
            ->set('status', 'overdue')
            ->update();
    }
}