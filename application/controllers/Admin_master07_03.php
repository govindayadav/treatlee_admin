<?php
/**
 * 
 */
if (!defined('BASEPATH'))exit('No direct script access allowed');
include (APPPATH . 'libraries/REST_Controller.php');

class Admin_master extends REST_Controller 
{
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
	/****************** Service Start ******************/
	public function addService_post()
	{
		//This function is for adding the services
		$service_name			= $this->input->post('service_name');
		$service_name_arabic	= $this->input->post('service_name_arabic');
		$user_id				= $this->input->post('user_id');
		$amount					= $this->input->post('amount');
		
		$data=array(
					'service_name'			=> $service_name,
					'service_name_arabic'	=> $service_name_arabic,
					'amount'				=> $amount,
					'created_by_id'			=> $user_id,
					'updated_by_id'			=> $user_id,
					'created'				=> date('Y-m-d H:i:s'));
		$result=$this->user_model->saveRecord('service', $data);
		if($result==0)
		{
			$post['message']="failure";
			$post['status']=0;
		}
		else 
		{
			$post['message']="success";
			$post['status']=1;
		}
		echo json_encode($post);
	}
	public function getService_post()
	{
		//This function is for get the service master
		$page_no		= $this->input->post('page_no');
		$limit			= $this->input->post('limit');
		$language		= $this->input->post('language');
		$limit1			= $page_no * $limit;
		$start			= $limit1 - $limit;
		$pagination		= " LIMIT ".$start.','.$limit;
		
		$query="SELECT * FROM `service` WHERE `is_delete`=0 ORDER BY `service_id` DESC";
		$result=$this->user_model->getRecordQuery($query.$pagination);
		if($result==0)
		{
			$post['message']="failure";
			$post['status']=0;
		}
		else 
		{
			$count=$this->user_model->getRecordQueryCount($query);
			$post['message']	= "success";
			$post['status']		= 1;
			$post['total']		= $count;
			
			foreach ($result as $value) 
			{
				$serv['service_id']				= $value['service_id'];
				$serv['service_name']			= $value['service_name'];
				$serv['service_name_arabic']	= $value['service_name_arabic'];
				$serv['amount']					= $value['amount'];
				$serv['created']				= $value['created'];
				$serv['updated']				= $value['updated'];
				$post['service'][]				= $serv;
			}
		}
		echo json_encode($post);
	}
	public function deleteService_post()
	{
		//This function is for delete the service master
		$service_id=$this->input->post('service_id');
		
		$this->user_model->updateRecord('service',array('is_delete'=>1),array('service_id'=>$service_id));
		
		$post['message']="success";
		$post['status']=1;
		echo json_encode($post);
	}
	public function updateService_post()
	{
		//This function is for update the services
		$service_name			= $this->input->post('service_name');
		$service_name_arabic	= $this->input->post('service_name_arabic');
		$user_id				= $this->input->post('user_id');
		$amount					= $this->input->post('amount');
		$service_id				= $this->input->post('service_id');
		
		$data=array(
					'service_name'			=> $service_name,
					'service_name_arabic'	=> $service_name_arabic,
					'amount'				=> $amount,
					'created_by_id'			=> $user_id,
					'updated_by_id'			=> $user_id,
					);
		$this->user_model->updateRecord('service',$data,array('service_id'=>$service_id));
		
		$post['message']="success";
		$post['status']=1;
		echo json_encode($post);
	}
	/****************** Service End ******************/
	/****************** Animal Start ******************/
	public function addAnimal_post()
	{    
		
		//This function is for adding the animals
		$animal_name		= $this->input->post('animal_name');
		$animal_name_arabic	= $this->input->post('animal_name_arabic');
		$user_id			= $this->input->post('user_id');
		
		$check=$this->user_model->getRecord('animal', array('animal_name'=>$animal_name));
		if($check==0)
		{
			if(isset($_FILES['animal_image']['name']))
			{
				if($_FILES['animal_image']['name']!='')
				{
					$imanename = $_FILES['animal_image']['name'];
					$temp = explode(".", $_FILES["animal_image"]["name"]);
					$newfilename = rand(1, 99999) . '.' . end($temp);
					//This is for upload the image
					$path= './uploads/animal/'.$newfilename;
					$upload = copy($_FILES['animal_image']['tmp_name'], $path);
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
						'animal_name'	=> $animal_name,
						'animal_name_arabic'=>$animal_name_arabic,
						'animal_image'	=> $newfilename,
						'created'		=> date('Y-m-d H:i:s'),
						'created_by_id'	=> $user_id,
						'updated_by_id'	=> $user_id);
			$result=$this->user_model->saveRecord('animal', $data);
			if($result==0)
			{
				$post['message']="failure";
				$post['status']=0;
			}
			else 
			{
				$post['message']="success";
				$post['status']=1;
			}
		}
		else 
		{
			$post['message']="failure";
			$post['status']=0;
		}
		echo json_encode($post);
	}
	public function updateAnimal_post()
	{
		//This function is for adding the animals
		$animal_name		= $this->input->post('animal_name');
		$animal_name_arabic	= $this->input->post('animal_name_arabic');
		$user_id			= $this->input->post('user_id');
		$animal_id			= $this->input->post('animal_id');	
		
		$check=$this->user_model->getRecord('animal', array('animal_name'=>$animal_name,'animal_id!='=>$animal_id));
		if($check==0)
		{
			$detail=$this->user_model->getRecord('animal', array('animal_id'=>$animal_id));
			if(isset($_FILES['animal_image']['name']))
			{
				if($_FILES['animal_image']['name']!='')
				{
					$imanename = $_FILES['animal_image']['name'];
					$temp = explode(".", $_FILES["animal_image"]["name"]);
					$newfilename = rand(1, 99999) . '.' . end($temp);
					//This is for upload the image
					$path= './uploads/animal/'.$newfilename;
					$upload = copy($_FILES['animal_image']['tmp_name'], $path);
				}
				else 
				{
					$newfilename=$detail[0]['animal_image'];
				}
			}
			else 
			{
				$newfilename=$detail[0]['animal_image'];
			}
			
			$data=array(
						'animal_name'		=> $animal_name,
						'animal_name_arabic'=> $animal_name_arabic,
						'animal_image'		=> $newfilename,
						'updated_by_id'		=> $user_id);
			$this->user_model->updateRecord('animal',$data,array('animal_id'=>$animal_id));						
			
			$post['message']="success";
			$post['status']=1;
		}
		else 
		{
			$post['message']="failure";
			$post['status']=0;
		}
		echo json_encode($post);
	}
	public function getAnimal_post()
	{
		//This function is for getting the animal list
		$query="SELECT * FROM `animal` WHERE `is_delete`=0 ORDER BY `animal_name` ASC";
		$result=$this->user_model->getRecordQuery($query);
		
		if($result==0)
		{
			$post['message']="failure";
			$post['status']=0;
		}
		else 
		{
			//$post['message']="success";
			//$post['status']=1;
			foreach ($result as $value) 
			{
				$ani['animal_id']			= $value['animal_id'];
				$ani['animal_name']			= $value['animal_name'];
				$ani['animal_name_arabic']	= $value['animal_name_arabic'];
				$ani['animal_image']		= $value['animal_image']==''?'':$this->user_model->getUserImgUrl('uploads/animal', $value['animal_image']);
				$ani['created']				= $value['created'];
				$ani['updated']				= $value['updated'];
				$post['animal'][]			= $ani;
			}
		}
		return $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode($post));
	//	return $this->response(json_encode($post), REST_Controller::HTTP_OK);
		//echo json_encode($post);
	}
	public function deleteAnimal_post()
	{
		//This function is for delete the animal
		$animal_id	= $this->input->post('animal_id');
		$user_id	= $this->input->post('user_id');	
		
		$this->user_model->updateRecord('animal',array('is_delete'=>1,'updated_by_id'=>$user_id),array('animal_id'=>$animal_id));
		
		$post['message']="success";
		$post['status']=1;
		
		echo json_encode($post);
	}
	/****************** Animal End ******************/
	/****************** Animal Breed Start ******************/
	public function addAnimalBreed_post()
	{
		//This function is for animal Breed
		$animal_id			= $this->input->post('animal_id');
		$breed_name			= $this->input->post('breed_name');
		$breed_name_arabic	= $this->input->post('breed_name_arabic');
		$user_id			= $this->input->post('user_id');
		
		$check=$this->user_model->getRecord('animal_breed', array('animal_id'=>$animal_id,'breed_name'=>$breed_name));
		if($check==0)
		{
			$data=array(
						'animal_id'			=> $animal_id,
						'breed_name'		=> $breed_name,
						'breed_name_arabic'	=> $breed_name_arabic,
						'create_by_id'		=> $user_id,
						'update_by_id'		=> $user_id,
						'created'			=> date('Y-m-d H:i:s'));
			$result=$this->user_model->saveRecord('animal_breed', $data);
			if($result==0)
			{
				$post['message']="failure";
				$post['status']=0;
			}
			else 
			{
				$post['message']="success";
				$post['status']=1;
			}
		}
		else 
		{
			$post['message']="failure";
			$post['status']=0;
		}
		echo json_encode($post);
	}
	public function updateAnimalBreed_post()
	{
		//This function is for update the animal breed
		$animal_id			= $this->input->post('animal_id');
		$breed_name			= $this->input->post('breed_name');
		$user_id			= $this->input->post('user_id');
		$animal_breed_id	= $this->input->post('animal_breed_id');
		$breed_name_arabic	= $this->input->post('breed_name_arabic');
		
		$check=$this->user_model->getRecord('animal_breed', array('animal_id'=>$animal_id,'breed_name'=>$breed_name,'animal_breed_id!='=>$animal_breed_id));
		if($check==0)
		{
			$data=array(
						'animal_id'			=> $animal_id,
						'breed_name'		=> $breed_name,
						'breed_name_arabic'	=> $breed_name_arabic,
						'update_by_id'		=> $user_id );
			$this->user_model->updateRecord('animal_breed',$data,array('animal_breed_id'=>$animal_breed_id));			
			$post['message']="success";
			$post['status']=1;
		}
		else 
		{
			$post['message']="failure";
			$post['status']=0;
		}
		echo json_encode($post);
	}
	public function deleteAnimalBreed_post()
	{
		//This function is for delete the animal breed
		$animal_breed_id	= $this->input->post('animal_breed_id');
		$user_id			= $this->input->post('user_id');
		
		$this->user_model->updateRecord('animal_breed',array('is_delete'=>1,'update_by_id'=>$user_id),array('animal_breed_id'=>$animal_breed_id));
		
		$post['message']="success";
		$post['status']=1;
		
		echo json_encode($post);
	}
	public function getAnimalBreed_post()
	{
		//This function is for getting the animal Breed
		$animal_id=$this->input->post('animal_id');
		$query="SELECT * FROM `animal_breed` WHERE `animal_id`='".$animal_id."' AND `is_delete`=0 ORDER BY `breed_name` ASC";
		$result=$this->user_model->getRecordQuery($query);
		
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
				$ani['animal_id']			= $value['animal_id'];
				$ani['animal_breed_id']		= $value['animal_breed_id'];
				$ani['breed_name']			= $value['breed_name'];
				$ani['breed_name_arabic']	= $value['breed_name_arabic'];
				$ani['created']				= $value['created'];
				$ani['updated']				= $value['updated'];
				$post['breed'][]			= $ani;
			}
		}
		echo json_encode($post);
	}
	public function getAllBreed_post()
	{
		$query="SELECT * FROM `animal` WHERE `is_delete`=0 ORDER BY `animal_name` ASC";
		$result=$this->user_model->getRecordQuery($query);
		
		if($result==0)
		{
			$post['message']="failure";
			$post['status']=0;
		}
		else 
		{
			//$post['message']="success";
			//$post['status']=1;
			foreach ($result as $value) 
			{
				$ani['animal_id']			= $value['animal_id'];
				$ani['animal_name']			= $value['animal_name'];
				$ani['animal_name_arabic']	= $value['animal_name_arabic'];
				$ani['animal_image']		= $value['animal_image']==''?'':$this->user_model->getUserImgUrl('uploads/animal', $value['animal_image']);
				$ani['created']				= $value['created'];
				$ani['updated']				= $value['updated'];
				$ani['breed']				= array();
				
				//$animal_id=$this->input->post('animal_id');
				$query1="SELECT * FROM `animal_breed` WHERE `animal_id`='".$value['animal_id']."' AND `is_delete`=0 ORDER BY `breed_name` ASC";
				$result1=$this->user_model->getRecordQuery($query1);
				
				if($result1!=0) 
				{
					foreach ($result1 as $valueb) 
					{
						$breed['animal_id']			= $valueb['animal_id'];
						$breed['animal_breed_id']	= $valueb['animal_breed_id'];
						$breed['breed_name']		= $valueb['breed_name'];
						$breed['breed_name_arabic']	= $valueb['breed_name_arabic'];
						$breed['created']			= $valueb['created'];
						$breed['updated']			= $valueb['updated'];
						$ani['breed'][]				= $breed;
					}
				}
				$post['animal'][]			= $ani;
			}
		}
		echo json_encode($post);
	}	
	/****************** Animal Breed End ******************/
	
	/****************** Facility Start ******************/
	public function addFacility_post()
	{
		//This function is for add the facility
		$facility_name			= $this->input->post('facility_name');
		$facility_name_arabic	= $this->input->post('facility_name_arabic');
		$user_id				= $this->input->post('user_id');
		
		$data=array(
					'facility_name'			=> $facility_name,
					'facility_name_arabic'	=> $facility_name_arabic,
					'created'				=> date('Y-m-d H:i:s'),
					'created_by_id'			=> $user_id,
					'updated_by_id'			=> $user_id);
					
		$result=$this->user_model->saveRecord('facilities', $data);
		if($result==0)
		{
			$post['message']="failure";
			$post['status']=0;
		}
		else 
		{
			$post['message']="success";
			$post['status']=1;
		}
		echo json_encode($post);
	}
	public function updateFacility_post()
	{
		//This function is for update facility
		$facility_id			= $this->input->post('facility_id');
		$facility_name			= $this->input->post('facility_name');
		$facility_name_arabic	= $this->input->post('facility_name_arabic');
		$user_id				= $this->input->post('user_id');
		
		$data=array(
					'facility_name'			=> $facility_name,
					'facility_name_arabic'	=> $facility_name_arabic,
					'updated_by_id'			=> $user_id);
		
		$this->user_model->updateRecord('facilities',$data,array('facility_id'=>$facility_id));			
		
		$post['message']="success";
		$post['status']=1;
		echo json_encode($post);
	}
	public function getFacility_post()
	{
		//This function is for getting the facility
		$query="SELECT * FROM `facilities` WHERE `is_delete`=0 ORDER BY `facility_id` DESC";
		$result=$this->user_model->getRecordQuery($query);
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
				$facili['facility_id']			= $value['facility_id'];
				$facili['facility_name']		= $value['facility_name'];
				$facili['facility_name_arabic']	= $value['facility_name_arabic'];
				$facili['created']				= $value['created'];
				$facili['updated']				= $value['updated'];
				$post['facility'][]				= $facili;
			}
		}
		echo json_encode($post);
	}
	public function deleteFacility_post()
	{
		//This function is for delete the facility
		$facility_id	= $this->input->post('facility_id');
		$user_id		= $this->input->post('user_id');
		
		$data=array(
					'is_delete'		=> 1,
					'updated_by_id'	=> $user_id);
		
		$this->user_model->updateRecord('facilities',$data,array('facility_id'=>$facility_id));			
		
		$post['message']="success";
		$post['status']=1;
		echo json_encode($post);
	}
	/****************** Facility End *******************/
	
	/****************** Country Start *****************/
	public function addCountry_post()
	{
		//This function is for add country master
		$country_code	= $this->input->post('country_code');
		$short_name		= $this->input->post('short_name');
		$country_name	= $this->input->post('country_name');
		$currency		= $this->input->post('currency');
		$phone_length	= $this->input->post('phone_length');
		$time_zone		= $this->input->post('time_zone');
		$user_id		= $this->input->post('user_id');
		
		$check=$this->user_model->getRecord('country', array('country_name'=>$country_name,'country_code'=>$country_code));
		if($check!=0)
		{
			$post['message']="country name or code already exist";
			$post['status']=0;
		}
		else 
		{
			if(isset($_FILES['currency_image']['name']))
			{
				if($_FILES['currency_image']['name']!='')
				{
					$imanename = $_FILES['currency_image']['name'];
					$temp = explode(".", $_FILES["currency_image"]["name"]);
					$newfilename = rand(1, 99999) . '.' . end($temp);
					//This is for upload the image
					$path= './uploads/currency/'.$newfilename;
					$upload = copy($_FILES['currency_image']['tmp_name'], $path);
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
			
			if(isset($_FILES['country_flag']['name']))
			{
				if($_FILES['country_flag']['name']!='')
				{
					$imanename1 = $_FILES['country_flag']['name'];
					$temp1 = explode(".", $_FILES["country_flag"]["name"]);
					$newfilename1 = rand(1, 99999) . '.' . end($temp1);
					//This is for upload the image
					$path1= './uploads/flages/'.$newfilename1;
					$upload = copy($_FILES['country_flag']['tmp_name'], $path1);
					
				}
				else 
				{
					$newfilename1="";
				}
			}
			else 
			{
				$newfilename1="";
			}
			
			$data=array(
						'country_code'		=> $country_code,
						'short_name'		=> $short_name,
						'country_name'		=> $country_name,
						'currency'			=> $currency,
						'currency_image'	=> $newfilename,
						'country_flag'		=> $newfilename1,
						'phone_length'		=> $phone_length,
						'time_zone'			=> $time_zone,
						'created_by_id'		=> $user_id,
						'updated_by_id'		=> $user_id,
						'created'			=> date('Y-m-d H:i:s'));
			
			$result=$this->user_model->saveRecord('country', $data);
			
			if($result==0)
			{
				$post['message']="failure";	
				$post['status']=0;
			}
			else 
			{
				$post['message']="success";
				$post['status']=1;
			}
		}
		echo json_encode($post);
	}
	public function updateCountry_post()
	{
		//This function is for update the country
		$country_id		= $this->input->post('country_id');
		$country_code	= $this->input->post('country_code');
		$short_name		= $this->input->post('short_name');
		$country_name	= $this->input->post('country_name');
		$currency		= $this->input->post('currency');
		$phone_length	= $this->input->post('phone_length');
		$time_zone		= $this->input->post('time_zone');
		$user_id		= $this->input->post('user_id');
		
		$check=$this->user_model->getRecord('country', array('country_name'=>$country_name,'country_code'=>$country_code,'country_id!='=>$country_id));
		if($check!=0)
		{
			$post['message']="country name or code already exist";
			$post['status']=0;
		}
		else 
		{
			$country=$this->user_model->getRecord('country', array('country_id'=>$country_id));	
			if(isset($_FILES['currency_image']['name']))
			{
				if($_FILES['currency_image']['name']!='')
				{
					$imanename = $_FILES['currency_image']['name'];
					$temp = explode(".", $_FILES["currency_image"]["name"]);
					$newfilename = rand(1, 99999) . '.' . end($temp);
					//This is for upload the image
					$path= './uploads/currency/'.$newfilename;
					$upload = copy($_FILES['currency_image']['tmp_name'], $path);
				}
				else 
				{
					$newfilename=$country[0]['currency_image'];
				}
			}
			else 
			{
				$newfilename=$country[0]['currency_image'];
			}
			
			if(isset($_FILES['country_flag']['name']))
			{
				if($_FILES['country_flag']['name']!='')
				{
					$imanename1 = $_FILES['country_flag']['name'];
					$temp1 = explode(".", $_FILES["country_flag"]["name"]);
					$newfilename1 = rand(1, 99999) . '.' . end($temp1);
					//This is for upload the image
					$path1= './uploads/flages/'.$newfilename1;
					$upload = copy($_FILES['country_flag']['tmp_name'], $path1);
				}
				else 
				{
					$newfilename1=$country[0]['country_flag'];
				}
			}
			else 
			{
				$newfilename1=$country[0]['country_flag'];
			}
			
			$data=array(
						'country_code'		=> $country_code,
						'short_name'		=> $short_name,
						'country_name'		=> $country_name,
						'currency'			=> $currency,
						'currency_image'	=> $newfilename,
						'country_flag'		=> $newfilename1,
						'phone_length'		=> $phone_length,
						'time_zone'			=> $time_zone,
						'updated_by_id'		=> $user_id,
						);
			$this->user_model->updateRecord('country',$data,array('country_id'=>$country_id));			
			$post['message']='success';
			$post['status']=1;
		}
		echo json_encode($post);
	}
	public function getCountry_post()
	{
		//This function is for getting the country list
		$query="SELECT * FROM `country` WHERE `is_delete`=0 ORDER BY `country_name` ASC";
		$result=$this->user_model->getRecordQuery($query);
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
				$country['country_id']		= $value['country_id'];
				$country['country_code']	= $value['country_code'];
				$country['short_name']		= $value['short_name'];
				$country['country_name']	= $value['country_name'];
				$country['currency']		= $value['currency'];
				$country['currency_image']	= $value['currency_image']==''?'':$this->user_model->getUserImgUrl('uploads/currency', $value['currency_image']);
				$country['country_flag']	= $value['country_flag']==''?'':$this->user_model->getUserImgUrl('uploads/flages', $value['country_flag']);
				$country['phone_length']	= $value['phone_length'];
				$country['time_zone']		= $value['time_zone'];
				$post['country'][]			= $country;
			}
		}
		echo json_encode($post);
	}
	public function deleteCountry_post()
	{
		$country_id		= $this->input->post('country_id');
		$user_id		= $this->input->post('user_id');
		
		$result=$this->user_model->updateRecord('country',array('is_delete'=>1),array('country_id'=>$country_id));
		if($result==0)
		{
			$post['message']="failure";	
			$post['status']=0;
		}
		else 
		{
			$post['message']="success";
			$post['status']=1;
		}	
		echo json_encode($post);
	}
	/****************** Country End ******************/
	
	/****************** City Start *****************/
	public function addCity_post()
	{
		//This function is for add the city
		$country_id		= $this->input->post('country_id');
		$user_id		= $this->input->post('user_id');
		$city_name		= $this->input->post('city_name');
		
		$check=$this->user_model->getRecord('city', array('country_id'=>$country_id,'city_name'=>$city_name));
		if($check==0)
		{
			$data=array(
						'city_name'		=> $city_name,
						'country_id'	=> $country_id,
						'created'		=> date('Y-m-d H:i:s'));
			$result=$this->user_model->saveRecord('city', $data);
			
			if($result==0)
			{
				$post['message']="failure";	
				$post['status']=0;
			}
			else 
			{
				$post['message']="success";
				$post['status']=1;
			}
		}
		else 
		{
			$post['message']="City name or code already exist";
			$post['status']=0;
		}
		echo json_encode($post);
	}
	public function updateCity_post()
	{
		//This function is for update the city master
		$city_id		= $this->input->post('city_id');
		$country_id		= $this->input->post('country_id');
		$user_id		= $this->input->post('user_id');
		$city_name		= $this->input->post('city_name');
		
		$check=$this->user_model->getRecord('city', array('country_id'=>$country_id,'city_name'=>$city_name,'city_id!='=>$city_id));
		if($check==0)
		{
			$data=array(
						'city_name'		=> $city_name,
						'country_id'	=> $country_id);
			$this->user_model->updateRecord('city',$data,array('city_id'=>$city_id));							
			
			$post['message']="success";
			$post['status']=1;
		}
		else 
		{
			$post['message']="City name or code already exist";
			$post['status']=0;
		}
		echo json_encode($post);
	}
	public function deleteCity_post()
	{
		//This function is for delete the city
		$city_id	= $this->input->post('city_id');	
		$user_id	= $this->input->post('user_id');
		
		$this->user_model->updateRecord('city',array('is_delete'=>1),array('city_id'=>$city_id));							
			
		$post['message']="success";
		$post['status']=1;
		
		echo json_encode($post);
	}
	public function getCityList_post()
	{
		//This function is for getting the city list
		$country_id=$this->input->post('country_id');
		$where=array('is_delete'=>0);
		if(!empty($country_id) && $country_id!=''){
			
			$where['country_id']=$country_id;
		}
		$result=$this->user_model->getRecord('city', $where);
		if($result==0)
		{
			$post['message']="No city found";
			$post['status']=0;
		}
		else 
		{
			$post['message']="success";
			$post['status']=1;
			foreach ($result as $value) 
			{   
				$country_name=$this->user_model->getRecord('country',array('country_id'=>$value['country_id']));
				
				if($country_name!=0){
					$list['country_name']	= $country_name[0]['country_name'];
				}
				
				$list['city_id']	= $value['city_id'];
				$list['city_name']	= $value['city_name'];
				$list['country_id']	= $value['country_id'];
				$post['city'][]		= $list;
			}
		}
		echo json_encode($post);
	}
	/****************** City End ******************/
}

?>