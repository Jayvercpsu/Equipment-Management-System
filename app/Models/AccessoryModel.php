<?php

namespace App\Models;

use CodeIgniter\Model;

class AccessoryModel extends Model
{
    protected $table = 'accessories';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'equipment_id', 'accessory_item_id', 'name', 'quantity', 'required_when_borrowed'
    ];
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    
    public function getEquipmentAccessories($equipmentId)
    {
        return $this->where('equipment_id', $equipmentId)->findAll();
    }
    
    public function getRequiredAccessories($equipmentId)
    {
        return $this->where('equipment_id', $equipmentId)
            ->where('required_when_borrowed', 1)
            ->findAll();
    }
}