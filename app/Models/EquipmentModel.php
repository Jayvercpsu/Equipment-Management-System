<?php

namespace App\Models;

use CodeIgniter\Model;

class EquipmentModel extends Model
{
    protected $table = 'equipment';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'item_id', 'name', 'description', 'category', 'brand', 'model',
        'serial_number', 'stock_count', 'minimum_stock', 'status',
        'image_path', 'thumbnail_path', 'created_by'
    ];
    protected $useTimestamps = true;
    protected $useSoftDeletes = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
    
    public function getAvailableEquipment()
    {
        return $this->where('status', 'available')
            ->where('stock_count >', 0)
            ->findAll();
    }
    
    public function getUnusableEquipment()
    {
        return $this->where('status', 'unusable')->findAll();
    }
    
    public function searchEquipment($search)
    {
        return $this->groupStart()
            ->like('item_id', $search)
            ->orLike('name', $search)
            ->orLike('category', $search)
            ->orLike('brand', $search)
            ->groupEnd()
            ->findAll();
    }
    
    public function updateStock($id, $increment = true)
    {
        $equipment = $this->find($id);
        if ($equipment) {
            $newCount = $increment ? $equipment['stock_count'] + 1 : $equipment['stock_count'] - 1;
            $this->update($id, ['stock_count' => max(0, $newCount)]);
            
            if ($newCount <= 0) {
                $this->update($id, ['status' => 'borrowed']);
            } elseif ($newCount > 0 && $equipment['status'] === 'borrowed') {
                $this->update($id, ['status' => 'available']);
            }
        }
    }
    
    public function getNextItemId()
    {
        $last = $this->orderBy('id', 'DESC')->first();
        if ($last) {
            $num = (int)substr($last['item_id'], 3) + 1;
        } else {
            $num = 1;
        }
        return 'EQ-' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }
}