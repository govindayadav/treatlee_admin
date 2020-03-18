<?php
/**
 * |------------------------|
 * |@Govinda Yadav			|
 * |Parkhya Solution PVT LTD|
 * |Indore					|
 * |________________________|
 *
 * This controller is for admin 
 */
if (!defined('BASEPATH'))exit('No direct script access allowed');
include (APPPATH . 'libraries/REST_Controller.php');

class Hotel_master extends REST_Controller 
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
	public function createHotel_post()
	{
		//This function is for create hotel master
	}
}
?>