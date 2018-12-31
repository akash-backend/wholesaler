<?php
 
	 
	 
	function Apn($deviceToken,$message){  
			$passphrase = '123456';
			$ctx = stream_context_create();
			stream_context_set_option($ctx, 'ssl', 'local_cert', 'cc.pem');
			stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
			$fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
			
			if (!$fp)
				exit("Failed to connect: $err $errstr" . PHP_EOL);
				echo 'Connected to APNS' . PHP_EOL;
			$body['aps'] = array(
				'alert' => $message,
				'badge' => 1,
				'sound' => 'oven.caf',
				); 
			
			$payload = json_encode($body);
			$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
			$result = fwrite($fp, $msg, strlen($msg));
			print_r($result);
			if (!$result)
				'Message not delivered' . PHP_EOL;
			else
				'Message successfully delivered' . PHP_EOL;
			fclose($fp);
}
	 
	
	   $notif_data = array( "msg"=>"12121" );
$rg = Apn('38ed95eddae56ca6517f9f9bf6aa996e0322fc4928aceca5e71372135d4546e1', $notif_data);
       //  print_r($rg);
	  	 
	 
 
?>

