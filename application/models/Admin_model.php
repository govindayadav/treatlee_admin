<?php
/**
 * 
 */
 if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
class Admin_model extends CI_Model 
{
	
	function __construct() 
	{
		parent::__construct();
		$this -> load -> database();
	}
	public function addressLatLong($address)
	{
		//"https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyCy-Aq5CqTYcA46TtyPNMT9fE5LXMpBz0Y&address=Indore";
		$geocode=file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyD_fHv2SFpBkeuFuq_r3e31AIcTqS_HkcA&address='.$address);

        $output=json_decode($geocode);
	
		if(isset($output->status))
		{
			if($output->status=='OK')
			{
				$north=$output->results[0]->geometry->location;
				$final['latitude']=$north->lat;
				$final['longitude']=$north->lng;				
				
				return $final;	
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
	public function runQuery($query)
	{
		$query=$this->db->query($query);
		if ($this -> db -> affected_rows() > 0) 
		{
			return 1;
		} 
		else 
		{
			return 0;
		}
	}
	public function record_count($query) 
	{
		$query=$this->db->query($query);
		 $rows= $query->num_rows();
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
	public function updateRecord($table,$data,$where)
	{
		$this -> db -> where($where);
		$this -> db -> update($table, $data);
		//echo $this->db->last_query();die;
		return $afftectedRows = $this -> db -> affected_rows();
	}
	public function deleteRecord($where,$table) 
	{
		$this -> db -> delete($table, $where);
		//echo $this->db->last_query();
		if ($this -> db -> affected_rows() > 0) 
		{
			return 1;
		} 
		else 
		{
			return 0;
		}
	}
	public function get_user_img_url($dir, $str)
	 {
		//return $str= 'http://192.168.1.44'.$str;
		return $str = 'http://' . $_SERVER["HTTP_HOST"] . '/' . $dir . '/' . $str;
	 }
	
	
	public function getRecord($table, $data)
	 {
		$query = $this -> db -> get_where($table, $data);
		$rows = $query -> result_array();
		if (!$rows) 
		{
			return 0;
		} else {
			return $rows;
		}
	 }	
	public function getRecordOrder($table, $data,$column,$order)
	 {
		$this -> db ->where($data);
		//$rows = $query -> result_array();
		//$this->db->order_by($column, 'RANDOM');
		$this->db->order_by($column, $order);
	    //$this->db->limit(1);
	    $query = $this->db->get($table);
	    $rows=$query->result_array();
		if (!$rows) 
		{
			return 0;
		} else {
			return $rows;
		}
	 }		 
	public function getLatLong($address)
	{

	    $address = str_replace(" ", "+", $address);
	
	    $json = file_get_contents("http://maps.google.com/maps/api/geocode/json?address=$address&sensor=false");
	    $json = json_decode($json);
		$status = $json->status;
		if($status=="OK")
		{
		    $lat = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
		    $long = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};
		    return $lat.','.$long;
		}
		else
		{
			return " ".','." ";	
		} 
	
	}

	public function getAddress($lat,$lng,$key)
	{
		$url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.trim($lat).','.trim($lng).'&sensor=false&key='.$key;
		$json = @file_get_contents($url);
		$data=json_decode($json);
		$status = $data->status;
		//echo "<pre>";print_r($data);die();
		if($status=="OK")
		return $data->results[0]->formatted_address;
		else
		return false;
	}	
	public function genRandomPassword($length)
	 {
		$characters = '12346789abcdefghjkmnpqrstuvwxyABCDEFGHJKLMNPQRSTUVWXYZ123456789000';
		$string = '';
		for ($p = 0; $p < $length; $p++)
		 {
			$string .= @$characters[@mt_rand(0, @strlen($characters))];
		 }
		return $string;
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
	public function dateDiff($date)
	{
		$date1=date('Y-m-d H:i:s');
		//$diff = abs( strtotime( $date1 ) - strtotime( $date ) );
		$diff = ( strtotime( $date1 ) - strtotime( $date ) );
		if((intval( $diff / 86400 ))==0)
		{
			//days is 0
			if(intval( ( $diff % 86400 ) / 3600)==0)
			{
				//hour is 0	
				if(intval( ( $diff / 60 ) % 60 )==0)
				{
					//minut is 0
					echo intval( $diff % 60 )." sec ago";	
				}
				else 
				{
					echo intval( ( $diff / 60 ) % 60 )." mins ago";
				}
			}
			else 
			{
				echo intval( ( $diff % 86400 ) / 3600)." hrs ago";
			}
		}
		else 
		{
			echo intval( $diff / 86400 )." days ago";
		}
	}	
	public function sendMail($email_id,$subject,$message)
	{
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
		
		// More headers
		$headers .= 'From: <tankerappco@gmail.com>' . "\r\n";
		
		echo mail($email_id,$subject,$message,$headers);
	}
	public function sendMail_old($email_id,$subject,$message) 
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
		
		
		$to = $email_id;
		$subject = $subject;
		$message =$message;		
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .= "Content-Type: image/jpg;\n" ;
		$headers .= "From:" . $from;	 
		
		
		$this->email->reply_to($from); 
		$this -> email -> from($from,'TankerApp');
		
		
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
	public function send_android_notification($array,$registatoin_ids) 
	{
		// Set POST variables
		//$url = 'https://android.googleapis.com/gcm/send';
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
		   //echo $pemfile;die();
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
		if (!$result)
           	return 'Message not delivered';
  		else
            return 'Message successfully delivered';
	
		 
		   // Close the connection to the server
		fclose($fp);
	}
}

?>