<?php

namespace App\Controllers;

use App\Models\UserModel;

class Profile extends BaseController
{
    protected $userModel;
    
    public function __construct()
    {
        $this->userModel = new UserModel();
        helper(['form', 'url']);
    }
    
    public function index()
    {
        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);
        
        if (!$user) {
            return redirect()->to('/auth/login')->with('error', 'User not found');
        }
        
        $data = [
            'title' => 'My Profile',
            'user' => $user
        ];
        
        return view('profile/index', $data);
    }
    
    public function update()
    {
        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);
        
        if (!$user) {
            return redirect()->to('/auth/login')->with('error', 'User not found');
        }
        
        $validation = \Config\Services::validation();
        
        // Check if updating password
        $isPasswordUpdate = !empty($this->request->getPost('current_password'));
        
        if ($isPasswordUpdate) {
            $validation->setRules([
                'first_name' => 'required|min_length[2]|max_length[50]',
                'last_name' => 'required|min_length[2]|max_length[50]',
                'email' => "required|valid_email|is_unique[users.email,id,{$userId}]",
                'contact_number' => 'permit_empty|min_length[10]|max_length[15]',
                'current_password' => 'required',
                'new_password' => 'required|min_length[8]',
                'confirm_password' => 'required|matches[new_password]'
            ]);
        } else {
            $validation->setRules([
                'first_name' => 'required|min_length[2]|max_length[50]',
                'last_name' => 'required|min_length[2]|max_length[50]',
                'email' => "required|valid_email|is_unique[users.email,id,{$userId}]",
                'contact_number' => 'permit_empty|min_length[10]|max_length[15]'
            ]);
        }
        
        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }
        
        // Verify current password if updating password
        if ($isPasswordUpdate) {
            if (!password_verify($this->request->getPost('current_password'), $user['password_hash'])) {
                return redirect()->back()->withInput()->with('error', 'Current password is incorrect');
            }
        }
        
        $updateData = [
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'email' => $this->request->getPost('email'),
            'contact_number' => $this->request->getPost('contact_number'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        if ($isPasswordUpdate) {
            $updateData['password_hash'] = password_hash($this->request->getPost('new_password'), PASSWORD_DEFAULT);
        }
        
        if ($this->userModel->update($userId, $updateData)) {
            // Update session data
            session()->set([
                'first_name' => $updateData['first_name'],
                'last_name' => $updateData['last_name'],
                'email' => $updateData['email']
            ]);
            
            return redirect()->to('/profile')->with('success', 'Profile updated successfully');
        }
        
        return redirect()->back()->withInput()->with('error', 'Failed to update profile');
    }
}