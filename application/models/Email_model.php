<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Email_model extends CI_Model {

	function send_email_verification($Fullname,$email,$randCode)
	{
		
	
	    $msg = $this->load->view('emails/change_email_format', $data,true);
	    $this->load->library('email');
        $this->email->set_newline("\r\n");

      
        $this->email->from(CNF_EMAIL, 'Central Mindanao University ');
        $this->email->to($email); 

        $this->email->subject('Important:Email Account Change Request');
        $this->email->message($msg);  


      	$result = $this->email->send();
	}

	function send_email_registration( $fname,$lname,$email,$randCode)
	{
		
		 $data = array(
	        'firstname'  => $fname  ,
	        'lastname'  => $lname ,
	        'email'    => $email,
	        'password'  => $npwd  ,
	        'code'    => $randCode

      );
      	
	   
	    $msg = $this->load->view('emails/registration', $data,true);
	    $this->load->library('email', $config);
        $this->email->set_newline("\r\n");

      
        $this->email->from(CNF_EMAIL, 'Central Mindanao University ');
        $this->email->to($email); 

        $this->email->subject('Important:Email Account Registration');
        $this->email->message($msg);  


      	$result = $this->email->send();
	}
	
	function process_reset($Fullname,$email,$randCode)
	{
		
	    $data = array(
        'Fullname'  => $Fullname  ,
        'email'    => $email ,
        'code'    => $randCode

      );
	    $msg = $this->load->view('emails/change_password', $data,true);
	    $this->load->library('email');
        $this->email->set_newline("\r\n");

      
        $this->email->from(CNF_EMAIL, 'Central Mindanao University ');
        $this->email->to($email); 

        $this->email->subject('Important:Account Change Password Request');
        $this->email->message($msg);  


      	$result = $this->email->send();
	}
	function process_2_way_auth($Fullname,$email,$randCode)
	{
		
	    $data = array(
        'Fullname'  => $Fullname  ,
        'email'    => $email ,
        'code'    => $randCode

      );
	    $msg = $this->load->view('emails/2_way_auth', $data,true);
	    $this->load->library('email', $config);
        $this->email->set_newline("\r\n");

      
        $this->email->from(CNF_EMAIL, 'Central Mindanao University ');
        $this->email->to($email); 

        $this->email->subject('Important:Email Verification Code Request');
        $this->email->message($msg);  


      	$result = $this->email->send();
	}

	function account_lock_notif($Fullname,$email)
	{
		
	    $data = array(
        'Fullname'  => $Fullname  ,
        'email'    => $email ,

      );
	    $msg = $this->load->view('emails/account_lock', $data,true);
	    $this->load->library('email', $config);
        $this->email->set_newline("\r\n");

      
        $this->email->from(CNF_EMAIL, 'Central Mindanao University ');
        $this->email->to($email); 

        $this->email->subject('Important:Account Temporary Lock');
        $this->email->message($msg);  


      	$result = $this->email->send();
	}


	function send_announcement($title,$description,$fullname,$email)
	{
		$data = array(
	        'title'  => $title  ,
	        'email'    => $email ,
	        'description'  => $description  ,
	        'Fullname'  => $fullname  ,
	     );
	    $msg = $this->load->view('emails/announcement_notif', $data,true);
	    $this->load->library('email');
        $this->email->set_newline("\r\n");

      
        $this->email->from(CNF_EMAIL, 'ibml274.org');
        $this->email->to($email); 

        $this->email->subject('Important:Announcement: '. $title);
        $this->email->message($msg);  
        $this->email->set_mailtype('html'); // Set email format to HTML

      	if ($this->email->send()) {
        // Email sent successfully, log it
	        log_message('info', 'Email sent successfully to: ' . $email);
	        return "success";
	    } else {
	        // Email sending failed, log error
	        $error_msg = '';//$this->email->print_debugger();
	        log_message('error', 'Failed to send email to ' . $email . ': ' . $error_msg);
	        return "error";
	    }
	}
}
