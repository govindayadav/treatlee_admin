<?php
if (!defined('BASEPATH'))exit('No direct script access allowed');
include (APPPATH . 'libraries/REST_Controller.php');

class Master extends REST_Controller 
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
	public function getAnimal_post()
	{
		//This function is for getting the animal list
		$page_no		= $this->input->post('page_no');
		$limit			= $this->input->post('limit');
		$language		= $this->input->post('language');
		$limit1			= $page_no * $limit;
		$start			= $limit1 - $limit;
		
		$query="SELECT * FROM `animal` ORDER BY `animal_name` ASC LIMIT ".$start.','.$limit;
		$result=$this->user_model->getRecordQuery($query);
		if($result==0)
		{
			$post['message']=$language==0?"No Animal Found ":"";
			$post['status']=0;
		}
		else 
		{
			$post['message']=$language==0?"success":"ناجح";
			$post['status']=1;
			foreach ($result as $value) 
			{
				$animal['animal_id']			= $value['animal_id'];
				$animal['animal_name']			= $language==0?$value['animal_name']:$value['animal_name_arabic'];
				//$animal['animal_name_arabic']	= $value['animal_name_arabic'];
				$animal['animal_image']			= $value['animal_image']==''?'':$this->user_model->getUserImgUrl('uploads/animal', $value['animal_image']);
				$animal['created']				= $value['created'];
				$animal['updated']				= $value['updated'];
				
				$post['animal'][]		= $animal;
			}
			
			$sql1 = "SELECT * FROM `animal` ORDER BY `animal_name` ASC";
        	$count = $this->user_model->getRecordQuery($sql1);	
			if($count==0)
			{
				$post['is_more_data']="no";
			}
			else 
			{
				if(count($count)>$limit1)
				{
					$post['is_more_data']="yes";
				}
				else 
				{
					$post['is_more_data']="no";
				}
			}
		}
		echo json_encode($post);
	}
	public function getAnimalBreed_post()
	{
		//This function is for getting the animal breed
		$animal_id		= $this->input->post('animal_id');
		$page_no		= $this->input->post('page_no');
		$limit			= $this->input->post('limit');
		$language		= $this->input->post('language');
		$keyword		= $this->input->post('keyword');
		$limit1			= $page_no * $limit;
		$start			= $limit1 - $limit;
		
		$sql=($keyword=='')?'':" AND `breed_name` LIKE '%".$keyword."%' ";
		$query="SELECT * FROM `animal_breed` WHERE `animal_id`='".$animal_id.$sql."' ORDER BY `breed_name` ASC LIMIT ".$start.','.$limit;
		$result=$this->user_model->getRecordQuery($query);
		if($result==0)
		{
			$post['message']=$language==0?"No Animal Breed Found ":"";
			$post['status']=0;
		}
		else 
		{
			$post['message']=$language==0?"success":"ناجح";
			$post['status']=1;
			foreach ($result as $value) 
			{
				$breed['animal_breed_id']		= $value['animal_breed_id'];
				$breed['animal_id']				= $value['animal_id'];
				$breed['breed_name']			= $language==0?$value['breed_name']:$value['breed_name_arabic'];
				//$breed['breed_name_arabic']		= $value['breed_name_arabic'];
				$breed['created']				= $value['created'];
				$breed['updated']				= $value['updated'];
				$post['animal_breed'][]			= $breed;
			}
			
			$sql1 = "SELECT * FROM `animal_breed` WHERE `animal_id`='".$animal_id.$sql."'";
        	$count = $this->user_model->getRecordQuery($sql1);	
			if($count==0)
			{
				$post['is_more_data']="no";
			}
			else 
			{
				if(count($count)>$limit1)
				{
					$post['is_more_data']="yes";
				}
				else 
				{
					$post['is_more_data']="no";
				}
			}
		}
		echo json_encode($post);
	}
	public function getHotelList_post()
	{
		//This function is for getting the hotel list
		
		$country_id		= $this->input->post('country_id');
		$city_id		= $this->input->post('city_id');
		$latitude		= $this->input->post('latitude');
		$longitude		= $this->input->post('longitude');
		$page_no		= $this->input->post('page_no');
		$limit			= $this->input->post('limit');
		$language		= $this->input->post('language');
		$date_from		= $this->input->post('date_from');
		$date_to		= $this->input->post('date_to');
		$animal_id		= $this->input->post('animal_id');
		$distance		= $this->input->post('distance');
		$limit1			= $page_no * $limit;
		$start			= $limit1 - $limit;
		
		$date_sql = $animal_sql ='';	
		/*$check_code=$this->checkSecurityCode_post($user_id, $security_code);
		if($check_code['status']==1)
		{*/
			//now getting the timezone of this country
			$country=$this->user_model->getRecord('country', array('country_id'=>$country_id));
			$time_zone	= $country[0]['time_zone'];
			
			if($date_from!='' && $date_to!='')
			{
				$days=$this->user_model->dateDiffInDays($date_from, $date_to);
				
				$date_sql=" AND `minimum_stay` <= '".$days."' ";
				$booking_sql='';
			}
			$country_sql	= ($country_id=='')?'':" AND `country_id`='".$country_id."'";
			$city_sql		= ($city_id=='')?'':" AND `city_id`='".$city_id."'";
			$animal_sql		= ($animal_id=='')?'':" AND ap.`animal_id`='".$animal_id."' ";
			$distance_sql	= ($distance=='')?'':" HAVING distance <= '500' ";
			
			
			$query= "SELECT *,(3959 * ACOS( COS( RADIANS(".$latitude.") ) * COS( RADIANS(`latitude`) ) * COS( RADIANS( `longitude` ) - RADIANS(".$longitude.") ) + SIN( RADIANS(".$latitude.") ) * SIN( RADIANS( `latitude` ) ) ) ) 
							AS distance FROM hotel h JOIN `allowed_pets` ap ON h.`hotel_id`=ap.`hotel_id` WHERE  `is_disable`='1' ".$country_sql.$city_sql.$date_sql.$animal_sql." GROUP BY h.`hotel_id` ".$distance_sql." ORDER BY distance ASC LIMIT ".$start.",".$limit;
							
			$result=$this->user_model->getRecordQuery($query);
			//echo $this->db->last_query();die();
			if($result==0)
			{
				$post['message']=$language==0?"No Hotel Found ":"";
				$post['status']=0;
			}
			else 
			{
				$post['message']=$language==0?"success":"ناجح";
				$post['status']=1;
				
				foreach ($result as $value) 
				{
					$hotel['hotel_id']				= $value['hotel_id'];
					$hotel['hotel_name']			= ($language==0)?$value['hotel_name']:$value['hotel_name_ar'];
					$hotel['description']			= ($language==0)?$value['description_en']:$value['description_ar'];
					$hotel['hotel_address']			= $value['hotel_address'];
					$hotel['latitude']				= $value['latitude'];
					$hotel['longitude']				= $value['longitude'];
					$hotel['country_id']			= $value['country_id'];
					$hotel['city_id']				= $value['city_id'];
					$hotel['price']					= $value['price'];
					$hotel['check_from']			= $value['check_from'];
					$hotel['check_to']				= $value['check_to'];
					$hotel['minimum_stay']			= $value['minimum_stay'];
					$hotel['commission']			= $value['commission'];
					$hotel['self_check_in']			= $value['self_check_in'];
					$hotel['home_pickup']			= $value['home_pickup'];
					$hotel['cancellation_free']		= $value['cancellation_free'];
					
					$hotel['hotel_facility']= array();
					$hotel['hotel_gallery']	= array();
					$hotel['hotel_service']	= array();
					$hotel['hotel_time']	= array();
					$hotel['hotel_policy']	= array();
					
					$queryt="SELECT * FROM `taxes` WHERE `hotel_id`='".$value['hotel_id']."' AND `is_disable`='0' ORDER BY `taxes_id` DESC LIMIT 1";
					$tax=$this->user_model->getRecordQuery($queryt);
					$hotel['tax_service_amount']	= ($tax==0)?0:$tax[0]['tax'];
					//now getting the hotel gallery
					$gallery_det=$this->user_model->getRecord('hotel_gallery', array('hotel_id'=>$value['hotel_id']));
					if($gallery_det!=0)
					{
						foreach ($gallery_det as $valueg) 
						{
							$gallery['hotel_gallery_id']	= $valueg['hotel_gallery_id'];
							$gallery['hotel_id']			= $valueg['hotel_id'];
							$gallery['hotel_image']			= $valueg['hotel_image']==''?'':$this->user_model->getUserImgUrl('uploads/hotel', $valueg['hotel_image']);
							$gallery['created']				= $valueg['created'];
							$hotel['hotel_gallery'][]	= $gallery;
						}
					}
					//now getting the hotel facility
					//$query1="SELECT hf.*,f.`facility_name`,f.`facility_name_arabic` FROM `hotel_facility` hf JOIN `facilities` f ON hf.`facility_id`=f.`facility_id` WHERE hf.`hotel_id`='".$value['hotel_id']."'";
					$query1="SELECT * FROM `hotel_facility` WHERE `hotel_id`='".$value['hotel_id']."'";
					$facility_det=$this->user_model->getRecordQuery($query1);
					//$facility_det=$this->user_model->getRecord('hotel_facility', array('hotel_id'=>$value['hotel_id']));
					if($facility_det!=0)
					{
						foreach ($facility_det as $valuef) 
						{
							$facility['hotel_facility_id']		= $valuef['hotel_facility_id'];
							$facility['hotel_id']				= $valuef['hotel_id'];
							$facility['facility_id']			= $valuef['facility_id'];
							$facility['facility_english']		= $valuef['facility_english'];
							$facility['facility_arabic']		= $valuef['facility_arabic'];
							$facility['description_en']			= $valuef['description_en'];
							$facility['description_ar']			= $valuef['description_ar'];
							$facility['amount']					= $valuef['amount'];
							$facility['is_highlight']			= $valuef['is_highlight'];
							$facility['created']				= $valuef['created'];
							$facility['updated']				= $valuef['updated'];
							
							$hotel['hotel_facility'][]	= $facility;
						}
					}
					//now getting the hotel service
					//$query2="SELECT hs.*,s.`service_name` FROM `hotel_service` hs JOIN `service` s ON hs.`service_id`=s.`service_id` WHERE hs.`hotel_id`='".$value['hotel_id']."'";
					$query2="SELECT * FROM `hotel_service` WHERE `hotel_id`='".$value['hotel_id']."'";
					$service_det=$this->user_model->getRecordQuery($query2);
					//$service_det=$this->user_model->getRecord('hotel_service', array('hotel_id'=>$value['hotel_id']));
					if($service_det!=0)
					{
						foreach ($service_det as $values) 
						{
							$service['hotel_service_id']	= $values['hotel_service_id'];
							$service['hotel_id']			= $values['hotel_id'];
							$service['service_id']			= $values['service_id'];
							$service['service_english']		= $values['service_english'];
							$service['service_arabic']		= $values['service_arabic'];
							$service['amount']				= $values['amount'];
							$service['created']				= $values['created'];
							$service['updated']				= $values['updated'];
							
							$hotel['hotel_service'][]	= $service;
						}
					}
					//now getting the hotel timeing
					$time_det=$this->user_model->getRecord('hotel_time', array('hotel_id'=>$value['hotel_id']));
					if($time_det!=0)
					{
						foreach ($time_det as $valuet) 
						{
							$time['hotel_time_id']	= $valuet['hotel_time_id'];
							$time['hotel_id']		= $valuet['hotel_id'];
							$time['time_type']		= $valuet['time_type'];
							$time['time_from']		= $valuet['time_from'];
							$time['time_to']		= $valuet['time_to'];
							$time['created']		= $valuet['created'];
							$time['updated']		= $valuet['updated'];
							
							$hotel['hotel_time'][]	= $time;
						}
					}
					//Now getting the cencallation policy
					$policy_det=$this->user_model->getRecord('hotel_cancellation', array('hotel_id'=>$value['hotel_id']));
					if($policy_det!=0)
					{
						foreach ($policy_det as $valuep) 
						{
							$policy['hotel_cancellation_id']	= $valuep['hotel_cancellation_id'];
							$policy['hotel_id']					= $valuep['hotel_id'];
							$policy['cancellation_policy_id']	= $valuep['cancellation_policy_id'];
							$policy['policy_name']				= $valuep['policy_name'];
							$policy['policy_text']				= $valuep['policy_text'];
							$policy['amount']					= $valuep['amount'];
							$policy['created']					= $valuep['created'];
							$policy['updated']					= $valuep['updated'];
							
							$hotel['hotel_policy'][]	= $policy;
						}
					}
					//No getting the important information
					$info_det=$this->user_model->getRecord('information', array('hotel_id'=>$value['hotel_id']));
					if($info_det!=0)
					{
						foreach ($info_det as $valuei) 
						{
							$infor['information_id']	= $valuei['information_id'];
							$infor['hotel_id']			= $valuei['hotel_id'];
							$infor['title']				= $valuei['title'];
							$infor['title_ar']			= $valuei['title_ar'];
							$infor['description_ar']	= $valuei['description_ar'];
							$infor['description']		= $valuei['description'];
							$infor['created']			= $valuei['created'];
							$infor['updated']			= $valuei['updated'];
							
							$hotel['information'][]	= $infor;
						}
					}
					$post['hotel'][]		= $hotel;
				}
			}
		/*}
		else 
		{
			$post['message']= $check_code['message'];
			$post['status']	= $check_code['status'];
		}*/
		echo json_encode($post);
	}
	public function checkRoomAvailability_post()
	{
		//This function is for checking the room availability
		$user_id			= $this->input->post('user_id');
		$security_code		= $this->input->post('security_code');
		$language			= $this->input->post('language');
		$hotel_id			= $this->input->post('hotel_id');
		$animal_id			= $this->input->post('animal_id');
		$breed_id			= $this->input->post('breed_id');
		$from_date			= $this->input->post('from_date');
		$to_date			= $this->input->post('to_date');
		
		//now check the security code
		$check_code=$this->checkSecurityCode_post($user_id, $security_code);
		if($check_code['status']==1)
		{
			//now check the room availability
			$data=array(
						'hotel_id'		=> $hotel_id,
						'animal_id'		=> $animal_id,
						'breed_id'		=> $breed_id,
						'room_count>'	=> 0);
			$result=$this->user_model->getRecord('room_availability', $data);
			if($result==0)
			{
				$post['message']=$language==0?"no room available":"ناجح";
				$post['status']=5;
			}
			else 
			{
				//now check the room availability for date range
				$data1=array(
						'hotel_id'	=> $hotel_id,
						'animal_id'	=> $animal_id,
						'breed_id'	=> $breed_id);
				$check_animal=$this->user_model->getRecord('allowed_pets', $data1);
				if($check_animal==0)
				{
					$post['message']=$language==0?"pet not allowed":"الحيوانات الأليفة غير مسموح بها";
					$post['status']=6;
				}
				else 
				{
					$post['message']=$language==0?"success":"ناجح";
					$post['status']=1;
				}
				/*$post['message']=$language==0?"success":"ناجح";
				$post['status']=1;*/
			}
		}
		else 
		{
			$post['message']= $check_code['message'];
			$post['status']	= $check_code['status'];
		}
		echo json_encode($post);
	}
	/*public function checkAllowedPet_pet()
	{
		//This function is for check the allowd pet in hotel
		$hotel_id			= $this->input->post('hotel_id');
		$animal_id			= $this->input->post('animal_id');
		$animal_breed_id	= $this->input->post('animal_breed_id');
		$user_id			= $this->input->post('user_id');
		$security_code		= $this->input->post('security_code');
		$language			= $this->input->post('language');
		
		$check_code=$this->checkSecurityCode_post($user_id, $security_code);
		if($check_code['status']==1)
		{
			$data=array(
						'hotel_id'	=> $hotel_id,
						'animal_id'	=> $animal_id,
						'breed_id'	=> $animal_breed_id);
			$check_animal=$this->user_model->getRecord('allowed_pets', $data);
			if($check_animal==0)
			{
				$post['message']=$language==0?"pet not allowed":"الحيوانات الأليفة غير مسموح بها";
				$post['status']=6;
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
	}*/
	public function bookHotel_post()
	{
		//This function is for book the hotel
		$hotel_id				= $this->input->post('hotel_id');
		$user_id				= $this->input->post('user_id');
		$security_code			= $this->input->post('security_code');
		$language				= $this->input->post('language');
		$user_email_id			= $this->input->post('user_email_id');
		$user_mobile_no			= $this->input->post('user_mobile_no');
		$from_date				= $this->input->post('from_date');
		$to_date				= $this->input->post('to_date');
		$check_in				= $this->input->post('check_in');
		$check_out				= $this->input->post('check_out');
		$total_amount			= $this->input->post('total_amount');
		//$commission				= $this->input->post('commission');
		//$tax_service_amount		= $this->input->post('tax_service_amount');	
		$user_payment			= $this->input->post('user_payment');
		$check_in_type			= $this->input->post('check_in_type');
		$check_out_type			= $this->input->post('check_out_type');
		$pickup_address			= $this->input->post('pickup_address');
		$drop_address			= $this->input->post('drop_address');
		$facilities				= $this->input->post('facilities');
		$services				= $this->input->post('services');
		$user_payment_status	= $this->input->post('user_payment_status');
		$pet_animal_id			= $this->input->post('pet_animal_id');
		$unique_booking_id 		= 'TREAT' . time() . mt_rand(100, 999);
		//$hotel_amount			= $this->input->post('hotel_amount');
		//$commission_amount	= $this->input->post('commission_amount');
		//$gateway_amount		= $this->input->post('gateway_amount');
		
		$check_code=$this->checkSecurityCode_post($user_id, $security_code);
		if($check_code['status']==1)
		{
			$query="SELECT * FROM `amount_setting` ORDER BY `amount_setting_id` DESC LIMIT 1";
			$setting=$this->user_model->getRecordQuery($query);
			
			$gateway_amount		= $setting[0]['gateway_amount'];
			//$commission_amount	= ($commission / 100) * $total_amount;
			
			$data=array(
						'unique_booking_id'		=> $unique_booking_id,
						'hotel_id'				=> $hotel_id,
						'user_id'				=> $user_id,
						'user_email_id'			=> $user_email_id,
						'user_mobile_no'		=> $user_mobile_no,
						'from_date'				=> $from_date,
						'to_date'				=> $to_date,
						'check_in'				=> $check_in,
						'check_out'				=> $check_out,
						'total_amount'			=> $total_amount,
						//'commission'			=> $commission,
						//'commission_amount'		=> $commission_amount,
						'gateway_amount'		=> $gateway_amount,
						'user_payment'			=> $user_payment,
						'user_payment_status'	=> $user_payment_status,
						'check_in_type'			=> $check_in_type,
						'check_out_type'		=> $check_out_type,
						'pickup_address'		=> $pickup_address,
						'drop_address'			=> $drop_address);
						
			$result=$this->user_model->saveRecord('booking', $data);
			if($result==0)
			{
				$post['message']=$language==0?"failure":"فشل";	
				$post['status']=0;
			}
			else 
			{
				//now add the facility and services
				if($facilities!='')
				{
					$all_facility=explode(',', $facilities);
					foreach ($all_facility as $value) 
					{
						$data=array(
									'hotel_id'			=> $hotel_id,
									'hotel_facility_id'	=> $value,
									'booking_id'		=> $result);
						$this->user_model->saveRecord('booking_facility', $data);
					}	
				}
				if($services!='')
				{
					$query="SELECT * FROM `hotel_service` WHERE `hotel_service_id` IN (".$services.")";
					$all_service=$this->user_model->getRecordQuery($query);
					foreach ($all_service as $values) 
					{
						$data=array(
									'hotel_id'			=> $hotel_id,
									'hotel_service_id'	=> $values['hotel_service_id'],
									'booking_id'		=> $result,
									'amount'			=> $values['amount']);
						$this->user_model->saveRecord('booking_facility', $data);
					}
				}
				
				$post['message']	= $language==0?"success":"ناجح";
				$post['status']		= 1;
				$post['booking_id']	= $unique_booking_id;	
			}
		}
		else 
		{
			$post['message']= $check_code['message'];
			$post['status']	= $check_code['status'];
		}
		echo json_encode($post);
	}
	public function getBooking_post()
	{
		//This function is for getting the booking list
		$user_id	= $this->input->post('user_id');
		$page_no	= $this->input->post('page_no');
		$limit		= $this->input->post('limit');
		$language	= $this->input->post('language');
		$limit1		= $page_no * $limit;
		$start		= $limit1 - $limit;
		
		$query="SELECT * FROM `booking` WHERE `user_id`='".$user_id."' ORDER BY `booking_id` DESC LIMIT ".$start.",".$limit;
		$result=$this->user_model->getRecordQuery($query);
		if($result==0)
		{
			$post['message']=$language==0?"no booking found":"لم يتم العثور على حجز";	
			$post['status']=0;
		}
		else 
		{
			foreach ($result as $value) 
			{
				$booking['booking_id']			= $value['booking_id'];
				$booking['unique_booking_id']	= $value['unique_booking_id'];
				$booking['hotel_id']			= $value['hotel_id'];
				$booking['user_id']				= $value['user_id'];
				$booking['pet_animal_id']		= $value['pet_animal_id'];
				$booking['user_email_id']		= $value['user_email_id'];
				$booking['user_mobile_no']		= $value['user_mobile_no'];
				$booking['from_date']			= $value['from_date'];
				$booking['to_date']				= $value['to_date'];
				$booking['check_in']			= $value['check_in'];
				$booking['check_out']			= $value['check_out'];
				$booking['total_amount']		= $value['total_amount'];
				$booking['booking_status']		= $value['booking_status'];
				$booking['user_payment']		= $value['user_payment'];
				$booking['check_in_type']		= $value['check_in_type'];
				$booking['check_out_type']		= $value['check_out_type'];
				$booking['pickup_address']		= $value['pickup_address'];
				$booking['drop_address']		= $value['drop_address'];
				$booking['created']				= $value['created'];
				$booking['updated']				= $value['updated'];
				$post['booking'][]				= $booking;
			}
		}
		echo json_encode($post);
	}
}
?>