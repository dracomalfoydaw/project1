<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Controllerannouncementinfo101 extends CI_Controller {

	function __construct() {
		parent::__construct();
		if(!$this->session->userdata('logged_in_session')) :
			redirect('login',301);

		endif;
		$this->load->model('Announcementinfomodel','announcement');
		$this->load->model('Email_model','email_model');
		$this->load->model('Membershipinfomodel','members');
	}




	public function index()
	{
		$this->data = [];
		$this->data['pageTitle'] = "Annoucement Information";
		$this->data['pageSubtitle'] = "";
		$this->data['pageSubtitleTable'] = "Annoucement List";
		$this->data['custom_css'] = $this->load->view('announcement/css_script',$this->data,true);
		$this->data['content'] = $this->load->view('announcement/home',$this->data, true );
		$this->data['home_script'] = $this->load->view('announcement/home_script',$this->data, true );
		$this->load->view('layouts/main', $this->data );
	}

	public function findinfo()
	{
		if(isset($this->input->post('search')['value']))
		{
			$fieldValue = $this->htmlpurifier_lib->purify($this->input->post('search')['value']) ;
		}
		else
		{
			$fieldValue = "" ;
		}
		
		echo json_encode($this->announcement->getData($fieldValue)) ;
	}

	public function updatetransactioninfo()
	{
		$this->form_validation->set_rules('userID', 'Data ', 'required|trim|htmlspecialchars');
		
		$this->form_validation->set_rules('TitleName', 'Title Name', 'required|trim|htmlspecialchars');

		if ($this->form_validation->run()) {
			$TitleName  = $this->htmlpurifier_lib->purify($this->input->post('TitleName'));
            $Description = $this->htmlpurifier_lib->purify($this->input->post('Description'));
            $userID = $this->htmlpurifier_lib->purify($this->input->post('userID'));



	        // Call the model method to update the profile
	        $result = $this->announcement->updateProfile($userID, $TitleName, $Description);

	        $data = array(
		        'message' =>  $result->SuccessMessage,  // Assuming success when validation passes
		        'message_details' => '',
		        
		    );
		
		} else {
		    // Form validation failed, there are errors
		    $message_details = validation_errors('<li>', '</li>');//'The following errors occurred <br>' . validation_errors('<li>', '</li>');
		    $data = array(
		        'message' => "error",
		        'message_details' => $message_details ,//$message_details,
		        'id' => '',
		    );
		}

		echo json_encode($data);
	}

	public function savetransactioninfo()
	{

		$this->form_validation->set_rules('Titlename', 'Title Name', 'required|trim|htmlspecialchars');

		// Check if form validation passed
		if ($this->form_validation->run()) {
		    // Form validation passed, no errors
		    
                    $TitleName  = $this->htmlpurifier_lib->purify($this->input->post('Titlename'));
            		$Description = $this->htmlpurifier_lib->purify($this->input->post('Description'));
            		$SendAnnouncement = $this->htmlpurifier_lib->purify($this->input->post('SendAnnouncement'));


                    $system_user = $this->session->userdata('uid');
                    $error_send = "";
                    if($SendAnnouncement==true)
                    {
                    	$memberActiveList = $this->members->getMembers();

                    	foreach ($memberActiveList as $key) { 
                    		$email_status = $this->email_model->send_announcement($TitleName, $Description,$key->Fullname, $key->Email);
                    		if($email_status=="error")
                    		{
                    			$error_send = "error";
                    			break;
                    		}
                    	}
                    }
                	
                    $result = $this->announcement->insert($TitleName, $Description, $system_user );
                    if($error_send=="error")
                    {
                    	$data = array(
					        'message' =>  $error_send,  // Assuming success when validation passes
					        'message_details' => '<li>Error on Sending Email. Please Contact the Developer on this Problem</li>',
					        
					    );
                    }
                    else
                    {
                    	$data = array(
					        'message' =>  $result->SuccessMessage,  // Assuming success when validation passes
					        'message_details' => '',
					        
					    );
                    }
                    	
                    

		    
		} else {
		    // Form validation failed, there are errors
		    $message_details = validation_errors('<li>', '</li>');//'The following errors occurred <br>' . validation_errors('<li>', '</li>');
		    $data = array(
		        'message' => "error",
		        'message_details' => $message_details,
		        'id' => '',
		    );
		}

		echo json_encode($data);

	}

	public function deletetransactioninfo()
	{
		$this->form_validation->set_rules('userID', 'Data ', 'required|trim|htmlspecialchars');
		if ($this->form_validation->run()) {
			$userID  = $this->htmlpurifier_lib->purify($this->input->post('userID'));
			$result = $this->announcement->deleteProfile($userID);
			$data = array(
		        'message' =>  $result->SuccessMessage,  // Assuming success when validation passes
		        'message_details' => $userID,
		        
		    );
		}
		else
		{
			// Form validation failed, there are errors
		    $message_details = validation_errors('<li>', '</li>');//'The following errors occurred <br>' . validation_errors('<li>', '</li>');
		    $data = array(
		        'message' => "error",
		        'message_details' => $message_details,
		        'id' => '',
		    );
		}
		echo json_encode($data);
	}
}



