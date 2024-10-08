<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Controllermembershipinfo101 extends CI_Controller {

	function __construct() {
		parent::__construct();
		if(!$this->session->userdata('logged_in_session')) :
			redirect('login',301);

		endif;
		

		if($this->session->userdata('gid')==1 or $this->session->userdata('gid')==2):
			$this->load->model('Membershipinfomodel','members');
			$this->load->model('User_model','user_model');
		else:
			redirect('home',301);
		endif;
		
	}




	public function index()
	{
		$this->data = [];
		$this->data['pageTitle'] = "Members Information";
		$this->data['pageSubtitle'] = "Profile";
		$this->data['pageSubtitleTable'] = "";
		$this->data['content'] = $this->load->view('members/profile/home',$this->data, true );
		$this->data['home_script'] = $this->load->view('members/profile/home_script',$this->data, true );
		$this->data['custom_css'] = $this->load->view('members/profile/css_script',$this->data,true);
		$this->load->view('layouts/main', $this->data );
	}

	public function uploadImage() {

		$entryBy =  $this->encryption->decrypt($this->session->userdata('uid'));
		$session_log  = $this->encryption->decrypt($this->input->post('session_log'));
		$ProfileID  = $this->htmlpurifier_lib->purify($this->input->post('ini_username'));

		if($session_log==CNF_SESSION_LOG): // session for ajax is active

			$config['upload_path'] = './uploads/users/';
	        $config['allowed_types'] = 'jpg';
	        $config['max_size'] = 5120; // 5MB limit

	        // Load upload library
	        $this->load->library('upload', $config);


			$data = $this->members->get_data_details($ProfileID);

	    	if($data !='')
	    	{
	    		$primaryID = $this->encryption->decrypt($data['TransID']);

	    		// Check if the file was uploaded
		        if (!$this->upload->do_upload('file')) {
		            $response = array('success' => false, 'error' => $this->upload->display_errors());
		        } else {
		            $data = $this->upload->data();
		            $file_ext = $data['file_ext'];
		            $new_filename = uniqid() . '-' . substr(md5(mt_rand()), 0, 5).date("YYmmddhhss") . $file_ext;
		            rename($data['full_path'], $data['file_path'] . $new_filename);
		            $data = array('avatar' => $new_filename, );
		            $this->db->where( array( "ProfileID" => $primaryID));
					$this->db->update( "tb_profile" , $data );

		            $response = array('success' => true, 'file_name' => $new_filename,'primaryID' => $primaryID, );
		        }

		        $note = "Update Members Picture";
		        //$this->user_model->insertLog($note,"profile" ,$entryBy ,"users", $entryBy,$entryBy);
	    	}
	    	else
	    	{
	    		$response = array('success' => false, 'file_name' => '');
	    	}

	       

	        
	        echo json_encode($response);
        else:
        	redirect('home',301);
        endif;
    }



	public function individual_details($id = null)
	{
		if($id!=null): // session for ajax is active

        	$data = $this->members->get_data_details($this->htmlpurifier_lib->purify($id));

        	if($data !='')
        	{
        		$this->data['data'] = $data;
        	}
        	else
        	{
        		redirect('members',301);
        	}
        	$this->data['pageTitle'] = "My Account";
			$this->data['pageSubtitle'] = "Profile";
			$this->data['pageSubtitleTable'] = "";
			$this->data['pageTitleOption'] = "";
			$this->data['content'] = $this->load->view('members/details/home',$this->data,true);
			$this->data['home_script'] = $this->load->view('members/details/script',$this->data,true);
			$this->data['custom_css'] = $this->load->view('members/details/css_script',$this->data,true);
			$this->load->view('layouts/main', $this->data );
        else:
			redirect('members',301);			
        endif;
	}

	public function findprofileinfo()
	{
		/*if(isset($this->input->post('search')['value']))
		{
			$fieldValue = trim($this->htmlpurifier_lib->purify($this->input->post('search')['value'])) ;
		}
		else
		{
			$fieldValue = "" ;
		}
		
		echo json_encode($this->members->getMembersData($fieldValue)) ;*/

		$system_user_login = $this->session->userdata('logged_in_session') ;
        $session_log  = $this->encryption->decrypt($this->input->get('session_log'));
        if($session_log==CNF_SESSION_LOG): // session for ajax is active
        
        	if($system_user_login == true):
        		$session_log = true;
        	else:
        		$session_log = true;
        	endif;

            // Get the start and limit parameters from the request
            $start = $this->input->get('start');
            $limit = $this->input->get('limit');
            $search = $this->htmlpurifier_lib->purify($this->input->get('search'));
            $sortColumn = $this->htmlpurifier_lib->purify($this->input->get('sortColumn'));
            $sortOrder = $this->htmlpurifier_lib->purify($this->input->get('sortOrder'));  

            $data = $this->members->search_data($start,$limit,$sortColumn,$sortOrder,$search,$session_log); 
            // Return the data as JSON
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($data));
   

        else:
            redirect('home',301);
        endif;
	}

	public function updatetransactioninfo()
	{
		/*$this->form_validation->set_rules('firstName', 'First Name', 'required|trim|htmlspecialchars');
		
		$this->form_validation->set_rules('lastName', 'Last Name', 'required|trim|htmlspecialchars');
		$this->form_validation->set_rules('userID', 'ID Number', 'required|trim|htmlspecialchars');
		if ($this->form_validation->run()) {
			$firstName  = $this->htmlpurifier_lib->purify($this->input->post('firstName'));
            $middleName = $this->htmlpurifier_lib->purify($this->input->post('middleName'));
            $lastName = $this->htmlpurifier_lib->purify($this->input->post('lastName'));
            $nameExtension = $this->htmlpurifier_lib->purify($this->input->post('nameExtension'));
            
            $email = $this->htmlpurifier_lib->purify($this->input->post('email'));
            $userID = $this->htmlpurifier_lib->purify($this->input->post('userID'));



	        // Call the model method to update the profile
	        $result = $this->members->updateProfile($userID, $firstName, $middleName, $lastName, $nameExtension, $email);

	        $data = array(
		        'message' =>  $result->SuccessMessage,  // Assuming success when validation passes
		        'message_details' => '',
		        
		    );
		
		} else {
		    // Form validation failed, there are errors
		    $message_details = validation_errors('<li>', '</li>');//'The following errors occurred <br>' . validation_errors('<li>', '</li>');
		    $data = array(
		        'message' => "error",
		        'message_details' =>  json_encode($_POST),//$message_details,
		        'id' => '',
		    );
		}

		echo json_encode($data);*/

		$system_user_login = $this->session->userdata('logged_in_session') ;
	   	$session_key  = $this->encryption->decrypt($this->input->post('session_log'));
        if($session_key==CNF_SESSION_LOG): // session for ajax is active

        	if($system_user_login == true):
        		$session_log = true;
        	else:
        		$session_log = false;
        	endif;
        	
        	$email_error_message = "Can't Proceed on Registration. Email already registered on Members' Profile";
	        $email_error_message_2 = "Invalid Email Address!";

	        $this->form_validation->set_rules(
			    'ProfileID',
			    'Profile ID',
			    'required|trim|htmlspecialchars',
			    array(
			        'required' => 'Profile ID is required.',
			    )
			);
			$this->form_validation->set_rules('ProfileStatus', 'Profile Status', 'required|trim|htmlspecialchars');
	    	$this->form_validation->set_rules('FirstName', 'First Name', 'required|trim|htmlspecialchars');
	    	$this->form_validation->set_rules('LastName', 'Last Name', 'required|trim|htmlspecialchars');
	    	$this->form_validation->set_rules('email', 'Email Address', 'required|trim|htmlspecialchars|htmlspecialchars', array('valid_email' => $email_error_message_2,) );
        	$form_validation_status = false;
        	$message_details = "";
        	if($system_user_login == true):
        		$session_log = true;
        		if ($this->form_validation->run()) {
        			$form_validation_status = true;
        		}
        		else
        		{
        			$form_validation_status = false;
        			$message_details = validation_errors('<li>', '</li>');//'The following errors occurred <br>' . validation_errors('<li>', '</li>');
        		}
        	else:
        		$form_validation_status = false;
        		$session_log = false;
        	endif;

        	if($this->input->post('ProfileStatus')=="Active")
        	{
        		$ProfileStatus = 1 ;
        	}
        	else
        	{
        		$ProfileStatus = 0 ;
        	}
        	$FirstName  =  $this->htmlpurifier_lib->purify($this->input->post('FirstName'));
	    	$LastName  =  $this->htmlpurifier_lib->purify($this->input->post('LastName'));
	    	$MiddleName  =  $this->htmlpurifier_lib->purify($this->input->post('MiddleName'));
	    	$nameExtension  =  $this->htmlpurifier_lib->purify($this->input->post('nameExtension'));
	    	$UniqueID  =  $this->htmlpurifier_lib->purify($this->input->post('ProfileID'));
	    	$email  =  $this->htmlpurifier_lib->purify($this->input->post('email'));
	    	$defaultuseraccount = $this->htmlpurifier_lib->purify($this->input->post('defaultuseraccount'));
	    	$data_transaction = array('is_active'=>$ProfileStatus , 'MiddleName' => $MiddleName,'nameExtension' => $nameExtension,'FirstName' => $FirstName,'LastName' => $LastName, 'email' => $email );

	        $this->db->where('UniqueID', $UniqueID); // Add the is_active condition
			$query = $this->db->get('tb_profile');
			$result = $query->result();
			$temp_id = "";

			if (count($result) > 0) {
			    foreach ($result as $row) {
			        $temp_id = $row->ProfileID; // Ensure ProfileID is a string or convertible to a string
			    }
			}


			if ($form_validation_status == true && $temp_id != '') {
			    // Assuming $data_transaction and $session_log are defined
			    $data = $this->members->update($temp_id, $session_log, $data_transaction);
			} else {
			    $data = array(
			        'session_log' => $session_log,
			        'data' => $temp_id,
			        'message_details' => $message_details,
			        'success' => false,
			    );
			}

			$this->output
			    ->set_content_type('application/json')
			    ->set_output(json_encode($data));

        else:            
			redirect('template',301);
        endif;
	}

	public function savetransactioninfo()
	{

		/*$this->form_validation->set_rules('firstName', 'First Name', 'required|trim|htmlspecialchars');
		
		$this->form_validation->set_rules('lastName', 'Last Name', 'required|trim|htmlspecialchars');
		$this->form_validation->set_rules('email', 'Email', 'required|trim|htmlspecialchars|valid_email|is_unique[tb_profile.email]');
		//$this->form_validation->set_rules('email', 'Email', 'required|trim|htmlspecialchars');
		$this->form_validation->set_rules('idnumber', 'ID Number', 'required|trim|htmlspecialchars|is_unique[tb_profile.UniqueID]');

		// Check if form validation passed
		if ($this->form_validation->run()) {
		    // Form validation passed, no errors
		    
                    $firstName  = $this->htmlpurifier_lib->purify($this->input->post('firstName'));
                    $middleName = $this->htmlpurifier_lib->purify($this->input->post('middleName'));
                    $lastName = $this->htmlpurifier_lib->purify($this->input->post('lastName'));
                    $nameExtension = $this->htmlpurifier_lib->purify($this->input->post('nameExtension'));
                    
                    $email = $this->htmlpurifier_lib->purify($this->input->post('email'));
                    $idnumber = $this->htmlpurifier_lib->purify($this->input->post('idnumber'));
                    $defaultuseraccount = $this->htmlpurifier_lib->purify($this->input->post('defaultuseraccount'));


                    $system_user = 1;
                
                    $result = $this->members->insertProfile($idnumber, $firstName, $middleName, $lastName, $nameExtension, $email);
                    if($defaultuseraccount=="defaultsystemuser")
                    {
                    	$result = $this->User_model->insertUser($idnumber, $idnumber,  $email , 3, $result->ProfileID ,  $firstName  , $lastName, 1, $system_user);
                    	$data = array(
					        'message' =>  $result->SuccessMessage,  // Assuming success when validation passes
					        'message_details' => '',
					        
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

		echo json_encode($data);*/


		$system_user_login = $this->session->userdata('logged_in_session') ;
	   	$session_key  = $this->encryption->decrypt($this->input->post('session_log'));

	   	$email_error_message = "Can't Proceed on Registration. Email already registered on Profile Members";
        $email_error_message_2 = "Invalid Email Address!";

    	$this->form_validation->set_rules('FirstName', 'First Name', 'required|trim|htmlspecialchars');
    	$this->form_validation->set_rules('LastName', 'Last Name', 'required|trim|htmlspecialchars');

    	$this->form_validation->set_rules('homeaddress', 'Home Address', 'required|trim|htmlspecialchars');
    	$this->form_validation->set_rules('Province', 'Province Name', 'required|trim|htmlspecialchars');
    	$this->form_validation->set_rules('Municipality', 'Municipality Name', 'required|trim|htmlspecialchars');
    	$this->form_validation->set_rules('Barangay', 'Barangay Name', 'required|trim|htmlspecialchars');
    	$this->form_validation->set_rules('ZipCode', 'ZipCode', 'required|trim|htmlspecialchars');

    	$this->form_validation->set_rules('email', 'Email Address', 'required|trim|htmlspecialchars|is_unique[tb_profile.email]', array('is_unique' => $email_error_message,'valid_email' => $email_error_message_2,) );
    	if($this->input->post('isAutomaticProfileID')==false):
	    	$this->form_validation->set_rules(
			    'ProfileID',
			    'Profile ID',
			    'required|trim|htmlspecialchars|is_unique[tb_profile.UniqueID]',
			    array(
			        'required' => 'Profile ID is required.',
			        'is_unique' => 'ProfileID is already registered and owned!',
			    )
			);
	    endif;

    	$FirstName  =  $this->htmlpurifier_lib->purify($this->input->post('FirstName'));
    	$MiddleName  =  $this->htmlpurifier_lib->purify($this->input->post('MiddleName'));
    	$LastName  =  $this->htmlpurifier_lib->purify($this->input->post('LastName'));
    	$NameExtension  =  $this->htmlpurifier_lib->purify($this->input->post('NameExtension'));
    	$ProfileID  =  $this->htmlpurifier_lib->purify($this->input->post('ProfileID'));
    	$email  =  $this->htmlpurifier_lib->purify($this->input->post('email'));

    	$homeaddress  =  $this->htmlpurifier_lib->purify($this->input->post('homeaddress'));
    	$Province  =  $this->htmlpurifier_lib->purify($this->input->post('Province'));
    	$Municipality  =  $this->htmlpurifier_lib->purify($this->input->post('Municipality'));
    	$Barangay  =  $this->htmlpurifier_lib->purify($this->input->post('Barangay'));
    	$ZipCode  =  $this->htmlpurifier_lib->purify($this->input->post('ZipCode'));

    	$sex  =  $this->htmlpurifier_lib->purify($this->input->post('sex'));
    	$dateofbirth  =  $this->htmlpurifier_lib->purify($this->input->post('dateofbirth'));
    	$placeofbirth  =  $this->htmlpurifier_lib->purify($this->input->post('placeofbirth'));
    	$bloodtype  =  $this->htmlpurifier_lib->purify($this->input->post('bloodtype'));

    	$Country  =  $this->htmlpurifier_lib->purify($this->input->post('Country'));

    	$contactno  =  $this->htmlpurifier_lib->purify($this->input->post('contactno'));
    	$faxno  =  $this->htmlpurifier_lib->purify($this->input->post('faxno'));
    	$homeno  =  $this->htmlpurifier_lib->purify($this->input->post('homeno'));
    	$officeno  =  $this->htmlpurifier_lib->purify($this->input->post('officeno'));

    	$Occupation  =  $this->htmlpurifier_lib->purify($this->input->post('Occupation'));
    	$Education  =  $this->htmlpurifier_lib->purify($this->input->post('Education'));
    	$Employment  =  $this->htmlpurifier_lib->purify($this->input->post('Employment'));
    	$EmploymentAddress  =  $this->htmlpurifier_lib->purify($this->input->post('EmploymentAddress'));

    	$familykin  =  $this->htmlpurifier_lib->purify($this->input->post('familykin'));
    	$familyrelation  =  $this->htmlpurifier_lib->purify($this->input->post('familyrelation'));
    	$familyaddress  =  $this->htmlpurifier_lib->purify($this->input->post('familyaddress'));
    	$familynokids  =  $this->htmlpurifier_lib->purify($this->input->post('familynokids'));
    	$familykidsname  =  $this->htmlpurifier_lib->purify($this->input->post('familykidsname'));

    	$recordStat  =  $this->htmlpurifier_lib->purify($this->input->post('recordStat'));
    	$LodgeNo  =  $this->htmlpurifier_lib->purify($this->input->post('LodgeNo'));
    	$LodgeName  =  $this->htmlpurifier_lib->purify($this->input->post('LodgeName'));
    	$MasonDistrict  =  $this->htmlpurifier_lib->purify($this->input->post('MasonDistrict'));
    	$initiated  =  $this->htmlpurifier_lib->purify($this->input->post('initiated'));
    	$passed  =  $this->htmlpurifier_lib->purify($this->input->post('passed'));
    	$raised  =  $this->htmlpurifier_lib->purify($this->input->post('raised'));
    	$memberstatus  =  $this->htmlpurifier_lib->purify($this->input->post('memberstatus'));

    	$defaultuseraccount = $this->htmlpurifier_lib->purify($this->input->post('defaultuseraccount'));
    	$data_transaction = array(
    		'FirstName' => $FirstName,
    		'MiddleName' => $MiddleName,
    		'LastName' => $LastName,
    		'UniqueID' => $ProfileID,
    		'email' => $email ,

    		'home_purok' => $homeaddress ,
    		'home_baranggay' => $Province ,
    		'home_muncity' => $Municipality ,
    		'home_province' => $Barangay ,
    		'zipcode' => $ZipCode ,

    		'sex' => $sex ,
    		'dateofbirth' => $dateofbirth ,
    		'placeofbirth' => $ZipCode ,
    		'bloodtype' => $bloodtype ,

    		'Country' => $Country ,

    		'contactno' => $contactno ,
    		'faxno' => $faxno ,
    		'homeno' => $homeno ,
    		'officeno' => $officeno ,

    		'Occupation' => $Occupation ,
    		'Education' => $Education ,
    		'Employment' => $Employment ,
    		'EmploymentAddress' => $EmploymentAddress ,

    		'familykin' => $familykin ,
    		'familyrelation' => $familyrelation ,
    		'familyaddress' => $familyaddress ,
    		'familynokids' => $familynokids ,
    		'familykidsname' => $familykidsname ,

    		'recordStat' => $recordStat ,
    		'LodgeNo' => $LodgeNo ,
    		'LodgeName' => $LodgeName ,
    		'MasonDistrict' => $MasonDistrict ,
    		'initiated' => $initiated ,
    		'passed' => $passed ,
    		'raised' => $raised ,
    		'memberstatus' => $memberstatus ,

    	);

        if($session_key==CNF_SESSION_LOG): // session for ajax is active

        	$form_validation_status = false;
        	$message_details = "";
        	if($system_user_login == true):
        		$session_log = true;
        		if ($this->form_validation->run()) {
        			$form_validation_status = true;
        		}
        		else
        		{
        			$form_validation_status = false;
        			$message_details = validation_errors('<li>', '</li>');//'The following errors occurred <br>' . validation_errors('<li>', '</li>');
        		}
        	else:
        		$form_validation_status = false;
        		$session_log = false;
        	endif;
        	
        	if($form_validation_status == true):
        		if($defaultuseraccount=="true"): // option for encoder if created profile has a default user
        			$email_error_message = "Can't Proceed on Registration. Email already registered on System Users";
        			$email_error_message_2 = "Invalid Email Address!";
        			$rules = array(
        			array('field'   => 'email', 'label'   => 'email', 'rules'   => 'required|valid_email|is_unique[tb_users.email]', 'errors' => array('is_unique' => $email_error_message,'valid_email' => $email_error_message_2,),),
        			);
        			$this->form_validation->set_rules( $rules );
        			if ($this->form_validation->run()) :
				    	$data_result = $this->members->add_get_id($session_log,$data_transaction);
				    	$ProfileID = trim($data_result['data']);
				    	//$password = password_hash($ProfileID, PASSWORD_BCRYPT); 
				    	$password = md5(md5(sha1(sha1($ProfileID))));
				    	if($data_result['success']==true):
				    		$transaction_result	 = $this->user_model->registration_form_profile($ProfileID,$email,$FirstName,"",$LastName,$password);

				    		if($transaction_result=="success"):
				    			$data =array('session_log' => $session_log , 'data' => $defaultuseraccount, 'message_details' => '', 'success' => true,);
				    		else:			    	
								$message_details = ' <li>Something went wrong during registration. Please try again. </li>';
							    $data = array('session_log' => $session_log ,'data' => '' , 'message_details' => $message_details, 'success' => false,);
					    	endif;
				    	else:
				    		$message_details = ' <li>Something went wrong during registration. Please try again. </li>';
						    $data = array('session_log' => $session_log ,'data' => '' , 'message_details' => $message_details, 'success' => false,);
				    	endif;
        			else:
        				$message_details = validation_errors('<li>', '</li>');
        				$data = array('session_log' => $session_log ,'data' => '' , 'message_details' => $message_details, 'success' => false,);
        			endif;
        		else:
	        		$data = $this->members->add($session_log,$data_transaction);
	        	endif;
        	else:
        		$data = array('session_log' => $session_log ,'data' => '' , 'message_details' => $message_details, 'success' => false,);
        	endif;



		    $this->output
		         ->set_content_type('application/json')
		         ->set_output(json_encode($data));
        else:            
			redirect('template',301);
        endif;

	}

	public function deletetransactioninfo()
	{
		/*$this->form_validation->set_rules('userID', 'User ID', 'required|trim|htmlspecialchars');
		if ($this->form_validation->run()) {
			$userID  = $this->htmlpurifier_lib->purify($this->input->post('userID'));
			$result = $this->members->deleteProfile($userID);
			$data = array(
		        'message' =>  $result->SuccessMessage,  // Assuming success when validation passes
		        'message_details' => '',
		        
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
		echo json_encode($data);*/

		$system_user_login = $this->session->userdata('logged_in_session') ;
	   	$session_key  = $this->encryption->decrypt($this->input->post('session_log'));
        if($session_key==CNF_SESSION_LOG): // session for ajax is active

        	if($system_user_login == true):
        		$session_log = true;
        	else:
        		$session_log = false;
        	endif;

        	

        	// Check if 'data' is set in $_POST and is not empty
			if (isset($_POST['data']) && !empty($_POST['data'])) {
			    // Decode JSON data from Vue.js
			    $temp_id_res = json_decode($_POST['data'], true);

			    if ($temp_id_res !== null) { // Check if decoding was successful

			    	$data = $this->members->delete($temp_id_res,$session_log);
			    } else {
			        // Handle case where JSON decoding failed
			        $data = array('session_log' => $session_log,  'success' => false);
			    }
			} else {
			    // Handle case where 'data' is not set or empty
			    $data = array('session_log' => $session_log,'success' => false);
			}



		    $this->output
		         ->set_content_type('application/json')
		         ->set_output(json_encode($data));
        else:
            redirect('home',301);
        endif;
	}

	public function updatemasoninfo()
	{
		$system_user_login = $this->session->userdata('logged_in_session') ;
	   	$session_key  = $this->encryption->decrypt($this->input->post('session_log'));
        if($session_key==CNF_SESSION_LOG): // session for ajax is active

        	if($system_user_login == true):
        		$session_log = true;
        	else:
        		$session_log = false;
        	endif;
        	
			/*$this->form_validation->set_rules('homeaddress', 'Home Address', 'required|trim|htmlspecialchars');
	    	$this->form_validation->set_rules('Province', 'Province Name', 'required|trim|htmlspecialchars');
	    	$this->form_validation->set_rules('Municipality', 'Municipality Name', 'required|trim|htmlspecialchars');
	    	$this->form_validation->set_rules('Barangay', 'Barangay Name', 'required|trim|htmlspecialchars');
	    	$this->form_validation->set_rules('ZipCode', 'ZipCode', 'required|trim|htmlspecialchars');*/
        	$form_validation_status = true;
        	$message_details = "";
        	/*if($system_user_login == true):
        		$session_log = true;
        		if ($this->form_validation->run()) {
        			$form_validation_status = true;
        		}
        		else
        		{
        			$form_validation_status = false;
        			$message_details = validation_errors('<li>', '</li>');//'The following errors occurred <br>' . validation_errors('<li>', '</li>');
        		}
        	else:
        		$form_validation_status = false;
        		$session_log = false;
        	endif;*/
        	$UniqueID  =  $this->htmlpurifier_lib->purify($this->input->post('ProfileID'));
        
        	$recordStat  =  $this->htmlpurifier_lib->purify($this->input->post('recordStat'));
	    	$LodgeNo  =  $this->htmlpurifier_lib->purify($this->input->post('LodgeNo'));
	    	$LodgeName  =  $this->htmlpurifier_lib->purify($this->input->post('LodgeName'));
	    	$MasonDistrict  =  $this->htmlpurifier_lib->purify($this->input->post('MasonDistrict'));
	    	$initiated  =  $this->htmlpurifier_lib->purify($this->input->post('initiated'));
	    	$passed  =  $this->htmlpurifier_lib->purify($this->input->post('passed'));
	    	$raised  =  $this->htmlpurifier_lib->purify($this->input->post('raised'));
	    	$memberstatus  =  $this->htmlpurifier_lib->purify($this->input->post('memberstatus'));

	    	$data_transaction = array(
	    		'recordStat' => $recordStat ,
	    		'LodgeNo' => $LodgeNo ,
	    		'LodgeName' => $LodgeName ,
	    		'MasonDistrict' => $MasonDistrict ,
	    		'initiated' => $initiated ,
	    		'passed' => $passed ,
	    		'raised' => $raised ,
	    		'memberstatus' => $memberstatus ,
	    		  );

	        $this->db->where('UniqueID', $UniqueID); // Add the is_active condition
			$query = $this->db->get('tb_profile');
			$result = $query->result();
			$temp_id = "";

			if (count($result) > 0) {
			    foreach ($result as $row) {
			        $temp_id = $row->ProfileID; // Ensure ProfileID is a string or convertible to a string
			    }
			}


			if ($form_validation_status == true && $temp_id != '') {
			    // Assuming $data_transaction and $session_log are defined
			    $data = $this->members->updateinfo($temp_id, $session_log, $data_transaction);
			} else {
			    $data = array(
			        'session_log' => $session_log,
			        'data' => $temp_id,
			        'message_details' => $message_details,
			        'success' => false,
			    );
			}

			$this->output
			    ->set_content_type('application/json')
			    ->set_output(json_encode($data));

        else:            
			redirect('template',301);
        endif;
	}

	public function updatetransactionaddress()
	{
		$system_user_login = $this->session->userdata('logged_in_session') ;
	   	$session_key  = $this->encryption->decrypt($this->input->post('session_log'));
        if($session_key==CNF_SESSION_LOG): // session for ajax is active

        	if($system_user_login == true):
        		$session_log = true;
        	else:
        		$session_log = false;
        	endif;
        	
			$this->form_validation->set_rules('homeaddress', 'Home Address', 'required|trim|htmlspecialchars');
	    	$this->form_validation->set_rules('Province', 'Province Name', 'required|trim|htmlspecialchars');
	    	$this->form_validation->set_rules('Municipality', 'Municipality Name', 'required|trim|htmlspecialchars');
	    	$this->form_validation->set_rules('Barangay', 'Barangay Name', 'required|trim|htmlspecialchars');
	    	$this->form_validation->set_rules('ZipCode', 'ZipCode', 'required|trim|htmlspecialchars');
        	$form_validation_status = false;
        	$message_details = "";
        	if($system_user_login == true):
        		$session_log = true;
        		if ($this->form_validation->run()) {
        			$form_validation_status = true;
        		}
        		else
        		{
        			$form_validation_status = false;
        			$message_details = validation_errors('<li>', '</li>');//'The following errors occurred <br>' . validation_errors('<li>', '</li>');
        		}
        	else:
        		$form_validation_status = false;
        		$session_log = false;
        	endif;

        
        	$homeaddress  =  $this->htmlpurifier_lib->purify($this->input->post('homeaddress'));
	    	$Province  =  $this->htmlpurifier_lib->purify($this->input->post('Province'));
	    	$Municipality  =  $this->htmlpurifier_lib->purify($this->input->post('Municipality'));
	    	$Barangay  =  $this->htmlpurifier_lib->purify($this->input->post('Barangay'));
	    	$ZipCode  =  $this->htmlpurifier_lib->purify($this->input->post('ZipCode'));
	    	$UniqueID  =  $this->htmlpurifier_lib->purify($this->input->post('ProfileID'));

	    	$sex  =  $this->htmlpurifier_lib->purify($this->input->post('sex'));
	    	$dateofbirth  =  $this->htmlpurifier_lib->purify($this->input->post('dateofbirth'));
	    	$placeofbirth  =  $this->htmlpurifier_lib->purify($this->input->post('placeofbirth'));
	    	$bloodtype  =  $this->htmlpurifier_lib->purify($this->input->post('bloodtype'));

	    	$Country  =  $this->htmlpurifier_lib->purify($this->input->post('Country'));

	    	$contactno  =  $this->htmlpurifier_lib->purify($this->input->post('contactno'));
	    	$faxno  =  $this->htmlpurifier_lib->purify($this->input->post('faxno'));
	    	$homeno  =  $this->htmlpurifier_lib->purify($this->input->post('homeno'));
	    	$officeno  =  $this->htmlpurifier_lib->purify($this->input->post('officeno'));

	    	$Occupation  =  $this->htmlpurifier_lib->purify($this->input->post('Occupation'));
	    	$Education  =  $this->htmlpurifier_lib->purify($this->input->post('Education'));
	    	$Employment  =  $this->htmlpurifier_lib->purify($this->input->post('Employment'));
	    	$EmploymentAddress  =  $this->htmlpurifier_lib->purify($this->input->post('EmploymentAddress'));

	    	$familykin  =  $this->htmlpurifier_lib->purify($this->input->post('familykin'));
	    	$familyrelation  =  $this->htmlpurifier_lib->purify($this->input->post('familyrelation'));
	    	$familyaddress  =  $this->htmlpurifier_lib->purify($this->input->post('familyaddress'));
	    	$familynokids  =  $this->htmlpurifier_lib->purify($this->input->post('familynokids'));
	    	$familykidsname  =  $this->htmlpurifier_lib->purify($this->input->post('familykidsname'));

	    	$data_transaction = array(
	    		'home_purok'=>$homeaddress ,
	    		'home_baranggay' => $Barangay,
	    		'home_muncity' => $Municipality,
	    		'home_province' => $Province,
	    		'zipcode' => $ZipCode, 

	    		'sex' => $sex ,
	    		'dateofbirth' => $dateofbirth ,
	    		'placeofbirth' => $ZipCode ,
	    		'bloodtype' => $bloodtype ,

	    		'Country' => $Country ,

	    		'contactno' => $contactno ,
	    		'faxno' => $faxno ,
	    		'homeno' => $homeno ,
	    		'officeno' => $officeno ,

	    		'Occupation' => $Occupation ,
	    		'Education' => $Education ,
	    		'Employment' => $Employment ,
	    		'EmploymentAddress' => $EmploymentAddress ,

	    		'familykin' => $familykin ,
	    		'familyrelation' => $familyrelation ,
	    		'familyaddress' => $familyaddress ,
	    		'familynokids' => $familynokids ,
	    		'familykidsname' => $familykidsname ,
	    		  );

	        $this->db->where('UniqueID', $UniqueID); // Add the is_active condition
			$query = $this->db->get('tb_profile');
			$result = $query->result();
			$temp_id = "";

			if (count($result) > 0) {
			    foreach ($result as $row) {
			        $temp_id = $row->ProfileID; // Ensure ProfileID is a string or convertible to a string
			    }
			}


			if ($form_validation_status == true && $temp_id != '') {
			    // Assuming $data_transaction and $session_log are defined
			    $data = $this->members->updateinfo($temp_id, $session_log, $data_transaction);
			} else {
			    $data = array(
			        'session_log' => $session_log,
			        'data' => $temp_id,
			        'message_details' => $message_details,
			        'success' => false,
			    );
			}

			$this->output
			    ->set_content_type('application/json')
			    ->set_output(json_encode($data));

        else:            
			redirect('template',301);
        endif;
	}

}



