<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\UserModel;

class ForgotPassword extends BaseController
{
    public function index()
    {
        return view('auth/forgot', ['title' => 'Forgot Password']);
    }
    
    public function send()
    {
        $validation = \Config\Services::validation();
        
        $validation->setRules([
            'email' => 'required|valid_email'
        ]);
        
        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }
        
        $userModel = new UserModel();
        $user = $userModel->where('email', $this->request->getPost('email'))->first();
        
        if (!$user) {
            return redirect()->back()->with('success', 'If the email exists, a reset link has been sent.');
        }
        
        $resetToken = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        $userModel->update($user['id'], [
            'password_reset_token' => $resetToken,
            'password_reset_expires' => $expires
        ]);
        
        $email = \Config\Services::email();
        $email->setTo($user['email']);
        $email->setSubject('Password Reset Request - ITSO Equipment System');
        
        $resetLink = base_url('auth/reset-password/' . $resetToken);
        $message = "Hello " . $user['first_name'] . ",\n\n";
        $message .= "We received a request to reset your password.\n\n";
        $message .= "Click the link below to reset your password:\n\n";
        $message .= $resetLink . "\n\n";
        $message .= "This link will expire in 1 hour.\n\n";
        $message .= "If you did not request this, please ignore this email.";
        
        $email->setMessage($message);
        $email->send();
        
        return redirect()->to('/auth/login')->with('success', 'Password reset link has been sent to your email.');
    }
}