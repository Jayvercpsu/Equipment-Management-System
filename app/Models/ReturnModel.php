<?php

namespace App\Models;

use CodeIgniter\Model;

class ReturnModel extends Model
{
    protected $table = 'returns';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'borrow_id', 'returned_by', 'return_date', 'condition_on_return',
        'fines', 'notes', 'created_at'
    ];
    protected $useTimestamps = false;
    
    public function getReturnsWithDetails()
    {
        return $this->select('returns.*, borrows.borrow_code, equipment.name as equipment_name,
                             equipment.item_id, users.first_name, users.last_name')
            ->join('borrows', 'borrows.id = returns.borrow_id')
            ->join('equipment', 'equipment.id = borrows.equipment_id')
            ->join('users', 'users.id = borrows.user_id')
            ->orderBy('returns.created_at', 'DESC')
            ->findAll();
    }
}