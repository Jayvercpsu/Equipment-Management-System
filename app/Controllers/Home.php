<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/admin/dashboard');
        }
        
        return redirect()->to('/auth/login');
    }
    
    public function about()
    {
        $data = [
            'title' => 'About ITSO Equipment System'
        ];
        
        return view('about', $data);
    }
}