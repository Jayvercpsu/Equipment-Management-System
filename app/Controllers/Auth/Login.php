<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Login extends BaseController
{
    public function index()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/admin/dashboard');
        }
        
        return view('auth/login', ['title' => 'Login']);
    }
    
    public function authenticate()
    {
        $validation = \Config\Services::validation();
        
        $validation->setRules([
            'email' => 'required|valid_email',
            'password' => 'required'
        ]);
        
        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }
        
        $userModel = new UserModel();
        $user = $userModel->where('email', $this->request->getPost('email'))->first();
        
        if (!$user) {
            return redirect()->back()->withInput()->with('error', 'Invalid email or password');
        }
        
        if (!password_verify($this->request->getPost('password'), $user['password_hash'])) {
            return redirect()->back()->withInput()->with('error', 'Invalid email or password');
        }
        
        if (!$user['is_active']) {
            return redirect()->back()->withInput()->with('error', 'Your account has been deactivated');
        }
        
        if (!$user['email_verified_at']) {
            return redirect()->back()->withInput()->with('error', 'Please verify your email before logging in');
        }
        
        $userWithRole = $userModel->getUserWithRole($user['id']);
        
        $sessionData = [
            'user_id' => $user['id'],
            'email' => $user['email'],
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'role_id' => $user['role_id'],
            'role_name' => $userWithRole['role_name'],
            'isLoggedIn' => true
        ];
        
        session()->set($sessionData);
        
        return redirect()->to('/admin/dashboard')->with('success', 'Welcome back, ' . $user['first_name'] . '!');
    }
    
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/auth/login')->with('success', 'You have been logged out successfully');
    }
}