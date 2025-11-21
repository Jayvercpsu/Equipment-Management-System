<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        $userRole = $session->get('role_name');
        
        if (!empty($arguments)) {
            $requiredRole = $arguments[0];
            
            if ($userRole !== $requiredRole) {
                $session->setFlashdata('error', 'Access denied. Insufficient permissions.');
                return redirect()->to('/admin/dashboard');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}