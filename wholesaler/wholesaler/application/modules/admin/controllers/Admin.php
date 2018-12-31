<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends Admin_Controller {

	public function __construct()
	{
		parent::__construct();
		$company = $this->session->userdata('admin');
	
		$this->id = $company['id'];

		$admin = $this->session->userdata('admin');
		if(empty($admin)){ 
			redirect(base_url('admin-login'));
		}

	}

	public function dashboard()
	{	
		$data['user'] = $this->common->getData('user','',array('count'));
		$where_all_event = 'status = 0';
		$today = Date('Y-m-d H:i:s'); 
		$where_active_event = 'event_time < "'.$today.'" AND status = 0';
		$where_event_not_active = 'event_time >= "'.$today.'" AND status = 0';
		$data['all_event'] = $this->common->getData('sport_event',$where_all_event,array('count'));
		$data['all_active_event'] = $this->common->getData('sport_event',$where_active_event,array('count'));
		$data['all_event_not_active'] = $this->common->getData('sport_event',$where_event_not_active,array('count'));
	
		$this->adminHtml('Dashboard','admin/dashboard',$data);;
	}

	public function logout()
	{
		$this->session->sess_destroy();
		$this->session->set_flashdata('msg','Logged out successfully');
		redirect(base_url('admin-login'));
	}

	public function addDriver()
	{
		$this->form_validation->set_rules('did','Driver ID','required');
		if($this->form_validation->run() == false){
			$this->adminHtml('Add Driver','add-driver');
		}else{			
			$_POST['licence'] = $_POST['mcertificate'] = $_POST['image'] = "";
			$_POST['created_at'] = date('Y-m-d');
			
			$image = $this->common->do_upload('image','./assets/userfile/driver/profile');
			
			if (isset($image['upload_data'])) {
				$_POST['image'] = $image['upload_data']['file_name'];
			}
			
			$licence = $this->common->do_upload('licence','./assets/userfile/driver/documents');
			
			if (isset($licence['upload_data'])) {
				$_POST['licence'] = $licence['upload_data']['file_name'];
			}
			
			$mcertificate = $this->common->do_upload('mcertificate','./assets/userfile/driver/documents');
			if (isset($mcertificate['upload_data'])) {
				$_POST['mcertificate'] = $mcertificate['upload_data']['file_name'];
			}
			
			$_POST['company_id'] = $this->company_id;

			$post = $this->common->getField('driver',$_POST); 
			
			$result = $this->common->insertData('driver',$post);
			if($result){
				$this->flashMsg('success','Driver added successfully');
				redirect(base_url('admin/driverlist'));
			}else{
				$this->flashMsg('danger','Some error occured. Please try again');
				redirect(base_url('admin/addDriver'));
			}
		}
	}


	public function addGame()
	{
		$this->form_validation->set_rules('sport_game','Game','required');
		
		if($this->form_validation->run() == false){
			$this->adminHtml('Add Game','add-game');
		}else{			
			
			$image = $this->common->do_upload('image','./assets/Game/gamelogo');
			
			if (isset($image['upload_data'])) {
				$image = $image['upload_data']['file_name'];
				$data['game_image']=$image;
			}
			else
			{
				$this->flashMsg('danger','File formate are Not Supported');
				redirect(base_url('admin/gamelist'));
			}
			
			
			
			$data['game_name'] = $this->input->post('sport_game');


			$result = $this->common->insertData('sport_game',$data);

			
			if($result){
				$this->flashMsg('success','Game added successfully');
				redirect(base_url('admin/gamelist'));
			}else{
				$this->flashMsg('danger','Some error occured. Please try again');
				redirect(base_url('admin/addGame'));
			}
		}
	}



	public function addEvent()
	{
		
		
		
			$this->form_validation->set_rules('title','Title','required');
		 	$this->form_validation->set_rules('game_id','Sport','required');
		 	$this->form_validation->set_rules('event_address','Address','required');
		 	$this->form_validation->set_rules('price','price','trim|required|regex_match[/^[0-9]+$/]');
		 	$this->form_validation->set_rules('event_time','Time','required');
		 	$this->form_validation->set_rules('event_description','Description','required');

		if($this->form_validation->run() == false){

			$where = 'status = 0';
			$data['game'] = $this->common->getData('sport_game',$where);
			$this->adminHtml('Add Event','add-event',$data);
			$where_all_event = 'status = 0';

		}else{	

			$data = $this->input->post();

			$image = $this->common->do_upload('image','./assets/event/image');
			
			if (isset($image['upload_data'])) {
				$image = $image['upload_data']['file_name'];
				$data['event_image']=$image;
			}
			else
			{
				$this->flashMsg('danger','File formate are Not Supported');
				redirect(base_url('admin/eventList'));
			}

			$data['latitude'] = $this->input->post('lat');
            $data['longitude'] = $this->input->post('lng');
            unset($data['submit']);
            unset($data['lat']);
            unset($data['lng']);

             $data['event_user_type'] =1;

           

			$result = $this->common->insertData('sport_event',$data);

			
			if($result){
				$this->flashMsg('success','Event added successfully');
				redirect(base_url('admin/eventList'));
			}else{
				$this->flashMsg('danger','Some error occured. Please try again');
				redirect(base_url('admin/addEvent'));
			}
		}
		
		

	}


	public function editEvent()
	{
		
		
		 $this->form_validation->set_rules('game_id','Sport','required');
		 	$this->form_validation->set_rules('event_address','Address','required');
		 	$this->form_validation->set_rules('price','price','required|numeric');
		 	$this->form_validation->set_rules('event_time','Time','required');
		 	$this->form_validation->set_rules('event_description','Description','required');

		if($this->form_validation->run() == false){

			 $id = $this->input->post('id');
			 if(empty($id))
			 {
			 	$id = $this->uri->segment(3);
			 }

			$data['game'] = $this->common->getData('sport_game');
			$data['event'] = $this->common->getData('sport_event',array('id' => $id), array('single'));


			$this->adminHtml('Add Event','add-event',$data);

		}else{	

			$data = $this->input->post();
		
			$data['latitude'] = $this->input->post('lat');
            $data['longitude'] = $this->input->post('lng');
            unset($data['submit']);
            unset($data['lat']);
            unset($data['lng']);
            $data['event_user_type'] =1;

             if(!empty($_FILES['image']['name']))
                {

			
			$image = $this->common->do_upload('image','./assets/event/image');
			
			if (isset($image['upload_data'])) {
				$image = $image['upload_data']['file_name'];
				$data['event_image']=$image;
			}
			else
			{
				$this->flashMsg('danger','File formate are Not Supported');
				redirect(base_url('admin/eventlist'));
			}
			

			}
			else
			{
				
		
			
			$id = $this->input->post('id');
			$event_detail = $this->common->getData('sport_event',array('id' => $id), array('single'));
			
			$data['event_image']= $event_detail['event_image'];
			
			}

           

			$id = $this->input->post('id');

				
		
		$result = $this->common->updateData('sport_event',$data,array('id'=>$id));
		

			
			if($result){
				$a = $this->flashMsg('success','Data update successfully');
			}else{
				$this->flashMsg('danger','Some Error occured.');
			} 			
			redirect(base_url('admin/eventList'),'refresh');
		}
		
	}


	public function editGame()
	{

		 $id = $this->uri->segment(3);
		$this->form_validation->set_rules('sport_game','Game','required');
		if($this->form_validation->run() == false){			
			$data['game'] = $this->common->getData('sport_game',array('id' => $id), array('single'));
			$this->adminHtml('Update Game','add-game',$data);
		}else{

			 if(!empty($_FILES['image']['name']))
                {

			
			$image = $this->common->do_upload('image','./assets/Game/gamelogo');
			
			if (isset($image['upload_data'])) {
				$image = $image['upload_data']['file_name'];
				$data['game_image']=$image;
			}
			else
			{
				$this->flashMsg('danger','File formate are Not Supported');
				redirect(base_url('admin/gamelist'));
			}
			



			}
			else
			{
				
		
			
			$id = $this->input->post('id');
			$game_detail = $this->common->getData('sport_game',array('id' => $id), array('single'));
			
			$data['game_image']= $game_detail['game_image'];
			
			}

			$data['game_name'] = $this->input->post('sport_game');

			

			$id = $this->input->post('id');
		
				$result = $this->common->updateData('sport_game',$data,array('id'=>$id));
			
			if($result){
				$a = $this->flashMsg('success','Data update successfully');
			}else{
				$this->flashMsg('danger','Some Error occured.');
			} 			
			redirect(base_url('admin/gamelist'),'refresh');
		}
	}


	function edit_brand(){
        if($this->input->post('submit')){
            $data = $this->input->post();
            
            
             if(!empty($_FILES))
                {
                
                $str=round(microtime(true) * 1000).  rand('111111', '999999');
                $image=$_FILES['brand_image']['name'];
                $filetempname=$_FILES['brand_image']['tmp_name'];
                $newfile=$str."-".$image;
             
                     $destination="./uploads/brands/".$newfile;
                     move_uploaded_file($filetempname,$destination);
                     
        
        
                  }
                    
                    
              
                    $data['brand_image']=$newfile;
      
            
            unset($data['submit']);
            unset($data['brand_id']);
         
    
            
            $where = array('brand_id' => $this->input->post('brand_id'));
            $edit = $this->login_model->update_data('tbl_brand', $data, $where);
            $this->session->set_flashdata('status', 'Brand  edited successfully');
            redirect('admin/brand_list');
        } else {
           $brand_id = $this->uri->segment(3);
            if(!empty($brand_id)){
                $where = array('brand_id'=>$brand_id);
                $get_brand = $this->login_model->get_column_data_where('tbl_brand','',$where);
              
                if(!empty($get_brand)){
                    $data['brand_list']=$get_brand;
                    
                    $this->load->view('admin/menu');
                    $this->load->view('admin/header');
                    $this->load->view('admin/add/add_brand',$data);
                    $this->load->view('admin/footer');
                } else {
                    redirect('admin/brand_list');
                }
            } else {
                redirect('admin/brand_list');
            }
        }
    }
    

	public function driverDetail($company_id,$did)
	{
		$data['driver'] = $this->common->getData('driver',array('company_id' => $this->company_id, 'did' =>$did), array('single'));
		if (isset($data['driver']['vehicle_id']) &&  $data['driver']['vehicle_id'] != null) {
			$car_no = $this->common->getData('vehicle_detail',array('id' => $data['driver']['vehicle_id']),array('field' => 'vehicle_no','single'));
			$data['driver']['vehicle_id'] = $car_no['vehicle_no'];
		}
		$data['class'] = $this->common->getData('vehicle_class');
		if(isset($data['driver']['vehicle_class_id'])){
			$data['car'] = $this->common->vehicleList(array('company_id'=>$this->company_id,'vehicle_class_id' => $data['driver']['vehicle_class_id'],'assign' => 0));
		}
		$this->adminHtml('Driver Detail','driver/driver-detail',$data);
	}


	public function userDetail($id)
	{

		$data['user'] = $this->common->getData('user',array('id' => $id), array('single'));
	
		$this->adminHtml('User Detail','user/user-detail',$data);
	}
		
	public function gameDetail($id)
	{

		$data['user'] = $this->common->getData('sport_game',array('id' => $id), array('single'));
	
		$this->adminHtml('User Detail','game/game-detail',$data);
	}

	public function user_eventDetail($id)
	{
		$where='SE.id = "'.$id.'"';
		$data['event'] = $this->common->get_eventList($where, array('single'));
		
		$user_id= $data['event']['user_id'];
		$user= $this->common->getData('user',array('id' => $user_id), array('single'));
		$data['event']['user_email'] = $user['email'];
		$data['event']['user_name'] = $user['name'];

		$this->adminHtml('Event Detail','event/user-event-detail',$data);
	}
		
		

	public function driverlist()
	{
		$data['driver'] = $this->common->getData('driver',array('company_id' => $this->company_id));
		$data['link'] = 'admin/driverDetail/';
		$this->adminHtml('Driver List','driver/driver-list',$data);
	}
		public function UserList()
	{
		
		$data['driver'] = $this->common->getData('user','',array('sort_by'=>'id','sort_direction' => 'desc'));
		$data['link'] = 'admin/userDetail/';
		$this->adminHtml('User List','user/user-list',$data);
	}


		public function gameList()
	{
		
		$data['driver'] = $this->common->getData('sport_game','',array('sort_by'=>'id','sort_direction' => 'desc'));
		$data['link'] = 'admin/gameDetail/';
		$data['update'] = 'admin/editGame/';
		$this->adminHtml('Game List','game/game-list',$data);
	}

		public function eventList()
	{
		$where='SE.event_user_type = 1';
		$data['event'] = $this->common->get_eventList($where);
		$data['link'] = 'admin/user_eventDetail/';
		
		$this->adminHtml('Event List','event/event-list',$data);
	}

		public function user_eventList()
	{
		
		$where='SE.event_user_type = 2';
		$data['event'] = $this->common->get_eventList($where);
	
		$data['link'] = 'admin/user_eventDetail/';
		
		$this->adminHtml('User Event List','event/userevent-list',$data);
	}


	


	public function assignCar()
	{
		$result = $this->common->updateData('driver',array('vehicle_id' => $_POST['vehicle_id'],'vehicle_class_id' => $_POST['class_id']),array('company_id' => $this->company_id,'did' => $_POST['did']));
		if($result){
			$car = $this->common->getData('vehicle_detail',array('id' => $_POST['vehicle_id']),array('field' => 'vehicle_no','single'));
			echo $car['vehicle_no'];
		}else{
			echo "0";
		}
	}

	public function profile($id)
	{	
		$data['user'] = $this->common->getData('user',array('id' => $id),array('single'));
		$this->adminHtml('Profile','profile',$data);
	}

	public function addVehicle()
	{
		$data['company'] = $this->common->getData('vehicle_company');
		$this->adminHtml('Add Vehicles','vehicle/add-vehicle',$data);
	}

	public function addVehicleDetail()
	{	
		if(!isset($_POST['model_id'])){ //print_r($_POST); die;
			redirect(base_url('admin/addVehicle'));
		} 
		$this->form_validation->set_rules('vehicle_no','Vehicle Number','required');
		if($this->form_validation->run() == false){
			$data = $_POST;
			$data['company_id'] = $this->company_id;
			$data['carClass'] = $this->common->getData('vehicle_class');
			$this->adminHtml('Add Vehicle Detail','vehicle/add-vehicle-detail',$data);		
		}else{
			$_POST['insurance'] = ""; 
			$path = './assets/vehicle/'.$_POST['company_id'];
  			if(!is_dir($path)){
	        	mkdir($path, 0755);
	        }
			$insurance = $this->common->do_upload('insurance',$path);
			if(isset($insurance['upload_data'])){
				$_POST['insurance'] = $insurance['upload_data']['file_name'];
			}
			$post = $this->common->getField('vehicle_detail',$_POST);
			
			$result = $this->common->insertData('vehicle_detail',$post);
			if($result){
				$this->flashMsg('success','Vehicle added successfully.Your vehicle is activated once it approved.');
			}else{
				$this->flashMsg('danger','Some error occured.Please try again');
			}
			redirect(base_url('admin/addVehicle'));
		}
	}

	public function vehicleList()
	{
		$data['vehicles'] = $this->common->vehicleList(array('company_id'=>$this->company_id));
		$this->adminHtml('Vehicle List','vehicle/vehicle-list',$data);
	}

	public function addPage()
	{
		$this->form_validation->set_rules('description','Description','required');
		if($this->form_validation->run() == false){						
			$this->adminHtml('Add Page','add-page');
		}else{
			$post = $this->common->getField('pages',$_POST);
			
			$result = $this->common->insertData('pages',$post);
			
			if($result){
				$this->flashMsg('success','Data added successfully');
			}else{
				$this->flashMsg('danger','Some Error occured.');
			} 			
			redirect(base_url('Admin/pageList'),'refresh');
		}
	}

	public function editPage($name)
	{
		$this->form_validation->set_rules('description','Description','required');
		if($this->form_validation->run() == false){			
			$data['page'] = $this->common->getData('pages',array('name' => $name), array('single'));
			$this->adminHtml('Update Page','add-page',$data);
		}else{
			$post = $this->common->getField('pages',$_POST);			
			$page = $this->common->getData('pages',array('name'=> $post['name']),array('single'));
			if($page){
				$result = $this->common->updateData('pages',$post,array('name'=>$post['name']));
			}
			if($result){
				$a = $this->flashMsg('success','Data update successfully');
			}else{
				$this->flashMsg('danger','Some Error occured.');
			} 			
			redirect(base_url('Admin/pageList'),'refresh');
		}
	}

	public function pageList()
	{
		$data['pages'] = $this->common->getData('pages');
		$this->adminHtml('Page List','page-list',$data);
	}

	public function aboutUs()
	{
		$this->form_validation->set_rules('description','Link','required');
		if($this->form_validation->run() == false){		
		$data['contact'] = $this->common->getData('pages',array('name'=> 'about-us'),array('single'));
			$this->adminHtml('About Us','about-us',$data);
		}else{
			$post = $this->common->getField('pages',$_POST);
			
			$result = $this->common->updateData('pages',$post,array('name' => 'about-us'));
			
			if($result){
				$a = $this->flashMsg('success','Data Update successfully');
			}else{
				$this->flashMsg('danger','Some Error occured.');
			} 			
			redirect(base_url('Admin/aboutUs'),'refresh');
		}
	}
}
