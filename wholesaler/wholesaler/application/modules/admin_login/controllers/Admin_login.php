<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_login extends Base_Controller {

	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		$admin = $this->session->userdata('admin');
		if(empty($admin)){ 
			redirect(base_url('admin_login/checkLogin'));
		}else{
			redirect(base_url('admin/dashboard'));
		}
	}

	public function checkLogin()
	{		
		$admin = $this->session->userdata('admin');
		if(!empty($admin)){ 
			redirect(base_url('admin/dashboard'));
		}

		$this->form_validation->set_rules('email','Login ID','trim|required');
		$this->form_validation->set_rules('password','Password','trim|required');
		if ($this->form_validation->run() == FALSE){
			$this->load->view('login');
		}else{
			$_POST['password'] = md5($_POST['password']);
			$result= $this->common->getData('admin',$_POST,array('single'));
			if($result){
				$this->flashMsg('success','Login Successfull');
				$this->loginRedirect($result);
			}else{
				$this->flashMsg('danger','Invalid login id or password');
				redirect(base_url('admin-login'));
			}		
		}
	}


	public function loginRedirect($data)
	{
		$admin = array( 'email' => $data['email'],
					
						'id' => $data['id'],
						'is_login' => true
					);
		$this->session->set_userdata('admin',$admin);
		redirect(base_url('admin/dashboard'));
	}

	public function logout()
	{
		$this->session->sess_destroy();
		$this->session->set_flashdata('msg','Logged out successfully');
		redirect(base_url('admin-login'));
	}
}
