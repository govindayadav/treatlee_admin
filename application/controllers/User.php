<?php
if (!defined('BASEPATH'))exit('No direct script access allowed');
include (APPPATH . 'libraries/REST_Controller.php');

class User extends REST_Controller 
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('user_model');
	}
	public function checkSecurityCode_post($user_id,$security_code)
	{
		//This function is for check the security code
		$check=$this->user_model->getRecord('user', array('user_id'=>$user_id,'security_code'=>$security_code));
		//echo $this->db->last_query();die();
		if($check==0)
		{
			$post['message']=$check[0]['language']==0?'security code not matched':'';
			$post['status']=2;
		}
		else 
		{
			$post['status']		= 1;
			$post['language']	= $check[0]['language'];
		}
		return $post;
	}
	public function getCountryList_post()
	{
		//This function is for getting the country list
		$language=$this->input->post('language');
		$query="SELECT * FROM `country` ORDER BY `country_name` ASC";
		$result=$this->user_model->getRecordQuery($query);
		if($result==0)
		{
			$post['message']=$language==0?"No Country Found ":"s";
			$post['status']=0;
		}
		else 
		{
			$post['message']=$language==0?"success":"ناجح";
			$post['status']=1;
			foreach ($result as $value) 
			{
				$list['country_id']		= $value['country_id'];
				$list['country_code']	= $value['country_code'];
				$list['short_name']		= $value['short_name'];
				$list['country_name']	= $value['country_name'];
				$list['currency']		= $value['currency'];
				$list['currency_image']	= ($value['currency_image']=='')?'':$this->user_model->getUserImgUrl('uploads/currency', $value['currency_image']);
				$list['country_flag']	= ($value['country_flag']=='')?'':$this->user_model->getUserImgUrl('uploads/flages', $value['country_flag']);
				$list['phone_length']	= $value['phone_length'];
				$post['country_list'][]	= $list;
			}
		}
		echo json_encode($post);
	}
	public function getCityList_post()
	{
		$language	= $this->input->post('language');
		$country_id	= $this->input->post('country_id');
		
		$query="SELECT * FROM `city` WHERE `country_id`='".$country_id."' ORDER BY `city_name` ASC";
		$result=$this->user_model->getRecordQuery($query);
		if($result==0)
		{
			$post['message']=$language==0?"No City Found ":"";
			$post['status']=0;
		}
		else 
		{
			$post['message']=$language==0?"success":"ناجح";
			$post['status']=1;
			
			foreach ($result as $value) 
			{
				$list['city_id']		= $value['city_id'];
				$list['city_name']		= $value['city_name'];
				$list['country_id']		= $value['country_id'];
				$post['city_list'][]	= $list;
			}
		}
		echo json_encode($post);
	}
	public function sendVerificationEmail_post()
	{
		//This function is for send link for email verification
		$email_id	= $this->input->post('email_id');
		$language	= $this->input->post('language');
		//$otp		= substr(time(), -4);
		$otp		= '1234';
		
		$otp_id=$this->user_model->saveRecord('otp', array('email_id'=>$email_id,'otp_code'=>$otp,'created'=>date('Y-m-d H:i:s')));
		if($otp_id==0)
		{
			$post['message']=$language==0?"otp could not send":"مكتب المدعي العام لا يمكن أن ترسل";
			$post['status']=0;
		}
		else 
		{
			$enc_email_id		= $this->user_model->encrypt_decrypt('encrypt', $email_id);
			$enc_otp			= $this->user_model->encrypt_decrypt('encrypt', $otp);
			$enc_otp_id			= $this->user_model->encrypt_decrypt('encrypt', $otp_id);
				
			$message123			= (base_url()."index.php/user/verifyEmail/".$enc_otp.'/'.$enc_email_id."/".$enc_otp_id);
			$subject			= "Treatlee Verification";
			
			$mess='<a target="_self" href="'.$message123.'" style="text-decoration: none;color: #fff !important;">
	<div style="color: #fff;background-color: #e85a5a;width: 256px;height: 30px;border-radius: 6px;line-height: 27px;text-align: center;text-decoration: none;">
	Click Here To Reset Your Password</div></a>';
	
			$message='<p>Don&#39;t feel embarassed. It happens to everyone </p>
				<p>You can reset your password using this link : '.$mess.'</p>
				<p>Treatlee Team.</p>';
				
			$this->user_model->sendMail($email_id, $subject, $message);
			
			$post['message']	= $language==0?"success":"ناجح";
			$post['status']		= 1;
			$post['otp_id']		= $otp_id;
			$post['otp']		= $otp;
			
			
			/*$query="SELECT * FROM `message_setting` ORDER BY `message_setting` DESC LIMIT 1";
			$detail=$this->user_model->getRecordQuery($query);
			if($detail==0)
			{
				$post['message']=$language==0?"Message setting Incorrect":"إعداد الرسالة غير صحيح";
				$post['status']=0;	
			}
			else 
			{
				$mobile=$country_code.$mobile_number;
				$url="http://sms2.thetopsms.com/SMSGateway/Services/Messaging.asmx/Http_SendSMS?username=".$detail[0]['username']."&password=".$detail[0]['password']."&customerId=".$detail[0]['customerId']."&senderText=".$detail[0]['senderText']."&messageBody=".$message."&recipientNumbers=".$mobile."&defdate=&isBlink=false&isFlash=false";
				
				$this->user_model->sendSMS($url);
				$post['message']=$language==0?"success":"ناجح";
				$post['status']=1;
				$post['otp_id']=$otp_id;
				$post['otp']=$otp;
			}*/
		}
		echo json_encode($post);
	}
	public function verifyEmail_get()
	{
		//This function is to verify the email id
		$dec_otp		= $this->uri->segment(3);
		$dec_email_id	= $this->uri->segment(4);
		$dec_otp_id	= $this->uri->segment(5);
		
		$email_id	= $this->user_model->encrypt_decrypt('decrypt', $dec_email_id);
		$otp		= $this->user_model->encrypt_decrypt('decrypt', $dec_otp);
		$otp_id		= $this->user_model->encrypt_decrypt('decrypt', $dec_otp_id);
		
		$where="`otp_id` ='".$otp_id."' AND `email_id`='".$email_id."' ORDER BY `otp_id` DESC";
		$check=$this->user_model->getRecord('otp', $where);
		//echo $this->db->last_query();
		if($check==0)
		{
			$post['message']="Invalid mobile number";
			$post['status']=0;
		}
		else 
		{
			if($check[0]['otp_code']==$otp)
			{
				$this->user_model->updateRecord('otp',array('is_verified'=>1),array('otp_id'=>$otp_id));
				
				$all['email_id']=$email_id;
				
				$this->load->view('change_password',$all);
				$post['message']="success";
				$post['status']=1;
			}
			else 
			{
				$post['message']="Invalid OTP";
				$post['status']=0;
				echo json_encode($post);
			}
		}
	}
	public function forgotPassword_post()
	{
		//This function is for forgot password
		if(isset($_POST['submit']))
		{
			$email_id=$this->input->post('email_id');
			$password=$this->input->post('password');
			
			$this->user_model->updateRecord('user',array('password'=>$password),array('email_id'=>$email_id));
			echo "password updated successfully";
		}
	}
	public function sendOtp_post()
	{
		//This function is for send otp
		$country_code	= $this->input->post('country_code');
		$mobile_number	= $this->input->post('mobile_number');
		$language		= $this->input->post('language');
		$send_type		= $this->input->post('send_type');
		$email_id		= $this->input->post('email_id');
		//send_type : 1 - forgot / 2 - register
		//$otp			= substr(time(), -4);
		if($send_type==2)
		{
			//This condition is for registeration
			$check_email=$this->user_model->getRecord('user', array('email_id'=>$email_id));
			if($check_email==0)
			{
				$check_mobile=$this->user_model->getRecord('user', array('country_code'=>$country_code,'mobile'=>$mobile_number));
				if($check_mobile==0)
				{
					$status=0;
				}
				else 
				{
					//mobile no already exist
					$post['message']=$language==0?"mobile no already exist":"ناجح";
					$post['status']=0;
					$status=1;
				}
			}
			else 
			{
				//email id already exist
				$post['message']=$language==0?"Email id already exist":"ناجح";
				$post['status']=0;
				$status=1;
			}
		}
		else 
		{
			$check_mobile=$this->user_model->getRecord('user', array('country_code'=>$country_code,'mobile'=>$mobile_number));
			if($check_mobile==0)
			{
				$post['message']=$language==0?"mobile no not found":"ناجح";
				$post['status']=0;
				$status=1;
			}
			else 
			{
				//mobile no already exist
				$status=0;
			}
			
		}
		//Now proceed the program according to the status
		if($status==0){
			$otp			= '1234';
			$message		= urlencode('Your OTP for Treatlee is : '.$otp);
			
			$otp_id=$this->user_model->saveRecord('otp', array('mobile'=>$mobile_number,'country_code'=>$country_code,'otp_code'=>$otp,'created'=>date('Y-m-d H:i:s')));
			if($otp_id==0)
			{
				$post['message']=$language==0?"otp could not send":"مكتب المدعي العام لا يمكن أن ترسل";
				$post['status']=0;
			}
			else 
			{
				
				$post['message']=$language==0?"success":"ناجح";
				$post['status']=1;
				$post['otp_id']=$otp_id;
				$post['otp']=$otp;
				
				/*$query="SELECT * FROM `message_setting` ORDER BY `message_setting` DESC LIMIT 1";
				$detail=$this->user_model->getRecordQuery($query);
				if($detail==0)
				{
					$post['message']=$language==0?"Message setting Incorrect":"إعداد الرسالة غير صحيح";
					$post['status']=0;	
				}
				else 
				{
					$mobile=$country_code.$mobile_number;
					$url="http://sms2.thetopsms.com/SMSGateway/Services/Messaging.asmx/Http_SendSMS?username=".$detail[0]['username']."&password=".$detail[0]['password']."&customerId=".$detail[0]['customerId']."&senderText=".$detail[0]['senderText']."&messageBody=".$message."&recipientNumbers=".$mobile."&defdate=&isBlink=false&isFlash=false";
					
					$this->user_model->sendSMS($url);
					$post['message']=$language==0?"success":"ناجح";
					$post['status']=1;
					$post['otp_id']=$otp_id;
					$post['otp']=$otp;
				}*/
			}
		}
		echo json_encode($post);
	}
	public function otpVerification_post()
	{
		//This function is for verifiy the otp 
		$country_code	= $this->input->post('country_code');
		$mobile_number	= $this->input->post('mobile_number');
		$otp			= $this->input->post('otp');
		$otp_id			= $this->input->post('otp_id');
		$user_type		= $this->input->post('user_type');
		$language		= $this->input->post('language');
		
		$where="`otp_id` ='".$otp_id."' AND `mobile`='".$mobile_number."' AND `country_code`='".$country_code."' ORDER BY `otp_id` DESC";
		$check=$this->user_model->getRecord('otp', $where);
		//echo $this->db->last_query();
		if($check==0)
		{
			$post['message']=$language==0?"Invalid mobile number":"رقم الجوال غير صالح";
			$post['status']=0;
		}
		else 
		{
			if($check[0]['otp_code']==$otp)
			{
				$this->user_model->updateRecord('otp',array('is_verified'=>1),array('otp_id'=>$otp_id));
				$post['message']=$language==0?"success":"ناجح";
				$post['status']=1;
			}
			else 
			{
				$post['message']=$language==0?"Invalid OTP":"مكتب المدعي العام غير صالح";
				$post['status']=0;
			}
		}
		echo json_encode($post);
	}
	public function userRegister_post()
	{
		//This function is for register the user
		$name			= $this->input->post('name');
		$email_id		= $this->input->post('email_id');
		$country_code	= $this->input->post('country_code');
		$mobile			= $this->input->post('mobile');
		$password		= $this->input->post('password');
		$date_birth		= $this->input->post('date_birth');
		$language		= $this->input->post('language');
		$country_id		= $this->input->post('country_id');
		$device_type	= $this->input->post('device_type');
		$device_token	= $this->input->post('device_token');
		$fcm_token		= $this->input->post('fcm_token');
		$security_code	= md5(uniqid(time(), true));
		
		if($country_code!='')
		{
			//check the mobile already exist or not
			$check_mobile_number=$this->user_model->getRecord('user', array('mobile'=>$mobile,'country_code'=>$country_code,'user_type'=>4));
			if($check_mobile_number==0)
			{
				//mobile not found 	
				//now check the email id already exist or not
				$check_email_id=$this->user_model->getRecord('user', array('email_id'=>$email_id,'user_type'=>4));
				if($check_email_id==0)
				{
					
					$data=array(
								'name'			=> $name,
								'email_id'		=> $email_id,
								'country_code'	=> $country_code,
								'mobile'		=> $mobile,
								'password'		=> $password,
								'date_birth'	=> $date_birth,
								'language'		=> $language,
								'country_id'	=> $country_id,
								'user_type'		=> 4,
								'is_login'		=> 1,
								'security_code'	=> $security_code,
								'device_type'	=> $device_type,
								'device_token'	=> $device_token,
								'fcm_token'		=> $fcm_token,
								'created'		=> date('Y-m-d H:i:s'));
					$result=$this->user_model->saveRecord('user', $data);
					if($result==0)
					{
						$post['message']=$language==0?"server issue":"قضية الخادم";	
						$post['status']=0;
					}
					else 
					{
						$post['message']=$language==0?"success":"ناجح";
						$post['status']=1;
						//now get the user information
						$information=$this->user_model->getRecord('user', array('user_id'=>$result));
						
						$info['user_id']				= $information[0]['user_id'];
						$info['name']					= $information[0]['name'];
						$info['email_id']				= $information[0]['email_id'];
						$info['country_code']			= $information[0]['country_code'];
						$info['mobile']					= $information[0]['mobile'];
						$info['password']				= $information[0]['password'];
						$info['date_birth']				= $information[0]['date_birth'];
						$info['language']				= $information[0]['language'];
						$info['country_id']				= $information[0]['country_id'];
						$info['mobile_notification']	= $information[0]['mobile_notification'];
						$info['email_notification']		= $information[0]['email_notification'];
						$info['user_type']				= $information[0]['user_type'];
						$info['login_with']				= $information[0]['login_with'];
						$info['social_id']				= $information[0]['social_id'];
						$info['created']				= $information[0]['created'];
						$info['updated']				= $information[0]['updated'];
						$info['security_code']			= $information[0]['security_code'];
						$info['device_type']			= $information[0]['device_type'];
						$info['device_token']			= $information[0]['device_token'];
						$info['fcm_token']				= $information[0]['fcm_token'];
						
						$post['user']=$info;
						
					}	
				}
				else 
				{
					//email id already exist
					$post['message']=$language==0?"email id already exist":"معرف البريد الإلكتروني موجود ";
					$post['status']=10;
				}
			}
			else 
			{
				//mobile already exist
				$post['message']=$language==0?"Mobile number already exist":"رقم الجوال موجود ";
				$post['status']=9;
			}
		}
		else 
		{
			$post['message']=$language==0?"Country code can not blank !":"رمز البلد لا يمكن فارغة!";
			$post['status']=0;
		}
		echo json_encode($post);
	}
	public function userLogin_post()
	{
		//This function is for login the user
		//now getting the values from view
		$user			= $this->input->post('user');
		$password		= $this->input->post('password');
		$device_type	= $this->input->post('device_type');
		$device_token	= $this->input->post('device_token');
		$user_type		= $this->input->post('user_type');
		$country_code	= $this->input->post('country_code');
		$language		= $this->input->post('language');
		$fcm_token		= $this->input->post('fcm_token');
		
		$check=$this->user_model->getRecord('user', array('email_id'=>$user,'user_type'=>$user_type));
		if($check==0)
		{
			$post['message']=$language==0?"user not found":"المستخدم ليس موجود";	
			$post['status']=7;
		}
		else 
		{
			$where="(`email_id`='".$user."' OR `mobile`='".$user."') AND `country_code`='".$country_code."' AND `password`='".$password."' AND `user_type`='".$user_type."'";
			$result=$this->user_model->getRecord('user', $where);
			if($result==0)
			{
				$post['message']=$language==0?"authentication failed":"خطأ في التسجيل";	
				$post['status']=0;
			}
			else 
			{
				//now update the user information
				$security_code=md5(uniqid(time(), true));
				$this->user_model->updateRecord('user',array('language'=>$language,'device_type'=>$device_type,'device_token'=>$device_token,'security_code'=>$security_code,'fcm_token'=>$fcm_token),array('user_id'=>$result[0]['user_id']));
				
				
				$post['message']=$language==0?"success":"ناجح";
				$post['status']=1;
				//now getting the user detail
				$user_d['user_id']				= $result[0]['user_id'];
				$user_d['name']					= $result[0]['name'];
				$user_d['email_id']				= $result[0]['email_id'];
				$user_d['country_code']			= $result[0]['country_code'];
				$user_d['mobile']				= $result[0]['mobile'];
				$user_d['date_birth']			= $result[0]['date_birth'];
				$user_d['language']				= $language;
				$user_d['country_id']			= $result[0]['country_id'];
				$user_d['mobile_notification']	= $result[0]['mobile_notification'];
				$user_d['email_notification']	= $result[0]['email_notification'];
				$user_d['user_type']			= $result[0]['user_type'];
				$user_d['login_with']			= $result[0]['login_with'];
				$user_d['social_id']			= $result[0]['social_id'];
				$user_d['security_code']		= $security_code;
				
				$post['user']		= $user_d;
			}
		}	
		echo json_encode($post);
	}
	public function userLogout_post()
	{
		//This function is for logout the user
		//now getting the values from view
		$language	= $this->input->post('language');
		$user_id	= $this->input->post('user_id');
		
		$res=$this->user_model->updateRecord('user',array('device_token'=>'','fcm_token'=>'','is_active'=>1,'is_login'=>0),array('user_id'=>$user_id));
		if($res==0)
		{
			$post['message']=$language==0?"failure":"فشل";	
			$post['status']=0;
		}
		else 
		{
			$post['message']=$language==0?"success":"ناجح";
			$post['status']=1;
		}
		echo json_encode($post);
	}
	public function socialLogin_post()
	{
		//This function is for login the social
		$login_with		= $this->input->post('login_with');
		$social_id		= $this->input->post('social_id');
		$language		= $this->input->post('language');
		$device_type	= $this->input->post('device_type');
		$device_token	= $this->input->post('device_token');
		$fcm_token		= $this->input->post('fcm_token');
		$email_id		= $this->input->post('email_id');
		$country_code	= $this->input->post('country_code');
		$mobile			= $this->input->post('mobile');
		$name			= $this->input->post('name');
		$security_code	= md5(uniqid(time(), true));
		//login_with : 0 - normal / 1 - facebook / 2 - google
		
		
		$check=$this->user_model->getRecord('user', array('login_with'=>$login_with,'social_id'=>$social_id));
		if($check==0)
		{
			$data=array(
						'login_with'	=> $login_with,
						'social_id'		=> $social_id,
						'is_active'		=> 0,
						'is_login'		=> 1,
						'user_type'		=> 4,
						'security_code'	=> $security_code,
						'name'			=> $name,
						'email_id'		=> $email_id,
						'country_code'	=> $country_code,
						'mobile'		=> $mobile,
						'language'		=> $language,
						'device_type'	=> $device_type,
						'device_token'	=> $device_token,
						'fcm_token'		=> $fcm_token,
						'created'		=> date('Y-m-d H:i:s'));
			$user_id=$this->user_model->saveRecord('user', $data);
		}
		else 
		{
			$user_id=$check[0]['user_id'];
		}
		
		if($user_id==0)
		{
			$post['message']=$language==0?"failure":"فشل";	
			$post['status']=0;
		}
		else 
		{
			$post['message']=$language==0?"success":"ناجح";
			$post['status']=1;
			
			$result=$this->user_model->getRecord('user', array('user_id'=>$user_id));
			$user_d['user_id']				= $result[0]['user_id'];
			$user_d['name']					= $result[0]['name'];
			$user_d['email_id']				= $result[0]['email_id'];
			$user_d['country_code']			= $result[0]['country_code'];
			$user_d['mobile']				= $result[0]['mobile'];
			$user_d['date_birth']			= $result[0]['date_birth'];
			$user_d['language']				= $language;
			$user_d['country_id']			= $result[0]['country_id'];
			$user_d['mobile_notification']	= $result[0]['mobile_notification'];
			$user_d['email_notification']	= $result[0]['email_notification'];
			$user_d['user_type']			= $result[0]['user_type'];
			$user_d['login_with']			= $result[0]['login_with'];
			$user_d['social_id']			= $result[0]['social_id'];
			$user_d['security_code']		= $security_code;
			
			$post['user']		= $user_d;
			
		}
		echo json_encode($post);
	}
	public function saveUserPet_post()
	{
		//This function is for save the user pet
		
		$user_id			= $this->input->post('user_id');
		$security_code		= $this->input->post('security_code');
		$pet_name			= $this->input->post('pet_name');
		$animal_id			= $this->input->post('animal_id');
		$breed_id			= $this->input->post('breed_id');
		$date_birth			= $this->input->post('date_birth');
		$gender				= $this->input->post('gender');
		$neutered			= $this->input->post('neutered');
		$special_medical	= $this->input->post('special_medical');
		$additional			= $this->input->post('additional');
		$favorite_game		= $this->input->post('favorite_game');
		$good_habit			= $this->input->post('good_habit');
		$bad_habit			= $this->input->post('bad_habit');
		$other				= $this->input->post('other');
		$language			= $this->input->post('language');
		$medical_condition	= $this->input->post('medical_condition')==''?'':$this->input->post('medical_condition');
		
		
		$check_code=$this->checkSecurityCode_post($user_id, $security_code);
		if($check_code['status']==1)
		{
			if(isset($_FILES['pet_image']['name']))
			{
				if($_FILES['pet_image']['name']!='')
				{
					$imanename = $_FILES['pet_image']['name'];
					$temp = explode(".", $_FILES["pet_image"]["name"]);
					$newfilename = rand(1, 99999) . '.' . end($temp);
					//This is for upload the image
					$path= './uploads/pet/'.$newfilename;
					$upload = copy($_FILES['pet_image']['tmp_name'], $path);
				}
				else 
				{
					$newfilename="";
				}
			}
			else 
			{
				$newfilename="";
			}
			$data=array(
						'user_id'			=> $user_id,
						'pet_name'			=> $pet_name,
						'pet_image'			=> $newfilename,
						'animal_id'			=> $animal_id,
						'breed_id'			=> $breed_id,
						'date_birth'		=> $date_birth,
						'gender'			=> $gender,
						'neutered'			=> $neutered,
						'special_medical'	=> $special_medical,
						'medical_condition'	=> $medical_condition,
						'additional'		=> $additional,
						'favorite_game'		=> $favorite_game,
						'good_habit'		=> $good_habit,
						'bad_habit'			=> $bad_habit,
						'other'				=> $other,
						'created'			=> date('Y-m-d H:i:s'));
			$result=$this->user_model->saveRecord('pet_animal', $data);
			if($result==0)
			{
				$post['message']=$language==0?"failure":"فشل";	
				$post['status']=0;
			}
			else 
			{
				$post['message']=$language==0?"success":"ناجح";
				$post['status']=1;
			}
		}
		else 
		{
			$post['message']= $check_code['message'];
			$post['status']	= $check_code['status'];
		}
		echo json_encode($post);
	}
	public function getUserPet_post()
	{
		//This function is for getting the user pet
		$user_id			= $this->input->post('user_id');
		$security_code		= $this->input->post('security_code');
		$language			= $this->input->post('language');
		$hotel_id			= $this->input->post('hotel_id');
		
		/*$country=$this->user_model->getRecord('country', array('country_id'=>$country_id));
		$time_zone	= $country[0]['time_zone'];*/
			
		$check_code=$this->checkSecurityCode_post($user_id, $security_code);
		if($check_code['status']==1)
		{
			$query="SELECT pe.*,a.`animal_image`,a.`animal_name` as `animal_name`,ab.`breed_name` as `breed_name` FROM `pet_animal` pe LEFT JOIN `animal` a ON pe.`animal_id`=a.`animal_id` LEFT JOIN `animal_breed` ab ON pe.`breed_id`=ab.`animal_breed_id` WHERE pe.`user_id`='".$user_id."' AND pe.`is_delete`= 0 ORDER BY pe.`pet_name` ASC";
			$result=$this->user_model->getRecordQuery($query);
			if($result==0)
			{
				$post['message']=$language==0?"failure":"فشل";	
				$post['status']=0;
			}
			else 
			{
				$post['message']=$language==0?"success":"ناجح";
				$post['status']=1;
				foreach ($result as $value) 
				{
					$pet['pet_animal_id']		= $value['pet_animal_id'];
					$pet['user_id']				= $value['user_id'];
					$pet['animal_name']			= $value['animal_name'];
					$pet['breed_name']			= $value['breed_name'];
					$pet['pet_name']			= $value['pet_name'];
					$pet['pet_image']			= $value['pet_image']==''?'':$this->user_model->getUserImgUrl('uploads/pet', $value['pet_image']);
					$pet['animal_image']		= $value['animal_image']==''?'':$this->user_model->getUserImgUrl('uploads/animal', $value['animal_image']);
					$pet['animal_id']			= $value['animal_id'];
					$pet['breed_id']			= $value['breed_id'];
					$pet['date_birth']			= $value['date_birth'];
					$pet['gender']				= $value['gender'];
					$pet['neutered']			= $value['neutered'];
					$pet['special_medical']		= $value['special_medical'];
					$pet['medical_condition']	= $value['medical_condition'];
					$pet['additional']			= $value['additional'];
					$pet['favorite_game']		= $value['favorite_game'];
					$pet['good_habit']			= $value['good_habit'];
					$pet['bad_habit']			= $value['bad_habit'];
					$pet['other']				= $value['other'];
					$pet['created']				= $value['created'];
					$pet['updated']				= $value['updated'];
					$pet['age']					= $this->user_model->dateDiff($value['date_birth']);
					
					$check_pet					= 0;
					
					if($hotel_id!=0){
						$check_pet=$this->user_model->getRecord('allowed_pets', array('hotel_id'=>$hotel_id,'animal_id'=>$value['animal_id'],'breed_id'=>$value['breed_id']));
					}	

					$pet['is_allowed']				= $check_pet==0?'0':'1';
					
					$post['pet'][]				= $pet;
				}
			}
		}
		else 
		{
			$post['message']= $check_code['message'];
			$post['status']	= $check_code['status'];
		}
		echo json_encode($post);
	}
	public function updateUserPet_post()
	{
		$user_id			= $this->input->post('user_id');
		$pet_animal_id		= $this->input->post('pet_animal_id');
		$security_code		= $this->input->post('security_code');
		$pet_name			= $this->input->post('pet_name');
		$animal_id			= $this->input->post('animal_id');
		$breed_id			= $this->input->post('breed_id');
		$date_birth			= $this->input->post('date_birth');
		$gender				= $this->input->post('gender');
		$neutered			= $this->input->post('neutered');
		$special_medical	= $this->input->post('special_medical');
		$additional			= $this->input->post('additional');
		$favorite_game		= $this->input->post('favorite_game');
		$good_habit			= $this->input->post('good_habit');
		$bad_habit			= $this->input->post('bad_habit');
		$other				= $this->input->post('other');
		$language			= $this->input->post('language');
		$medical_condition	= $this->input->post('medical_condition')==''?'':$this->input->post('medical_condition');
		
		
		$check_code=$this->checkSecurityCode_post($user_id, $security_code);
		if($check_code['status']==1)
		{
			$record=$this->user_model->getRecord('pet_animal', array('pet_animal_id'=>$pet_animal_id));
			if(isset($_FILES['pet_image']['name']))
			{
				if($_FILES['pet_image']['name']!='')
				{
					$imanename = $_FILES['pet_image']['name'];
					$temp = explode(".", $_FILES["pet_image"]["name"]);
					$newfilename = rand(1, 99999) . '.' . end($temp);
					//This is for upload the image
					$path= './uploads/pet/'.$newfilename;
					$upload = copy($_FILES['pet_image']['tmp_name'], $path);
				}
				else 
				{
					$newfilename=$record[0]['pet_image'];
				}
			}
			else 
			{
				$newfilename=$record[0]['pet_image'];
			}
			$data=array(
						'user_id'			=> $user_id,
						'pet_name'			=> $pet_name,
						'pet_image'			=> $newfilename,
						'animal_id'			=> $animal_id,
						'breed_id'			=> $breed_id,
						'date_birth'		=> $date_birth,
						'gender'			=> $gender,
						'neutered'			=> $neutered,
						'special_medical'	=> $special_medical,
						'medical_condition'	=> $medical_condition,
						'additional'		=> $additional,
						'favorite_game'		=> $favorite_game,
						'good_habit'		=> $good_habit,
						'bad_habit'			=> $bad_habit,
						'other'				=> $other,
						'updated_by_id'		=> $user_id);
			
			$this->user_model->updateRecord('pet_animal',$data,array('pet_animal_id'=>$pet_animal_id));	
			
			$post['message']=$language==0?"success":"ناجح";
			$post['status']=1;
		}
		else 
		{
			$post['message']= $check_code['message'];
			$post['status']	= $check_code['status'];
		}
		echo json_encode($post);
	}
	public function deleteUserPet_post()
	{
		//This function is for delete the pet user
		$user_id			= $this->input->post('user_id');
		$language			= $this->input->post('language');
		$security_code		= $this->input->post('security_code');
		$pet_animal_id		= $this->input->post('pet_animal_id');
		
		$check_code=$this->checkSecurityCode_post($user_id, $security_code);
		if($check_code['status']==1)
		{
			$result=$this->user_model->updateRecord('pet_animal',array('is_delete'=>1),array('pet_animal_id'=>$pet_animal_id,'user_id'=>$user_id));
			if($result==0)
			{
				$post['message']=$language==0?"failure":"فشل";	
				$post['status']=0;
			}
			else 
			{
				$post['message']=$language==0?"success":"ناجح";
				$post['status']=1;	
			}
		}
		else 
		{
			$post['message']= $check_code['message'];
			$post['status']	= $check_code['status'];
		}
		echo json_encode($post);
	}
	public function updatePassword_post()
	{
		//This function is for update the user password
		$mobile			= $this->input->post('mobile');
		$country_code	= $this->input->post('country_code');
		$password		= $this->input->post('password');
		
		$this->user_model->updateRecord('user',array('password'=>$password),array('country_code'=>$country_code,'mobile'=>$mobile));
		
		$post['message']="success";
		$post['status']=1;
		
		echo json_encode($post);		
	}
	public function getLanguage_post()
	{
		//This function is for getting the language
		$query="SELECT * FROM `language` ORDER BY `language_code` ASC";
		$result=$this->user_model->getRecordQueryCount($query);
		if($result==0)
		{
			$post['message']="failure";	
			$post['status']=0;
		}
		else 
		{
			$post['message']="success";	
			$post['status']=1;
			foreach ($result as $value) 
			{
				$lang['language_id']=$value['language_id'];
				$lang['language_name']=$value['language_name'];
				$lang['language_code']=$value['language_code'];
				$lang['created']=$value['created'];
				$lang['updated']=$value['updated'];
				$post['language'][]=$lang;
			}
		}
		echo json_encode($post);
	}
	public function changePassword_post()
	{
		//This function is for change the user password
		$user_id			= $this->input->post('user_id');
		$security_code		= $this->input->post('security_code');
		$old_password		= $this->input->post('old_password');
		$new_password		= $this->input->post('new_password');
		$language			= $this->input->post('language');
		
		$check_code=$this->checkSecurityCode_post($user_id, $security_code);
		if($check_code['status']==1)
		{
			
			$check=$this->user_model->getRecord('user', array('user_id'=>$user_id,'password'=>$old_password));
			if($check==0)
			{
				//old password not matched
				$post['message']=$language==0?"old password not matched":"";	
				$post['status']=4;
			}
			else 
			{
				//old password matched now change the new password
				$this->user_model->updateRecord('user',array('password'=>$new_password),array('user_id'=>$user_id));
				
				$post['message']=$language==0?"success":"ناجح";
				$post['status']=1;
			}
		}
		else 
		{
			$post['message']= $check_code['message'];
			$post['status']	= $check_code['status'];
		}
		echo json_encode($post);
	}
	public function updateUserProfile_post()
	{
		//This function is for change the user profile
		$user_id			= $this->input->post('user_id');
		$security_code		= $this->input->post('security_code');
		$name				= $this->input->post('name');
		$email_id			= $this->input->post('email_id');
		$country_code		= $this->input->post('country_code');
		$mobile				= $this->input->post('mobile');
		$date_birth			= $this->input->post('date_birth');
		$language			= $this->input->post('language');
		$country_id			= $this->input->post('country_id');
		$old_password		= $this->input->post('old_password');
		$new_password		= $this->input->post('new_password');
		
		
		$check_code=$this->checkSecurityCode_post($user_id, $security_code);
		if($check_code['status']==1)
		{
			if($new_password!='')
			{
				$check=$this->user_model->getRecord('user', array('user_id'=>$user_id,'password'=>$old_password));
				
				if($check==0)
				{
					//old password not matched
					$post['message']=$language==0?"old password not matched":"";	
					$post['status']=4;
				}
				else 
				{
					//old password matched now change the new password
					$this->user_model->updateRecord('user',array('password'=>$new_password),array('user_id'=>$user_id));
					
					$post['message']=$language==0?"success":"ناجح";
					$post['status']=1;
				}
				echo json_encode($post);die();
			}
			$data=array(
						'name'			=> $name,
						'email_id'		=> $email_id,
						'country_code'	=> $country_code,
						//'mobile'		=> $mobile,
						'date_birth'	=> $date_birth,
						'language'		=> $language,
						'country_id'	=> $country_id);
			$this->user_model->updateRecord('user',$data,array('user_id'=>$user_id));	
			
			$post['message']=$language==0?"success":"ناجح";
			$post['status']=1;		
		}
		else 
		{
			$post['message']= $check_code['message'];
			$post['status']	= $check_code['status'];
		}
		echo json_encode($post);
	}
	public function saveUserAddress_post()
	{
		//This function is for save the user address
		$user_id		= $this->input->post('user_id');
		$security_code	= $this->input->post('security_code');
		$language		= $this->input->post('language');
		$address_name	= $this->input->post('address_name');
		$area			= $this->input->post('area');
		$type			= $this->input->post('address_type');
		$block			= $this->input->post('block');
		$street			= $this->input->post('street');
		$building		= $this->input->post('building');
		$avenue			= $this->input->post('avenue');
		$floor			= $this->input->post('floor');
		$office			= $this->input->post('office');
		$latitude		= $this->input->post('latitude');
		$longitude		= $this->input->post('longitude');
		$additional		= $this->input->post('additional');
		$mobile			= $this->input->post('mobile');
		
		$check_code=$this->checkSecurityCode_post($user_id, $security_code);
		if($check_code['status']==1)
		{
			$data=array(
						'user_id'		=> $user_id,
						'address_name'	=> $address_name,
						'area'			=> $area,
						'type'			=> $type,
						'block'			=> $block,
						'street'		=> $street,
						'building'		=> $building,
						'avenue'		=> $avenue,
						'floor'			=> $floor,
						'office'		=> $office,
						'latitude'		=> $latitude,
						'longitude'		=> $longitude,
						'mobile'		=> $mobile,
						'additional'	=> $additional,	
						'created_by_id'	=> $user_id,
						'updated_by_id'	=> $user_id,
						'created'		=> date('Y-m-d H:i;s'));
			$result=$this->user_model->saveRecord('user_address', $data);
			if($result==0)
			{
				$post['message']=$language==0?"failure":"فشل";	
				$post['status']=0;
			}
			else 
			{
				$post['message']=$language==0?"success":"ناجح";
				$post['status']=1;	
			}
		}
		else 
		{
			$post['message']= $check_code['message'];
			$post['status']	= $check_code['status'];
		}
		echo json_encode($post);
	}
	public function getSavedAddress_post()
	{
		//This function is for agetting the saved user address
		$user_id		= $this->input->post('user_id');
		$security_code	= $this->input->post('security_code');
		$language		= $this->input->post('language');
		
		$check_code=$this->checkSecurityCode_post($user_id, $security_code);
		if($check_code['status']==1)
		{
			$result=$this->user_model->getRecord('user_address', array('user_id'=>$user_id,'is_delete'=>0));
			if($result==0)
			{
				$post['message']=$language==0?"No address found":"";
				$post['status']=3;	
			}
			else 
			{
				$post['message']=$language==0?"success":"ناجح";
				$post['status']=1;	
				foreach ($result as $value) 
				{
					$user_add['user_address_id']	= $value['user_address_id'];
					$user_add['user_id']			= $value['user_id'];
					$user_add['address_name']		= $value['address_name'];
					$user_add['area']				= $value['area'];
					$user_add['type']				= $value['type'];
					$user_add['block']				= $value['block'];
					$user_add['street']				= $value['street'];
					$user_add['building']			= $value['building'];
					$user_add['avenue']				= $value['avenue'];
					$user_add['floor']				= $value['floor'];
					$user_add['office']				= $value['office'];
					$user_add['latitude']			= $value['latitude'];
					$user_add['longitude']			= $value['longitude'];
					$user_add['additional']			= $value['additional'];
					$user_add['mobile']				= $value['mobile'];
					$user_add['created']			= $value['created'];
					$post['user_address'][]			= $user_add;
				}
			}
		}
		else 
		{
			$post['message']= $check_code['message'];
			$post['status']	= $check_code['status'];
		}
		echo json_encode($post);
	}
	public function updateUserAddress_post()
	{
		//This function is for update the user saved address
		$user_id			= $this->input->post('user_id');
		$security_code		= $this->input->post('security_code');
		$language			= $this->input->post('language');
		$user_address_id	= $this->input->post('user_address_id');
		$address_name		= $this->input->post('address_name');
		$area				= $this->input->post('area');
		$type				= $this->input->post('address_type');
		$block				= $this->input->post('block');
		$street				= $this->input->post('street');
		$building			= $this->input->post('building');
		$avenue				= $this->input->post('avenue');
		$floor				= $this->input->post('floor');
		$office				= $this->input->post('office');
		$latitude			= $this->input->post('latitude');
		$longitude			= $this->input->post('longitude');
		$additional			= $this->input->post('additional');
		$mobile				= $this->input->post('mobile');
		
		$check_code=$this->checkSecurityCode_post($user_id, $security_code);
		if($check_code['status']==1)
		{
			$data=array(
						'user_id'		=> $user_id,
						'address_name'	=> $address_name,
						'area'			=> $area,
						'type'			=> $type,
						'block'			=> $block,
						'street'		=> $street,
						'building'		=> $building,
						'avenue'		=> $avenue,
						'floor'			=> $floor,
						'office'		=> $office,
						'latitude'		=> $latitude,
						'longitude'		=> $longitude,
						'mobile'		=> $mobile,
						'additional'	=> $additional,	
						'updated_by_id'	=> $user_id );
			$this->user_model->updateRecord('user_address',$data,array('user_address_id'=>$user_address_id,'user_id'=>$user_id));	
			
			$post['message']=$language==0?"success":"ناجح";
			$post['status']=1;
		}
		else 
		{
			$post['message']= $check_code['message'];
			$post['status']	= $check_code['status'];
		}
		echo json_encode($post);
	}
	public function deleteUserAddress_post()
	{
		//This function is for delete the user saved address
		$user_id			= $this->input->post('user_id');
		$security_code		= $this->input->post('security_code');
		$language			= $this->input->post('language');
		$user_address_id	= $this->input->post('user_address_id');
		
		$this->user_model->updateRecord('user_address',array('is_delete'=>1),array('user_address_id'=>$user_address_id,'user_id'=>$user_id));
		
		$post['message']=$language==0?"success":"ناجح";
		$post['status']=1;
		
		echo json_encode($post);
	}
}
?>