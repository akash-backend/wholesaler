<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends Base_Controller {
	
	function __construct()
	{
		parent::__construct();
	}
	
	public function aboutUs()
	{
		$about = $this->common->getData('pages',array('name' => 'about-us'),array('single'));
		$response = $this->response(true,"About Us page",array('page'=> $about));
		echo json_encode($response);
	}

	public function getPage($page)
	{
		$data['page'] = $this->common->getData('pages',array('name' => $page),array('single'));
		$this->frontHtml('Eula','page',$data);
	}

	public function forgetPassword()
	{
		$this->form_validation->set_rules('email','Email','trim|required|valid_email');
		if ($this->form_validation->run() == FALSE){			
		   $message1 = "Enter email address "; 	
		}else{			
			$user = $this->common->getData('user',array('email'=>$_POST['email']),array('single','field'=>'id,name,social'));
		
			if(!empty($user)){
				if($user['social'] == '1'){
					$message1 = "You are logged in with social login.";
					$response = $this->response(false,$message1);
					echo json_encode($response); die;
				}
				$token = $this->generateToken();
				$token = $user['id'].$token;
				$this->common->updateData('user',array('forgot_token' => $token),array('id'=> $user['id']));
				$message = 'Dear '.$user['name'].' <br> click here to reset your password :-  <a href="'.base_url('home/resetPassword?token='.$token).'">'.base_url('home/resetPassword?token='.$token).'</a>';
				$mail = $this->common->sendMail($_POST['email'],'Forget Password',$message);
				if($mail){
					$message1 = "Email send successfully";
				}else{
					$message1 = "Email not send. Enter again"; 	
				}
			}else{
				$message1 = "No user found.Enter correct email address "; 	
			}		
		}
		$response = array(
					"code" => "1",
					"success" => "true",
					"message" => $message1
				); 	
		echo json_encode($response);	
	}

	public function resetPassword()
	{
		$this->form_validation->set_rules('password','Password','required');
		$this->form_validation->set_rules('confPassword','Confirm Password', 'required|matches[password]');
		$this->load->library('user_agent');

		if ($this->form_validation->run() == FALSE){
			if ($this->agent->is_mobile('iphone'))
			{			  
			    //$newURL ="bosala://home/resetPassword?token=".$_REQUEST["token"];
			    $newURL = "bosala://mailbox?token=".$_REQUEST["token"];
	 			redirect($newURL);
			}
			elseif ($this->agent->is_mobile())
			{
			    $message = "Please check password or confirm password";
			}
			else
			{
			    //$this->load->view('header');
				$this->load->view('login/change-password',compact('token'));
				//$this->load->view('footer');
			}

			// if(isset($_POST['type']) && $_POST['type'] == 'mobile'){
			// 	$message = "Please check password or confirm password";
			// }else{
			// 	//$this->load->view('header');
			// 	$this->load->view('login/change-password',compact('token'));
			// 	//$this->load->view('footer');
			// }
		}else{ 
			$user = $this->common->getData('user',array('forgot_token'=>$_REQUEST['token']),array('single'));
			
			$this->common->updateData('user',array('forgot_token'=> "",'password'=>md5($_POST['password'])),array('forgot_token'=>$_REQUEST['token']));
			if ($this->agent->is_mobile('iphone'))
			{			  
			    $message = "Password changed successfully";
			}
			elseif ($this->agent->is_mobile())
			{
			    $message = "Password changed successfully";
			}
			else
			{
			    $this->session->set_flashdata('msg',$this->lang->line('pwd_chng_succ'));
				redirect('login');
			}
					
		}
		$response = array(
					"code" => "1",
					"success" => "true",
					"message" => $message
				); 	
		echo json_encode($response);
	}

	public function changePassword()
	{
		$this->form_validation->set_rules('old_password','Old Password','required');
		$this->form_validation->set_rules('password','Password','required');
		$this->form_validation->set_rules('confPassword','Confirm Password', 'required|matches[password]');
		$this->load->library('user_agent');

		if ($this->form_validation->run() == FALSE){
			if ($this->agent->is_mobile('iphone'))
			{			  
			    $newURL ="bosala://mailbox?token=".$_REQUEST["token"]."";
	 			redirect($newURL);
			}
			elseif ($this->agent->is_mobile())
			{
			    $message = "Please check password or confirm password";
			}
			else
			{
			    //$this->load->view('header');
				$this->load->view('login/change-password');
				//$this->load->view('footer');
			}
		}else{
			$user_exist = $this->common->getData('user',array('id' => $_POST['id'],'password' => md5($_POST['old_password'])),array('single'));
			if(empty($user_exist)){
				$response = array(
					"code" => "0",
					"success" => "false",
					"message" => "old password is wrong"
				); 	
				echo json_encode($response); die;
			}
						
			$this->common->updateData('user',array('forgot_token'=> "",'password'=>md5($_POST['password'])),array('id'=>$_POST['id']));
			if($this->agent->is_mobile()){
				$message = "Password changed successfully";
			}else{
				$this->session->set_flashdata('msg',$this->lang->line('pwd_chng_succ'));
				redirect('login');
			}			
		}
		$response = array(
					"code" => "1",
					"success" => "true",
					"message" => $message
				); 	
		echo json_encode($response);
	}

	public function checkPost($id)
	{
		$data['post'] = $this->common->getData('post',array('id'=>$id),array('single'));
		echo $this->load->view('report',$data['post'],true);
	}
}

