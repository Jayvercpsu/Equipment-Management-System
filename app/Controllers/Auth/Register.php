<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\RoleModel;

class Register extends BaseController
{
    public function index()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/admin/dashboard');
        }
        
        try {
            $roleModel = new RoleModel();
            $roles = $roleModel->whereIn('name', ['associate', 'student'])->findAll();
            
            $data = [
                'title' => 'Register',
                'roles' => $roles
            ];
            
            return view('auth/register', $data);
        } catch (\Exception $e) {
            log_message('error', 'Register page error: ' . $e->getMessage());
            die('Error loading register page: ' . $e->getMessage());
        }
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
        
        $userModel = new UserModel();
        
        $verificationToken = bin2hex(random_bytes(32));
        
        $data = [
            'uuid' => \CodeIgniter\I18n\Time::now()->getTimestamp() . rand(1000, 9999),
            'role_id' => $this->request->getPost('role_id'),
            'email' => $this->request->getPost('email'),
            'password_hash' => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT),
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'contact_number' => $this->request->getPost('contact_number'),
            'verification_token' => $verificationToken,
            'is_active' => 1
        ];
        
        if ($userModel->insert($data)) {
            $email = \Config\Services::email();
            $email->setTo($data['email']);
            $email->setSubject('Verify Your Email - ITSO Equipment System');
            
            $verifyLink = base_url('auth/verify/' . $verificationToken);
            $message = "Hello " . $data['first_name'] . ",\n\n";
            $message .= "Thank you for registering with ITSO Equipment Management System.\n\n";
            $message .= "Please click the link below to verify your email address:\n\n";
            $message .= $verifyLink . "\n\n";
            $message .= "This link will expire in 24 hours.\n\n";
            $message .= "If you did not create this account, please ignore this email.";
            
            $email->setMessage($message);
            $email->send();
            
            return redirect()->to('/auth/login')->with('success', 'Registration successful! Please check your email to verify your account.');
        }
        
        return redirect()->back()->withInput()->with('error', 'Failed to register. Please try again.');
    }
    
    public function verify($token)
    {
        $userModel = new UserModel();
        $user = $userModel->where('verification_token', $token)->first();
        
        if (!$user) {
            return redirect()->to('/auth/login')->with('error', 'Invalid verification token');
        }
        
        if ($user['email_verified_at']) {
            return redirect()->to('/auth/login')->with('info', 'Email already verified. Please login.');
        }
        
        $userModel->update($user['id'], [
            'email_verified_at' => date('Y-m-d H:i:s'),
            'verification_token' => null
        ]);
        
        return redirect()->to('/auth/login')->with('success', 'Email verified successfully! You can now login.');
    }
}