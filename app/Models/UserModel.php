<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'uuid', 'role_id', 'email', 'password_hash', 'first_name', 'last_name',
        'contact_number', 'is_active', 'email_verified_at', 'verification_token',
        'password_reset_token', 'password_reset_expires'
    ];
    protected $useTimestamps = true;
    protected $useSoftDeletes = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
    
    public function getUserWithRole($id)
    {
        return $this->select('users.*, roles.name as role_name, roles.description as role_description')
            ->join('roles', 'roles.id = users.role_id')
            ->where('users.id', $id)
            ->first();
    }
    
    public function getAllWithRoles()
    {
        return $this->select('users.*, roles.name as role_name')
            ->join('roles', 'roles.id = users.role_id')
            ->orderBy('users.created_at', 'DESC')
            ->findAll();
    }
    
    public function searchUsers($search)
    {
        return $this->select('users.*, roles.name as role_name')
            ->join('roles', 'roles.id = users.role_id')
            ->groupStart()
                ->like('users.first_name', $search)
                ->orLike('users.last_name', $search)
                ->orLike('users.email', $search)
            ->groupEnd()
            ->findAll();
    }
}