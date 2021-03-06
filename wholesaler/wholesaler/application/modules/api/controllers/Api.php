<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends Base_Controller {

	public function __construct()
	{
		parent:: __construct();
		$this->checkAuth();		
		$this->load->helper('common');
		$this->load->library('email');
		$this->load->library('m_pdf');
		$this->load->library('paypal_lib');
	}	




	 





	public function friend_request()
	{
		if(!empty($_REQUEST['user_id']) && !empty($_REQUEST['friend_id'] ))
        {
        	$user_id=$_REQUEST['user_id'];
			$friend_id=$_REQUEST['friend_id'];
			

			$where = '(user_id = '.$_REQUEST['user_id'].' AND friend_id	= '.$_REQUEST['friend_id'].') OR (user_id = '.$_POST['friend_id'].' AND friend_id = '.$_REQUEST['user_id'].')';
			$result = $this->common->getData('friend_tbl',$where,array('single'));

			if(empty($result))
			{
				$insert = $this->common->insertData('friend_tbl',array('user_id' => $user_id,'friend_id' => $friend_id,'created_at' => Date('Y-m-d H:i:s')));

				// notification start

				$today = Date('Y-m-d H:i:s');

				$this->notification_count($friend_id);


				$insert_notification = $this->common->insertData('notification_tbl',array('user_id' => $friend_id,'message' => "You have a new friend request",'date'=>$today,'user_send_from'=>$user_id,'type'=>1));

				$user_friend_id = $this->common->getData('user',array('id'=>$friend_id),array('single'));

				$user_my_id = $this->common->getData('user',array('id'=>$user_id),array('single'));



				$ios_token = $user_friend_id['ios_token'];
				
				$android_token = $user_friend_id['android_token'];
				
				$user_data_from_name = $user_my_id['name']; 
				$message_push = $user_data_from_name." sent You a friend request";
				$title = "friend request";
				$type = "friend request";

				$msg_notification = array
					(
					'body' 	=> $message_push,
					'title'	=> $title,
					'icon'	=> 'ic_stat_wholesaler',
					'sound' => '',
					'color' => '',
					//'badge' => 1,
					// 'click_action' => 'com.mycoach.content_manager.activity.ContentDetailActivity'
					);




				if($ios_token != ""){

					$messages_push = array("alert" => $title, "msg" => $message_push,"sound"=>"default","type" => $type,"user_id"=>$user_id);	
					
					
					$this->push_iOS($ios_token,$messages_push);

					
				}
				else if($android_token != "")
				{
					
					$messages_push = array('notification'=>$msg_notification,'notification_type'=>$type,"user_id"=>$user_id);	

					
					

					$registatoin_id = array($android_token); 
					$this->send_notification($registatoin_id, $messages_push);

				}


				// notification end


				$this->response(true,"Send Successfully");
	        }
	        else
	        {
	        	if($result['status'] == 1)
	        	{
	        		$this->response(true,"Already added friend");
	        	}
	        	else
	        	{
	        		$this->response(true,"Already have sent friend request");
	        	}
	        }
		}
		else
		{
			$this->response(false,"Missing Parameter.");
		}
	}



		//batch count code start

			function notification_count($user_id)
			{
				$user_batch_count = $this->common->getData('notification_count_tbl',array('user_id'=>$user_id),array('single'));
				
				if(empty($user_batch_count))
				{
					$data_batch['batch_count'] = 1;
					$data_batch['user_id'] = $user_id;
					$this->common->insertData('notification_count_tbl',$data_batch);
				}
				else
				{
					$batch_count_no = $user_batch_count['batch_count']+1;
					$data_batch['batch_count']=$batch_count_no;
					$result = $this->common->updateData('notification_count_tbl',$data_batch,array('user_id' => $user_id));
				}
			}

				//batch count code end




	public function getKey()
	{
		$result = $this->common->getFieldKey($_POST['table']);
		echo json_encode($result);
	}

	public function login()
	{				
		$_POST['password'] = md5($_POST['password']);
		$result = $this->common->getData('user',array('email = ' => $_POST['email'], 'password' => $_POST['password']),array('single'));

		if($result)
				{

			if(!empty($result['image']))
			{
				$result['image'] = base_url('/assets/userfile/profile/'.$result['image']);
			}
			else
			{
				$result['image'] = "";
			}

			if($result['user_type'] == 2 || $result['user_type'] == 3)
			{

				$category = $result['category'];
				if($category != "")
				{
					$category  = explode(",",$category);

					foreach($category as $cat)
					{
						$cat_info = $this->common->getData('category_tbl',array('category_id' => $cat),array('single')); 
						$category_data[] = array('category_id'=>$cat_info['category_id'],'category_name'=>$cat_info['category_name'],'category_image'=>$cat_info['category_image']);
					}

				
				}
				else
				{
					$category_data =array();
				}

				
				$result['category_info'] = $category_data;
				$other_info = $this->common->getData('other_user_info',array('user_id' => $result['id']),array('single'));
			}

			if(!empty($other_info))
			{

					if(!empty($other_info['user_video']))
					{
						$result['user_video'] = base_url('/assets/userfile/profile/'.$other_info['user_video']);
					}
					else
					{
						$result['user_video'] = "";
					}


					$result['get_percent'] =  $other_info['get_percent'];
					$result['min_price'] =  $other_info['min_price'];
					$result['max_price'] =  $other_info['max_price'];
					$result['expected_delivery_date'] =  $other_info['expected_delivery_date'];
					$result['dropship'] =  $other_info['dropship'];
			}
		
				// if($result['verified'] != 1)
				// {
				// 	$this->response(false,'firstly verify email then login');
				// 	exit();
				// } 
		

				


			if(isset($_POST['android_token'])){
				$old_device = $this->common->getData('user',array('android_token' => $_POST['android_token']),array('single','field'=>'id'));	
			}		
			if (isset($_POST['ios_token'])) {
				$old_device = $this->common->getData('user',array('ios_token' => $_POST['ios_token']),array('single','field'=>'id'));	
			}
			if($old_device){
				$this->common->updateData('user',array('android_token' => "", "ios_token" => ""),array('id' => $old_device['id']));
			}

			$this->common->updateData('user',array('ios_token' =>$_POST['ios_token'], 'android_token' => $_POST['android_token']), array('id' => $result['id']));
			$this->response(true,'Login Successfull',array("userinfo" => $result));					
		}else{
			$message = "Wrong email or password";			
			$this->response(false,$message,array("userinfo" => ""));
		}
	}


	function multi_array_search_with_condition($array, $condition)
	{
	    $foundItems = array();

	    foreach($array as $item)
	    {
	        $find = TRUE;
	        foreach($condition as $key => $value)
	        {
	        	
			   if(isset($item[$key]) && strpos(strtolower($item[$key]),$value) !== false)
	            {
	            	if (strpos(strtolower($item[$key]), $value) == 0)
	            	{
	            		$find = TRUE;
	            	}
	            	else
	            	{
	            		$find = FALSE;
	            	}
	                
	            } else {
	                $find = FALSE;
	            }
	        }
	        if($find)
	        {
	            array_push($foundItems, $item);
	        }
	    }
	    return $foundItems;
	}


	public function broadcast_list()
	{
		$where = "user_type = '".$_REQUEST['user_type']."' OR user_type = 4";
        $result = $this->common->getData('group_message_tbl',$where,array('sort_by'=>'id','sort_direction'=>'DESC'));
       
        if(!empty($result)){

        	 foreach ($result as $key => $value) {
        	 	if(!empty($value['image']))
                 	{
                 		$image = base_url('/assets/chat/'.$value['image']);
                 	}
                 	else
                 	{
                 		$image = '';
                 	}


                 $result_user_detail = $this->common->getData('user',array('id' => $value['user_id']),array('single'));

                 	if(!empty($result_user_detail['image']))
                 	{
                 		$user_image = base_url('/assets/userfile/profile/'.$result_user_detail['image']);
                 	}
                 	else
                 	{
                 		$user_image = '';
                 	}


                 $arr[]=array('user_id'=>$value['user_id'],'user_name'=>$result_user_detail['name'],'user_image'=>$user_image,'message'=>$value['message'],'image'=>$image);
        	 }
			$this->response(true,"user fetch Successfully.",array("broadcast_list" => $arr));			
		}else{
			$this->response(false,"There is a problem, please try again.",array("broadcast_list" => array()));
		}

	}




	public function searchItem()
	{
		if(!empty($_REQUEST['name']) && !empty($_REQUEST['type']))
		{
			$name = $_REQUEST['name'];
			if($_REQUEST['type'] == 1)
			{
				$where = "category = '".$_REQUEST['category']."'  AND user_id = '".$_REQUEST['user_id']."'";
		        $result = $this->common->getData('product_tbl',$where);
	        	
	        	if(!empty($result))
	        	{
					foreach ($result as $value)
					{
		        		$product_id = $value['product_id'];
		        		$product_name = $value['product_name'];
		        		$price = $value['price'];
		        		$description = $value['description'];
		        		$quantity = $value['quantity'];

	        			$avg_rating = $this->rating_count_product($product_id);

	        			$product_image = $this->common->getData('product_image',array('product_id' => $product_id),array('single'));
	        			
	        			if(!empty($product_image))
	        			{
	        				$image_name = base_url('/assets/product/'.$product_image['image_name']);
						}
	        			else
	        			{
	        				$image_name = "";
	        			}

	        			$array_data[] = array('product_id'=>$product_id,'product_name'=>$product_name,'price'=>$price,'description'=>$description,'quantity'=>$quantity,'main_image'=>$image_name,'rating'=>$avg_rating);

	        		}
				}
				else
				{
					$array_data[] = array();
				}
			}

			if($_REQUEST['type'] == 2)
			{
				$where = "(user_type = 2 or user_type=3) AND FIND_IN_SET('".$_REQUEST['category']."', category)";
            	$result = $this->common->getData('user',$where);

            	
     			if(!empty($result))
         		{    
	            	foreach($result as $value)
	            	{
	            		if(!empty($value['image']))
						{
							$image = base_url('/assets/userfile/profile/'.$value['image']);
						}
						else
						{
							$image = "";
						}

						$rating = $this->rating_count($value['id']);
	            		$array_data[] = array('id'=>$value['id'],'name'=>$value['name'],'email'=>$value['email'],'mobile'=>$value['mobile'],'user_address'=>$value['user_address'],'image'=>$image,'user_type'=>$value['user_type'],'id'=>$value['id'],'id'=>$value['id'],'id'=>$value['id'],'id'=>$value['id'],'id'=>$value['id'],'rating'=> $rating);
            		}
    			}
    			else
    			{
    				$array_data[] = array();
    			}
					
			}


			if($_REQUEST['type'] == 3)
			{	

				if($_REQUEST['search_type'] == 1)
				{
					$user_id = $_REQUEST['user_id'];
					$where = "id != '" . $user_id . "'";
	            	$result = $this->common->getData('user',$where);
				}


				if($_REQUEST['search_type'] == 2)
				{
					$user_id = $_REQUEST['user_id'];
					$where = "id != '" . $user_id . "' AND user_type = 1";
	            	$result = $this->common->getData('user',$where);
				}

				if($_REQUEST['search_type'] == 3)
				{
					$user_id = $_REQUEST['user_id'];
					$where = "id != '" . $user_id . "' AND user_type = 2";
	            	$result = $this->common->getData('user',$where);
				}

				if($_REQUEST['search_type'] == 4)
				{
					$user_id = $_REQUEST['user_id'];
					$where = "id != '" . $user_id . "' AND user_type = 3";
	            	$result = $this->common->getData('user',$where);
				}
				
     			
     			if(!empty($result))
         		{    
	            	foreach($result as $value)
	            	{
	            		if(!empty($value['image']))
						{
							$image = base_url('/assets/userfile/profile/'.$value['image']);
						}
						else
						{
							$image = "";
						}


						$where_friend = '(user_id = '.$value['id'].' AND friend_id	= '.$user_id.') OR (user_id = '.$user_id.' AND friend_id = '.$value['id'].')';
						
						$result_friend = $this->common->getData('friend_tbl',$where_friend,array('single'));



					if(!empty($result_friend))
					{
						if(($result_friend['friend_id'] == $user_id )&& ($result_friend['status'] ==0) )
						{
							$friend_status = 1;
						}

						if(($result_friend['friend_id'] == $value['id'] )&& ($result_friend['status'] ==0) )
						{
							$friend_status = 2;
						}

						if(($result_friend['status'] ==1))
						{
							$friend_status = 4;
						}

						$friend_id = $result_friend['id'];	
					}
					else
					{
						$friend_status = 3;
						$friend_id = "";	
					}


						$rating = $this->rating_count($value['id']);
	            		$array_data[] = array('id'=>$value['id'],'name'=>$value['name'],'email'=>$value['email'],'mobile'=>$value['mobile'],'user_address'=>$value['user_address'],'image'=>$image,'user_type'=>$value['user_type'],'id'=>$value['id'],'id'=>$value['id'],'id'=>$value['id'],'id'=>$value['id'],'id'=>$value['id'],'rating'=> $rating,'friend_status'=>$friend_status,'friend_id'=>$friend_id);
            		}
    			}
    			else
    			{
    				$array_data[] = array();
    			}
					
			}

				
			if($_REQUEST['type'] == 1)
			{
				$filtered = $this->multi_array_search_with_condition($array_data, array( 'product_name' =>strtolower($name) ));
			}
			else
			{
				$filtered = $this->multi_array_search_with_condition($array_data, array( 'name' =>strtolower($name) ));
			}

			if(!empty($filtered))
			{
				if($_REQUEST['type'] == 1)
				{
					$this->response(true,"product Found Successfully.",array("product_list" => $filtered));
				}

				if($_REQUEST['type'] == 2)
				{
					$this->response(true,"wholesaler Found Successfully.",array("wholesaler_list" => $filtered));
				}

				if($_REQUEST['type'] == 3)
				{
					$this->response(true,"User Found Successfully.",array("all_user_list" => $filtered));
				}
				
			}
			else
			{

				if($_REQUEST['type'] == 1)
				{
					$this->response(true,"Product not found",array("product_list" => array()));
				}

				if($_REQUEST['type'] == 2)
				{
					$this->response(true,"Wholesaler not found",array("wholesaler_list" => array()));
				}

				if($_REQUEST['type'] == 3)
				{
					$this->response(true,"User not found",array("all_user_list" => array()));
				}
			}
    		
	      
	     

		}
		 else
        {
        	$this->response(false,"Missing Parameter.");
        }


	}




 public function save_download()
  { 

  	if(!empty($_REQUEST['wholesaler_id']))
  	{

  		error_reporting(0);
		//load mPDF library
 		
 		//now pass the data//
		$this->data['title']="MY PDF TITLE 1.";
		$this->data['description']="";
		$this->data['description']=$this->official_copies;
		//now pass the data //
 
		$wholesaler_id = $_REQUEST['wholesaler_id'];
		
		$where="product_tbl.user_id = '" . $wholesaler_id . "'";
		$data['product_list'] = $this->common->get_record_join_two_table('product_tbl','category_tbl','	category','category_id','',$where,'product_tbl.product_id');

		if($data['product_list'])
		{
			$template = $this->load->view('template/product-list',$data,true);
			$html=$template; 
			
			//this the the PDF filename that user will get to download
			$pdfFilePath ="mypdfName12-".time()."-download.pdf";
	 
			//actually, you can pass mPDF parameter on this load() function
			$pdf = $this->m_pdf->load();
			
			//generate the PDF!
			$pdf->WriteHTML($html,2);
			
			//offer it to user via browser download! (The PDF won't be saved on your server HDD)
			//$pdf->Output($pdfFilePath, "D");
			//$pdf->Output($pdfFilePath, \Mpdf\Output\Destination::FILE);
			
			$pdf->Output('./assets/pdf/'.$pdfFilePath,'F');

			$result_pdf = base_url('/assets/pdf/'.$pdfFilePath);

			$this->response(true,"PDF Download Successfully.",array("pdf_file" => $result_pdf));
		}
		else
		{
			$this->response(true,"Product Not Found");
		}			
	}
	else
	{
		$this->response(false,"Missing Parameter.");
	} 	
  }


  	
	



	public function category_list()
	{
		
        $result = $this->common->getData('category_tbl');
        
        foreach($result as $value)
       	{
       	
       		$count = $this->common->getData('product_tbl',array('category'=>$value['category_id']),array('count'));


       		if(!empty($value['category_image']))
			{
				$value['category_image'] = base_url('/assets/category/'.$value['category_image']);
			}
			else
			{
				$value['category_image'] = "";
			}

       		$category_info[]=array('category_id'=>$value['category_id'],'category_name'=>$value['category_name'],'status'=>$value['status'],'category_image'=>$value['category_image'],'count'=>$count);

       	}
        if(!empty($result)){
			$this->response(true,"Category fetch Successfully.",array("category_list" => $category_info));			
		}else{
			$this->response(false,"Item not found",array("category_list" => array()));
		}

	}




	public function country_list()
	{
		
        $result = $this->common->getData('countries');
        
        if(!empty($result))
        {
        	foreach($result as $value)
       		{
       			$country_list[]=array('id'=>$value['id'],'name'=>$value['name']);
			}
			$this->response(true,"Country fetch Successfully.",array("country_list" => $country_list));			
		}else{
			$this->response(false,"Country not found",array("country_list" => array()));
		}

	}


	public function state_list()
	{
		if(!empty($_REQUEST['country_id']))
		{
		
	        $result = $this->common->getData('states',array('country_id'=>$_REQUEST['country_id']));

	        
	        if(!empty($result))
	        {
	        	foreach($result as $value)
	       		{
	       			$state_list[]=array('id'=>$value['id'],'name'=>$value['name']);
				}
				$this->response(true,"State fetch Successfully.",array("state_list" => $state_list));			
			}else{
				$this->response(false,"State not found",array("state_list" => array()));
			}
		}
		else
		{
			$this->response(false,"Missing Parameter.");
		}

	}


	public function city_list()
	{
		if(!empty($_REQUEST['state_id']))
		{
		
	        $result = $this->common->getData('cities',array('state_id'=>$_REQUEST['state_id']));
	        
	        
	        if(!empty($result))
	        {
	        	foreach($result as $value)
	       		{
	       			$cities_list[]=array('id'=>$value['id'],'name'=>$value['name']);
				}
				$this->response(true,"Cities fetch Successfully.",array("cities_list" => $cities_list));			
			}else{
				$this->response(false,"Cities not found",array("cite_list" => array()));
			}
		}
		else
		{
			$this->response(false,"Missing Parameter.");
		}

	}



	public function delete_product()
	{
		if(!empty($_REQUEST['product_id']))
		{
			$product_id = $_REQUEST['product_id'];
			$where="product_id	='" . $product_id . "'";
            $this->common->deleteData('product_tbl',$where);
            $this->response(true,"delete product Successfully.");	
		}
		else
		{
			$this->response(false,"Missing Parameter.");
		}
	}



	public function  add_product()
	{

		if(!empty($_REQUEST['product_name']) && !empty($_REQUEST['price']) && !empty($_REQUEST['category']) && !empty($_REQUEST['user_id']) && !empty($_REQUEST['quantity']) && !empty($_REQUEST['minimum_quantity']))
		{
			$product_name = $_REQUEST['product_name'];
			$user_id = $_REQUEST['user_id'];
			$price = $_REQUEST['price'];
			$category = $_REQUEST['category'];
			$description = $_REQUEST['description'];
			$quantity = $_REQUEST['quantity'];
			$minimum_quantity = $_REQUEST['minimum_quantity'];

			if(!empty($_FILES['product_img'])){
				$product_img = $this->common->multi_upload('product_img','./assets/product/');
				
			}
			
			$where_user="user_id	='". $user_id ."'";
			$user_info_single = $this->common->getData('other_user_info',$where_user,array('single'));
			$min_price = $user_info_single['min_price'];
			$max_price = $user_info_single['max_price'];

			 // comment in small time
			// if($min_price>$price)
			// {
			// 	$this->response(false,"Please enter minimum price ".$min_price);
			// 	exit();	
			// }

			// if($max_price<$price)
			// {
			// 	$this->response(false,"Please enter under maximum price ".$max_price);	
			// 	exit();
			// }

			// comment in small time

			if(!empty($_FILES['product_video']['name'])){
					$product_video = $this->common->do_upload_file('product_video','./assets/product/video/');
						if(isset($product_video['upload_data'])){
						$data['product_video'] = $product_video['upload_data']['file_name'];
						}
					}


			$data['product_name'] = $product_name;
			$data['price'] = $price;
			$data['category'] = $category;
			$data['description'] = $description;
			$data['user_id'] = $user_id;
			$data['quantity'] = $quantity;
			$data['minimum_quantity'] = $minimum_quantity;



			$result = $this->common->insertData('product_tbl',$data);
			

			if($result)
			{
				$insid = $this->db->insert_id();
				if (!empty($product_img)) 
				{
					foreach($product_img as $keyimg) 
					{
					        $data_img['image_name'] = $keyimg['file_name'];
					        $data_img['product_id'] = $insid;
					        $result_other = $this->common->insertData('product_image',$data_img);
					}
				}

				$this->response(true,"Product Added Successfully");	
			}

				
			else
			{
				$this->response(false,"There is a problem, please try again.");
			}
		}
		else
		{
			$this->response(false,"Missing Parameter.");
		}
	}




	public function update_product(){
		
	
		if(!empty($_REQUEST['id']) && !empty($_REQUEST['user_id']))
	    {
	    	$id = $_POST['id']; 
	    	$user_id = $_POST['user_id']; 
			unset($_POST['id']);
			unset($_POST['user_id']);

			
			$user_data_info = $this->common->getData('other_user_info',array('user_id'=>$user_id),array('single'));

		
			$min_price = $user_data_info['min_price'];
			$max_price = $user_data_info['max_price'];


			
			
			// if(!empty($_POST['price']))
			// {
			// 	 $price = $_POST['price'];
			// 	if($min_price>$price)
			// 	{
			// 		$this->response(false,"Please enter minimum price ".$min_price);
			// 		exit();	
			// 	}

			// 	if($max_price<$price)
			// 	{
			// 		$this->response(false,"Please enter under maximum price ".$max_price);	
			// 		exit();
			// 	}
			// }	



			if(!empty($_FILES['product_video']['name'])){
					$product_video = $this->common->do_upload_file('product_video','./assets/product/video/');
						if(isset($product_video['upload_data'])){
						$_POST['product_video'] = $product_video['upload_data']['file_name'];
						}
					}


			$post = $this->common->getField('product_tbl',$_POST);
			
			if(!empty($post))
			{		
				$result = $this->common->updateData('product_tbl',$post,array('product_id' => $id)); 
			}
			else
			{
				$result = "";
			}

			if(!empty($_FILES['product_img'])){
				$product_img = $this->common->multi_upload('product_img','./assets/product/');
			}

			
			if($result){

				if (!empty($product_img)) 
				{
					foreach($product_img as $keyimg) 
					{
					        $data_img['image_name'] = $keyimg['file_name'];
					        $data_img['product_id'] = $id;
					        $result_other = $this->common->insertData('product_image',$data_img);
					}
				}


				$this->response(true,"Profile Update Successfully.");
			}
			else{
				$this->response(false,"There is a problem, please try again.");
			}
		}
		else
		{
			$this->response(false,'Missing parameter');
		}
	}

	public function delete_image()
	{
		if(!empty($_REQUEST['id']))
		{
			$id = $_REQUEST['id'];
			$where= "id	='".$id."'";
            $value = $this->common->deleteData('product_image',$where);
			$this->response(true,"Delete Successfully.");
		}
		else
		{
			$this->response(false,"Missing Parameter.");
		}
	}


		public function delete_video()
			{
				if(!empty($_REQUEST['product_id']))
				{
					$product_id = $_REQUEST['product_id'];
					$data['product_video'] = "";
    				$result = $this->common->updateData('product_tbl',$data,array('product_id' => $product_id));


					$this->response(true,"Delete Successfully.");
				}
				else
				{
					$this->response(false,"Missing Parameter.");
				}
			}





	public function get_product_detail()
	{
		if(!empty($_REQUEST['product_id']))
		{
			$product_id = $_REQUEST['product_id'];
			$where="product_id = '" . $product_id . "'";
        	$result = $this->common->getData('product_tbl',$where,array('single'));

        	 if(!empty($result)){

        	 	if(!empty($result['product_video']))
					{
						$result['product_video'] = base_url('/assets/product/video/'.$result['product_video']);
					}
					else
					{
						$result['product_video'] = "";
					}

        	 	$where_img ="product_id = '" . $product_id . "'";
        	 	$result_img = $this->common->getData('product_image',$where);
        	 	if(!empty($result_img))
        	 	{

        	 		foreach ($result_img as $value_img) {
        	 			$image = base_url('/assets/product/'.$value_img['image_name']);
        	 			$image_product[] = array('id' => $value_img['id'] ,'image_name' => $image);

        	 		}
        	 		$result['image'] = $image_product; 
        	 	}
        	 	else
        	 	{
        	 		$result['image'] = array(); 
        	 	}

        	 		$count_user_no = $this->common->getData('product_rating',array('product_id'=>$product_id),array('count'));
        	 		if($count_user_no)
        	 		{
        	 			$result['rating_user_no'] = $count_user_no;
        	 		}
        	 		else
        	 		{
        	 			$result['count_user_no'] = 0;
        	 		}
        	 	
        	 	$result['rating'] = $this->rating_count_product($product_id);
				$this->response(true,"Product fetch Successfully.",array("product_detail" => $result));			
			}else{
				$this->response(true,"Product not found",array("product_detail" => ""));
			}
		}
		else
		{
			$this->response(false,"Missing Parameter.");
		}
	}


	public function product_rating_detail()
	{
		if(!empty($_REQUEST['id']))
		{
			$id = $_REQUEST['id'];
			
			$where="product_rating.product_id = '" . $id . "'";
			
        	$result = $this->common->get_record_join_two_table('product_rating','user','user_id','id','',$where,'product_rating.id');

        	if($result)
        	{
	        	foreach($result as $value)
	        	{
	        		$rating_detail[] = array('product_id'=>$value->product_id,'user_id'=>$value->user_id,'name'=>$value->name,'email'=>$value->email,'image'=>$value->image,'rating'=>$value->rating,'comment'=>$value->comment);
	        	}

	        	$this->response(true,"Rating Found",array("rating_list" => $rating_detail));
	        }
	        else
	        {
	        	$this->response(true,"Rating Not Found",array("rating_list" => array()));		
	        }
		}
		else
		{
			$this->response(false,"Missing Parameter.");
		}

	}




	public function wholesaler_rating_detail()
	{
		if(!empty($_REQUEST['id']))
		{
			$id = $_REQUEST['id'];
			
			$where="wholesaler_rating.wholesaler_id = '" . $id . "'";
			
        	$result = $this->common->get_record_join_two_table('wholesaler_rating','user','user_id','id','',$where,'wholesaler_rating.id');

        	if($result)
        	{
	        	foreach($result as $value)
	        	{
	        		$rating_detail[] = array('wholesaler_id'=>$value->wholesaler_id,'user_id'=>$value->user_id,'name'=>$value->name,'email'=>$value->email,'image'=>$value->image,'rating'=>$value->rating,'comment'=>$value->comment);
	        	}

	        	$this->response(true,"Rating Found",array("rating_list" => $rating_detail));
	        }
	        else
	        {
	        	$this->response(true,"Rating Not Found",array("rating_list" => array()));		
	        }
		}
		else
		{
			$this->response(false,"Missing Parameter.");
		}

	}


	public function product_by_category()
	{
		if(!empty($_REQUEST['category_id']))
		{
			
		$category_id = $_REQUEST['category_id'];
		$where="category = '" . $category_id . "'";
        $result = $this->common->getData('product_tbl',$where);

		if(!empty($result)){
			foreach($result as $value)
			{
				$avg_rating = $this->rating_count_product($value['product_id']);
				$product_info[] = array('product_id'=>$value['product_id'],'product_name'=>$value['product_name'],'price'=>$value['price'],'category'=>$value['category'],'product_video'=>$value['product_video'],'description'=>$value['description'],'quantity'=>$value['quantity'],'user_id'=>$value['user_id'],'rating'=>$avg_rating);
			}
			$this->response(true,"Product fetch Successfully.",array("product_list" => $product_info));			
		}else{
			$this->response(true,"Product not found",array("product_list" => array()));
		}
		
		}
		else
		{
			$this->response(false,"Missing Parameter.");
		}

	}


	public function add_to_cart()
	{
		if(!empty($_REQUEST['product_id']) && !empty($_REQUEST['user_id']) && !empty($_REQUEST['quantity']))
		{
			$product_id = $_REQUEST['product_id'];
			$user_id = $_REQUEST['user_id'];
			$quantity = $_REQUEST['quantity'];
			

			$where="user_id = '".$user_id."' AND product_id = '".$product_id."'AND paid_status = 0";
       		$result = $this->common->getData('cart_tbl',$where,array('single'));

       		if(!empty($result))
       		{
       			$old_quantity = $result['quantity'];
       			// $update_data['quantity'] =  $old_quantity + $quantity;
       			$update_data['quantity'] = $quantity;
       			$update_cart = $this->common->updateData('cart_tbl',$update_data,array('user_id' => $user_id,'product_id' => $product_id));
       			$this->response(true,"Update Cart Successfully.");	
       		}
       		else
       		{
				$data['product_id'] =  $product_id;
				$product_info_result = $this->common->getData('product_tbl',array('product_id'=>$product_id),array('single'));
				$data['seller_id'] =  $product_info_result['user_id'];
				$data['user_id'] =  $user_id;
				$data['quantity'] =  $quantity;
				$result = $this->common->insertData('cart_tbl',$data);
				$this->response(true,"Add Cart Successfully.");	
			}

			
		}
		else
		{
			$this->response(false,"Missing Parameter.");
		}
	}

	public function delete_cart_product()
	{
		if(!empty($_REQUEST['product_id']) && !empty($_REQUEST['user_id']) && !empty($_REQUEST['type']))
		{
			$product_id = $_REQUEST['product_id'];
			$user_id = $_REQUEST['user_id'];
			$type = $_REQUEST['type'];
			

			if($type == 1)
			{
				$quantity_delete = $_REQUEST['quantity'];
				$where="user_id = '".$user_id."' AND product_id = '".$product_id."' ";
       			$result = $this->common->getData('cart_tbl',$where,array('single'));

	       		if(!empty($result))
	       		{

	       			$old_quantity = $result['quantity'];

	       			if($quantity_delete == $old_quantity)
	       			{
						$where="user_id = '".$user_id."' AND product_id = '".$product_id."' ";
			            $this->common->deleteData('cart_tbl',$where);
			            $this->response(true,"Delete Product Successfully.");
			            exit();	
					}
	       			
	       			if($quantity_delete < $old_quantity)
	       			{
	       				$update_data['quantity'] =  $old_quantity - $quantity_delete;
	       				$update_cart = $this->common->updateData('cart_tbl',$update_data,array('user_id' => $user_id,'product_id' => $product_id));
	       				$this->response(true,"Update Cart Successfully.");	
	       				exit();	
	       			}
	       			else
	       			{
	       				$this->response(true,"Product Quantity is Low");	
	       			}
				}
				else
				{
					$this->response(true,"Product Not Found");
				}
			}
			else
			{
				$where="user_id = '".$user_id."' AND product_id = '".$product_id."' ";
	            $this->common->deleteData('cart_tbl',$where);
	            $this->response(true,"Delete Product Successfully.");
			}

		}
		else
		{
			$this->response(false,"Missing Parameter.");
		}	
	}



	public function update_cart_product()
	{
		if(!empty($_REQUEST['id']) && !empty($_REQUEST['type']))
		{
			$id = $_REQUEST['id'];
			$type = $_REQUEST['type'];
			

			if($type == 1)
			{
				$quantity = $_REQUEST['quantity'];
				$where="id = '".$id."'";
       			$result = $this->common->getData('cart_tbl',$where,array('single'));

	       		if(!empty($result))
	       		{
	       			$update_data['quantity'] =  $quantity;
	       			$update_cart = $this->common->updateData('cart_tbl',$update_data,array('id' => $id));
	       			$this->response(true,"Update Cart Successfully.");	
	       			exit();	
	       			
				}
				else
				{
					$this->response(true,"Product Not Found");
				}
			}
			else
			{
				$where="id = '".$id."' ";
	            $this->common->deleteData('cart_tbl',$where);
	            $this->response(true,"Delete Product Successfully.");
			}

		}
		else
		{
			$this->response(false,"Missing Parameter.");
		}	
	}


	function unique_sort($arrs, $id) 
	{
	    $unique_arr = array();
	    foreach ($arrs AS $arr) 
	    {
	    	if (!in_array($arr[$id], $unique_arr)) 
	    	{
	            $unique_arr[] = $arr[$id];
	        }
	    }
	    sort($unique_arr);
	    return $unique_arr;
	}


	public function add_order()
	{
		if(!empty($_REQUEST['cart_id']) && !empty($_REQUEST['address']))
		{
			$order_id = rand(1111,9999);
			$cart_id 	=	$_REQUEST['cart_id'];
			$address 	=	$_REQUEST['address'];
			$today = Date('Y-m-d H:i:s');
			$cart_array = explode(",",$cart_id);
			foreach ($cart_array as $key => $value) 
			{
				$product_price = $this->common->get_record_join_two_table('cart_tbl','product_tbl','product_id','product_id','product_tbl.price as product_price,product_tbl.	product_id,cart_tbl.quantity',array('cart_tbl.id' => $value));

				$product_general_info = $this->common->getData('product_tbl',array('product_id' => $product_price[0]->product_id),array('single'));

				if(!empty($product_general_info))
				{
					$product_general_info['quantity'];
					if($product_price[0]->quantity <= $product_general_info['quantity'])
					{
							$main_quantity = $product_general_info['quantity']-$product_price[0]->quantity;
							$data_update_product['quantity']=$main_quantity;
        					$result = $this->common->updateData('product_tbl',$data_update_product,array('product_id' => $product_price[0]->product_id));
        					
					}
					else
					{
						$this->response(false,"Quantity is low");
						die();
					}

				}
				else
				{
					$this->response(false,"Product not found");
					die();
				}

				$product_main_price = $product_price[0]->product_price;

				$data_update['price'] = $product_main_price;
				$data_update['order_id'] = $order_id;
				$data_update['paid_status'] = 1;
				$result = $this->common->updateData('cart_tbl',$data_update,array('id' => $value));
			}

			$insert = $this->common->insertData('order_tbl',array('order_id' => $order_id,'address' => $address,'created_at' => $today)); 
			$order_info = $this->product_info($cart_id);
		
			$order_info_send =$order_info;
			unset($order_info_send['main_total_amount']);
			
			$i = 1;
			$seller_id_invoice = '';

			$sort_arr = $this->unique_sort($order_info_send, 'seller_id');

		

			foreach($sort_arr as $sort_value)
        	{
        		$seller_key = 'seller'.$i;
				foreach($order_info_send as $value)
	        	{

	        		if($value['seller_id'] == $sort_value)
	        		{
	        			$seller[$seller_key][] = array('seller_id'=>$value['seller_id'],'product_name'=>$value['product_name'],'price'=>$value['price'],'order_quantity'=>$value['order_quantity'],'total_price'=>$value['total_price']);
	        		}
	        	}
	        	$i++;
			}



		
			
			
			

			
        	foreach($seller as $saller_arr)
        	{
        		

				$message_row="";
				$total_price_mail = 0;
				foreach($saller_arr as $saller_value)
        		{
        			
        				$message_row.= '<tr>
            <td style="background: #eef4f9; width: 150px; padding: 10px;">'.$saller_value['product_name'].'</td>
            <td style="background: #eef4f9; width: 150px; padding: 10px;">'.$saller_value['order_quantity'].'</td>
            <td style="background: #eef4f9; width: 150px; padding: 10px;">'.$saller_value['price'].'</td>
            <td style="background: #eef4f9; width: 150px; padding: 10px; text-align: right;">'.$saller_value['total_price'].'</td>
          </tr>';
          		$total_price_mail += $saller_value['total_price'];
          		$seller_id_value = $saller_value['seller_id'];

        		}

        		 $message_mail ='
<html lang="en">
<head>
</head>
<body style="height: 668px; background: #fff; display: flex; align-items: center;">
      <div style="background: #fff; margin: 0 auto; width: 500px; padding: 15px; border-radius: 5px; box-shadow: 0px 0px 20px #eaeaea;">
        <div style="text-align: center; background: #231e20; padding:10px 20px;">
            <div>
              <img src="'.base_url('assets/logo.png').'" style="width: 70px;">
            </div>  
            <h2 style="margin-top: 10px; margin-bottom: 0px; font-family: arial; font-weight: 100; color:#fff;">Inovice from wholesaler</h2>
            <p style="color:#fff;">Invoice #'.$order_id.'</p>
        </div>
        <table>
          <tr style="text-align: left;">
            <td style="background: #eef4f9; width: 150px; padding: 10px;">Product Name</th>
            <td style="background: #eef4f9; width: 150px; padding: 10px;">Quantity</th>
            <td style="background: #eef4f9; width: 150px; padding: 10px; text-align: right;">Price</th>
            <td style="background: #eef4f9; width: 150px; padding: 10px; text-align: right;">Total Price</th>
          </tr>
        
         
          	'.$message_row.'
         <tr>
            <th colspan="3" style="background: #eef4f9; width: 150px; padding: 10px;">Grand Total</th>
            <th style="background: #eef4f9; width: 150px; padding: 10px; text-align: right;">'.$total_price_mail.'</th>
          </tr>
        </table>
         <div style="text-align: center; background: #0d9342; padding: 5px 20px;">
            <h2 style="margin-top: 10px; margin-bottom: 0px; font-family: arial; font-weight: 100; color:#fff;">Inovice from wholesaler</h2>
            <p style="color:#fff;">Invoice #'.$order_id.'</p>
        </div>
      </div>

    
</body>
</html>';
				

				$where_seller_info="id	='" . $seller_id_value . "'";
				$result_seller_info = $this->common->getData('user',$where_seller_info,array('single'));

				


					 // Always set content-type when sending HTML email
		



					$headers = "MIME-Version: 1.0" . "\r\n";
					$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

					// More headers
					$headers .= 'From: <info@creativethoughtsinfo.com>' . "\r\n";
					mail($result_seller_info['email'],'Invoce order',$message_mail,$headers);
        	}
        		

        	
			
			$order_info['order_id'] = $order_id;
			$this->response(true,"Order create successfully",array("order_info" => $order_info));
		}
		else
		{
			$this->response(false,"Missing Parameter.");
		}

	}


	public function create_order()
	{
		$productJson 	 	=	$_REQUEST['product'];
		$product_info 		= 	json_decode($productJson);

		if(!empty($product_info))
		{											
			for($i=0;$i<count($product_info);$i++)
			{
				$product_info_data[] = array('product_id' => $product_info[$i]->product_id,'quantity' => $product_info[$i]->quantity,'seller_id' => $product_info[$i]->seller_id,'price' => $product_info[$i]->price,'user_id' => $product_info[$i]->user_id,'address' => $product_info[$i]->address);

				$this->response(true,"Order create successfully",array("order_info" => array()));
			}
		}
		else
		{
			$product_info_data 		= 	array();
		
		}

		$today = Date('Y-m-d H:i:s'); 

		$order_id =rand(1111,9999);

		if(!empty($product_info_data))
		{
			foreach ($product_info_data as $value) 
				{

					$order_product_id = $value['product_id'];
					$order_quantity = $value['quantity'];
					


					$insert = $this->common->insertData('order_tbl',array('order_id' => $order_id,'product_id' => $value['product_id'],'quantity' =>$value['quantity'],'seller_id' => $value['seller_id'],'price' => $value['price'],'user_id' => $value['user_id'],'address' => $value['address'],'created_at' => $today,'status' => 1)); 

				}


				$order_info  = $this->show_order_info($order_id);

				$this->response(true,"Order create successfully",array("order_info" => $order_info));
		}

		else
		{
			$this->response(false,"Missing Parameter.");
		}
	}
	
	public function show_order_detail()
	{

		if(!empty($_REQUEST['id']))
		{
			$id = $_REQUEST['id'];
			$where = "id = '" .$id."'";
			$result = $this->common->getData('cart_tbl',$where,array('single'));
			$main_total_amount = 0;
			if(!empty($result))
			{
				$product_id	= $result['product_id'];
				$quantity	= $result['quantity'];
				$seller_id	= $result['seller_id'];
				$price	= $result['price'];

				$result_product = $this->common->getData('product_tbl',array('product_id' => $product_id ),array('single'));
				$result_seller = $this->common->getData('user',array('id' => $seller_id ),array('single'));
				$total_price = $price * $quantity;

				$result_order = $this->common->getData('order_tbl',array('order_id' => $result['order_id'] ),array('single'));
				$total_price = $price * $quantity;

				$product_info_payment  = array('message'=>'order info','order_id' => $result['order_id'],'product_id' => $product_id,'product_name' => $result_product['product_name'],'seller_id' => $seller_id,'price' => $price,'order_quantity' => $quantity,'total_price' => $total_price,'address'=>$result_order['address']);

				
				
				$this->response(true,"order  fetch Successfully.",array("order_info" => $product_info_payment));
			}
			else
			{
				$this->response(true,"No Order Found",array("order_info" =>array()));
			}
			
		}
		else
		{
			$this->response(false,"Missing Parameter.");
		}

	}



	public function show_order_info($order_id)
	{

		
		
			$where = "order_id = '" .$order_id."'";
			$result = $this->common->getData('order_tbl',$where);
			$main_total_amount = 0;
			if(!empty($result))
			{
				foreach ($result as $value) 
				{
						$product_id	= $value['product_id'];
						$seller_id	= $value['seller_id'];
						$result_product = $this->common->getData('product_tbl',array('product_id' => $product_id ),array('single'));
						$result_seller = $this->common->getData('user',array('id' => $seller_id ),array('single'));


						$price = $value['price'];					
						$total_price = $value['price'] * $value['quantity'];

						$product_info_payment[] = array('message'=>'Product Info','product_id' => $value['product_id'],'product_name' => $result_product['product_name'],'seller_id' => $seller_id,'price' => $price,'order_quantity' => $value['quantity'],'total_price' => $total_price,'address'=>$value['address'],'status' => 1);

						$main_total_amount += $total_price;
						
				}

				$product_info_payment['main_total_amount'] = $main_total_amount;
				return  $product_info_payment;
				
			}
			else
			{
				return FALSE;
			}
			
		

	}

	public function update_order_status()
	{
		if(!empty($_REQUEST['id']) && !empty($_REQUEST['date']))
		{
			$data['delivery_status']=1;
			$data['delivery_date']=$_REQUEST['date'];
			$result = $this->common->updateData('cart_tbl',$data,array('id' => $_REQUEST['id']));
			$this->response(true,"Update successfully");
		}
		else
		{
			$this->response(false,"Missing Parameter.");
		}
	}

	public function order_product_list()
	{
		if(!empty($_REQUEST['user_id']) && !empty($_REQUEST['type']))
		{
			$user_id = $_REQUEST['user_id'];
			$type = $_REQUEST['type'];
			if($type == 1)
			{
				$result = $this->common->getData('cart_tbl',array('user_id' => $user_id,'paid_status'=>1));
			}
			if($type == 2)
			{
				$result = $this->common->getData('cart_tbl',array('seller_id' => $user_id,'paid_status'=>1));
			}



			

			if(!empty($result))
			{
				foreach ($result as $key => $value)
        		{ 			
        			$order_product_id = $value['product_id'];
					
					$order_quantity = $value['quantity'];
				
					$result_product = $this->common->getData('product_tbl',array('product_id' => $order_product_id ),array('single'));

					$product_image = $this->common->getData('product_image',array('product_id' => $order_product_id),array('single'));
	        		if(!empty($product_image))
	        		{
	        			$product_image = base_url('/assets/product/'.$product_image['image_name']);

	        		}
	        		else
	        		{
	        			$product_image = "";
	        		}

	        		$result_user = $this->common->getData('user',array('id' => $value['user_id'] ),array('single'));

	        		$result_seller = $this->common->getData('user',array('id' => $value['seller_id'] ),array('single'));

	        		


					$price = $value['price'];					
					$total_price = $value['price'] * $order_quantity;	

					$product_info_payment[] = array('id'=>$value['id'],'order id'=>$value['order_id'],'product_id' => $order_product_id,'product_name' => $result_product['product_name'],'seller_id' => $value['seller_id'],'seller_name'=>$result_seller['name'],'user_id' => $value['user_id'],'user_name'=>$result_user['name'],'price' => $value['price'],'order_quantity' => $order_quantity,'total_price' => $total_price,'status' => 1,'delivery_status'=>$value['delivery_status'],'product_image'=>$product_image);

        		}

        		$this->response(true,"order fetch Successfully.",array("order_list" => $product_info_payment));
				
			}
			else
			{
				$this->response(true,"order not Found",array("order_list" =>array()));
			}
				
				

		}
		else
		{
			$this->response(false,"Missing Parameter.");
		}
	}




	public function get_cart()
	{
		if(!empty($_REQUEST['user_id']))
		{
			$user_id = $_REQUEST['user_id'];
			$where = "user_id = '" .$user_id."' AND  paid_status = 0";
			$result = $this->common->getData('cart_tbl',$where);
			if(!empty($result))
			{

				foreach ($result as $value) 
				{
					$order_product_id = $value['product_id'];
					$order_quantity = $value['quantity'];


					$product_image = $this->common->getData('product_image',array('product_id' => $order_product_id),array('single'));
	        		if(!empty($product_image))
	        		{
	        			$image_name = base_url('/assets/product/'.$product_image['image_name']);

	        		}
	        		else
	        		{
	        			$image_name = "";
	        		}


				
					$result_product = $this->common->getData('product_tbl',array('product_id' => $order_product_id ),array('single'));


					if(empty($result_product))
					{
						$product_info[] = array('message'=> "Product are not available",'product_id' => $order_product_id,'status' => 2);
					}

					
					else if(empty($result_product['quantity']))
					{
						$product_info[] = array('message'=> "Out Of Stock",'product_id' => $order_product_id,'product_name' => $result_product['product_name'],'image' => $image_name,'status' => 2);
					}
					else if($result_product['quantity'] < $order_quantity)
					{
						$msg = "Quantity Availabe Minimum ".$result_product['quantity'];
						$product_info[] = array('message'=> $msg,'product_id' => $order_product_id,'product_name' => $result_product['product_name'],'image' => $image_name,'status' => 2);
					}
					else if($result_product['minimum_quantity'] > $order_quantity)
					{
						
						$msg = "Minimum Quantity ".$result_product['minimum_quantity'];
						$product_info[] = array('message'=> $msg,'product_id' => $order_product_id,'product_name' => $result_product['product_name'],'image' => $image_name,'status' => 2);
					}
					else
					{
						$price = $result_product['price'];					
					
						$product_info[] = array('id'=>$value['id'],'product_id' => $order_product_id,'product_name' => $result_product['product_name'],'price' => $result_product['price'],'order_quantity' => $order_quantity,'image' => $image_name,'status' => 1);
					}

				}

					$this->response(true,"Cart info fetch Successfully.",array("cart_info" => $product_info));
			}
			else
			{
				$this->response(true,"No Product Found",array("cart_info" =>array()));
			}

		}
		else
		{
			$this->response(false,"Missing Parameter.");
		}
	}








	public function purchase()
	{
		$cart_id 	=	$_REQUEST['cart_id'];
		$cart_array = explode(",",$cart_id);

		foreach ($cart_array as $value) 
		{
			$result_product = $this->common->getData('cart_tbl',array('id' => $value ),array('single'));

			$product_array[] = array('id'=>$value,'product_id'=>$result_product['product_id'],'quantity'=>$result_product['quantity']);

		}

		
		$main_total_amount = 0;

		if(!empty($product_array))
		{
				foreach ($product_array as $value) 
				{
					 $order_product_id = $value['product_id'];
					
					$order_quantity = $value['quantity'];
				
					$result_product = $this->common->getData('product_tbl',array('product_id' => $order_product_id ),array('single'));

					if(empty($result_product))
					{
						$error_info = array('product_id' => $order_product_id,'status' => 2);
					
						$this->response(true,"Product are not available",array("product_info" => $error_info));
						exit();
					}
					
					if(empty($result_product['quantity']))
					{
						$error_info = array('product_id' => $order_product_id,'product_name' => $result_product['product_name'],'status' => 2);
					
						$this->response(true,"Out Of Stock",array("product_info" => $error_info));
						exit();
					}
				
					 if($result_product['quantity'] < $order_quantity)
					{
						$msg = "Quantity Availabe Minimum ".$result_product['quantity'];
						$error_info = array('product_id' => $order_product_id,'product_name' => $result_product['product_name'],'status' => 2);
						
						$this->response(true,$msg,array("product_info" => $error_info));
						exit();
					}
					
					


					if($result_product['minimum_quantity'] > $order_quantity)
					{
						
						$msg = "Minimum Quantity ".$result_product['minimum_quantity'];
						$error_info = array('product_id' => $order_product_id,'product_name' => $result_product['product_name'],'status' => 2);

						$this->response(false,$msg,array("product_info" => $error_info));
						exit();
					}
					
						$price = $result_product['price'];					
						$total_price = $result_product['price'] * $order_quantity;

						$product_info_payment[] = array(
							'id'=>$value['id'],
							'product_id' => $order_product_id,
							'product_name' => $result_product['product_name'],
							'seller_id' => $result_product['user_id'],
							'price' => $result_product['price'],
							'order_quantity' => $order_quantity,
							'total_price' => $total_price,'status' => 1);

					$main_total_amount += $total_price;

				}
					
				// $product_info_payment['main_total_amount'] = $main_total_amount;
				$this->response(true,"Product found",array("product_list" => $product_info_payment,"main_total_amount"=>$main_total_amount));
		}
		else
		{
			$this->response(false,"Missing Parameter.");
		}
		

	
	}



	public function product_info($cart_id)
	{
		
		$cart_array = explode(",",$cart_id);

		foreach ($cart_array as $value) 
		{
			$result_product = $this->common->getData('cart_tbl',array('id' => $value ),array('single'));

			$product_array[] = array('product_id'=>$result_product['product_id'],'quantity'=>$result_product['quantity']);

		}
		
		
		$main_total_amount = 0;

		if(!empty($product_array))
		{
				foreach ($product_array as $value) 
				{
					$order_product_id = $value['product_id'];
					
					$order_quantity = $value['quantity'];
				
					$result_product = $this->common->getData('product_tbl',array('product_id' => $order_product_id ),array('single'));

					if(empty($result_product))
					{
						$error_info = array('product_id' => $order_product_id,'status' => 2);
					
						$this->response(true,"Product are not available",array("product_info" => $error_info));
						exit();
					}
					
					if(empty($result_product['quantity']))
					{
						$error_info = array('product_id' => $order_product_id,'product_name' => $result_product['product_name'],'status' => 2);
					
						$this->response(true,"Out Of Stock",array("product_info" => $error_info));
						exit();
					}
				
					 if($result_product['quantity'] < $order_quantity)
					{
						$msg = "Quantity Availabe Minimum ".$result_product['quantity'];
						$error_info = array('product_id' => $order_product_id,'product_name' => $result_product['product_name'],'status' => 2);
						
						$this->response(true,$msg,array("product_info" => $error_info));
						exit();
					}
					
					


					if($result_product['minimum_quantity'] > $order_quantity)
					{
						
						$msg = "Minimum Quantity ".$result_product['minimum_quantity'];
						$error_info = array('product_id' => $order_product_id,'product_name' => $result_product['product_name'],'status' => 2);

						$this->response(false,$msg,array("product_info" => $error_info));
						exit();
					}
					
						$price = $result_product['price'];					
						$total_price = $result_product['price'] * $order_quantity;

						$product_info_payment[] = array('message'=>'Product Info','product_id' => $order_product_id,'product_name' => $result_product['product_name'],'seller_id' => $result_product['user_id'],'price' => $result_product['price'],'order_quantity' => $order_quantity,'total_price' => $total_price,'status' => 1);

					$main_total_amount += $total_price;

				}
					
				$product_info_payment['main_total_amount'] = $main_total_amount;
				return $product_info_payment;
		}
		else
		{
			return false;
		}
		

	
	}




	public function category_by_wholesaler()
	{
		if(!empty($_REQUEST['id']))
		{
			
		$id = $_REQUEST['id'];
		$where="id = '" .$id. "'";
        $result = $this->common->getData('user',$where,array('single'));
        $category = $result['category'];

        if($category != "")
			{
				$category  = explode(",",$category);

				foreach($category as $cat)
				{
					$cat_info = $this->common->getData('category_tbl',array('category_id' => $cat),array('single')); 
					if(!empty($cat_info['category_image']))
					{
						 $cat_info['category_image'] = base_url('/assets/category/'.$cat_info['category_image']);
					}
					else
					{
						$cat_info['category_image'] = "";
					}


					


					$count = $this->common->getData('product_tbl',array('category' => $cat_info['category_id'],'user_id' => $id),array('count'));
					
					$category_data[] = array('category_id'=>$cat_info['category_id'],'category_name'=>$cat_info['category_name'],'category_image'=>$cat_info['category_image'],'count'=>$count);
				}

			
			}
			else
			{
				$category_data ="";
			}



        if(!empty($category_data)){
			$this->response(true,"Category fetch Successfully.",array("category_list" => $category_data));			
		}else{
			$this->response(true,"Category not found",array("category_list" => array()));
		}
		
		}
		else
		{
			$this->response(false,"Missing Parameter.");
		}

	}



	public function get_wholesaler_product()
	{
		if(!empty($_REQUEST['category_id']))
		{
			if(!empty($_REQUEST['id']))
			{
				$id = $_REQUEST['id'];
				$category_id = $_REQUEST['category_id'];

				$where="category = '" .$category_id."' AND user_id = '" .$id."'";
		        $result = $this->common->getData('product_tbl',$where);
			}
			else
			{
				$category_id = $_REQUEST['category_id'];

				$where="category = '" .$category_id."'";
		        $result = $this->common->getData('product_tbl',$where);
			}
			
			
	        


	        if(!empty($result)){

	        	foreach ($result as $value) {
	        		$product_id = $value['product_id'];
	        		$product_name = $value['product_name'];
	        		$price = $value['price'];
	        		$description = $value['description'];
	        		$quantity = $value['quantity'];

	        		$avg_rating = $this->rating_count_product($product_id);

	        		$product_image = $this->common->getData('product_image',array('product_id' => $product_id),array('single'));
	        		if(!empty($product_image))
	        		{
	        			$image_name = base_url('/assets/product/'.$product_image['image_name']);

	        		}
	        		else
	        		{
	        			$image_name = "";
	        		}

	        		$array_prduct[] = array('product_id'=>$product_id,'product_name'=>$product_name,'price'=>$price,'description'=>$description,'quantity'=>$quantity,'main_image'=>$image_name,'rating'=>$avg_rating);
	        	}

				$this->response(true,"Product fetch Successfully.",array("product_list" => $array_prduct));			
			}else{
				$this->response(true,"Product not found",array("product_list" => array()));
			}
			
		}
		else
		{
			$this->response(false,"Missing Parameter.");
		}

	}





	public function signup()
	{	
		if(!empty($_REQUEST['user_type']) && !empty($_REQUEST['name']) && !empty($_REQUEST['email']))
	    {

	    	$user_type = $_REQUEST['user_type'];

			if($_POST['email'] == ""){
				$exist = $this->common->getData('user',array('email' => $_POST['mobile']),array('single'));
				$_POST['otp'] = '1234';
			}else{
				$exist = $this->common->getData('user',array('email' => $_POST['email']),array('single'));
				$_POST['otp'] = str_pad(rand(0, pow(10, 4)-1), 4, '0', STR_PAD_LEFT);
			}
			if(!empty($exist)){
				$response = $this->response(false,"This email or mobile number already exists.",array("userinfo" => ""));				
			}
			else
			{
				$iname = '';
				if(!empty($_FILES['image']['name'])){
					$image = $this->common->do_upload('image','./assets/userfile/profile/');
					if(isset($image['upload_data'])){
						$iname = $image['upload_data']['file_name'];
					}
				}			
				$_POST['image'] = $iname;
				$_POST['password'] = md5($_POST['password']);
				$_POST['created_at'] = date('Y-m-d H:i:s');
							
				$old_device = $old_ios = false;
				if(isset($_POST['android_token'])){
					$old_device = $this->common->getData('user',array('android_token' => $_POST['android_token']),array('single','field'=>'id'));
				}
				if(isset($_POST['ios_token'])){
					$old_ios =  $this->common->getData('user',array('ios_token' => $_POST['ios_token']),array('single','field'=>'id'));
				}
				if($old_device || $old_ios){
					$this->common->updateData('user',array('android_token' => "", "ios_token" => ""),array('id' => $old_device['id']));
				}
				$post = $this->common->getField('user',$_POST); 
				
				$result = $this->common->insertData('user',$post);
				if($result)
				{
					$userid = $this->db->insert_id();


					$user_type  =  $_REQUEST['user_type'];
			    	if($user_type == 2 || $user_type == 3)
			    	{
						$data['user_id'] =$userid;
								
						$result_other = $this->common->insertData('other_user_info',$data);

						$other_info = $this->common->getData('other_user_info',array('user_id' => $userid),array('single'));	
											
					}


				$info = $this->common->get_user_info(array('U.id' => $userid),array('single'));



				if(!empty($info['image']))
					{
						$info['image'] = base_url('/assets/userfile/profile/'.$info['image']);
					}
					else
					{
						$info['image'] = "";
					}

				if($_POST['email'] != ""){
					$template = $this->load->view('template/verify-email',array('email' => $_POST['email'],'otp' => $_POST['otp'],'name' => $_POST['name']),true);
						
			
				

					// $headers = "MIME-Version: 1.0" . "\r\n";
					// $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

					// // More headers
					// $headers .= 'From: <info@creativethoughtsinfo.com>' . "\r\n";
					

					// mail($_POST['email'],'Signup',$template,$headers);


						
						
				}

				if(!empty($other_info))
				{
					
					if(!empty($other_info['user_video']))
					{
						$info['user_video'] = base_url('/assets/userfile/profile/'.$other_info['user_video']);
					}
					else
					{
						$info['user_video'] = "";
					}

					$info['get_percent'] =  $other_info['get_percent'];
					$info['min_price'] =  $other_info['min_price'];
					$info['max_price'] =  $other_info['max_price'];
					$info['expected_delivery_date'] =  $other_info['expected_delivery_date'];
					$info['dropship'] =  $other_info['dropship'];
					$info['category_info'] = array();
				}
					
				$this->response(true,"Your registration successfully completed.",array("userinfo" => $info));					
				}else{
					$this->response(false,"There is a problem, please try again.",array("userinfo" => ""));
				}
			}

		}
		else
        {
        	$this->response(false,"Missing Parameter.");
        }
	}



	public function updateProfile(){
		chmod('./assets/userfile/profile/',0777);
			$id = $_POST['id']; unset($_POST['id']);
		if(!empty($_REQUEST['user_type']) && !empty($_REQUEST['id']))
    	{
    		$user_type  =  $_REQUEST['user_type'];
    		if($user_type == 2 || $user_type == 3)
    		{

		    		if(!empty($_FILES['user_video']['name'])){
					$user_video = $this->common->do_upload_file('user_video','./assets/userfile/profile/');
						if(isset($user_video['upload_data'])){
						$_POST['user_video'] = $user_video['upload_data']['file_name'];
						}
					}
		


				$post_other = $this->common->getField('other_user_info',$_POST);
				
				if(!empty($post_other))
				{		

					$result_other = $this->common->updateData('other_user_info',$post_other,array('user_id' => $id));					
				}
				else
				{
					$result_other = "";
				}
    		}
    
			
		if(!empty($_FILES['image']['name'])){

			$image = $this->common->do_upload('image','./assets/userfile/profile/');
			$_POST['image'] = $image['upload_data']['file_name'];
			$old_image = $this->common->getData('user',array('id'=>$id),array('single','field'=>'image'));
			if(file_exists('./assets/userfile/profile/'.$old_image['image'])){ 
				unlink('./assets/userfile/profile/'.$old_image['image']);

			}
		}	



		$post = $this->common->getField('user',$_POST);
	
		if(!empty($post))
		{		
			$result = $this->common->updateData('user',$post,array('id' => $id)); 
		}
		else
		{
			$result = "";
		}
		
		if($result){
			$user = $this->common->get_user_info(array('U.id' => $id),array('single'));

			if(!empty($user['image']))
			{
				$user['image'] = base_url('/assets/userfile/profile/'.$user['image']);
			}
			else
			{
				$user['image'] = "";
			}

				
			if(!empty($user['category']))
			{
				$category = $user['category'];
				$category  = explode(",",$category);

				foreach($category as $cat)
				{
					$cat_info = $this->common->getData('category_tbl',array('category_id' => $cat),array('single')); 
					$category_data[] = array('category_id'=>$cat_info['category_id'],'category_name'=>$cat_info['category_name'],'category_image'=>$cat_info['category_image']);
				}

			
			}
			else
			{
				$category_data =array();
			}

			
			$user['category_info'] = $category_data;

			$other_info = $this->common->getData('other_user_info',array('user_id' => $id),array('single'));	
			if(!empty($other_info))
				{
					

					if(!empty($other_info['user_video']))
					{
						$user['user_video'] = base_url('/assets/userfile/profile/'.$other_info['user_video']);
					}
					else
					{
						$user['user_video'] = "";
					}

					$user['get_percent'] =  $other_info['get_percent'];
					$user['min_price'] =  $other_info['min_price'];
					$user['max_price'] =  $other_info['max_price'];
					$user['expected_delivery_date'] =  $other_info['expected_delivery_date'];
					$user['dropship'] =  $other_info['dropship'];

				}
			$this->response(true,"Profile Update Successfully.",array("userinfo" => $user));
		}else{
			$this->response(false,"There is a problem, please try again.",array("userinfo" => ""));
		}
	}
	else
		{
				$this->response(false,'Missing parameter');
		}
	}

	public function mailcheck()
	{
		$template = $this->load->view('template/verify-email',array('email' => 'devendra@mailinator.com','otp' => '1258','name' => 'Devendra'),true);
		$r = $this->common->sendMail('devendra@mailinator.com','verify mail',$template);
		if($r){
			echo "send";
		}else{
			echo "not send";
		}
	}

		public function change_password()
		{
			if(!empty($_REQUEST['user_id']) && !empty($_REQUEST['old_password']) && !empty($_REQUEST['new_password']))
    		{
    			$user_id = $_REQUEST['user_id'];
    			$old_password = $_REQUEST['old_password'];
    			$new_password = $_REQUEST['new_password'];

    			$user_info = $this->common->getData('user',array('id' => $user_id),array('single'));
    			$old_user_password = $user_info['password'];
    			$old_password = md5($old_password);
    			if ($old_password == $old_user_password) 
				{
    				$data['password'] = md5($new_password);
    				$result = $this->common->updateData('user',$data,array('id' => $user_id));
    				$this->response(true,'Password changed successfully');
    			} 
    			else 
    			{
					$this->response(false,'Invalid old password');
					exit();
				}
    		}
			else
			{
				$this->response(false,'Missing parameter');
			}
		}


		public function forgot_passowrd()
		{
			if(!empty($_REQUEST['email']))
			{
				$email = $_REQUEST['email'];
				$record = $this->common->getData('user',array('email' => $email),array('single'));
				if(!empty($record)){
						$id = $record['id'];
						$password = rand(111111111,999999999);
						$new_password = md5($password);
						$data['password'] = $new_password;
    					$result = $this->common->updateData('user',$data,array('id' => $id));
    					
    					$template = $this->load->view('template/forgot_password',array('email' => $email,'password' =>$password),true);
						
						$headers = "MIME-Version: 1.0" . "\r\n";
						$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
						// More headers
						$headers .= 'From: <info@creativethoughtsinfo.com>' . "\r\n";
						mail($_POST['email'],'Forgot Password',$template,$headers);
						$this->response(true,'New password sends to your registered email');
				}
				else
				{
					$this->response(false,'Invalid or non-mobile email');
				}
			}
			else
			{
				$this->response(false,'Missing parameter');
			}

		}


	public function joinevent($event_id,$user_id)
	{
		
        	$user_id1 =$user_id;
        	$event_count = $this->common->getData('sport_event',array('id' => $event_id),array('single'));

        	$user_event_count = $this->common->getData('user',array('id' => $user_id),array('single'));

        	$join_user_event = $user_event_count['user_event'];			
        	$join_user = $event_count['join_user'];
        	$event_participant_no = $event_count['event_participant_no'];
        	$arr=(explode(",",$join_user));
        	
        	
        	
        	if (in_array($user_id,$arr))
        	{
        		$Join_data = array('status'=>false,'message'=>"particepent already added");
        		return  $Join_data;
        	}
        	
			if(count($arr) >= $event_participant_no)
        	{
   				$Join_data = array('status'=>false,'message'=>"Particepent have not added because does not have any place available");
        		return  $Join_data;
			}
        	

    		if(empty($join_user))
        	{
        		$user_id;
        	}
        	else
        	{
        		$user_id = $join_user.','.$user_id;
        	}

        	if(empty($join_user_event))
        	{
        		$user_event_id = $event_id;
        	}
        	else
        	{
        		$user_event_id = $join_user_event.','.$event_id;
        	}

        	$arr_image=(explode(",",$user_id));
        	if(!empty($arr_image))
        	{
        		foreach($arr_image as $value)
        		{

        			$user_image = $this->common->getData('user',array('id' => $value),array('single'));
        			if(!empty($user_image['image']))
        			{
        				$image_user[]=base_url('/assets/userfile/profile/'.$user_image['image']);
        			 
        			}
        			else
        			{
        				$image_user[]="";
        			}
        		}
        	}
        	


        	$data['join_user']=$user_id;
        	$user_info['user_event']=$user_event_id;
        	
        	$result = $this->common->updateData('sport_event',$data,array('id' => $event_id));
        	$result = $this->common->updateData('user',$user_info,array('id' => $user_id1));

        	
        	if($result){
			$event = $this->common->getData('sport_event',array('id' => $event_id),array('single'));
			$event['image_user']=$image_user;
			$notification_id=$event['user_id'];


			

			
			$Join_data = array('status'=>true,'message'=>"Join Event Successfully.",'eventinfo' => $event);
        		return  $Join_data;
			}else{
			
				$Join_data = array('status'=>false,'message'=>"There is a problem, please try again.",'eventinfo' => "");
        		return  $Join_data;
			}
	}

	public function pending_user()
	{
		if(!empty($_REQUEST['event_id']))
        {
        	$event_id = $_REQUEST['event_id'];
        	$event = $this->common->getData('join_event_tbl',array('event_id' => $event_id),array('single'));

        	$where = "JE.event_id = '".$event_id."'";
        	$user_info = $this->common->get_join_user($where);

        	if($user_info){
				$this->response(true,"Profile fetch Successfully.",array("userinfo" => $user_info));			
			}else{
				$this->response(false,"There is a problem, please try again.",array("userinfo" => ""));
			}		


        }
        else
        {
        	$this->response(false,"Missing Parameter.");
        }
	}



	public function joinevent_user()
	{

		
		if(!empty($_REQUEST['event_id']) && !empty($_REQUEST['user_id']))
        {
        	$event_id=$_REQUEST['event_id'];
			$user_id=$_REQUEST['user_id'];
        	
        	$event_count = $this->common->getData('sport_event',array('id' => $event_id),array('single'));

        	$user_event_count = $this->common->getData('user',array('id' => $user_id),array('single'));

        	$join_user_event = $user_event_count['user_event'];			
        	 $join_user = $event_count['join_user'];
        	 $event_participant_no = $event_count['event_participant_no'];
        	 $event_user_id = $event_count['user_id'];

        	$arr=(explode(",",$join_user));

        	if (in_array($user_id,$arr))
        	{
        		$this->response(false,"particepent already added");
        		exit();
        	}
        	

        	

        	if(count($arr) >= $event_participant_no)
        	{
   				$this->response(false,"Particepent have not added because does not have any place available");
		        exit();
        	}


			$where="user_id	='" .  $event_user_id . "' AND join_id ='" . $user_id . "' AND event_id ='" . $event_id . "' ";
		    $value = $this->common->getData('join_event_tbl',$where,array('single'));
						;
						
		if(empty($value))
		{	
			$insert = $this->common->insertData('join_event_tbl',array('user_id' => $event_user_id,'join_id' => $user_id,'event_id' =>$event_id));

			$user_data = $this->common->getData('user',array('id'=>$user_id),array('single'));
			$name = $user_data['name'];
			$today = Date('Y-m-d H:i:s'); 

			$get_user_data = $this->common->getData('user',array('id'=>$event_user_id),array('single'));
			
			$ios_token = $get_user_data['ios_token'];

			$insert_notification = $this->common->insertData('notification_tbl',array('user_id' => $event_user_id,'message' => "Your Event",'date'=>$today,'user_send_from'=>$user_id,'type'=>"Join"));

			$notification = array('user_id' => $event_user_id,'message' => "wants to join",'date'=>$today,'user_send_from'=>$user_id,'type'=>"Event");
			$sendmsg = "join event";

			// if($user['android_token'] != ""){
			// 		 $isSend = $this->push_iOS($ios_token,$notification,$sendmsg);

			// }
			// else
			// {
			// 	$registatoin_id = array($user["android_token"]); 
			// 	$this->send_notification($registatoin_id, $messages_push);

			// }
		
							
	
						
			$this->response(true,"join user request send successfully");
							
		}
		else
		{
			$this->response(false,"join user request already added");
		}
        	

        	
        	
        }
        else
        {
        	$this->response(false,"Missing Parameter.");
        }
	}




	 // For IOS notification
      function push_iOS($token, $msg, $alert) {
        if (!empty($this->pem_Pro) && !empty($this->passPhrase)) {
            // Provide the Host Information.

            if (!empty($this->sandBox))
                $tHost = 'gateway.push.apple.com';
            else
                $tHost = 'gateway.sandbox.push.apple.com';

            $tPort = 2195;

            // Provide the Certificate and Key Data.
            
            // $counts = $this->get_record_where("user", array("user_device_token" => $token),"user_id,noti_count");
            // $data1=array("noti_count"=>$counts[0]['noti_count']+1);
            // $where=array("user_device_token" => $token);
            // $up_id = $this->update_records('user', $data1,$where);

            if (!empty($this->sandBox))
                $tCert = $this->pem_Pro;
            else
                $tCert = $this->pem_Dev;

            // Provide the Private Key Passphrase

            $tPassphrase = $this->passPhrase;

            // Provide the Device Identifier (Ensure that the Identifier does not have spaces in it).

            $tToken = $token;

            // The message that is to appear on the dialog.

            $tAlert = $alert;

            // The Badge Number for the Application Icon (integer >=0).
            //            $tBadge = 8;
            // Audible Notification Option.

            $tSound = 'default';

            // The content that is returned by the LiveCode "pushNotificationReceived" message.

            $tPayload = 'Notification sent';

            // Create the message content that is to be sent to the device.

            $tBody['aps'] = array(
                'alert' => $tAlert,
                'msg' => $msg,
              //  'badge' => intval($counts[0]['noti_count']+1),
                'sound' => $tSound,
            );

            $tBody ['payload'] = $tPayload;

            // Encode the body to JSON.

            $tBody = json_encode($tBody);

            // Create the Socket Stream.

            $tContext = stream_context_create();

            stream_context_set_option($tContext, 'ssl', 'local_cert', $tCert);

            // Remove this line if you would like to enter the Private Key Passphrase manually.

            stream_context_set_option($tContext, 'ssl', 'passphrase', $tPassphrase);

            // Open the Connection to the APNS Server.

            $tSocket = stream_socket_client('ssl://' . $tHost . ':' . $tPort, $error, $errstr, 30, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $tContext);

            // Check if we were able to open a socket.

            if (!$tSocket)
                exit("APNS Connection Failed: $error $errstr" . PHP_EOL);

            // Build the Binary Notification.

            $tMsg = chr(0) . chr(0) . chr(32) . pack('H*', $tToken) . pack('n', strlen($tBody)) . $tBody;

            // Send the Notification to the Server.

                $tResult = fwrite($tSocket, $tMsg, strlen($tMsg));

        //       if (!$result){
        // echo 'Message not delivered' . PHP_EOL;
        //  }
        
        // else{
        //      echo 'Message successfully delivered' . PHP_EOL;
        //  }


            // if ($tResult)
           //  echo 'Delivered Message to APNS' . PHP_EOL;
           // else
           // echo 'Could not Deliver Message to APNS' . PHP_EOL;
           // Close the Connection to the Server.

            fclose($tSocket);
        }
    }





	public function notification_list()
	{
		if(!empty($_REQUEST['user_id']))
        {
        	$user_id = $_REQUEST['user_id'];
        
        	$where = "NT.user_id = '".$user_id."'";

        	$user_info = $this->common->get_notification_user($where);

		   if(!empty($user_info))
            {
             	$i = 0;
        	foreach ($user_info as $key => $value)
        	{
        			
        			if(!empty($value['image']))
                 	{
                 		$image = base_url('/assets/userfile/profile/'.$value['image']);
                 	}
                 	else
                 	{
                 		$image = '';
                 	}


				$arr[]=array('Notification_id'=>$value['Notification_id'],'user_id'=>$value['id'],'name'=>$value['name'],'email'=>$value['email'],'image'=>$image,'message'=>$value['message'],'date'=>$value['date'],'type'=>$value['type'],);

				if($value['type'] == 1)
				{
					$where_friend = '(user_id = '.$value['id'].' AND friend_id	= '.$user_id.') OR (user_id = '.$user_id.' AND friend_id = '.$value['id'].')';
						
					$result_friend = $this->common->getData('friend_tbl',$where_friend,array('single'));


					if(!empty($result_friend))
					{
						if(($result_friend['friend_id'] == $user_id )&& ($result_friend['status'] ==0) )
						{
							$friend_status = 1;
						}

						if(($result_friend['friend_id'] == $value['id'] )&& ($result_friend['status'] ==0) )
						{
							$friend_status = 2;
						}

						if(($result_friend['status'] ==1))
						{
							$friend_status = 4;
						}

						$friend_id = $result_friend['id'];	
					}
					else
					{
						$friend_status = 3;
						$friend_id = "";	
					}

					
					$arr[$i]['friend_status'] = $friend_status;
					$arr[$i]['friend_id'] = $friend_id;
					
				}

				$i++;
			}

        	

        
        	$this->response(true,"Notification list.",array("notification_list" =>$arr));
            }
            else
            {
                $this->response(true,"No Record Found",array("notification_list" =>array()));
            }
        }
        else
        {
        	$this->response(false,"Missing Parameter.");
        }
	}

	public function searchUser()
	{
		if(!empty($_REQUEST['user_id']) && !empty($_REQUEST['user_name']))
        {
        	$user_id = $_REQUEST['user_id'];
        	$user_name = $_REQUEST['user_name'];
        	$where="id	!='" . $user_id . "' AND name ='" . $user_name . "' AND status = 0";
			$result = $this->common->getData('user',$where);
			if(!empty($result))
			{
				foreach ($result as $key => $value) {
					if(!empty($value['image']))
					{
						$image =  base_url('/assets/userfile/profile/'.$value['image']);
					}
					else
					{
						$image ="";
					}

						$age= date_diff(date_create($value['user_dob']), date_create('today'))->y;
					$arr[]=array('id'=>$value['id'],'name'=>$value['name'],'email'=>$value['email'],'user_dob'=>$value['user_dob'],'user_address'=>$value['user_address'],'image'=>$image,'Age'=>$age);

				}
				$this->response(true,"User Found Successfully.",array("userinfo" => $arr));
			}
			else
			{
				$this->response(false,"User Not Found");
			}		
        }
        else
        {
        	$this->response(false,"Missing Parameter.");
        }
	
	}


	public function create_event()
	{
		if(!empty($_REQUEST['game_id']) && !empty($_REQUEST['event_time']) && !empty($_REQUEST['event_duration']) && !empty($_REQUEST['event_participant_no']) && !empty($_REQUEST['event_description']) && !empty($_REQUEST['latitude']) && !empty($_REQUEST['longitude']) && !empty($_REQUEST['event_address']) && !empty($_REQUEST['user_id']) && !empty($_REQUEST['title']) )
		{

		$data['title'] = $_REQUEST['title'];
		$data['game_id'] = $_REQUEST['game_id'];
		$data['event_time'] = $_REQUEST['event_time'];
		$data['event_duration'] = $_REQUEST['event_duration'];
		$data['event_participant_no'] = $_REQUEST['event_participant_no'];
		$data['event_description'] = $_REQUEST['event_description'];
		$data['latitude'] = $_REQUEST['latitude'];
		$data['longitude'] = $_REQUEST['longitude'];
		$data['event_address'] = $_REQUEST['event_address'];
		$data['user_id'] = $_REQUEST['user_id'];
		$user_id = $_REQUEST['user_id'];
		$data['event_user_type'] = 2;


		$result = $this->common->insertData('sport_event',$data);
		$insert_id = $this->db->insert_id();
		if($result){
			$event = $this->common->getData('sport_event',array('id'=>$insert_id),array('single'));
			$data['join_user']=$user_id;
        	

        	$user_event_count = $this->common->getData('user',array('id' => $user_id),array('single'));

        	$join_user_event = $user_event_count['user_event'];	

        	if(empty($join_user_event))
        	{
        		$user_event_id = $insert_id;
        	}
        	else
        	{
        		$user_event_id = $join_user_event.','.$insert_id;
        	}
        	$user_info['user_event']=$user_event_id;

			$result = $this->common->updateData('sport_event',$data,array('id' => $insert_id));
        	$result = $this->common->updateData('user',$user_info,array('id' => $_REQUEST['user_id']));

			$this->response(true,"Event Create Successfully.",array("eventinfo" => $event));
		}
		else{
			$this->response(false,"There is a problem, please try again.");
		}
		
		}
		else
		{
			$this->response(false,"Missing Parameter.");
		}		 	
		
	}

	public function event_detail()
	{
		if(!empty($_REQUEST['id']))
		{
			$id = $_REQUEST['id'];


			$result = $this->common->get_eventList(array('SE.id'=>$id));
		
			if(!empty($result))
			{


				

			foreach ($result as $key => $value) {


				if(!empty($value['join_user']))
				{
						$join_user = $value['join_user'];
						$arr_user=(explode(",",$join_user));
						$user_id_string = implode("','", $arr_user);
						$where = "`id` IN ('".$user_id_string."')";
						$result_user = $this->common->getData('user',$where);

						

						foreach($result_user as $user_data)
						{
							if(!empty($user_data['image']))
							{
								$image = base_url('/assets/userfile/profile/'.$user_data['image']);
							}
							else
							{
								$image ="";
							}
							
                 		
							$user_info[]=array('id'=>$user_data['id'],'image'=>$image);
						}



				}
				else
				{
					$user_info="";
				}

				if(!empty($value['game_image']))
			{
				$game_image = base_url('/assets/Game/gamelogo/'.$value['game_image']);
			}
			else
			{
				$game_image = "";
			}


			if(!empty($value['event_image']))
			{
				$event_image = base_url('/assets/event/image/'.$value['event_image']);
			}
			else
			{
				$event_image = "";
			}

;
			$event_arr=array('id'=>$value['id'],'title'=>$value['title'],'game_id'=>$value['game_id'],'event_user_type'=>$value['event_user_type'],'event_time'=>$value['event_time'],'event_duration'=>$value['event_duration'],'event_participant_no'=>$value['event_participant_no'],'event_description'=>$value['event_description'],'status'=>$value['status'],'game_name'=>$value['game_name'],'event_address'=>$value['event_address'],'game_image'=>$game_image,'event_image'=>$event_image,'longitude'=>$value['longitude'],'latitude'=>$value['latitude'],'user_info' => $user_info);

			if($value['event_user_type']==2)
				{
					$event_arr['user_id']=$value['user_id'];
					$userinfo = $this->common->getData('user',array('id'=>$value['user_id']),array('single'));



					$event_arr['user_name'] = $userinfo['name'];
					$event_arr['user_email'] = $userinfo['email'];
				
				}
				else
				{
					$event_arr['price'] = $value['price'];
				}

		}
	}
			
			if($result){
				$this->response(true,"Profile fetch Successfully.",array("userinfo" => $event_arr));			
			}else{
				$this->response(false,"There is a problem, please try again.",array("userinfo" => ""));
			}		
			
		}
		else
		{
			$this->response(false,"Missing Parameter.");
		}
	}



	public function my_sport()
	{
		if(!empty($_REQUEST['user_id']))
		{
			$user_id = $_REQUEST['user_id'];

			$where = 'SE.status = 0';
        	$result = $this->common->get_eventList($where);

        	


        	foreach ($result as $key => $value) {
			$game_image = base_url('/assets/Game/gamelogo/'.$value['game_image']);

			$join_user = $value['join_user'];
			
			$join_user_arr=(explode(",",$join_user));
			

        	if (in_array($user_id,$join_user_arr))
        	{
        		 $event_time=$value['event_time'];
        		$today = Date('Y-m-d H:i:s'); 
        		
        		if($event_time < $today)
        		{
        			 $Sport_status = 'active';
        		}
        		else
        		{
        			$Sport_status = 'done';
        		}

        		$arr[]=array('id'=>$value['id'],'title'=>$value['title'],'game_id'=>$value['game_id'],'event_user_type'=>$value['event_user_type'],'event_time'=>$value['event_time'],'event_duration'=>$value['event_duration'],'event_participant_no'=>$value['event_participant_no'],'event_description'=>$value['event_description'],'status'=>$value['status'],'game_name'=>$value['game_name'],'game_image'=>$game_image,'join_user'=>$value['join_user'],'Sport_status'=>$Sport_status,'event_address'=>$value['event_address'],'latitude'=>$value['latitude'],'longitude'=>$value['longitude']);
        	}

	}

		if(!empty($arr))
		{
			$this->response(true,"Sport fetch Successfully.",array("sportinfo" => $arr));
		}
		else
		{
			$this->response(false,"Sport Not found.");
		}
		

		}
		else
		{
			$this->response(false,"Missing Parameter.");
		}
	}



	public function follow_user()
	{
		if(!empty($_REQUEST['user_id']) && !empty($_REQUEST['follow_uid']))
		{
			 $user_id = $_REQUEST['user_id'];
			 $follow_uid = $_REQUEST['follow_uid'];
				$status = $_REQUEST['status'];
				if ($status == 1) {
					if (!empty($follow_uid)) {
					
						$where="user_id	='" . $user_id . "' AND following_id ='" . $follow_uid . "' ";
						$value = $this->common->getData('user_following',$where,array('single'));
						;
						
					if(empty($value))
						{	
							$insert = $this->common->insertData('user_following',array('user_id' => $user_id,'	following_id' => $follow_uid));

							 $user_data = $this->common->getData('user',array('id'=>$user_id),array('single'));
							 	$name = $user_data['name'];
							 	$today = Date('Y-m-d H:i:s'); 
								$insert_notification = $this->common->insertData('notification_tbl',array('user_id' => $user_id,'message' => "Started",'user_send_from'=>$follow_uid,'date'=>$today,'type'=>"Following"));
							
								$uid  = $this->db->insert_id();
						
							$this->response(true,"Follow added");
							
						}
						else
						{
							$this->response(false,"Follow already added");
						}

					}
				}

				else
                {
                    
                    if (!empty($follow_uid)) {
                    	$where="user_id	='" . $user_id . "' AND following_id ='" . $follow_uid . "' ";
                    	
                    	$value = $this->common->deleteData('user_following',$where);
                    	$this->response(true,"follow deleted");
                    	$today = Date('Y-m-d H:i:s'); 
                    	$insert_notification = $this->common->insertData('notification_tbl',array('user_id' => $user_id,'message' =>"You",'user_send_from'=>$follow_uid,'date'=>$today,'type'=>"Unfollowed"));
							
                    }
                }
		}
		else
		{
			$this->response(false,"Missing Parameter.");
		}	
	}

	public function accept_reject()
	{
	if(!empty($_REQUEST['user_id']) && !empty($_REQUEST['following_id']) && !empty($_REQUEST['status']))
		{
			$user_id = $_REQUEST['user_id'];
			$following_id = $_REQUEST['following_id'];
			$status = $_REQUEST['status'];

			$where="user_id	='" . $user_id . "' AND user_id	='" . $user_id . "' AND user_id	='" . $user_id . "' ";
			$follow = $this->common->getData('join_event_tbl',$where1,array('single'));

			$data['status']	= $status;
			if($status == 1)
			{

				$message = "Accept User Successfully";
			}
			else
			{
				$message = "Reject User Successfully";
			}
			$result = $this->common->updateData('user_following',$data,array('user_id' => $user_id,'following_id' => $following_id));
			if($result){
			$this->response(true,"$message");
			}else{
			$this->response(false,"There is a problem, please try again.",array("userinfo" => ""));
			}
		}
		else
		{
			$this->response(false,"Missing Parameter.");
		}	

	}





	public function accept_reject_event()
	{
		if(!empty($_REQUEST['join_id']) && !empty($_REQUEST['event_id']) &&!empty($_REQUEST['status']))
		{
			
			$join_id = $_REQUEST['join_id'];
			$event_id = $_REQUEST['event_id'];
			$status = $_REQUEST['status'];

			$even_data_user = $this->common->getData('sport_event',array('id'=>$event_id),array('single'));
			$user_id = $even_data_user['user_id'];

			$where1 = "user_id	='" . $user_id . "' AND join_id	='" . $join_id . "' AND event_id	='" . $event_id . "' ";
			$status_match = $this->common->getData('join_event_tbl',$where1,array('single'));
			if($status_match['status']==1)
			{
				$this->response(false,"Already Accepted");
				exit();
			}
			if($status_match['status']==2)
			{
				$this->response(false,"Already Rejected");
				exit();
			}

				
			if($status == 1)
			{

				$data_join = $this->joinevent($event_id,$join_id);
				$message = $data_join['message'];
					
				if($data_join['status'])
				{
					$message = "Accept User Successfully";
					$data['status'] = $status;
					$result = $this->common->updateData('join_event_tbl',$data,array('user_id' => $user_id,'join_id' => $join_id,'event_id'=>$event_id));
						
					if($result){

						$user_data = $this->common->getData('user',array('id'=>$user_id),array('single'));

					$name = $user_data['name'];
					$today = Date('Y-m-d H:i:s'); 
					$insert_notification = $this->common->insertData('notification_tbl',array('user_id' => $join_id,'message' => "Joinee",'date'=>$today,'user_send_from'=>$user_id,'type'=>"Event"));



						$this->response(true,$message);
						exit();
					}
					else
					{
						$this->response(false,"There is a problem, please try again.");
						exit();
					}
				
				}else
				{
					$this->response(false,$message);
					exit();
				}
			}
			else
			{
				$message = "Reject User Successfully";
				$data['status'] = $status;
				$result = $this->common->updateData('join_event_tbl',$data,array('user_id' => $user_id,'join_id' => $join_id,'event_id'=>$event_id));
				
				if($result){

					$user_data = $this->common->getData('user',array('id'=>$user_id),array('single'));

					$name = $user_data['name'];
					$today = Date('Y-m-d H:i:s'); 
					$insert_notification = $this->common->insertData('notification_tbl',array('user_id' => $join_id,'message' =>"Reject your request for",'date'=>$today,'user_send_from'=>$user_id,'type'=>"Joining"));


					$this->response(true,$message);
					exit();
				}
				else
				{
					$this->response(false,"There is a problem, please try again.");
					exit();
				}
			}
					
					
		}
		else
		{
			$this->response(false,"Missing Parameter.");
		}	

	}



	public function game_list()
	{
		$where="status = 0";
        $result = $this->common->getData('sport_game',$where);
        if(!empty($result)){

        	 foreach ($result as $key => $value) {
        	 	if(!empty($value['game_image']))
                 	{
                 		$game_image = base_url('/assets/Game/gamelogo/'.$value['game_image']);
                 	}
                 	else
                 	{
                 		$game_image = '';
                 	}

                 $arr[]=array('id'=>$value['id'],'game_name'=>$value['game_name'],'game_image'=>$game_image);
        	 }
			$this->response(true,"user fetch Successfully.",array("gameinfo" => $arr));			
		}else{
			$this->response(false,"There is a problem, please try again.",array("gameinfo" => ""));
		}

	}


	public function accept_reject_friend()
	{
		if(!empty($_REQUEST['id']) && !empty($_REQUEST['status']))
		{
				$id = $_REQUEST['id'];
				$status = $_REQUEST['status'];


				// notification start




				$today = Date('Y-m-d H:i:s');

				$user_friend_info = $this->common->getData('friend_tbl',array('id' => $id),array('single'));

				

				$where_delete_notification  = '(user_id = '.$user_friend_info['friend_id'].' AND user_send_from = '.$user_friend_info['user_id'].') OR (user_id = '.$user_friend_info['user_id'].' AND user_send_from = '.$user_friend_info['friend_id'].') AND type=1';
				$notification_tbl_result = $this->common->getData('notification_tbl',$where_delete_notification,array('single'));
						;

				
				if(!empty($notification_tbl_result))
				{
					
					$delete_where="id	='" . $notification_tbl_result['id'] . "'";
                    $this->common->deleteData('notification_tbl',$delete_where);
					
				}



				$user_get_notification = $this->common->getData('user',array('id'=>$user_friend_info['user_id']),array('single'));

				$user_send_notification = $this->common->getData('user',array('id'=>$user_friend_info['friend_id']),array('single'));

				$ios_token = $user_get_notification['ios_token'];
				
				$android_token = $user_get_notification['android_token'];
				
				$user_data_from_name = $user_send_notification['name']; 


				if($status == 1)
				{ 

					$this->notification_count($user_friend_info['user_id']);


					$insert_notification = $this->common->insertData('notification_tbl',array('user_id' => $user_friend_info['user_id'],'message' => "Accepted friend Request",'date'=>$today,'user_send_from'=>$user_friend_info['friend_id'],'type'=>2));

					$title = "Accept friend request";
					$type = "Accept friend request";
					$message_push = $user_data_from_name." accept your friend request";

					$msg_notification = array
					(
					'body' 	=> $message_push,
					'title'	=> $title,
					'icon'	=> 'ic_stat_wholesaler',
					'sound' => '',
					'color' => '',
					//'badge' => 1,
					// 'click_action' => 'com.mycoach.content_manager.activity.ContentDetailActivity'
					);



					if($ios_token != "")
					{

					$messages_push = array("alert" => $title, "msg" => $message_push,"sound"=>"default","type" => $type,"user_id"=>$user_friend_info['friend_id']);	
					
					$this->push_iOS($ios_token,$messages_push);

					
				}
				else if($android_token != "")
				{
					

					$messages_push = array('notification'=>$msg_notification,'notification_type'=>$type,"user_id"=>$user_friend_info['friend_id']);	

					$registatoin_id = array($android_token); 
					$this->send_notification($registatoin_id, $messages_push);

				}


				}

				
			// notification end



				if($status == 1)
				{
					$data['status']=$status;
        			$result = $this->common->updateData('friend_tbl',$data,array('id' => $id));
        			$this->response(true,"Accepted friend Request successfully");
				}

				if($status == 2)
				{
					$where="id	='" . $id . "'";
                    $value = $this->common->deleteData('friend_tbl',$where);
                    $this->response(true,"Rejected friend Request successfully");
				}



		}
		else
		{
			$this->response(false,"Missing Parameter.");
		}
	
}




	public function all_user_list()
	{	
		if(!empty($_REQUEST['user_id']))
		{

			$user_id = $_REQUEST['user_id'];

			$where="id	!='" . $user_id . "'";
			$result = $this->common->getData('user',$where);
     		if(!empty($result))
         	{    
	           	foreach($result as $value)
	            {

	            	$where_friend = '(user_id = '.$value['id'].' AND friend_id	= '.$user_id.') OR (user_id = '.$user_id.' AND friend_id = '.$value['id'].')';
						
					$result_friend = $this->common->getData('friend_tbl',$where_friend,array('single'));

					if(!empty($result_friend))
					{
						if(($result_friend['friend_id'] == $user_id )&& ($result_friend['status'] ==0) )
						{
							$friend_status = 1;
						}

						if(($result_friend['friend_id'] == $value['id'] )&& ($result_friend['status'] ==0) )
						{
							$friend_status = 2;
						}

						if(($result_friend['status'] ==1))
						{
							$friend_status = 4;
						}

						$friend_id = $result_friend['id'];	
					}
					else
					{
						$friend_status = 3;
						$friend_id = "";	
					}

					$arr['friend_status'] = $friend_status;
					$arr['friend_id'] = $friend_id;



	            	if(!empty($value['image']))
					{
						$image = base_url('/assets/userfile/profile/'.$value['image']);

					}
					else
					{
						$image = "";
					}

					

					if($_REQUEST['type'] == 3 && $value['user_type'] == 2)
					{
							$wholesaler_list[] = array('id'=>$value['id'],'name'=>$value['name'],'email'=>$value['email'],'mobile'=>$value['mobile'],'user_address'=>$value['user_address'],'image'=>$image,'user_type'=>$value['user_type'],'friend_status'=>$friend_status,'friend_id'=>$friend_id);
					}


					if($_REQUEST['type'] == 4 && $value['user_type'] == 3)
					{
							$drop_shipper_list[] = array('id'=>$value['id'],'name'=>$value['name'],'email'=>$value['email'],'mobile'=>$value['mobile'],'user_address'=>$value['user_address'],'image'=>$image,'user_type'=>$value['user_type'],'friend_status'=>$friend_status,'friend_id'=>$friend_id);
					}

					if($_REQUEST['type'] == 5 && $value['user_type'] == 1)
					{
							$retailer_list[] = array('id'=>$value['id'],'name'=>$value['name'],'email'=>$value['email'],'mobile'=>$value['mobile'],'user_address'=>$value['user_address'],'image'=>$image,'user_type'=>$value['user_type'],'friend_status'=>$friend_status,'friend_id'=>$friend_id);
					}



					if($_REQUEST['type'] == 2 && $friend_status == 4)
					{
							$fiend_list[] = array('id'=>$value['id'],'name'=>$value['name'],'email'=>$value['email'],'mobile'=>$value['mobile'],'user_address'=>$value['user_address'],'image'=>$image,'user_type'=>$value['user_type'],'friend_status'=>$friend_status,'friend_id'=>$friend_id);
					}





					
	            	$all_user_array[] = array('id'=>$value['id'],'name'=>$value['name'],'email'=>$value['email'],'mobile'=>$value['mobile'],'user_address'=>$value['user_address'],'image'=>$image,'user_type'=>$value['user_type'],'friend_status'=>$friend_status,'friend_id'=>$friend_id);
            	}
            	

            		if($_REQUEST['type'] == 1)
            		{
            			$this->response(true,"user fetch Successfully.",array("all_user_list" => $all_user_array));
            		}


            		if($_REQUEST['type'] == 2)
            		{
            			if(empty($fiend_list))
            			{
            				$fiend_list =array();
            			}

            			$this->response(true,"friend fetch Successfully.",array("all_user_list" => $fiend_list));
            		}

            		if($_REQUEST['type'] == 3)
            		{
            			if(empty($wholesaler_list))
            			{
            				$wholesaler_list =array();
            			}

            			$this->response(true,"wholesaler fetch Successfully.",array("all_user_list" => $wholesaler_list));
            		}


            		if($_REQUEST['type'] == 4)
            		{
            			if(empty($drop_shipper_list))
            			{
            				$drop_shipper_list =array();
            			}

            			$this->response(true,"Drop Sheeper fetch Successfully.",array("all_user_list" => $drop_shipper_list));
            		}

            		if($_REQUEST['type'] == 5)
            		{
            			if(empty($retailer_list))
            			{
            				$retailer_list =array();
            			}

            			$this->response(true,"Retailer fetch Successfully.",array("all_user_list" => $retailer_list));
            		}

            		
             
    				
    		}
    		else
    		{	
				$this->response(true,"user Not Found",array("all_user_list" => array()));
			}

		}
		else
		{
			$this->response(false,"Missing Parameter.");
		}

	}














	public function user_list()
	{
		if(!empty($_REQUEST['user_id']))
		{
				$user_id = $_REQUEST['user_id'];
				 $where="id	!='" . $user_id . "' AND status = 0 ";
               
                 $result = $this->common->getData('user',$where);
               

            if(!empty($result)){     
                 foreach ($result as $key => $value) {
                 	if(!empty($value['image']))
                 	{
                 		$image = base_url('/assets/userfile/profile/'.$value['image']);
                 	}
                 	else
                 	{
                 		$image = '';
                 	}
		
		$where1="user_id ='" . $user_id . "' AND following_id = '" . $value['id'] . "' ";
		$follow = $this->common->getData('user_following',$where1,array('single'));

	
	

		if(!empty($follow))
		{
			$follow_status = 1;
		}
		else
		{
			$follow_status = 2;
		}

			$id = $value['id'];
		

		$user_where = "(user_from='".$user_id."' and user_to='".$id."') or (user_from='".$id."' and user_to='".$user_id."')";
	
		$result_chat = $this->common->getData('chat',$user_where,array('sort_by'=>'created_at','sort_direction' => 'desc'));
		if(!empty($result_chat))
		{
			$created_at = $result_chat[0]['created_at'];

		}
		else
		{
			$created_at ="";
		}
		

		


		$arr[]=array('id'=>$value['id'],'name'=>$value['name'],'email'=>$value['email'],'image'=>$image,'follow_status'=>$follow_status,'created_at'=>$created_at);
		}

	
		
			$this->response(true,"user fetch Successfully.",array("userinfo" => $arr));			
		}else{
			$this->response(false,"User Not Found",array("userinfo" => ""));
		}
		}
		else
		{
			$this->response(false,"Missing Parameter.");
		}

	}



	public function wholesaler_list()
	{
		if(!empty($_REQUEST['category_id']))
		{
			$category_id = $_REQUEST['category_id'];
			$where = "FIND_IN_SET('".$category_id ."',category) and (user_type = 2 or user_type=3)";
            $result = $this->common->getData('user',$where);
     	
     		if(!empty($result))
         	{    
	            foreach($result as $value)
	            {
	            	if(!empty($value['image']))
					{
						$image = base_url('/assets/userfile/profile/'.$value['image']);

					}
					else
					{
						$image = "";
					}

					$rating = $this->rating_count($value['id']);
	            	$wholesaler_array[] = array('id'=>$value['id'],'name'=>$value['name'],'email'=>$value['email'],'mobile'=>$value['mobile'],'user_address'=>$value['user_address'],'image'=>$image,'user_type'=>$value['user_type'],'id'=>$value['id'],'id'=>$value['id'],'id'=>$value['id'],'id'=>$value['id'],'id'=>$value['id'],'rating'=> $rating);
            	}
            
             
    				$this->response(true,"user fetch Successfully.",array("wholesaler_list" => $wholesaler_array));
    			}
    			else
    			{
					$this->response(true,"Wholesaler Not Found",array("wholesaler_list" => array()));
				}
		}
		else
		{
			$this->response(false,"Missing Parameter.");
		}

	}





	public function user_by_type()
	{
		if(!empty($_REQUEST['user_type']))
		{
			$user_type = $_REQUEST['user_type'];
			$where = "user_type != '".$user_type ."'";
            $result = $this->common->getData('user',$where);
     	
     		if(!empty($result))
         	{    
	            foreach($result as $value)
	            {
	            	if(!empty($value['image']))
					{
						$image = base_url('/assets/userfile/profile/'.$value['image']);

					}
					else
					{
						$image = "";
					}

					
	            	$user_array[] = array('id'=>$value['id'],'name'=>$value['name'],'email'=>$value['email'],'image'=>$image,'user_type'=>$value['user_type']
	            );
            	}
            
             
    				$this->response(true,"user fetch Successfully.",array("user_list" => $user_array));
    			}
    			else
    			{
					$this->response(true,"Wholesaler Not Found",array("user_list" => array()));
				}
		}
		else
		{
			$this->response(false,"Missing Parameter.");
		}

	}




	public function wholesaler_add_rating()
	{
		if(!empty($_REQUEST['wholesaler_id'])&& !empty($_REQUEST['user_id']))
		{
			$wholesaler_id = $_REQUEST['wholesaler_id'];
			$user_id = $_REQUEST['user_id'];
			
			$where = "wholesaler_id ='".$wholesaler_id ."' AND user_id ='".$user_id ."' ";
            $result = $this->common->getData('wholesaler_rating',$where);
            
            if(empty($result))
            	{     
            		

					$post = $this->common->getField('wholesaler_rating',$_POST);

					$result_insert = $this->common->insertData('wholesaler_rating',$post);
					$avg_rating = $this->rating_count($wholesaler_id);

					
					$this->response(true,"Rating add Successfully.",array("rating" =>$avg_rating));
    			}
    			else
    			{

    				unset($_POST['wholesaler_id']);
    				unset($_POST['user_id']);
    				$post = $this->common->getField('wholesaler_rating',$_POST);
    				$where_update = "wholesaler_id ='".$wholesaler_id ."' AND user_id ='".$user_id ."' ";
    				
					$result = $this->common->updateData('wholesaler_rating',$post,$where_update); 
					$avg_rating = $this->rating_count($wholesaler_id);
					
					$this->response(true,"Rating Edited Successfully.",array("rating" =>$avg_rating));

				}
		}
		else
		{
			$this->response(false,"Missing Parameter.");
		}

	}


	public function product_add_rating()
	{
		if(!empty($_REQUEST['product_id']) && !empty($_REQUEST['user_id']))
		{
			$product_id = $_REQUEST['product_id'];
			$user_id = $_REQUEST['user_id'];
			

			$where = "product_id ='".$product_id ."' AND user_id ='".$user_id ."' ";
            $result = $this->common->getData('product_rating',$where);
            
            if(empty($result))
            	{     

            		$post = $this->common->getField('product_rating',$_POST);

					$result_insert = $this->common->insertData('product_rating',$post);
					$avg_rating = $this->rating_count_product($product_id);

					
					$this->response(true,"Rating add Successfully.",array("rating" =>$avg_rating));
    			}
    			else
    			{

    				unset($_POST['product_id']);
    				unset($_POST['user_id']);
    				$post = $this->common->getField('product_rating',$_POST);


    				$where_update = "product_id ='".$product_id ."' AND user_id ='".$user_id ."' ";
    		

					$result = $this->common->updateData('product_rating',$post,$where_update); 
					$avg_rating = $this->rating_count_product($product_id);
					
					$this->response(true,"Rating Edited Successfully.",array("rating" =>$avg_rating));

				}
		}
		else
		{
			$this->response(false,"Missing Parameter.");
		}

	}




	function rating_count($wholesaler_id)
	{
		
		$count_user = $this->common->getData('wholesaler_rating',array('wholesaler_id'=>$wholesaler_id),array('count'));

		if($count_user)
		{
			$query="SELECT SUM(`rating`) AS rating_count FROM wholesaler_rating  WHERE wholesaler_id='".$wholesaler_id."'";
			$total_wholesale_rating = $this->common->query($query);
			$total_rating_user = $total_wholesale_rating[0]['rating_count'];
			$avg=$total_rating_user/$count_user;
		}
		else
		{
			$avg = 0;
		}
		return $avg;
	}

	function rating_count_product($product_id)
	{
		
		$count_user = $this->common->getData('product_rating',array('product_id'=>$product_id),array('count'));

		if($count_user)
		{
			$query="SELECT SUM(`rating`) AS rating_count FROM product_rating  WHERE product_id='".$product_id."'";
			$total_product_rating = $this->common->query($query);
			$total_rating_user = $total_product_rating[0]->rating_count;
			$avg=$total_rating_user/$count_user;
		}
		else
		{
			$avg = 0;
		}
		return $avg;
	}





	public function user_detail()
	{
		if(!empty($_REQUEST['user_id']))
		{
			$user_id = $_REQUEST['user_id'];
			$result = $this->common->getData('user',array('id' => $user_id),array('single'));
			if(!empty($result))
			{

				if(!empty($result['user_game_id']))
			{

				$game = $this->common->getData('sport_game',array('id' => $result['user_game_id']),array('single'));
			


				if(!empty($game['game_image']))
				{
					$game_image = base_url('/assets/Game/gamelogo/'.$game['game_image']);
				
				}
				else
				{
					$game_image  = "";
				}
			}
			else
			{
				$game_image = "";
			}



				$user_dob= $result['user_dob'];
				$user_address= $result['user_address'];
				 $age= date_diff(date_create($user_dob), date_create('today'))->y;
				if(!empty($result['image']))
				{
				$user_image = $image = base_url('/assets/userfile/profile/'.$result['image']);
				}
				else
				{
					$user_image="";
				}

				$arr[]=array('id'=>$result['id'],'name'=>$result['name'],'email'=>$result['email'],'user_image'=>$user_image,'game_image'=>$game_image,'user_dob'=>$age,'user_address'=>$user_address);
				
				$this->response(true,"user fetch Successfully.",array("userinfo" => $arr));	
			}
			else
			{
				$this->response(false,"User Not Found",array("userinfo" => ""));
			}
		}
		else
		{
			$this->response(false,"Missing Parameter.");
		}

	}


	


	public function verification()
	{		
		if($_POST['type'] =='mobile'){
			$userinfo = $this->common->getData('user',array('mobile'=>$_POST['mobile']),array('single'));
			if($_POST['otp'] != $userinfo['otp']){
				$this->response(false,"Wrong OTP entered. please try again.",array("userinfo" => $userinfo)); exit();
			}
			$this->common->updateData('user',array('verified'=> '1','otp' => null),array('mobile'=> $_POST['mobile']));
			$message = "OTP verified successfully.";
		}

		if($_POST['type'] == 'email'){
			$userinfo = $this->common->getData('user',array('email'=>$_POST['email']),array('single'));
			if($_POST['otp'] != $userinfo['otp']){
				$this->response(false,"Wrong OTP entered. please try again.",array("userinfo" => $userinfo)); exit();
			}
			$this->common->updateData('user',array('verified' => '1','otp' => null),array('email' => $_POST['email']));
			$message = "Email verified successfully.";	
		}
		
		$this->response(true,$message,array("userinfo" => $userinfo));
	}



	public function social_login()
	{		
		$user = $this->common->getData('user',array('email' => $_POST['email']),array('single'));
		$url = $this->input->post('image');
		$uimg = "";
		if($url != ""){
			$uimg = rand().time().'.png';
			file_put_contents('assets/userfile/profile/'.$uimg, file_get_contents($url));
		}
		if($user){
			
			$old_device = $this->common->getData('user',array('ios_token' => $_POST['ios_token']),array('single','field'=>'id'));
			if($old_device){
				$this->common->updateData('user',array('android_token' => "", "ios_token" => ""),array('id' => $old_device['id']));
			}
			$update = $this->common->updateData('user',array('image' => $uimg, 'ios_token' =>$_POST['ios_token'], 'android_token' => $_POST['android_token']),array('id' => $user['id']));
			if($update){				
				if($user['image'] != "" && file_exists('assets/userfile/profile/'.$user['image'])){
					unlink('assets/userfile/profile/'.$user['image']);
				}
				$user['image'] = $uimg;
				$this->response(true,"Login Successfully.",array("userinfo" => $user));
			}else{
			 	$this->response(false,"There is a problem, please try again.",array("userinfo" => ""));
  			}
		}else{			
			$insert = $this->common->insertData('user',array('email' => $_POST['email'],'image' => $uimg,'name' => $_POST['name'],'ios_token' =>$_POST['ios_token'],'user_dob'=>$_POST['user_dob'],'user_address'=>$_POST['user_address'],'user_latitude'=>$_POST['user_latitude'],'user_longitude'=>$_POST['user_longitude'],'android_token' => $_POST['android_token'],'created_at' => Date('Y-m-d H:i:s'),'social'=>1));


			$uid  = $this->db->insert_id();
			if($insert){
		    $user = $this->common->getData('user',array('id'=> $uid),array('single'));
		
		    // $user['image'] =base_url('/assets/userfile/profile/'.$user['image']);
		    
				$this->response(true,"Your Registration Successfully Completed.",array("userinfo" => $user));
			}else {
		     	$this->response(false,"There is a problem, please try again.",array("userinfo" => ""));
		    }
		}
	}


	


	public function updateEvent(){
		chmod('./assets/userfile/profile/',0777);
		$id = $_POST['id']; unset($_POST['id']);		
		
		$post = $this->common->getField('sport_event',$_POST);		
		$result = $this->common->updateData('sport_event',$post,array('id' => $id)); 

		if($result){
			$this->response(true,"Event Update Successfully.",array("userinfo" => $user));
		}else{
			$this->response(false,"There is a problem, please try again.",array("userinfo" => ""));
		}
	}




	public function getData1()
	{
		echo "hello" ;
	}

	public function getProfile()
	{
		if(!empty($_REQUEST['id']))
		{
			$id = $_REQUEST['id'];
			$result = $this->common->get_user_info(array('U.id' => $id),array('single'));
			
			if($result['user_type'] == 2 || $result['user_type'] == 3)
			{
				if(!empty($result['image']))
				{
					$result['image'] = base_url('/assets/userfile/profile/'.$result['image']);
				}
				else
				{
					$result['image'] = "";
				}
				$rating = $this->rating_count($id);
				$category = $result['category'];
				if($category != "")
				{
					$category  = explode(",",$category);

					foreach($category as $cat)
					{
						$cat_info = $this->common->getData('category_tbl',array('category_id' => $cat),array('single')); 
						$category_data[] = array('category_id'=>$cat_info['category_id'],'category_name'=>$cat_info['category_name'],'category_image'=>$cat_info['category_image']);
					}

				
				}
				else
				{
					$category_data ="";
				}

				$count_user_no = $this->common->getData('wholesaler_rating',array('	wholesaler_id'=>$id),array('count'));
        	 		if($count_user_no)
        	 		{
        	 			$result['rating_user_no'] = $count_user_no;
        	 		}
        	 		else
        	 		{
        	 			$result['rating_user_no'] = 0;
        	 		}
        	 		
				$result['rating']= $rating;
				$result['category_info'] = $category_data;
				$other_info = $this->common->getData('other_user_info',array('user_id' => $result['id']),array('single'));
			}

			if(!empty($other_info))
			{
						$result['user_video'] =  $other_info['user_video'];

						if(!empty($other_info['user_video']))
						{
							$result['user_video'] = base_url('/assets/userfile/profile/'.$other_info['user_video']);
						}
						else
						{
							$result['user_video'] = "";
						}
						$result['get_percent'] =  $other_info['get_percent'];
						$result['min_price'] =  $other_info['min_price'];
						$result['max_price'] =  $other_info['max_price'];
						$result['expected_delivery_date'] =  $other_info['expected_delivery_date'];
						$result['dropship'] =  $other_info['dropship'];
			}
			
			if($result)
			{

				$this->response(true,'User found Successfully',array("userinfo" => $result));					
			}else{		
				$this->response(false,'User Not Found',array("userinfo" => ""));
			}
		}
		else
		{
			$this->response(false,'Missing parameter');
					
		}
	}

	



// 	function get_mysqli() { 
// $db = (array)get_instance()->db;
// return mysqli_connect('localhost', $db['username'], $db['password'], $db['database']);}


	public function product_list_pdf()
	{
		if(!empty($_POST['wholesaler_id']))
		{
			$wholesaler_id = $_REQUEST['wholesaler_id'];
			

				$where="product_tbl.user_id = '" . $wholesaler_id . "'";
				$data['product_list'] = $this->common->get_record_join_two_table('product_tbl','category_tbl','	category','category_id','',$where,'product_tbl.product_id');
				$template = $this->load->view('template/product-list',$data,true);


				$apikey = 'd44f85bd-b9ae-4f91-94b6-ab5c1bc4d812';
				$value = $template;
				$result = file_get_contents("http://api.html2pdfrocket.com/pdf?apikey=" . urlencode($apikey) . "&value=" . urlencode($value));
 
			// Output headers so that the file is downloaded rather than displayed
			// Remember that header() must be called before any actual output is sent
			header('Content-Description: File Transfer');
			header('Content-Type: application/pdf');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . strlen($result));
			 
			// Make the file a downloadable attachment - comment this out to show it directly inside the 
			// web browser.  Note that you can give the file any name you want, e.g. alias-name.pdf below:
			//  header('Content-Disposition: attachment; filename=' . 'alias-nametype.pdf' );
 
			// Stream PDF to user
			echo $result;
		}
		else
		{
			$this->response(false,'Missing parameter');
		}
	}

	public function update_badge_count()
	{
		if(!empty($_REQUEST['user_id']))
		{
			if($_REQUEST['type'] == 1)
			{
				$batch_count_no = 0;
				$data_batch['batch_count']=$batch_count_no;
				$result = $this->common->updateData('batch_count_tbl',$data_batch,array('user_id' => $_REQUEST['user_id']));
				$this->response(true,"batch count update Successfully.");
				exit();
			}

			if($_REQUEST['type'] == 2)
			{
				$batch_count_no = 0;
				$data_batch['batch_count']=$batch_count_no;
				$result = $this->common->updateData('notification_count_tbl',$data_batch,array('user_id' => $_REQUEST['user_id']));
				$this->response(true,"batch count update Successfully.");
				exit();
			}
		}
		else
		{
			$this->response(false,'Missing Parameter');	
		}
	}


	public function show_count()
	{
		if(!empty($_REQUEST['user_id']))
		{
			$user_batch_count = $this->common->getData('batch_count_tbl',array('user_id'=>$_REQUEST['user_id']),array('single'));
				if(!empty($user_batch_count))
				{
					$batch_count = $user_batch_count['batch_count'];
				}
				else
				{
					$batch_count = "0";
				}

			$user_notification_count = $this->common->getData('notification_count_tbl',array('user_id'=>$_REQUEST['user_id']),array('single'));

			if(!empty($user_notification_count))
				{
					$noitification_count = $user_notification_count['batch_count'];
				}
				else
				{
					$noitification_count = "0";
				}



				$cart_count = $this->common->getData('cart_tbl',array('paid_status'=>0,'user_id'=>$_REQUEST['user_id']),array('count'));


			$this->response(true,"batch count fetch Successfully.",array("batch_count" => $batch_count,"cart_count" => $cart_count,"noitification_count" => $noitification_count));
		}
		else
		{
			$this->response(false,'Missing Parameter');	
		}
	}




	public function chat()
	{

		if(!empty($_REQUEST['user_from']) && !empty($_REQUEST['user_to']) && !empty($_REQUEST['type']))
		{
			$post['user_from'] = $_REQUEST['user_from'];
			
			$post['user_to'] = $_REQUEST['user_to'];
			$user_to = $_REQUEST['user_to'];
			$type = $_REQUEST['type'];

			if($type == 2)
			{
				$image = $this->common->do_upload_file('message','./assets/chat/');
				if(isset($image['upload_data']))
				{
					$msg_image = $image['upload_data']['file_name'];
					$msg = base_url('/assets/chat/'.$msg_image);
					$post['message']=json_encode($image['upload_data']['file_name']);
					$message_send_notification = base_url('/assets/chat/'.$msg_image);
					$message_type_notification = 1;

				}
				else
				{
					$this->response(false,'Missing parameter');
					exit();
				}
			}
			else
			{
				$message_user = $_REQUEST['message'];
				$message_user = json_encode($message_user);
				$post['message']  =  $message_user;
				$msg = $_REQUEST['message'];
				$message_send_notification = $msg;
				$message_type_notification = 2;
			}
			
			$post['created_at'] = date('Y-m-d H:i:s');
			$result = $this->common->insertData('chat',$post);
			$insert_id = $this->db->insert_id();

			if($result)
			{
				$message = "message sent successfully";
				$last_msg =  array("id" => $insert_id,
				"user_from" => $_POST['user_from'],
				"user_to" => $user_to,
				"message"=> $msg,
				"message_staus"=> $message_type_notification,
				"created_at" => $post['created_at']);

				//batch count code start

				$user_batch_count = $this->common->getData('batch_count_tbl',array('user_id'=>$user_to),array('single'));
				
				if(empty($user_batch_count))
				{
					$data_batch['batch_count'] = 1;
					$data_batch['user_id'] = $user_to;
					$this->common->insertData('batch_count_tbl',$data_batch);
				}
				else
				{
					$batch_count_no = $user_batch_count['batch_count']+1;
					$data_batch['batch_count']=$batch_count_no;
					$result = $this->common->updateData('batch_count_tbl',$data_batch,array('user_id' => $user_to));
				}

				//batch count code end



				// notification start

				$user_data_to = $this->common->getData('user',array('id'=>$user_to),array('single'));

				$user_data_from = $this->common->getData('user',array('id'=>$_REQUEST['user_from']),array('single'));

				$ios_token = $user_data_to['ios_token'];
				$android_token = $user_data_to['android_token'];
				$user_data_from_name = $user_data_from['name']; 
				$message_push = $user_data_from_name." Sent You a Message";
				$title = "chat";
				$type = "chat";
				

				$msg_notification = array
					(
					'body' 	=> $message_push,
					'title'	=> $title,
					'icon'	=> 'ic_stat_wholesaler',
					'sound' => '',
					'color' => '',
					'badge' => 1,
					// 'click_action' => 'com.mycoach.content_manager.activity.ContentDetailActivity'
					);
				

				if($ios_token != ""){

					

					$messages_push = array('notification'=>$msg_notification,'notification_type'=>$type,"message_send"=>$message_send_notification,"message_staus"=>$message_type_notification,"last_msg"=>$last_msg);		
					
					$this->push_iOS($ios_token,$messages_push);

					
				}
				else if($android_token != "")
				{
					
					$messages_push = array('notification'=>$msg_notification,'notification_type'=>$type,"message_send"=>$message_send_notification,"message_staus"=>$message_type_notification,"last_msg"=>$last_msg);	

					$registatoin_id = array($android_token); 
					$this->send_notification($registatoin_id, $messages_push);

				}

				// notification end
		}
		else
		{
			$message = false;
		}

		if($message){

			$this->response(true,$message,array("last_msg" => $last_msg));		

		}else{

			$this->response(false,$message,array("last_msg" => $last_msg));		

		}		 	

		}

		else

		{

			$this->response(false,'Missing Parameter');	

		}

	}



	function group_message()
	{
		if(!empty($_REQUEST['user_id']))
		{
				$user_id = $_REQUEST['user_id'];
				$message = $_REQUEST['message'];
			
					$image = $this->common->do_upload_file('image','./assets/chat/');
					if(isset($image['upload_data']))
					{
						$msg_image = $image['upload_data']['file_name'];
						$msg = base_url('/assets/chat/'.$msg_image);
						$post['image']=$image['upload_data']['file_name'];
						$message_send_notification = base_url('/assets/chat/'.$msg_image);
						
					}
					
				$post['user_id'] = $user_id;
				$post['message'] = $message;
				$post['created_at'] = date('Y-m-d H:i:s');
				$post['user_type'] = $_REQUEST['user_type'];
				$result = $this->common->insertData('group_message_tbl',$post);
				$insert_id = $this->db->insert_id();

				$group_message_detail = $this->common->getData('group_message_tbl',array('id'=>$insert_id),array('single'));

				$my_message = $group_message_detail['message'];
				if(!empty($group_message_detail['image']))
				{
					$my_image = base_url('/assets/chat/'.$group_message_detail['image']);	
				}
				else
				{
					$my_image ="";
				}

				
				if($_REQUEST['user_type'] == 1)
				{
					$user_list = $this->common->getData('user',array('user_type'=>1));
				}


				if($_REQUEST['user_type'] == 2)
				{
					$user_list = $this->common->getData('user',array('user_type'=>2));
				}


				if($_REQUEST['user_type'] == 3)
				{
					$user_list = $this->common->getData('user',array('user_type'=>3));

				}

				if($_REQUEST['user_type'] == 4)
				{
					$user_list = $this->common->getData('user');
				}



				
				

				foreach ($user_list as $key => $value)
        		{
        			// notification start


        			$ios_token =$value['ios_token'];
					$android_token = $value['android_token'];
					$today = Date('Y-m-d H:i:s');
					

					$user_data = $this->common->getData('user',array('id'=>$user_id),array('single'));
					$user_data_name = $user_data['name']; 

					if(!empty($user_data['image']))
                 	{
                 		$user_data_image = base_url('/assets/userfile/profile/'.$user_data['image']);
                 	}
                 	else
                 	{
                 		$user_data_image = '';
                 	}
					$message_push = $user_data_name." Sent You a broadcast Message";
					$title = "Broadcast Message";
					$type = "Broadcast Message";

					$msg_notification = array
					(
					'body' 	=> $message_push,
					'title'	=> $title,
					'icon'	=> 'ic_stat_wholesaler',
					'sound' => '',
					'color' => '',
					'badge' => 1,
					// 'click_action' => 'com.mycoach.content_manager.activity.ContentDetailActivity'
					);
				
					$last_msg =  array("id" => $insert_id,
									"message" => $my_message,
									"image" => $user_data_image,
									"created_at" => $post['created_at'],
									"user_id" => $user_id,
									"user_name" => $user_data_name,
									"user_image" => $user_data_image);

				


					if($ios_token != "")
					{
						$messages_push = array('notification'=>$msg_notification,'notification_type'=>$type,"message_send"=>$message_send_notification,"message_staus"=>$message_type_notification,"group_msg"=>$last_msg);		
						$this->push_iOS($ios_token,$messages_push);
					}
					else if($android_token != "")
					{
					
						$messages_push = array('notification'=>$msg_notification,'notification_type'=>$type,"last_msg"=>$last_msg);	

						$registatoin_id = array($android_token); 
						$this->send_notification($registatoin_id, $messages_push);

					}
					// notification end
        		}
				
				$this->response(true,"message sent successfully");
		}
		else
		{
			$this->response(false,"Missing parameter");
		}	
	}


	public function get_event()
	{
		if(!empty($_REQUEST['get_type']) && !empty($_REQUEST['user_latitude']) && !empty($_REQUEST['user_longitude']))
        {

        	$user_latitude = $_REQUEST['user_latitude'];
        	$user_longitude = $_REQUEST['user_longitude'];
        	if($_REQUEST['get_type'] == 1)
        	{
        	

        		$where = 'SE.event_user_type = 1 AND SE.status = 0 AND SG.status = 0';
				$result = $this->common->get_eventList_by_lat($where,$user_latitude,$user_longitude);

        	}
        	else
        	{
        		$where = 'SE.status = 0 AND SG.status = 0';
        		$result = $this->common->get_eventList_by_lat($where,$user_latitude,$user_longitude);
        	}

        	
		$arr=array();
		$i=0;
		foreach ($result as $key => $value) {
			if(!empty($value['game_image']))
			{
				$game_image = base_url('/assets/Game/gamelogo/'.$value['game_image']);
			}
			else
			{
				$game_image = "";
			}


			if(!empty($value['event_image']))
			{
				$event_image = base_url('/assets/event/image/'.$value['event_image']);
			}
			else
			{
				$event_image = "";
			}
			
			
		$arr[$i]=array('id'=>$value['id'],'title'=>$value['title'],'game_id'=>$value['game_id'],'event_user_type'=>$value['event_user_type'],'event_time'=>$value['event_time'],'event_duration'=>$value['event_duration'],'event_participant_no'=>$value['event_participant_no'],'price'=>$value['price'],'event_description'=>$value['event_description'],'status'=>$value['status'],'game_name'=>$value['game_name'],'game_image'=>$game_image,'event_image'=>$event_image,'latitude'=>$value['latitude'],'longitude'=>$value['longitude'],'event_address'=>$value['event_address'],'distance'=>$value['distance']);

			if($value['event_user_type']==2)
				{
					$arr[$i]['user_id']=$value['user_id'];
					$userinfo = $this->common->getData('user',array('id'=>$value['user_id']),array('single'));



					$arr[$i]['user_name'] = $userinfo['name'];
					$arr[$i]['user_email'] = $userinfo['email'];
				
				}
				$i++;
		}

	
		if($result){
			$this->response(true,"Event fetch Successfully.",array("eventinfo" => $arr));			
		}else{
			$this->response(false,"There is a problem, please try again.",array("userinfo" => ""));
		}
		}
		else
		{
			$this->response(false,"Missing parameter");
		}			
		
		

	}
	


	public function chatlist()
	{
		$where = "user_from = '".$_POST['id']."' or user_to = '".$_POST['id']."'";
		$result = $this->common->getData('chat',$where,array('sort_by'=>'created_at','sort_direction' => 'desc'));
		$user_id = $user = array();
		
		if(!empty($result))
		{			
			foreach ($result as $key => $value) 
			{					
				if($value['user_from'] == $_POST['id'])
				{
					if (!in_array($value['user_to'], $user_id))
					{
				  		$userinfo = $this->common->getData('user',array('id' => $value['user_to']),array('single'));
				  
						$user_id[] = $value['user_to'];
					}
					
				}
				else
				{
					if (!in_array($value['user_from'], $user_id))
				  	{
				  		$userinfo = $this->common->getData('user',array('id' => $value['user_from']),array('single'));
				  		
							$user_id[] = $value['user_from'];
												
					}
				}				
			}
		} 

		
		
		if(!empty($user_id))
		{



			foreach ($user_id as $key => $value) 
			{
				$result1 = array();
				$userdetail = array();
				
				$userinfo = $this->common->getData('user',array('id'=> $value),array('single'));
				
				if(!empty($userinfo['image']))
                {
                 	$image = base_url('/assets/userfile/profile/'.$userinfo['image']);
                }
                else
                {
                 	$image = '';
                }
				
				$where_user = "(user_from='".$value."' and user_to='".$_POST['id']."') or (user_to='".$value."' and user_from='".$_POST['id']."')";

				$result_user= $this->common->getData('chat',$where_user,array('single','field' => 'message,created_at,id','sort_by' =>'id' , 'sort_direction' => 'desc'));
				


				$result1[] = array('message'=>$result_user['message'],'created_at'=>$result_user['created_at'] ,'image' => $image,'id'=>$result_user['id'],'user_id' =>$value);
				
				if(!empty($result1))
				{

				foreach($result1 as $value)
				{
					if(!empty($value['message']))
					{
					 	$msg = json_decode($value['message']);
					 	preg_match('/\.[^\.]+$/i',$msg,$ext);
					 	
					 	if(!empty($ext))
					 	{
					 		$ext = $ext[0];
					 	}
						else
						{
						 	 $ext = "";
						}
				
                        $type=Array(1 => '.jpg', 2 => '.jpeg', 3 => '.png', 4 => '.gif',5 => '.3gp',6 => '.mp4',7 => '.avi',8 =>'.wmv');
                            
                        if(!(in_array($ext,$type)))
                        {
                            $message=$msg;	
                            $message_staus = 2;
                        }
                        else 
                        {
                            $message = base_url('/assets/chat/'.$msg);	
                            $message_staus = 1;
                        }

                    }
                    else
                   {
                       $message="";	
                       $message_staus ="";
                   }

					$created_at = $value['created_at'];
				  	$image = $value['image'];
				  	$id = $value['id'];
				  	$user_id = $value['user_id'];
				  
				 	$userdetail[] = array('message'=>$message,'created_at'=>$created_at,'image' => $image,'message_staus'=>$message_staus,'id'=>$id,'user_id'=>$user_id);
				}

				$data_array_new = $this->array_sort($userdetail,'id', SORT_DESC);
				
				


				foreach ($data_array_new as $key => $value) 
				{
					$user_info = $this->common->getData('user',array('id' => $value['user_id']),array('single'));
					$user[]= array('message'=>$value['message'],'created_at'=>$value['created_at'],'image' => $value['image'],'message_staus'=>$value['message_staus'],'id'=>$value['id'],'user_id'=>$value['user_id'],'name'=>$user_info['name']);
				}

				
				

			}

		}
			
		}

		
				
		
		if($user)
		{
			$this->response(true,$user);		
		}
		else
		{
			$this->response(true,array());		
		}	
	}





	function array_sort($array, $on, $order=SORT_ASC){

    $new_array = array();
    $sortable_array = array();

    if (count($array) > 0) {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $k2 => $v2) {
                    if ($k2 == $on) {
                        $sortable_array[$k] = $v2;
                    }
                }
            } else {
                $sortable_array[$k] = $v;
            }
        }

        switch ($order) {
            case SORT_ASC:
                asort($sortable_array);
                break;
            case SORT_DESC:
                arsort($sortable_array);
                break;
        }

        foreach ($sortable_array as $k => $v) {
            $new_array[$k] = $array[$k];
        }
    }

    return $new_array;
}




	public function chatHistory()
	{
		$where = '(user_from = '.$_POST['id'].' AND user_to = '.$_POST['uid'].') OR (user_from = '.$_POST['uid'].' AND user_to = '.$_POST['id'].')';

		$result = $this->common->getData('chat',$where,array('sort_by'=>'created_at','sort_direction' => 'asc'));
		
		if(!empty($result))
		{
			foreach($result as $value)
			{
				if(!empty($value['message']))
				{
					$msg = json_decode($value['message']);
					preg_match('/\.[^\.]+$/i',$msg,$ext);

					if(!empty($ext))
					{
						$ext = $ext[0];
					}
					else
					{
						$ext = "";
					}

					$type=Array(1 => '.jpg', 2 => '.jpeg', 3 => '.png', 4 => '.gif',5 => '.3gp',6 => '.mp4',7 => '.avi',8 =>'.wmv');

                    if(!(in_array($ext,$type)))
                    {
						$value['message']=$msg;	
						$value['message_staus'] = 2;
					}
					else 
					{
						$value['message']=base_url('/assets/chat/'.$msg);	
						$value['message_staus'] = 1;
					}
				}
				else
				{
					$value['message']="";	
					$value['message_staus'] ="";
				}

				$arr_chat[]=array('id'=>$value['id'],'user_from'=>$value['user_from'],'user_to'=>$value['user_to'],'message'=>$value['message'],'message_staus' => $value['message_staus'],'created_at'=>$value['created_at']);
			}
		
			$user = $this->common->getData('user',array('id' => $_POST['uid']),array('single','field' => 'name,image'));

			if($user)
			{
				$arr_chat = $arr_chat ? $arr_chat : array();
				if(!empty($user['image']))
				{
					$image = base_url('/assets/userfile/profile/'.$user['image']);

	            }
				else
				{
					$image = '';

	            }
				
				$this->response(true,$arr_chat,array("name" => $user['name'],"image" => $image));		

			}
			else
			{
				$this->response(true,array());		
			}	

		}
		else
		{
			$this->response(true,array());	
		}		 
	}



	// public function searchUser()
	// {
	// 	$user = $this->common->searchUser($_POST);
	// 	$this->findUser($user);
	// }
	
	public function contactUs()
	{
		$message = '<h4>'.$_POST['name'].'</h4><p>'.$_POST['message'].'</p>';
		$mail = $this->common->sendMail('devendra@mailinator.com','Contact Us',$message,array('fromEmail'=>$_POST['email']));
		$mail_msg = $mail ? 'Email send successfully' : 'Email not send. Please send again';
		$this->response($mail,$mail_msg);	
	}
	
	public function report()
	{		
		$_POST['created_at'] = date('Y-m-d H:i:s');
		$post = $this->common->getData('post',array('id' => $_POST['post_id']),array('single'));
		$user = $this->common->getData('user',array('id'=> $post['uid']),array('single','field'=>'email,name'));
		$post1 = $this->common->getField('report',$_POST);
		$report = $this->common->insertData('report',$post1);
		$mail = false;
		if($report){
			//$this->checkMail();
			$message = "Hello Administrator <br> One post <a href='".base_url('api/postDetail/'.$post['id'])."'>".$post['title']."</a> is reported. We will delete your post if found inappropriate. <br>".$_POST['comment'];

			$mail = $this->common->sendMail("info@positivenetwork.com.au",'Report on your post',$message);
		}
		$response = $this->response($mail,"Reported Successfully");		
	}
	
}