<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class test extends CI_Controller {

	public function __construct()
	{
		parent:: __construct();
		$this->checkAuth();		
		$this->load->helper('common');
		$this->data = json_decode(file_get_contents("php://input"));
	}	

	public function index()
	{
		echo"hello";
		die();
		$this->load->view('test');
	}


}