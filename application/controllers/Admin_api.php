<?php
/**
 * 
 */

if (!defined('BASEPATH'))exit('No direct script access allowed');
include (APPPATH . 'libraries/REST_Controller.php');

class Admin_api extends REST_Controller {
	
	function __construct() 
	{
		parent::__construct();
		$this->load->model('user_model');
		
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Headers: X-Requested-With');
		header('Content-Type: application/json');		
		
		//header('Access-Control-Allow-Origin: http://admin.example.com');
	    header("Access-Control-Allow-Credentials: true");
	    header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
	    header('Access-Control-Max-Age: 100000');
	    header('Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description');
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
	public function adminLogin_post()
	{
		//This function is for login the admins
		$email_id=$this->input->post('email_id');
		$password=$this->input->post('password');
		
		$result=$this->user_model->getRecord('user', array('email_id'=>$email_id,'password'=>$password));
		
		if($result==0)
		{
			$post['message']="authentication failed";	
			$post['status']=0;
			$post['param']=$_POST;
		}
		else 
		{
			$security_code=md5(uniqid(time(), true));
			$this->user_model->updateRecord('user',array('security_code'=>$security_code),array('user_id'=>$result[0]['user_id']));
			
			$post['message']="success";
			$post['status']=1;
			$post['param']=$_POST;
			//now getting the user detail
			$user_d['user_id']				= $result[0]['user_id'];
			$user_d['name']					= $result[0]['name'];
			$user_d['email_id']				= $result[0]['email_id'];
			$user_d['country_code']			= $result[0]['country_code'];
			$user_d['mobile']				= $result[0]['mobile'];
			$user_d['date_birth']			= $result[0]['date_birth'];
			$user_d['language']				= $result[0]['language'];
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
}

?>