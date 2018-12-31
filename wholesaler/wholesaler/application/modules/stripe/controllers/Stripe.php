<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stripe extends Base_Controller {

	public function __construct()
	{
		parent:: __construct();		
		$this->load->helper('common');
	}

	 function index()
	 {
	 	$this->load->view('index');
     }


     function submit()
    {
      
      if(!empty($_POST['stripeToken']))
      {
        //get token, card and user info from the form
        echo $token  = $_POST['stripeToken'];
	   }
	}



	public function addCard()
    {           
        require_once APPPATH."third_party/stripe/init.php";
        //set api key in above file
                
        \Stripe\Stripe::setApiKey($stripe['secret_key']);
        
       	$token = $_REQUEST['stripeToken'];
        $user = $this->common->getData('user',array('id'=>$_REQUEST['user_id']),array('single'));
            
        try
        {
            $bank = $this->common->getData('account_detail',array('user_id'=>$_REQUEST['user_id']),array('single'));
            if(!empty($bank) && $bank['card_id'] == "")
            {
				$acct = \Stripe\Customer::create(array(
                        "card" => $token,
                        "description" => "Description",
                        "email" => $user['email']
                    ));
				
				$this->common->updateData('account_detail',array('card_id' => $acct->id,'card_holder'=>$_REQUEST['card_holder']),array('user_id'=>$_REQUEST['user_id']));
				$card_id = $acct->id;
			}

            
            if(!empty($bank) && $bank['card_id'] != "")
            {
                $customer = \Stripe\Customer::retrieve($bank['card_id']);
				$src = $customer->sources->create(array("source" => $token));
                $this->common->updateData('account_detail',array('card_holder'=>$_REQUEST['card_holder']),array('user_id'=>$_REQUEST['user_id']));
                $card_id = $src->id;
      		}
            
            if(empty($bank))
            {
                $acct = \Stripe\Customer::create(array(
                        "card" => $token,
                        "description" => "Description",
                        "email" =>$user['email']
                    ));
                
                $stripe_bank_token = array('user_id' => $_REQUEST['user_id'],'card_id' => $acct->id,'card_holder' => $_REQUEST['card_holder']);

                $card_id = $acct->id;
                $this->common->insertData('account_detail',$stripe_bank_token);                
            }

           	$result = $card_id;
        }
        catch (Exception $e)
        {
            $errormsg= "Card information wrong ". $e->getMessage();
            $result = $errormsg;
             $this->response(false,$errormsg); 
             die();
        } 


        $this->response(true,"Card Added Successfully");
        return $result;
    }



    public function card_list()
    {        
        $bank = $this->common->getData('account_detail',array('user_id' => $_REQUEST['user_id']),array('single'));
		
		$card_detail = $payment = array(); 
        
        if($bank)
        {
            require_once APPPATH."third_party/stripe/init.php";
            //set api key in above file
                        
            \Stripe\Stripe::setApiKey($stripe['secret_key']);           

          	if($bank['card_id'] != "")
          	{
                $card = \Stripe\Customer::retrieve($bank['card_id'])->sources->all(array('object' => 'card')); 
                $cards=$card['data'];

                
			    if(!empty($cards))
        		{
        		 	foreach($cards as $card)
        		 	{
        		 		$card_detail[] =array(
                                'card_id' => $card['id'],
                                'exp_month' => $card['exp_month'],
                                'exp_year' => $card['exp_year'],
                                'last4' => $card['last4'],
                                'cust_id' => $card['customer'],                                
                                'card_holder' => $bank['card_holder']            
                            );  
        		 	}

        		 	$this->response(true,"Bank detail fetch successfully.",array("card_detail"=>$card_detail));

        		}
        		else
        		{
        			$this->response(false,"No card found",array("card_detail"=>$card_detail));
        		}

            	
        	}
	        else
	        {
	             $this->response(false,"no card found");
	        }
   		}
   		else
   		{
   			$this->response(false,"No card found");
   		}
   	}


   	public function retrieve_card()
	{
    	$cardid = $_REQUEST['cardid'];
    	$user_id = $_REQUEST['user_id'];

    	$account_info = $this->common->getData('account_detail',array('user_id' => $_REQUEST['user_id']),array('single')); 

    	require_once APPPATH."third_party/stripe/init.php";
        //set api key in above file
                
        \Stripe\Stripe::setApiKey($stripe['secret_key']);

    	if(!empty($account_info))
    	{

    		try
    		{
				$customer = \Stripe\Customer::retrieve($account_info['card_id']);
				$card_detail = $customer->sources->retrieve($cardid);
 			}
 			catch (Exception $e)
 			{
                // echo "Couldn't create customer id...";
                // echo $e->getMessage();
                $errormsg= $e->getMessage();
                $this->response(false,$errormsg);
            } 

           
            $this->response(true,"card detail fetch successfully",array("card_detail"=>$card_detail));
    	}
    	else
    	{
    		$this->response(false,"no card found");
    	}
	} 



	public function remove_card()
    {
    	$cardid = $_REQUEST['cardid'];
    	$user_id = $_REQUEST['user_id'];

    	$account_info = $this->common->getData('account_detail',array('user_id' => $_REQUEST['user_id']),array('single')); 

    	require_once APPPATH."third_party/stripe/init.php";
        //set api key in above file
                
        \Stripe\Stripe::setApiKey($stripe['secret_key']);

    	if(!empty($account_info))
    	{

    		try
    		{
				$customer = \Stripe\Customer::retrieve($account_info['card_id']);
                $customer->sources->retrieve($cardid)->delete();
 			}
 			catch (Exception $e)
 			{
                echo "Couldn't create customer id...";
                echo $e->getMessage();
                $errormsg= $e->getMessage();
                $this->response(true,$errormsg);
            } 

            $card_detail = $this->cardlist_function($user_id);

            if(!empty($card_detail))
            {
            	 $this->response(true,"Delete card successfully",array("card_detail"=>$card_detail));
            }
            else
            {
            	$this->response(false,"No card found",array("card_detail"=>$card_detail));
            }
           
    	}
    	else
    	{
    		$this->response(false,"no card found");
    	}
	}


	public function updateCard()
    {      

     	require_once APPPATH."third_party/stripe/init.php";
        //set api key in above file
                
        \Stripe\Stripe::setApiKey($stripe['secret_key']);  

        $token = $_REQUEST['stripeToken']; 
        $user_id = $_REQUEST['user_id'];
        $cardid = $_REQUEST['cardid'];
        $card_holder = $_REQUEST['card_holder'];

       
        $user = $this->common->getData('user',array('id'=>$user_id),array('single'));
       	 
          $bank = $this->common->getData('account_detail',array('user_id'=>$user_id),array('single'));
   
           
            try{  
                    
                    if(!empty($bank) && $bank['card_id'] == ""){
                        $acct = \Stripe\Customer::create(array(
                            "card" => $token,
                            "description" => "Description",
                            "name"=>"amar"
                        ));
                        
                       
          
                        $this->common->updateData('account_detail',array('card_id' => $acct->id,'card_holder'=>$card_holder),array('user_id'=>$user_id));                             
                    }
                    if(!empty($bank) && $bank['card_id'] != ""){

                        $customer = \Stripe\Customer::retrieve($bank['card_id']);
                        if($customer->default_source != null ){
                			$customer->sources->retrieve($cardid)->delete();                
                        }
                        $src = $customer->sources->create(array("source" => $token));
                        
                       $this->common->updateData('account_detail',array('card_holder'=>$card_holder),array('user_id'=>$user_id));
               			 $card_id = $src->id;                
                    }
                    if(empty($bank)){
                         $acct = \Stripe\Customer::create(array(
                            "card" => $token,
                            "description" => "Description",
                            "email" => $user->email
                        ));
                        $stripe_bank_token = array('user_id' => $this->user_id,'card_id' => $acct->id);
                        
                        $card_id = $acct->id;
               			 $this->common->insertData('account_detail',$stripe_bank_token); 
                        
                    }
               

            }catch (Exception $e){
                $errormsg= "Card information wrong ". $e->getMessage();
				$this->response(false,$errormsg); 
            }

            $this->response(true,"card update successfully");


    }





	public function cardlist_function($user_id)
    {        
        $bank = $this->common->getData('account_detail',array('user_id' => $user_id),array('single'));
		
		$card_detail = $payment = array(); 
        
        if($bank)
        {
            // require APPPATH."third_party/stripe/init.php";
            // //set api key in above file
                        
            // \Stripe\Stripe::setApiKey($stripe['secret_key']);
                    

          	if($bank['card_id'] != "")
          	{
                $card = $customer = \Stripe\Customer::retrieve($bank['card_id'])->sources->all(array('object' => 'card')); 
                $cards=$card['data'];

                
			    if(!empty($cards))
        		{
        		 	foreach($cards as $card)
        		 	{
        		 		$card_detail[] =array(
                                'card_id' => $card['id'],
                                'exp_month' => $card['exp_month'],
                                'exp_year' => $card['exp_year'],
                                'last4' => $card['last4'],
                                'cust_id' => $card['customer'],                                
                                'card_holder' => $bank['card_holder']            
                            );  
        		 	}
        		}

            	
            	return $card_detail;
        	}
	        else
	        {
	        	 $card_detail = array();
	             return $card_detail;
	        }
   		}
   		else
   		{
   			$card_detail = array();
	        return $card_detail;
   		}
   	}




   	public function addCard_function($user_id,$stripeToken,$card_holder)
    {           
        require_once APPPATH."third_party/stripe/init.php";
        //set api key in above file
                
        \Stripe\Stripe::setApiKey($stripe['secret_key']);
        
       	$token = $stripeToken;
        $user = $this->common->getData('user',array('id'=>$user_id),array('single'));
            
        try
        {
            $bank = $this->common->getData('account_detail',array('user_id'=>$user_id),array('single'));
            if(!empty($bank) && $bank['card_id'] == "")
            {
				$acct = \Stripe\Customer::create(array(
                        "card" => $token,
                        "description" => "Description",
                        "email" => $user['email']
                    ));
				
				$this->common->updateData('account_detail',array('card_id' => $acct->id,'card_holder'=>$card_holder),array('user_id'=>$user_id));
				$card_id = $acct->id;
			}

            
            if(!empty($bank) && $bank['card_id'] != "")
            {
                $customer = \Stripe\Customer::retrieve($bank['card_id']);
				$src = $customer->sources->create(array("source" => $token));
                $this->common->updateData('account_detail',array('card_holder'=>$card_holder),array('user_id'=>$user_id));
                $card_id = $src->id;
      		}
            
            if(empty($bank))
            {
                $acct = \Stripe\Customer::create(array(
                        "card" => $token,
                        "description" => "Description",
                        "email" =>$user['email']
                    ));
                
                $stripe_bank_token = array('user_id' => $user_id,'card_id' => $acct->id,'card_holder' => $card_holder);

                $card_id = $acct->id;
                $this->common->insertData('account_detail',$stripe_bank_token);                
            }

           	$result = $card_id;
        }
        catch (Exception $e)
        {
            $errormsg= "Card information wrong ". $e->getMessage();
            $result = $errormsg;
        } 

        return $result;
    }




    public function addBank()
    { 
  		
  		$account_info = $this->common->getData('account_detail',array('user_id'=>$_REQUEST['user_id']),array('single'));

  		$user_detail = $this->common->getData('user',array('id'=>$_REQUEST['user_id']),array('single'));



        if(!empty($account_info['stripe_account']))
        {
          	try 
          	{
          		require_once APPPATH."third_party/stripe/init.php";
	        	//set api key in above file
	                
	        	\Stripe\Stripe::setApiKey($stripe['secret_key']);

  			   
  			    $account = \Stripe\Account::retrieve($account_info['stripe_account']);
           		$token = \Stripe\Token::create(array(
                                          "bank_account" => array(
  										                    "country" => "US",
                                          "currency" => "USD",
                                          "routing_number" => trim($_REQUEST['sort_code']),
                                          "account_number" => trim($_REQUEST['acc_number']),
  										 "account_holder_name" => trim($_REQUEST['account_name']),
  										  "account_holder_type" =>'individual'
  										
  										)
                  )) ;
          		
          		$account->external_accounts->create(array("external_account" => $token));	
          		$account->save();
        	}
	        catch (Exception $e) 
	        {
	           $errormsg= "Bank details wrong ". $e->getMessage();
	          	
	          	$this->response(false, $errormsg);
	          	die();
	        }

	        $message = "Add Bank account Successfully";
	        $bank_list = $this->bankAccountList_function($_REQUEST['user_id']);
			$this->response(true, $message,array("bank_list"=>$bank_list));
  		}
    	else
    	{
    		require_once APPPATH."third_party/stripe/init.php";
	        //set api key in above file
	                
	        \Stripe\Stripe::setApiKey($stripe['secret_key']);
  		
	  		try 
	        {
	            $user_detail['dob']= $user_detail['dob'];
	            $fname = $user_detail['name'];
	            $lname = "";
	            $doctor_dob_n =strtotime($user_detail['dob']);
	            $doctor_dob = date('Y/m/d',$doctor_dob_n);
	           	$dob_year =   date('Y', strtotime($doctor_dob));
	            $dob_month =   date('m', strtotime($doctor_dob));
	            $dob_day =    date('d', strtotime($doctor_dob));
	  						 
	  			$acct = \Stripe\Account::create(array(
	                                      "type" => "custom",
	                                      "country" => "US",
	                                      "external_account" => array(
	                                          "object" => "bank_account",
	                                          "country" => "US",
	                                          "currency" => "USD",
	                                          "routing_number" => trim($_REQUEST['sort_code']),
	                                          "account_number" => trim($_REQUEST['acc_number']),
	  										                    "account_holder_name" => trim($_REQUEST['account_name']),
	  										                    "account_holder_type" =>'individual'
	                                      ),
	                                      "legal_entity[type]" => "individual",
	                                      "legal_entity[first_name]" => $fname,
	                                      "legal_entity[last_name]" => $lname,
	                                      "legal_entity[address][city]" => trim($_REQUEST['city']),
	  									                  "legal_entity[address][state]" => trim($_REQUEST['state']),
	                                      "legal_entity[address][line1]" => trim($_REQUEST['address_line1']),
	                                      "legal_entity[address][line2]" =>  $_REQUEST['address_line2'],
	                                      "legal_entity[address][postal_code]" => trim($_REQUEST['post_code']), 
	                                      "legal_entity[dob][day]" => $dob_day,
	                                      "legal_entity[dob][month]" => $dob_month,
	                                      "legal_entity[dob][year]" => $dob_year,
	                                       "legal_entity[ssn_last_4]" => $_REQUEST['ssn_last_4'],
	                                      "tos_acceptance" => array(
	                                          "date" => time(),
	                                          "ip" => $_SERVER['REMOTE_ADDR']
	                                      )
	                          ));

  						
	  			$stripe_data = array('stripe_account' => $acct->id,'user_id'=>$_REQUEST['user_id']);
				$result = $this->common->insertData('account_detail',$stripe_data);

				

				$bank_list = $this->bankAccountList_function($_REQUEST['user_id']);
				$message = "Add Bank account Successfully";
				$this->response(true, $message,array("bank_list"=>$bank_list));
	        } 
          	catch (Exception $e)
          	{
              	$errormsg= "". $e->getMessage();
              	$this->response(false, $errormsg);
              	die();
            } 
  		}
  	}


  	public function bankAccountList($user_id)
    {        
        $bank = $this->common->getData('account_detail',array('user_id' => $user_id),array('single'));
		
		$bank_list = $payment = array(); 
        
        if($bank)
        {
            // require_once APPPATH."third_party/stripe/init.php";
            // //set api key in above file
                        
            // \Stripe\Stripe::setApiKey($stripe['secret_key']);           

          	if($bank['stripe_account'] != "")
          	{
               	$Bankaccount=	\Stripe\Account::retrieve($bank['stripe_account'])->external_accounts->all(array('object' => 'bank_account'));

                $banks = $Bankaccount->data;

               

                
			    if(!empty($banks))
        		{
        		 	foreach($banks as $bank)
        		 	{
        		 		$bank_list[] =array(
        		 				'bank_account_id' => $bank->id,
                                'last4' => 'XXXX XXXX XXXX '.$bank->last4,
                                'bank_name' => $bank->bank_name,
                                'account_holder_name' => $bank->account_holder_name,
                                'account_holder_type' => $bank->account_holder_type,
                                'default_for_currency' => $bank->default_for_currency,
                             
                            );  
        		 	}
        		}

            	return $bank_list;
        	}
	        else
	        {
	             $bank_list = array();
	             return $bank_list;
	        }
   		}
   		else
   		{
   			 $bank_list = array();
	         return $bank_list;
   		}
   	}


   	public function bank_account_list()
    {        
        $bank = $this->common->getData('account_detail',array('user_id' => $_REQUEST['user_id']),array('single'));
		
		$card_detail = $payment = array(); 
        
        if($bank)
        {
            require_once APPPATH."third_party/stripe/init.php";
            //set api key in above file
                        
            \Stripe\Stripe::setApiKey($stripe['secret_key']);           

          	if($bank['stripe_account'] != "")
          	{
               	$Bankaccount=	\Stripe\Account::retrieve($bank['stripe_account'])->external_accounts->all(array('object' => 'bank_account'));

                $banks = $Bankaccount->data;

               

                
			    if(!empty($banks))
        		{
        		 	foreach($banks as $bank)
        		 	{
        		 		$bank_list[] =array(
        		 				'bank_account_id' => $bank->id,
                                'last4' => 'XXXX XXXX '.$bank->last4,
                                'bank_name' => $bank->bank_name,
                                'account_holder_name' => $bank->account_holder_name,
                                'account_holder_type' => $bank->account_holder_type,
                                'default_for_currency' => $bank->default_for_currency,
                             
                            );  
        		 	}
        		}

            	$this->response(true,"Bank List fetch successfully.",array("bank_list"=>$bank_list));
        	}
	        else
	        {
	             $this->response(true,"no card found");
	        }
   		}
   		else
   		{
   			$this->response(true,"firstly add card in bank");
   		}
   	}



   	public function remove_bank_account()
    {
    	$bank_account_id = $_REQUEST['bank_account_id'];
    	$user_id = $_REQUEST['user_id'];

    	$account_info = $this->common->getData('account_detail',array('user_id' => $_REQUEST['user_id']),array('single')); 

    	require_once APPPATH."third_party/stripe/init.php";
        //set api key in above file
                
        \Stripe\Stripe::setApiKey($stripe['secret_key']);

    	if(!empty($account_info))
    	{

    		try
    		{

                $account = \Stripe\Account::retrieve($account_info['stripe_account']);
                $account->external_accounts->retrieve($bank_account_id)->delete();
 			}
 			catch (Exception $e)
 			{
               
                $errormsg= $e->getMessage();
                $this->response(false,$errormsg);
                die();
            } 

            $bank_list = $this->bankAccountList_function($user_id);
            $this->response(true,"Delete bank account successfully",array("bank_list"=>$bank_list));
    	}
    	else
    	{
    		$this->response(false,"no bank account found");
    	}
    }



    public function update_bank_account()
    { 
  		
  		$bank_account_id = $_REQUEST['bank_account_id'];
  		$account_info = $this->common->getData('account_detail',array('user_id'=>$_REQUEST['user_id']),array('single'));

  		$user_detail = $this->common->getData('user',array('id'=>$_REQUEST['user_id']),array('single'));



        if(!empty($account_info['stripe_account']))
        {
          	try 
          	{
          		require_once APPPATH."third_party/stripe/init.php";
	        	//set api key in above file
	                
	        	\Stripe\Stripe::setApiKey($stripe['secret_key']);

  			   
  			    $account = \Stripe\Account::retrieve($account_info['stripe_account']);
           		$token = \Stripe\Token::create(array(
                                          "bank_account" => array(
  										                    "country" => "US",
                                          "currency" => "USD",
                                          "routing_number" => trim($_REQUEST['sort_code']),
                                          "account_number" => trim($_REQUEST['acc_number']),
  										 "account_holder_name" => trim($_REQUEST['account_name']),
  										  "account_holder_type" =>'individual'
  										
  										)
                  )) ;
          		
          		$acc = $account->external_accounts->create(array("external_account" => $token));	
          		$account->save();

          		
          		// default new bank account
          		 $account = \Stripe\Account::retrieve($account_info['stripe_account']);
				 $bank_account = $account->external_accounts->retrieve($acc->id);
              	 $bank_account->default_for_currency = true;
              	 $bank_account->save();
				
				// default new bank account
              	
          	    //delete bank account
				$account->external_accounts->retrieve($bank_account_id)->delete();
        	}
	        catch (Exception $e) 
	        {
	           $errormsg= "Bank details wrong ". $e->getMessage();
	          	
	          	$this->response(false, $errormsg);
	          	die();
	        }
			
			 $bank_list = $this->bankAccountList($_REQUEST['user_id']);
			

			$message = "Update Bank account Successfully";
	       	$this->response(true, $message,array("bank_list"=>$bank_list));
  		}
  		else
  		{
  			$message = "please create bank account first";
	       	$this->response(false, $message);
  		}
    	
  	}


  	public function default_bank_account()
   	{
   		$bank_account_id = $_REQUEST['bank_account_id'];
   		$account_info = $this->common->getData('account_detail',array('user_id' => $_REQUEST['user_id']),array('single'));

   		 if($account_info)
        {
            require_once APPPATH."third_party/stripe/init.php";
            //set api key in above file
                        
            \Stripe\Stripe::setApiKey($stripe['secret_key']);  


            $account = \Stripe\Account::retrieve($account_info['stripe_account']);
			$bank_account = $account->external_accounts->retrieve($bank_account_id);
            $bank_account->default_for_currency = true;
            $bank_account->save();

            $bank_list = $this->bankAccountList($_REQUEST['user_id']);
            $message = "Default bank account successfully";
	       	$this->response(true, $message,array("bank_list"=>$bank_list));


   		}
   		else
  		{
  			$message = "please create bank account first";
	       	$this->response(false, $message);
  		}
   }



  	public function bankAccountList_function($user_id)
    {        
        $bank = $this->common->getData('account_detail',array('user_id' => $user_id),array('single'));
		
		$bank_list = $payment = array(); 
        
        if($bank)
        {
            // require_once APPPATH."third_party/stripe/init.php";
            // //set api key in above file
                        
            // \Stripe\Stripe::setApiKey($stripe['secret_key']);           

          	if($bank['stripe_account'] != "")
          	{
               	$Bankaccount=	\Stripe\Account::retrieve($bank['stripe_account'])->external_accounts->all(array('object' => 'bank_account'));

                $banks = $Bankaccount->data;

               

                
			    if(!empty($banks))
        		{
        		 	foreach($banks as $bank)
        		 	{
        		 		$bank_list[] =array(
        		 				'bank_account_id' => $bank->id,
                                'last4' => 'XXXX XXXX XXXX '.$bank->last4,
                                'bank_name' => $bank->bank_name,
                                'account_holder_name' => $bank->account_holder_name,
                                'account_holder_type' => $bank->account_holder_type,
                                'default_for_currency' => $bank->default_for_currency,
                             
                            );  
        		 	}
        		}

            	return $bank_list;
        	}
	        else
	        {
	             $bank_list = array();
	             return $bank_list;
	        }
   		}
   		else
   		{
   			 $bank_list = array();
	         return $bank_list;
   		}
   	}



   	public function update_default_card_1($cardid,$user_id)
     { 

		$bank = $this->common->getData('account_detail',array('user_id' => $user_id),array('single'));

		// require_once APPPATH."third_party/stripe/init.php";
	 //        //set api key in above file
	                
	 //    \Stripe\Stripe::setApiKey($stripe['secret_key']);

     	$customer = \Stripe\Customer::retrieve($bank['card_id']);
        $customer->default_source=$cardid;
        $customer->save(); 

        // echo"<pre>";
        // print_r($customer);
        // die();

        
	}





public function add_order()
{
	if(!empty($_REQUEST['cart_id']) && !empty($_REQUEST['address']))
	{

    if($_REQUEST['type'] == 1)
    {
      $card_id = $this->addCard_function($_REQUEST['user_id'],$_REQUEST['token'],$_REQUEST['card_holder']);
    }
    else
    {
       require_once APPPATH."third_party/stripe/init.php";
        //set api key in above file
                
        \Stripe\Stripe::setApiKey($stripe['secret_key']);
        
      $card_id = $_REQUEST['card_id'];

    }
		
		
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

			
		}

		
		$order_info = $this->product_info($cart_id);
	
		$order_info_send =$order_info;
		unset($order_info_send['main_total_amount']);
		
		$i = 1;
		$seller_id_invoice = '';

		$sort_arr = $this->unique_sort($order_info_send, 'seller_id');

		// echo"<pre>";
		// print_r($order_info_send);
		// // die();

		foreach($sort_arr as $sort_value)
    	{
    		$seller_key = 'seller'.$i;
			foreach($order_info_send as $value)
        	{

        		if($value['seller_id'] == $sort_value)
        		{
        			$seller[$seller_key][] = array('cart_id'=>$value['cart_id'],'seller_id'=>$value['seller_id'],'product_name'=>$value['product_name'],'price'=>$value['price'],'order_quantity'=>$value['order_quantity'],'total_price'=>$value['total_price']);
        		}
        	}
        	$i++;
		}

		// seller loop
		foreach($seller as $saller_arr)
    	{
    		$order_id = rand(1111,9999);
    		$cart_id_string="";
			$message_row="";
			$cart_id_data = "";
			$cart_id_array = array();
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
		  		$cart_id_array[] = $saller_value['cart_id'];
		  		$cart_id_data .= $saller_value['cart_id'].",";

    		}

    		$cart_id_string = "'".implode("','", $cart_id_array)."'";
    		$cart_id_data = rtrim($cart_id_data,',');

    		
    		// payment start
    		


    		$this->update_default_card_1($card_id,$_REQUEST['user_id']);

    		$tobank = $this->common->getData('account_detail',array('user_id' => $seller_id_value),array('single'));

    		$tocard = $this->common->getData('account_detail',array('user_id' => $_REQUEST['user_id']),array('single'));

    		// client charge token create
    		$client_charge_token = \Stripe\Token::create(array("customer" =>$tocard['card_id']), array("stripe_account" =>$tobank['stripe_account']));


    	

    		try{   
  				   $itemPrice = $total_price_mail; 
        		   $fee = $itemPrice*10;// admin charges stripe fee 
                   
                   $charge = \Stripe\Charge::create(array(
            			"amount" => $itemPrice*100,
            			"receipt_email" => $_REQUEST['email'],
            			"currency" => 'USD',
            			"source" => $client_charge_token, // $client_charge_token,
            			"application_fee" =>  round($fee),// wholesaler admin fee + remaining amount from cover charges after stripe fee
        ), array("stripe_account" => $tobank['stripe_account'])); //$bank['stripe_account']
        $date = date('Y-m-d H:i:s');


        $data_update['price'] = $product_main_price;
		$data_update['order_id'] = $order_id;
		$data_update['paid_status'] = 1;

		$where_card_id = "`id` IN (".$cart_id_string.")";
		$result = $this->common->updateData('cart_tbl',$data_update,$where_card_id);


		$insert = $this->common->insertData('order_tbl',array('order_id' => $order_id,'address' => $address,'user_from'=>$_REQUEST['user_id'],'user_to'=>$seller_id_value,'created_at' => $date,'amount'=> $itemPrice,'cart_id'=>$cart_id_data,"stripe_receipt"=>$charge->id)); 	


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

    catch (Exception $e) {            
        $errormsg = "Some error occured ". $e->getMessage();
        $this->response(false, $e->getMessage());  
    } 

    	// payment end

	}
    		
		$order_info['order_id'] = $order_id;
		$this->response(true,"Order create successfully");
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

			$product_array[] = array('id'=>$result_product['id'],'product_id'=>$result_product['product_id'],'quantity'=>$result_product['quantity']);

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

						$product_info_payment[] = array('message'=>'Product Info','cart_id'=>$value['id'],'product_id' => $order_product_id,'product_name' => $result_product['product_name'],'seller_id' => $result_product['user_id'],'price' => $result_product['price'],'order_quantity' => $order_quantity,'total_price' => $total_price,'status' => 1);

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






}