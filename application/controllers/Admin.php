<?php
/**
 * 
 */
class Admin extends CI_Controller 
{
	
	function __construct() 
	{
		parent::__construct();
		$this -> load -> library('session');
		$this -> load -> model('admin_model');
		$this -> no_cache();
		$this -> load -> database();
		$this->load->library('form_validation');	
	}
	protected function no_cache() 
	{
		//this function is for clearing cache
		header('Cache-Control: no-store, no-cache, must-revalidate');
		header('Cache-Control: post-check=0, pre-check=0', false);
		header('Pragma: no-cache');
	}
	public function index()
	{
		$session=$this->session->all_userdata();
		if(isset($session['admin_email_id']))
		{
			redirect('admin/dashboard');
		}
		else 
		{
			$this->load->view('admin/user_login');
			/*if(isset($_POST['login']))
			{
				$data=array('admin_email_id'=>'Govinda@gmail.com','admin_id'=>1,'admin_type'=>1);
					$this->session->set_userdata($data);
				redirect('admin/dashboard');
			}*/	
			if(isset($_POST['login']))
			{
				//echo "<pre>";print_r($_POST);die();
				$email_id	= $this->input->post('email_id');
				$password	= $this->input->post('password');
				$data=array('email_id'=>$email_id,'password'=>$password,'is_delete'=>0);
				$result=$this->admin_model->getRecord('admin', $data);
				
				if($result==0)
				{
					$this->session->set_flashdata('message','Invalid Email id or Password');
					redirect('admin');
				}
				else 
				{
					$data1=array(
								'admin_email_id'=> $result[0]['email_id'],
								'admin_id'		=> $result[0]['admin_id'],
								'admin_type'	=> $result[0]['admin_type'],
								'country_id1'	=> $result[0]['country_id'],
								'states_id'		=> $result[0]['states_id'],
								'city_id'		=> $result[0]['city_id']);
								//echo "<pre>";print_r($data1);die();
					$this->session->set_userdata($data1);
					redirect('admin/dashboard');	
				}
			}
		}	
	}
	public function dashboard()
	{
		$session=$this->session->all_userdata();
		//echo "<pre>";print_r($session);die();
		if(!isset($session['admin_email_id']))
		{
			redirect('admin');
		}
		else 
		{
			$admin_type		= $session['admin_type'];
			$scountry_id	= $session['country_id1'];
			$sstates_id		= $session['states_id'];
			$scity_id		= $session['city_id'];
			$ssql			= "";
			$counsql		= "";
			if($admin_type==1)
			{
				$ssql=$scountry_id!=0?($sstates_id!=0?($scity_id!=0?" AND sa.`country`='".$scountry_id."' AND sa.`state`='".$sstates_id."' AND sa.`city`='".$scity_id."'":" AND sa.`country`='".$scountry_id."' AND sa.`state`='".$sstates_id."'"):" AND sa.`country`='".$scountry_id."' "):'';
				if($scountry_id!=0)
				{
					$countriess=$this->admin_model->getRecord('countries', array('country_id'=>$scountry_id));
					$counsql=" AND `country_code`='".$countriess[0]['phonecode']."'";
				}
			}
			$query="SELECT * FROM `request` r JOIN `service_area` sa ON r.`service_area_id`=sa.`service_area_id` WHERE r.`request_status`=0 ".$ssql." ORDER BY r.`request_id` DESC LIMIT 5";
			$dash['pending']=$this->admin_model->getRecordQuery($query);
			//echo $this->db->last_query();die();
			$query1="SELECT * FROM `request` r JOIN `service_area` sa ON r.`service_area_id`=sa.`service_area_id` WHERE r.`request_status`=1 ".$ssql." ORDER BY r.`request_id` DESC LIMIT 5";
			$dash['active']=$this->admin_model->getRecordQuery($query1);
			
			$query2="SELECT SUM(r.`amount`) as today FROM `request` r JOIN `service_area` sa ON r.`service_area_id`=sa.`service_area_id` WHERE DATE(`created`)=CURDATE() AND (`request_status`=3 OR `request_status`=5)".$ssql;
			//$query2="SELECT SUM(`amount`) as today FROM `request` WHERE DATE(`created`)=CURDATE() AND (`request_status`=3 OR `request_status`=5)";
			$dash['today']=$this->admin_model->getRecordQuery($query2);
			//echo $this->db->last_query();die();
			
			$query3="SELECT SUM(r.`amount`) as total FROM `request` r JOIN `service_area` sa ON r.`service_area_id`=sa.`service_area_id` WHERE (`request_status`=3 OR `request_status`=5)".$ssql;
			//$query3="SELECT SUM(`amount`) as total FROM `request` WHERE `request_status`=3 OR `request_status`=5";
			$dash['total']=$this->admin_model->getRecordQuery($query3);
			
			$query4="SELECT count(*) as customer FROM `user` WHERE `user_type`=0 AND `is_delete`=0 ".$counsql;
			$dash['customer']=$this->admin_model->getRecordQuery($query4);
			//echo $this->db->last_query();die();
			
			$query5="SELECT count(*) as driver FROM `user` WHERE `user_type`=1 AND `is_active`=1 AND `is_delete`=0 ".$counsql;
			$dash['driver']=$this->admin_model->getRecordQuery($query5);
			//echo "<pre>";print_r($dash);die();
			
			$this->load->view('admin/dashboard',$dash);
		}         
	}
	
	public function logout() 
	{
		//This function is used to logout the user and destroy the session
		if(isset($session['admin_email_id']))
		{
			//now check that session have the value
			if($session['admin_email_id']!='')
			{
				//redirect to the home page 
				redirect('admin/dashboard');
			}
			else 
			{
				//session is not set and it's does not have any value 
				$this->load->view('admin/login');
			}	
		}
			$this -> session -> unset_userdata('admin_email_id');
			$this -> session -> unset_userdata('admin_id');
	      	$this -> session -> sess_destroy();
			redirect(site_url('admin'));
			$this -> db -> cache_delete_all();
	}
	public function updateProfile()
	{
		$session=$this->session->all_userdata();
		
		if(!isset($session['admin_email_id']))
		{
			redirect('admin');
		}
		else 
		{
			$this->load->view('admin/user_profile');
		}
	}
	public function getPrivacyPolicy()
	{
		//This function is for getting the privacy policy
		/*language change to arabic by ritesh sir at 16-06-2017*/
		$result['data']=$this->admin_model->getRecord('contents', array('content_type'=>3));
		$this->load->view('webservices/privacy_policay',$result);
	}
	public function getTermsCondition()
	{
		//This function is for getting the terms and condition
		$result['data']=$this->admin_model->getRecord('contents', array('content_type'=>1));
		$this->load->view('webservices/privacy_policay',$result);
	}
	public function getAboutUs()
	{
		//This function is for getting the privacy policy
		/*language change to arabic by ritesh sir at 16-06-2017*/
		$result['data']=$this->admin_model->getRecord('contents', array('content_type'=>2));
		$this->load->view('webservices/privacy_policay',$result);
	}
	
	public function forgotPassword()
	{
		$this->load->view('admin/forgot_password');
	}	
	public function sendEmail()
	{
		$template=$this->admin_model->getRecord('template', array('template_type'=>1));
		if($template!=0)
		{
			$customer_name="Govinda Yadav";
			$customer_address="Bhandari Mill Agniban, Sahakar Nagar, Snehlataganj, Indore, Madhya Pradesh 452001, India";
			$invoice_id="10";
			$order_name="1558591967";
			$order_amount="10 KD";
			$cust_email="parkhya.developer@gmail.com";
			$subject="Tanker App";
			/*
			 * customer_name
			 * customer_address
			 * invoice_id
			 * issue_date
			 * order_name
			 * order_amount
			 * total_amount*/
			 
			$content1=str_replace('customer_name', $customer_name, $template[0]['content']);
			$content2=str_replace('customer_address', $customer_address, $content1);
			$content3=str_replace('invoice_id', $invoice_id, $content2);
			$content4=str_replace('issue_date', date('Y-m-d'), $content3);
			$content5=str_replace('order_name', $order_name, $content4);
			$content6=str_replace('order_amount', $order_amount, $content5);
			
			echo $this->admin_model->sendMail("mannu.donps@gmail.com",$subject,$content6);
		}
	}
}

?>