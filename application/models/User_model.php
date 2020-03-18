<?php
/**
 * 
 */
class User_model extends CI_Model 
{
	
	function __construct() 
	{
		parent::__construct();
		$this->load->database();	
	}
	function dateDiffInDays($date1, $date2)  
	{ 
	    $diff = strtotime($date2) - strtotime($date1); 
	    
	    // 1 day = 24 hours 
	    // 24 * 60 * 60 = 86400 seconds 
	    return abs(round($diff / 86400)); 
	} 
	public function dateDiff($date)
	{
		$date1=date('Y-m-d H:i:s');
		
		$hours_in_day   = 24;
		$minutes_in_hour= 60;
		$seconds_in_mins= 60;
		
		$birth_date     = new DateTime($date);
		$current_date   = new DateTime();
		
		$diff           = $birth_date->diff($current_date);
		
		return $years     = $diff->y . " years " . $diff->m . " months " . $diff->d . " day(s)"; echo "<br/>";
		/*echo $months    = ($diff->y * 12) + $diff->m . " months " . $diff->d . " day(s)"; echo "<br/>";
		echo $weeks     = floor($diff->days/7) . " weeks " . $diff->d%7 . " day(s)"; echo "<br/>";
		echo $days      = $diff->days . " days"; echo "<br/>";
		echo $hours     = $diff->h + ($diff->days * $hours_in_day) . " hours"; echo "<br/>";
		echo $mins      = $diff->h + ($diff->days * $hours_in_day * $minutes_in_hour) . " minutest"; echo "<br/>";
		echo $seconds   = $diff->h + ($diff->days * $hours_in_day * $minutes_in_hour * $seconds_in_mins) . " seconds"; echo "<br/>";-*/
	}
	public function get_tiny_url($url)  
	{  
		$ch = curl_init();  
		$timeout = 5;  
		curl_setopt($ch,CURLOPT_URL,'http://tinyurl.com/api-create.php?url='.$url);  
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);  
		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);  
		$data = curl_exec($ch);  
		curl_close($ch);  
		return $data;  
	}
	public function sendSMS($url)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
		curl_setopt($ch, CURLOPT_USERAGENT ,'');
		$output = curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);
		//echo "<pre>";
		//print_r($output);die;
		$output = json_decode($output,true);
		if($output)
		{
			return 1;
		}
		else {
			return 0;
		}
	}
	public function distance($lat1, $lon1, $lat2, $lon2, $unit) 
	{

	  $theta = $lon1 - $lon2;
	  $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
	  $dist = acos($dist);
	  $dist = rad2deg($dist);
	  $miles = $dist * 60 * 1.1515;
	  $unit = strtoupper($unit);
	
	  if ($unit == "K") {
	    return ($miles * 1.609344);
	  } else if ($unit == "N") {
	      return ($miles * 0.8684);
	    } else {
	        return $miles;
	      }
	}
	public function saveRecord($table, $data)
	{
		$this -> db -> insert($table, $data);
		$id = $this -> db -> insert_id();
		//echo $this->db->last_query();die; 
		if($id==0)
		{
			return 0;
		}
		else 
		{
			return $id;	
		}
	}
	public function updateRecord($table,$data,$where)
	{
		$this -> db -> where($where);
		$this -> db -> update($table, $data);
		//echo $this->db->last_query();die;
		return $afftectedRows = $this -> db -> affected_rows();
	}
	function getUserImgUrl($dir, $str)
	 {
		//return $str= 'http://192.168.1.44'.$str;
		return $str = 'http://' . $_SERVER["HTTP_HOST"] . '/treatlee/' . $dir . '/' . $str;
	 }
	public function getRecord($table, $data)
	 {
		$query = $this -> db -> get_where($table, $data);
		//echo $this->db->last_query();
		$rows = $query -> result_array();
		if (!$rows) 
		{
			return 0;
		} 
		else 
		{
			return $rows;
		}
	 }	
	public function getRecordLimit($table, $data,$limit,$start)
	 {
		$this -> db ->where($data);
		//$rows = $query -> result_array();
		//$this->db->order_by($column, 'RANDOM');
	 	$this->db->limit($limit,$start);
	    $query = $this->db->get($table);
	    $rows=$query->result_array();
		if (!$rows) 
		{
			return 0;
		} else {
			return $rows;
		}
	 }
	public function genRandomPassword($length)
	 {
		$characters = '12346789abcdefghjkmnpqrstuvwxyABCDEFGHJKLMNPQRSTUVWXYZ1234567890';
		$string = '';
		for ($p = 0; $p < $length; $p++)
		 {
			$string .= @$characters[@mt_rand(0, @strlen($characters))];
		 }
		return $string;
    }
	public function getRecordQuery($query)
	{
		$query=$this->db->query($query);
		$rows=$query->result_array();
		if (!$rows) 
		{
			return 0; 
		} 
		else 
		{
			return $rows;
		}
	}
	public function getRecordQueryCount($query)
	{
		$query=$this->db->query($query);
		$rows=$query->result_array();
		$rowcount = $query->num_rows();
		if (!$rows) 
		{
			return 0; 
		} 
		else 
		{
			return $rowcount;
		}
	}
	public function deleteRecord($where,$table) 
	{
		$this -> db -> delete($table, $where);
		//echo $this->db->last_query();die;
		if ($this -> db -> affected_rows() > 0) 
		{
			return 1;
		} 
		else 
		{
			return 0;
		}
	}
	public function encrypt_decrypt($action, $string) 
	{
	    $output = false;
	    $encrypt_method = "AES-256-CBC";
	    $secret_key = 'This is my secret key';
	    $secret_iv = 'This is my secret iv';
	
	    // hash
	    $key = hash('sha256', $secret_key);
	    
	    // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
	    $iv = substr(hash('sha256', $secret_iv), 0, 16);
	
	    if( $action == 'encrypt' ) {
	        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
	        $output = base64_encode($output);
	    }
	    else if( $action == 'decrypt' ){
	        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
	    }
	
	    return $output;
	}
	public function getState($latitude,$longitude)
	{
		$geocode=file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?latlng='.$latitude.','.$longitude.'&sensor=false');

        $output=json_decode($geocode);
	
		if(isset($output->status))
		{
			if($output->status=='OK')
			{
				for($j=0;$j<count($output->results[0]->address_components);$j++)
				{
			        $cn=array($output->results[0]->address_components[$j]->types[0]);
			        if(in_array("administrative_area_level_1", $cn))
			        {
						$state= $output->results[0]->address_components[$j]->long_name;
			        }
				}
				return $state;	
			}	
			else 
			{
				return 0;
			}
		}
		else 
		{
			return 0;
		}
	}
	public function GetDrivingDistance($lat1, $lat2, $long1, $long2)
	{
	    $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$lat1.",".$long1."&destinations=".$lat2.",".$long2."&mode=driving&language=pl-PL";
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	    $response = curl_exec($ch);
	    curl_close($ch);
	    $response_a = json_decode($response, true);
		if(isset($response_a['rows'][0]['elements'][0]['status']))
		{
		    if($response_a['rows'][0]['elements'][0]['status']=='OK')
    		{
    			$dist = $response_a['rows'][0]['elements'][0]['distance']['text'];
    	    	$time = $response_a['rows'][0]['elements'][0]['duration']['text'];
    			$distan=explode(' ', $dist);
    			if($distan[1]=='m')
    			{
    				$distance=$distan[0]/1000;
    				$final_distance= $distance ;
    			}
    			else 
    			{
    				$final_distance	=$distan[0];
    			}	
    		}
    		else 
    		{
    			$final_distance = '0 ';
    		}
		}
		else 
		{
			$final_distance = '0 ';
		}
	    return $final_distance;
	}
	
	public function sendMail_old($to,$subject,$message) 
	{
		$base_url=base_url();
		$str="&lsquo;";
		
		$this->load->library('Email'); 
		$config['protocol'] = 'sendmail';
		$config['mailpath'] = '/usr/sbin/sendmail';
		$config['charset'] = 'utf-8';
		$config['wordwrap'] = TRUE;
		$config['mailtype'] = 'html';
		$this->email->initialize($config);
		
		
		
		$from='no-reply@watertank.com';
		$subject = $subject;
		$message =$message;		
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .= "Content-Type: image/jpg;\n" ;
		$headers .= "From:" . $from;	 
		
		// $this->email->set_header('MIME-Version', '1.0; charset=utf-8');
		// $this->email->set_header('Content-type', 'text/plain');
		// $this->email->set_header('From', $from);
		
		$this->email->reply_to($from); 
		$this -> email -> from($from,'WaterTank');
		
		
		$this->email->to($to);
		$this->email->subject($subject);
		$this->email->message($message);  
		
		//$mail = $this->email->send();
		if($mail = $this->email->send())
		{
			return 1;
		}
		else 
		{
			return 0;
		}
	}
	function sendMail($toemail, $subject, $message) 
    {
    	$base_url=base_url();
		$str="&lsquo;";
		
		$this->load->library('Email'); 
		$config['protocol'] = 'sendmail';
		$config['mailpath'] = '/usr/sbin/sendmail';
		$config['charset'] = 'utf-8';
		$config['wordwrap'] = TRUE;
		$config['mailtype'] = 'html';
		$this->email->initialize($config);
		
		$from='parkhya.developer@gmail.com';
		
		
		$to = $toemail;
		$subject = $subject;
		$message =$message;		
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .= "Content-Type: image/jpg;\n" ;
		$headers .= "From:" . $from;	 
		
		
		$this->email->reply_to($from); 
		$this -> email -> from($from,'Treatlee');
		
		
		$this->email->to($to);
		$this->email->subject($subject);
		$this->email->message($message);  
		//$this->email->attach($file);
		//$mail = $this->email->send();
		if($mail = $this->email->send())
		{
			return 1;
		}
		else 
		{
			return 0;
		}
    }
	public function send_android_notification($array,$registatoin_ids) 
	{
		// Set POST variables
		$url = 'https://fcm.googleapis.com/fcm/send';
		
		$fields = array(
						'registration_ids' => array($registatoin_ids),
						'data' =>  $array
						);
		   
		
		$headers = array(
						'Authorization: key=AAAA2eDRYyI:APA91bFwoUmNgVs8mCgeAKue6-9i9U6PIdfQA52qtxugBPR5YoRvPrlttdBPNVDpIdTb3vwix77KP_dVj0qCHZIqEARP5rJ3yQCGKyLxU6CznaoQ_AkdKMsRlPzZljeldo1H_9brInG7','Content-Type: application/json'
					//'Authorization: key=AAAASOzLEJQ:APA91bEznDyUlhBf6saHRHo9S0cIjusCic03Zx8LT8BZh_ujpDAv4aqCOoxX24txhaB8_1C23Yb2jdd1K1S6wxKyKQ4kgHyAPBGBMeptl8E39gY2127a2FHAwZSeaQ6D5i3U9Pm8V1om',						
	     			
						);
						
						
		// Open connection
		$ch = curl_init();
		   
		// Set the url, number of POST vars, POST data
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		// Disabling SSL Certificate support temporarly
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
		
		// Execute post
		$result = curl_exec($ch);
		/*echo "<pre>";
		print_r($result);*/
		if ($result === FALSE) 
		{
			// die('Curl failed: ' . curl_error($ch));
			return 0;
		}
		else 
		{
			return 1;	
		}
		
		// Close connection
		curl_close($ch);
		//   echo $result;
	}
	public function sendIphoneNotification($deviceToken,$array,$type)
	{
		
		$passphrase = '1234';
		
		 ////////////////////////////////////////////////////////////////////////////////
		if($type==0)
		{
			//This type is for customer
			$pemfile = $_SERVER['DOCUMENT_ROOT'] . "/assets/ios/TankerDevelopment.pem";
		} 
		else 
		{
			//type 1 is for driver
			$pemfile = $_SERVER['DOCUMENT_ROOT'] . "/assets/ios/DriverDev.pem";
		}
		  // echo $pemfile;die();
		$ctx = stream_context_create();
		stream_context_set_option($ctx, 'ssl', 'local_cert', $pemfile);
		stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
		
		$oldErrorReporting = error_reporting(); // save error reporting level
		error_reporting($oldErrorReporting ^ E_WARNING); // disable warnings   
		// Open a connection to the APNS server
		//$fp = stream_socket_client('ssl://gateway.push.apple.com:2195', $err,$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
		//$fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err,$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
		  $fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT,  $ctx);
		error_reporting($oldErrorReporting); // restore error reporting level   
		if (!$fp)
		exit("Failed to connect: $err $errstr" . PHP_EOL);
		   
		// Create the payload body
		$body['aps'] = $array;
		   
		// Encode the payload as JSON
		$payload = json_encode($body);
		   
		// Build the binary notification
		$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
		   
		// Send it to the server
		$result = fwrite($fp, $msg, strlen($msg));
		stream_set_blocking($fp, 0);
		//echo $result;
		if (!$result)
           	return 'Message not delivered';
  		else
            return 'Message successfully delivered';
	
		 
		   // Close the connection to the server
		fclose($fp);
	}
}

?>