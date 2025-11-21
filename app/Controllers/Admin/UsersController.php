<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;

class UsersController extends BaseController
{
    protected $userModel;
    
    public function __construct()
    {
        $this->userModel = new UserModel();
        helper(['form', 'url']);
    }
    
    public function index()
    {
        $perPage = 10;
        $search = $this->request->getGet('search');
        
        if ($search) {
            $users = $this->userModel->searchUsers($search);
            $pager = null;
        } else {
            $users = $this->userModel->select('users.*, roles.name as role_name')
                ->join('roles', 'roles.id = users.role_id')
                ->paginate($perPage);
            $pager = $this->userModel->pager;
        }
        
        $data = [
            'title' => 'User Management',
            'users' => $users,
            'pager' => $pager,
            'search' => $search
        ];
        
        return view('admin/users/index', $data);
    }
    
    public function create()
    {
        $roleModel = new \App\Models\RoleModel();
        
        $data = [
            'title' => 'Add User',
            'user' => null,
            'roles' => $roleModel->findAll()
        ];
        
        return view('admin/users/form', $data);
    }
    
    public function store()
    {
        $validation = \Config\Services::validation();
        
        $validation->setRules([
            'first_name' => 'required|min_length[2]|max_length[100]',
            'last_name' => 'required|min_length[2]|max_length[100]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'contact_number' => 'required|min_length[10]|max_length[20]',
            'role_id' => 'required|integer',
            'password' => 'required|min_length[8]|regex_match[/^(?=.*[A-Z])(?=.*\d).+$/]',
            'password_confirm' => 'required|matches[password]'
        ], [
            'password' => [
                'regex_match' => 'Password must contain at least one uppercase letter and one number'
            ]
        ]);
        
        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }
        
        $data = [
            'uuid' => \CodeIgniter\I18n\Time::now()->getTimestamp() . rand(1000, 9999),
            'role_id' => $this->request->getPost('role_id'),
            'email' => $this->request->getPost('email'),
            'password_hash' => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT),
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'contact_number' => $this->request->getPost('contact_number'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
            'email_verified_at' => date('Y-m-d H:i:s')
        ];
        
        if ($this->userModel->insert($data)) {
            return redirect()->to('/admin/users')->with('success', 'User created successfully');
        }
        
        return redirect()->back()->withInput()->with('error', 'Failed to create user');
    }
    
    public function edit($id)
    {
        $user = $this->userModel->find($id);
        
        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'User not found');
        }
        
        $roleModel = new \App\Models\RoleModel();
        
        $data = [
            'title' => 'Edit User',
            'user' => $user,
            'roles' => $roleModel->findAll()
        ];
        
        return view('admin/users/form', $data);
    }
    
    public function update($id)
    {
        $user = $this->userModel->find($id);
        
        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'User not found');
        }
        
        $validation = \Config\Services::validation();
        
        $rules = [
            'first_name' => 'required|min_length[2]|max_length[100]',
            'last_name' => 'required|min_length[2]|max_length[100]',
            'email' => "required|valid_email|is_unique[users.email,id,{$id}]",
            'contact_number' => 'required|min_length[10]|max_length[20]',
            'role_id' => 'required|integer'
        ];
        
        if ($this->request->getPost('password')) {
            $rules['password'] = 'min_length[8]|regex_match[/^(?=.*[A-Z])(?=.*\d).+$/]';
            $rules['password_confirm'] = 'matches[password]';
        }
        
        $validation->setRules($rules);
        
        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }
        
        $data = [
            'role_id' => $this->request->getPost('role_id'),
            'email' => $this->request->getPost('email'),
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'contact_number' => $this->request->getPost('contact_number'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0
        ];
        
        if ($this->request->getPost('password')) {
            $data['password_hash'] = password_hash($this->request->getPost('password'), PASSWORD_BCRYPT);
        }
        
        if ($this->userModel->update($id, $data)) {
            return redirect()->to('/admin/users')->with('success', 'User updated successfully');
        }
        
        return redirect()->back()->withInput()->with('error', 'Failed to update user');
    }
    
    public function toggle($id)
    {
        $user = $this->userModel->find($id);
        
        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'User not found');
        }
        
        $newStatus = $user['is_active'] ? 0 : 1;
        
        if ($this->userModel->update($id, ['is_active' => $newStatus])) {
            $message = $newStatus ? 'User activated successfully' : 'User deactivated successfully';
            return redirect()->to('/admin/users')->with('success', $message);
        }
        
        return redirect()->to('/admin/users')->with('error', 'Failed to toggle user status');
    }
    
    public function delete($id)
    {
        $user = $this->userModel->find($id);
        
        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'User not found');
        }
        
        if ($user['id'] == session()->get('user_id')) {
            return redirect()->to('/admin/users')->with('error', 'Cannot delete your own account');
        }
        
        if ($this->userModel->delete($id)) {
            return redirect()->to('/admin/users')->with('success', 'User deleted successfully');
        }
        
        return redirect()->to('/admin/users')->with('error', 'Failed to delete user');
    }
}