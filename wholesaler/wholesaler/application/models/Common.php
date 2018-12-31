<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Common extends CI_Model
{
	
	function __construct()
	{
		parent:: __construct();
	}

	public function getData($table,$where="",$options=array())
	{
		if(isset($options['field'])){
			$this->db->select($options['field']);
		}
				
		if($where != ""){
			$this->db->where($where);
		}
		if(isset($options['where_in']) && isset($options['where_in'])){
			 $this->db->where_in($options['colname'] ,$options['where_in']);
		}

		if (isset($options['sort_by']) && isset($options['sort_direction'])) {
			$this->db->order_by($options['sort_by'], $options['sort_direction']);
		}
		
		if (isset($options['group_by']) ) {
			$this->db->group_by($options['group_by']);
		}
		if (isset($options['limit']) && isset($options['offset']))	{
			$this->db->limit($options['limit'], $options['offset']);
		}
		else {
			if (isset($options['limit'])) {
			    $this->db->limit($options['limit']);
			}
		}
		$query = $this->db->get($table);
		$result = $query->result_array();
		if (!empty($options) && in_array('count', $options)) {

			return count($result);
		}
		if($result){
			if(isset($options) && in_array('single',$options)){ 
				return $result[0];
			}else{
				return $result;
			}
		}else{
			return false;
		}
	}

	public function getField($table,$data)
	{
		$post = array();
		$fields = $this->db->list_fields($table);
		foreach ($data as $key => $value) {
			if(in_array($key, $fields)){
				$post[$key] = $value;
			}
		}
		return $post;
	}

	public function getFieldKey($table)
	{
		return $this->db->list_fields($table);
	}

	public function insertData($table,$data)
	{
		return $this->db->insert($table,$data);
	}

	public function updateData($table,$data,$where)
	{		
		return $this->db->update($table,$data,$where);			
	}

	public function checkTrue(){
		if($this->db->affected_rows()){
			return true;
		}else{
			return false;
		}
	}

	public function deleteData($table,$where)
	{		
		return $this->db->delete($table,$where);
	}


	public function whereIn($table,$colname,$in,$where= array())
	{
		$this->db->where($where);
		$search  = "FIND_IN_SET('".$in."', $colname)";
		$this->db->where($search);
        $query=$this->db->get($table);
         $result = $query->result_array();	
        $result = $query->result_array();
		if($result){			
			return $result[0];
			
		}else{
			return false;
		}	
		
	}

	public function arrayToName($table,$field,$array)
	{		
		foreach ($array as $value) {
			$name[] = $this->getData($table,array('id'=> $value),array('field'=>$field,'single'));
		}		
		if(!empty($name)){
			foreach ($name as $key => $value) {
				$name1[] = $value[$field];
			}
			return implode(',', $name1);			
		}else{
			return false;
		}		
	}


	public function sendMail($to,$subject,$message,$options = array())
	{
		$this->load->library('email');
		$config = array (
                  'mailtype' => 'html',
                  'charset'  => 'utf-8',
                  'priority' => '1'
                );

		// $config = Array(
		//     'protocol' => 'smtp',
		//     'smtp_host' => 'ssl://smtp.googlemail.com',
		//     'smtp_port' => 465,
		//     'smtp_user' => 'ctinfotechindore@gmail.com',
		//     'smtp_pass' => 'android@123',
		//     'mailtype'  => 'html', 
		//     'charset'   => 'utf-8'
		// );
		//charset : iso-8859-1
        $this->email->initialize($config);
        if (isset($options['fromEmail']) && isset($options['fromName'])) {
			$this->email->from($options['fromEmail'], $options['fromName']);  
        }else{
			$this->email->from('support@taxi-app.com', 'Taxi App');        	
        }
		$this->email->to($to);
		if(isset($options['replyToEmail']) && isset($options['replyToName'])){
			$this->email->reply_to($options['replyToEmail'],$options['replyToName']);
		}
		$this->email->subject($subject);
		$this->email->message($message);
		if($this->email->send()){
			return true;
		}else{
			return false;
		}
	}

	public function do_upload($file,$path)
    { 	
        $config['upload_path']          = $path;
        $config['allowed_types']        = 'gif|jpg|png|gif|jpeg|wmv';
        $config['encrypt_name']        = true;
        // $config['max_size']             = 100;
        // $config['max_width']            = 1024;
        // $config['max_height']           = 768;

        $this->load->library('upload');
        $this->upload->initialize($config);
        
        if ( ! $this->upload->do_upload($file))
        {
            $error = array('error' => $this->upload->display_errors());
            return $error;
        }
        else
        {
            $data = array('upload_data' => $this->upload->data());
            return $data;
        }
    }


    public function do_upload_file($file,$path)
    { 	
        $config['upload_path']          = $path;
        $config['allowed_types']        = 'gif|jpg|png|gif|3gp|mp4|avi|wmv';
        $config['encrypt_name']        = true;
        // $config['max_size']             = 100;
        // $config['max_width']            = 1024;
        // $config['max_height']           = 768;

        $this->load->library('upload');
        $this->upload->initialize($config);
        
        if ( ! $this->upload->do_upload($file))
        {
            $error = array('error' => $this->upload->display_errors());
            return $error;
        }
        else
        {
            $data = array('upload_data' => $this->upload->data());
            return $data;
        }
    }


    public function multi_upload($file,$path)
	{       
		$config = array();
	    $config['upload_path'] = $path; // upload path eg. - './resources/images/products/';
	    $config['allowed_types'] = '*';
	    $config['encrypt_name'] = true;
	    //$config['max_size']      = '0';
	    $config['overwrite']     = FALSE;
	    $this->load->library('upload',$config);
	    $dataInfo = array();
	    $files = $_FILES; 
	    	    
	    foreach ($files[$file]['name'] as $key => $image) {  
	    	
            $_FILES[$file]['name']= $files[$file]['name'][$key]; 
            $_FILES[$file]['type']= $files[$file]['type'][$key];
            $_FILES[$file]['tmp_name']= $files[$file]['tmp_name'][$key];
            $_FILES[$file]['error']= $files[$file]['error'][$key];
            $_FILES[$file]['size']= $files[$file]['size'][$key];

            $this->upload->initialize($config);

            if ($this->upload->do_upload($file)) {
               $dataInfo[] = $this->upload->data();
            } else {
                return $this->upload->display_errors();
            }
        }
	    if(!empty($dataInfo)){
	    	return $dataInfo;
	    }else{
	    	return false;
	    }
	}

	public function vehicleList($where="")
	{
		$this->db->select('VD.id,VD.vehicle_no,VD.status,VC.company,VM.model,VCl.class');
		$this->db->from('vehicle_detail as VD');
		if($where != ""){
			$this->db->where($where);
		}
		$this->db->join('vehicle_company as VC','VC.id = VD.vehicle_company_id');
		$this->db->join('vehicle_model as VM','VM.id = VD.model_id');
		$this->db->join('vehicle_class as VCl','VCl.id = VD.vehicle_class_id');
		$res = $this->db->get()->result_array();
		
		if($res){			
			return $res;
		}
		else
		{
			return false;
		}
	}







	public function get_eventList($where="",$options=array())
	{
		$this->db->select('SE.id,SE.title,SE.latitude,SE.longitude,SE.event_address,SE.price,SE.join_user,SE.price,SE.event_image,SE.join_user,SE.user_id,SE.game_id,SG.game_image,SE.event_user_type,SE.event_time,SE.event_duration,SE.event_participant_no,SE.event_description,SE.status,SG.game_name,');
		$this->db->from('sport_event as SE');
		if($where != ""){
			$this->db->where($where);
		}
		$this->db->join('sport_game as SG','SG.id = SE.game_id');
		$this->db->order_by("SE.id",'DESC');

		$res = $this->db->get()->result_array();
		
		if($res){			
			if(isset($options) && in_array('single',$options)){ 
				return $res[0];
			}else{
				return $res;
			}
		}
		else
		{
			return false;
		}
	}



	public function get_join_user($where="",$options=array())
	{
		$this->db->select('U.id,U.name,U.email,U.image');
		$this->db->from('join_event_tbl as JE');
		if($where != ""){
			$this->db->where($where);
		}
		$this->db->join('user as U','U.id = JE.join_id');
		$this->db->order_by("JE.id",'DESC');

		$res = $this->db->get()->result_array();
		
		if($res){			
			if(isset($options) && in_array('single',$options)){ 
				return $res[0];
			}else{
				return $res;
			}
		}
		else
		{
			return false;
		}
	}

	public function get_notification_user($where="",$options=array())
	{
		$this->db->select('U.id,U.name,U.email,U.image,NT.message,NT.date,NT.type,NT.id as Notification_id');
		$this->db->from('notification_tbl as NT');
		if($where != ""){
			$this->db->where($where);
		}
		$this->db->join('user as U','U.id = NT.user_send_from');
		$this->db->order_by("NT.id",'DESC');

		$res = $this->db->get()->result_array();
		
		if($res){			
			if(isset($options) && in_array('single',$options)){ 
				return $res[0];
			}else{
				return $res;
			}
		}
		else
		{
			return false;
		}
	}



	function get_record_join_two_table($table1,$table2,$id1,$id2,$column='',$where='',$orderby=''){
        if($column !='')
        {
            $this->db->select($column);
        }
        else
        {
            $this->db->select('*');
        }    
        $this->db->from($table1);
        $this->db->join($table2,$table2.'.'.$id2.'='.$table1.'.'.$id1);        
        if($where !='')
        {
            $this->db->where($where);
        }
		if($orderby!='')
			{
				$this->db->order_by($orderby, 'desc');
			}
        $query=$this->db->get();
        return $query->result();
    }
		public function check_data($table_name, $where) {
		$this -> db -> select("*");
		$this -> db -> from($table_name);
		$this -> db -> where($where);
		$query = $this -> db -> get();

		if ($query -> num_rows() > 0) {
			return $query -> first_row();
		} else {
			return FALSE;
		}
	}



	public function get_user_info($where="",$options=array())
	{
		$this->db->select('U.id,U.name,U.email,U.mobile,U.suburb,U.user_address,U.created_at,U.android_token,U.ios_token,U.user_latitude,U.user_longitude,U.user_type,U.otp,U.category,U.image,C.name as country_name');
		$this->db->from('user as U');
		if($where != ""){
			$this->db->where($where);
		}
		$this->db->join('countries as C','C.id = U.country_id');
		// $this->db->join('states as S','S.id = U.state_id');
		// $this->db->join('cities as CT','CT.id = U.city_id');
		$this->db->order_by("U.id",'DESC');

		$res = $this->db->get()->result_array();
		
		if($res){			
			if(isset($options) && in_array('single',$options)){ 
				return $res[0];
			}else{
				return $res;
			}
		}
		else
		{
			return false;
		}
	}





	public function get_eventList_by_lat($where,$user_latitude,$user_longitude)
	{
		
		$this->db->select('SE.id,SE.title,SE.latitude,SE.longitude,SE.event_address,SE.join_user,SE.event_image,SE.price,SE.join_user,SE.user_id,SE.game_id,SG.game_image,SE.event_user_type,SE.event_time,SE.event_duration,SE.event_participant_no,SE.event_description,SE.status,SG.game_name, (
     3959 * acos (
      cos ( radians("'.$user_latitude.'") )
      * cos( radians( SE.latitude ) )
      * cos( radians( SE.longitude ) - radians("'.$user_longitude.'") )
      + sin ( radians("'.$user_latitude.'") )
      * sin( radians( SE.latitude ) )
    )
  ) * 1.609344 AS distance');



		$this->db->from('sport_event as SE');
		if($where != ""){
			$this->db->where($where);
		}
		$this->db->join('sport_game as SG','SG.id = SE.game_id');
		$this->db->order_by("distance");

		$res = $this->db->get()->result_array();


		
		if($res){			
			return $res;
		}
		else
		{
			return false;
		}
	}


	function query($sql) {
       $query = $this->db->query($sql);
       if ($query->num_rows() > 0) {
           
           return $query->result();
       }else
       {
        return FALSE;
       }
   }


}
