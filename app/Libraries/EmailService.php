<?php

namespace App\Libraries;

use Config\Services;

class EmailService
{
    protected $email;
    
    public function __construct()
    {
        $this->email = Services::email();
    }
    
    public function sendVerification($to, $name, $token)
    {
        $this->email->setTo($to);
        $this->email->setSubject('Verify Your Email - ITSO Equipment System');
        
        $verifyLink = base_url('auth/verify/' . $token);
        $message = "Hello " . $name . ",\n\n";
        $message .= "Thank you for registering with ITSO Equipment Management System.\n\n";
        $message .= "Please click the link below to verify your email address:\n\n";
        $message .= $verifyLink . "\n\n";
        $message .= "This link will expire in 24 hours.\n\n";
        $message .= "If you did not create this account, please ignore this email.";
        
        $this->email->setMessage($message);
        
        return $this->email->send();
    }
    
    public function sendPasswordReset($to, $name, $token)
    {
        $this->email->setTo($to);
        $this->email->setSubject('Password Reset Request - ITSO Equipment System');
        
        $resetLink = base_url('auth/reset-password/' . $token);
        $message = "Hello " . $name . ",\n\n";
        $message .= "We received a request to reset your password.\n\n";
        $message .= "Click the link below to reset your password:\n\n";
        $message .= $resetLink . "\n\n";
        $message .= "This link will expire in 1 hour.\n\n";
        $message .= "If you did not request this, please ignore this email.";
        
        $this->email->setMessage($message);
        
        return $this->email->send();
    }
    
    public function sendBorrowNotification($to, $borrowData)
    {
        $this->email->setTo($to);
        $this->email->setSubject('Borrow Request Submitted - ITSO Equipment');
        
        $message = "Hello " . $borrowData['user_name'] . ",\n\n";
        $message .= "Your borrow request has been submitted:\n\n";
        $message .= "Borrow Code: " . $borrowData['borrow_code'] . "\n";
        $message .= "Equipment: " . $borrowData['equipment_name'] . "\n";
        $message .= "Borrow Date: " . $borrowData['borrow_date'] . "\n";
        $message .= "Due Date: " . $borrowData['due_date'] . "\n\n";
        $message .= "Your request is pending approval by ITSO personnel.";
        
        $this->email->setMessage($message);
        
        return $this->email->send();
    }
    
    public function sendBorrowApproval($to, $borrowData)
    {
        $this->email->setTo($to);
        $this->email->setSubject('Borrow Request Approved - ITSO Equipment');
        
        $message = "Hello " . $borrowData['user_name'] . ",\n\n";
        $message .= "Your borrow request has been approved!\n\n";
        $message .= "Borrow Code: " . $borrowData['borrow_code'] . "\n";
        $message .= "Equipment: " . $borrowData['equipment_name'] . "\n";
        $message .= "Due Date: " . $borrowData['due_date'] . "\n\n";
        $message .= "Please return the equipment on or before the due date.";
        
        $this->email->setMessage($message);
        
        return $this->email->send();
    }
    
    public function sendReturnConfirmation($to, $returnData)
    {
        $this->email->setTo($to);
        $this->email->setSubject('Equipment Return Confirmation - ITSO');
        
        $message = "Hello " . $returnData['user_name'] . ",\n\n";
        $message .= "Equipment return has been processed:\n\n";
        $message .= "Borrow Code: " . $returnData['borrow_code'] . "\n";
        $message .= "Equipment: " . $returnData['equipment_name'] . "\n";
        $message .= "Return Date: " . $returnData['return_date'] . "\n";
        $message .= "Condition: " . ucfirst($returnData['condition']) . "\n";
        
        if ($returnData['fines'] > 0) {
            $message .= "Fines: PHP " . number_format($returnData['fines'], 2) . "\n";
        }
        
        $message .= "\nThank you for returning the equipment.";
        
        $this->email->setMessage($message);
        
        return $this->email->send();
    }
    
    public function sendReservationConfirmation($to, $reservationData)
    {
        $this->email->setTo($to);
        $this->email->setSubject('Reservation Confirmation - ITSO Equipment');
        
        $message = "Hello " . $reservationData['user_name'] . ",\n\n";
        $message .= "Your reservation has been confirmed:\n\n";
        $message .= "Reservation Code: " . $reservationData['reservation_code'] . "\n";
        $message .= "Equipment: " . $reservationData['equipment_name'] . "\n";
        $message .= "Start: " . $reservationData['start_datetime'] . "\n";
        $message .= "End: " . $reservationData['end_datetime'] . "\n\n";
        $message .= "Please pick up the equipment at the scheduled time.";
        
        $this->email->setMessage($message);
        
        return $this->email->send();
    }
    
    public function sendReservationCancellation($to, $reservationData)
    {
        $this->email->setTo($to);
        $this->email->setSubject('Reservation Cancelled - ITSO Equipment');
        
        $message = "Hello " . $reservationData['user_name'] . ",\n\n";
        $message .= "Your reservation has been cancelled:\n\n";
        $message .= "Reservation Code: " . $reservationData['reservation_code'] . "\n\n";
        $message .= "If you have any questions, please contact us.";
        
        $this->email->setMessage($message);
        
        return $this->email->send();
    }
}