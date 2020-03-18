<?php
/**
 * 
 */
if (!defined('BASEPATH'))exit('No direct script access allowed');
include (APPPATH . 'libraries/REST_Controller.php');

class Menu_master extends REST_Controller 
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
  
	public function getUserType_post(){
		  $query="SELECT * FROM user_type WHERE user_type_id!=4";
		  $types=array();
		   $result=$this->user_model->getRecordQuery($query);
         if(!empty($result)){
          foreach($result as $type){
			  $types[]=$type;  
		  }
		 }	 
		
		echo '{"status":1,"data":'.json_encode($types).'}'; die();
	}

  function getMenu($id,$parent,$status=1){
        $menuss=array();
             if($status==0){
           
         $query="SELECT menu.id,parent,path,title,icon,class,badge,badgeClass,isExternalLink,menu_permission.status as view,menu_permission.edit,menu_permission.add FROM `menu` INNER JOIN menu_permission ON menu_permission.menu_id=menu.id  WHERE menu_permission.role_id=$id and menu.parent=$parent ORDER BY menu.`id` ASC";
 

        }else{
         $query="SELECT menu.id,parent,path,title,icon,class,badge,badgeClass,isExternalLink,menu_permission.status as view,menu_permission.edit,menu_permission.add FROM `menu` INNER JOIN menu_permission ON menu_permission.menu_id=menu.id  WHERE menu_permission.`status`=1 AND menu_permission.role_id=$id and menu.parent=$parent ORDER BY menu.`id` ASC";
 
        }

         $result=$this->user_model->getRecordQuery($query);
         if(!empty($result)){
          foreach($result as $menus){

            if($menus['isExternalLink']==1){
               $menus['isExternalLink']=true;
            }else{
               $menus['isExternalLink']=false;
            }
              $sub=$this->getMenu($id,$menus['id']);
              if(!empty($sub)){
                $menus['submenu']=$sub;  
              }else{
                $menus['submenu']=array();
              }
              
             $menuss[]=$menus;
          }
        }

         return $menuss;
  }
	  
	
public function getAdminMenu_post(){

      $menu=$this->getMenu(1,0,0); 

        echo json_encode($menu);  exit;

}
	   public function CheckPermission_post(){
           
   $user_id=($this->input->post('user_id'))?$this->input->post('user_id'):0;
  
    $user_type=($this->input->post('user_type'))?$this->input->post('user_type'):0;

    $path=($this->input->post('path'))?$this->input->post('path'):'';

    $check=$this->user_model->getRecord('user', array('user_id'=>$user_id,'user_type'=>$user_type));
  
  if($check!=0)
    {
        $query="SELECT distinct menu.id from menu INNER JOIN menu_permission ON menu.id=menu_permission.menu_id WHERE menu_permission.`status`=1 AND menu_permission.role_id=".$user_type." AND menu.path='".$path."'  ORDER BY menu.`id` ASC";

         $result=$this->user_model->getRecordQuery($query);

         if(!empty($result)){
          echo '{"status":1}';

         }else{
          echo '{"status":0}'; 
         }
        
    }   
     
     die();

   }



    public function getMenu_post(){
  
  $user_id=($this->input->post('user_id'))?$this->input->post('user_id'):0;
  
  $user_type=($this->input->post('user_type'))?$this->input->post('user_type'):0;
  
  $menu=array();
    
  $check=$this->user_model->getRecord('user', array('user_id'=>$user_id,'user_type'=>$user_type));
  
  if($check!=0)
    {
      $menu=$this->getMenu($user_type,0); 

    }

    echo json_encode($menu);  exit;
     
}
    
   public function staticMenu_post(){
  
  $user_id=($this->input->post('user_id'))?$this->input->post('user_id'):0;
  
  $user_type=($this->input->post('user_type'))?$this->input->post('user_type'):0;
  
  $menu=array();

  // if($user_type==0 || $user_type==0){

    
  // $check=$this->user_model->getRecord('user', array('user_id'=>$user_id,'user_type'=>$user_type));
  
  // if($check!=0)
  //   {
      $menu=$this->getMenu(1,0); 

    echo json_encode($menu);
      exit;

     
     $dashboard=array();
     $booking_menu=array();
     $hotel_owner=array();
     $pet_owner=array();

      $chat=array();
     $master_data=array();
     $Settings=array();
     $Reports=array();

     $CMS=array();
     $Enquiry=array();

     $HotelSettings=array();
     $Accounts=array();

     $Profile=array();
   
    //Dashboard Menu //

  if($user_type==0 || $user_type==0){    

  $dashboard=array('path'=>'/dashboard/dashboard1','title'=>'Dashboard','icon'=>'ft-home','class'=>'','badge'=>'','badgeClass'=> '', 'isExternalLink'=> false, 'submenu'=>array(),'role_id'=>array(1,3));
  
  }

      if(!empty($dashboard)){
      $menu[]=$dashboard;
    }

  //Booking Menu //
if($user_type==0 || $user_type==0){

    $booking_menu=array('path'=>'','title'=>'Booking','icon'=>'ft-book','class'=>'has-sub','badge'=>'','badgeClass'=> '', 'isExternalLink'=> false, 'submenu'=>array() ,'role_id'=>array(1,3));
}

if($user_type==0 || $user_type==0){
 
  $booking_menu['submenu'][]= array('path'=>'/components/online','title'=>'Online','icon'=>'','class'=>'','badge'=>'','badgeClass'=> '', 'isExternalLink'=> false, 'submenu'=>array(),'role_id'=>array(1,3) );
}

if($user_type==0 || $user_type==0){

  $booking_menu['submenu'][]= array('path'=>'/components/manual','title'=>'Manual','icon'=>'','class'=>'','badge'=>'','badgeClass'=> '', 'isExternalLink'=> false, 'submenu'=>array(),'role_id'=>array(1,3));
}

if($user_type==0 || $user_type==0){
 
  $booking_menu['submenu'][]= array('path'=>'/components/cancellation','title'=>'Cancellation','icon'=>'','class'=>'','badge'=>'','badgeClass'=> '', 'isExternalLink'=> false, 'submenu'=>array(),'role_id'=>array(1,3));

}


if($user_type==0){

  $booking_menu['submenu'][]= array('path'=>'/components/cagestatus','title'=>'Cage Status','icon'=>'','class'=>'','badge'=>'','badgeClass'=> '', 'isExternalLink'=> false, 'submenu'=>array(),'role_id'=>array(3));
}

if($user_type==0){
 
  $booking_menu['submenu'][]= array('path'=>'/components/cancellation-refund','title'=>'Cancellation Refund','icon'=>'','class'=>'','badge'=>'','badgeClass'=> '', 'isExternalLink'=> false, 'submenu'=>array(),'role_id'=>array(3));
}


      if(!empty($booking_menu)){
      $menu[]=$booking_menu;
    }
  

   // Hotel Owners //

  if($user_type==0){

  $hotel_owner=array('path'=>'','title'=>'Hotel Owners','icon'=>'ft-users','class'=>'has-sub','badge'=>'','badgeClass'=> '', 'isExternalLink'=> false, 'submenu'=>array(),'role_id'=>array(1));

  }

  if($user_type==0){

    $hotel_owner['submenu'][]=array('path'=>'/components/view-hotel-owner','title'=>'View Hotel Owner','icon'=>'','class'=>'','badge'=>'','badgeClass'=> '', 'isExternalLink'=> false, 'submenu'=>array(),'role_id'=>array(1));

  }

   if($user_type==0){   
   $hotel_owner['submenu'][]= array('path'=>'/components/add-hotel-owner','title'=>'Add Hotel Owner','icon'=>'','class'=>'','badge'=>'','badgeClass'=> '', 'isExternalLink'=> false, 'submenu'=>array(),'role_id'=>array(1));
 }

 if($user_type==0){
   $hotel_owner['submenu'][]=array('path'=>'/components/awaiting-approval','title'=>'Awaiting Approval','icon'=>'','class'=>'','badge'=>'','badgeClass'=> '', 'isExternalLink'=> false, 'submenu'=>array(),'role_id'=>array(1));
  }


    if(!empty($hotel_owner)){
      $menu[]=$hotel_owner;
    }
  
  // Pet Owners //
   if($user_type==0 || $user_type==0){
    $pet_owner=array('path'=>'','title'=>'Pet Owners','icon'=>'ft-users','class'=>'has-sub','badge'=>'','badgeClass'=> '', 'isExternalLink'=> false, 'submenu'=>array(),'role_id'=>array(1,3));
   }

   if($user_type==0 || $user_type==0){
    $pet_owner['submenu'][]=array('path'=>'/components/view-pet-owner','title'=>'View Pet Owner','icon'=>'','class'=>'','badge'=>'','badgeClass'=> '', 'isExternalLink'=> false, 'submenu'=>array(),'role_id'=>array(1,3));
   }

   if($user_type==0 || $user_type==0){
   $pet_owner['submenu'][]=array('path'=>'/components/add-pet-owner','title'=>'Add Pet Owner','icon'=>'','class'=>'','badge'=>'','badgeClass'=> '', 'isExternalLink'=> false, 'submenu'=>array(),'role_id'=>array(1,3));
   }

 
    if(!empty($pet_owner)){
      $menu[]=$pet_owner;
    }
  // Chat // 

    if($user_type==0 || $user_type==0){
    $chat=array('path'=>'','title'=>'Chat','icon'=>'ft-message-square','class'=>'has-sub','badge'=>'','badgeClass'=> '', 'isExternalLink'=> false, 'submenu'=>array(),'role_id'=>array(1,3));
     }

    if($user_type==0 || $user_type==0){ 
    $chat['submenu'][]=array('path'=>'/chat','title'=>'Chat','icon'=>'','class'=>'','badge'=>'','badgeClass'=> '', 'isExternalLink'=> false, 'submenu'=>array(),'role_id'=>array(1,3));
    }

   
     if(!empty($chat)){
      $menu[]=$chat;
    }
  // Master Data // 

    if($user_type==0){
    $master_data=array('path'=>'','title'=>'Master Data','icon'=>'ft-star','class'=>'has-sub','badge'=>'','badgeClass'=> '', 'isExternalLink'=> false, 'submenu'=>array(),'role_id'=>array(1));
    }

    if($user_type==0){
    $master_data['submenu'][]=array('path'=>'','title'=>'Pet Data','icon'=>'','class'=>'','badge'=>'','badgeClass'=> '', 'isExternalLink'=> false, 'submenu'=>array(),'role_id'=>array(1));
     }

    if($user_type==0){ 
    $master_data['submenu'][0]['submenu'][]= array('path'=>'/components/master-pet','title'=>'Add Pet','icon'=>'','class'=>'','badge'=>'','badgeClass'=> '', 'isExternalLink'=> false, 'submenu'=>array(),'role_id'=>array(1));
  }

   if($user_type==0){
    $master_data['submenu'][0]['submenu'][]=array('path'=>'/components/master-pet-breed','title'=>'Add Pet Breed','icon'=>'','class'=>'','badge'=>'','badgeClass'=> '', 'isExternalLink'=> false, 'submenu'=>array(),'role_id'=>array(1));  
     }

     if($user_type==0){
     $master_data['submenu'][]=array('path'=>'/components/master-check-in-type','title'=>'Check In Type','icon'=>'','class'=>'','badge'=>'','badgeClass'=> '', 'isExternalLink'=> false, 'submenu'=>array(),'role_id'=>array(1));
     }

     if($user_type==0){
     $master_data['submenu'][]= array('path'=>'/components/master-time-slot','title'=>'Time Slot','icon'=>'','class'=>'','badge'=>'','badgeClass'=> '', 'isExternalLink'=> false, 'submenu'=>array(),'role_id'=>array(1));
     }

    if($user_type==0){ 
    $master_data['submenu'][]= array('path'=>'/components/master-servises','title'=>'Common Servises','icon'=>'','class'=>'','badge'=>'','badgeClass'=> '', 'isExternalLink'=> false, 'submenu'=>array(),'role_id'=>array(1));
    }

    if($user_type==0){

    $master_data['submenu'][]= array('path'=>'/components/hotel-services','title'=>'Specific Servises','icon'=>'','class'=>'','badge'=>'','badgeClass'=> '', 'isExternalLink'=> false, 'submenu'=>array(),'role_id'=>array(1));
     }
    
      if($user_type==0){

    $master_data['submenu'][]=array('path'=>'/components/master-facilities','title'=>'Facilities & Amenities','icon'=>'','class'=>'','badge'=>'','badgeClass'=> '', 'isExternalLink'=> false, 'submenu'=>array(),'role_id'=>array(1));

     }

 
  if(!empty($master_data)){
      $menu[]=$master_data;
    }

     // Settings //
     if($user_type==0){
     $Settings=array('path'=>'','title'=>'Settings','icon'=>'ft-settings','class'=>'has-sub','badge'=>'','badgeClass'=> 'badge badge-pill badge-success float-right mr-1 mt-1', 'isExternalLink'=> false, 'submenu'=>array(),'role_id'=>array(1));
      }

     if($user_type==0){  
    $Settings['submenu'][]=   array('path'=>'','title'=>'Location Settings','icon'=>'','class'=>'has-sub','badge'=>'','badgeClass'=> '', 'isExternalLink'=> false, 'submenu'=>array(),'role_id'=>array(1));
     }

     if($user_type==0){ 
    $Settings['submenu'][0]['submenu'][]=array('path'=>'/components/master-country','title'=>'Master Country','icon'=>'','class'=>'','badge'=>'','badgeClass'=> '', 'isExternalLink'=> false, 'submenu'=>array(),'role_id'=>array(1));
    }

    if($user_type==0){ 
   $Settings['submenu'][0]['submenu'][]=array('path'=>'/components/master-state','title'=>'Master State','icon'=>'','class'=>'','badge'=>'','badgeClass'=> '', 'isExternalLink'=> false, 'submenu'=>array(),'role_id'=>array(1));
    }

     if($user_type==0){
   $Settings['submenu'][0]['submenu'][]=  array('path'=>'/components/master-city','title'=>'Master City','icon'=>'','class'=>'','badge'=>'','badgeClass'=> '', 'isExternalLink'=> false, 'submenu'=>array(),'role_id'=>array(1));

    }

     if($user_type==0){
 
   $Settings['submenu'][]=array('path'=>'/components/master-currency','title'=>'Currency Settings','icon'=>'','class'=>'','badge'=>'','badgeClass'=> '', 'isExternalLink'=> false, 'submenu'=>array(),'role_id'=>array(1));
 }

  if($user_type==0){
   $Settings['submenu'][]=array('path'=>'/components/master-cancellation','title'=>'Cancellation Settings','icon'=>'','class'=>'','badge'=>'','badgeClass'=> '', 'isExternalLink'=> false, 'submenu'=>array(),'role_id'=>array(1));
    }

     if($user_type==0){
   $Settings['submenu'][]= array('path'=>'/components/master-commission','title'=>'Commission Settings','icon'=>'','class'=>'','badge'=>'','badgeClass'=> '', 'isExternalLink'=> false, 'submenu'=>array(),'role_id'=>array(1));
     }

     if($user_type==0){ 
    $Settings['submenu'][]= array('path'=>'/components/master-site','title'=>'Site Settings','icon'=>'','class'=>'','badge'=>'','badgeClass'=> '', 'isExternalLink'=> false, 'submenu'=>array(),'role_id'=>array(1));
     }
    
     if($user_type==0){
   $Settings['submenu'][]= array('path'=>'/components/master-general','title'=>'General Settings','icon'=>'','class'=>'','badge'=>'','badgeClass'=> '', 'isExternalLink'=> false, 'submenu'=>array(),'role_id'=>array(1));
    }

     if($user_type==0){
   $Settings['submenu'][]= array('path'=>'/components/master-payment','title'=>'payment Settings','icon'=>'','class'=>'','badge'=>'','badgeClass'=> '', 'isExternalLink'=> false, 'submenu'=>array(),'role_id'=>array(1));
     }
   
    if(!empty($Settings)){
      $menu[]=$Settings;
    }


    // Hotel Settings //

    if($user_type==0){
     $HotelSettings=array('path'=>'','title'=>'Hotel Settings','icon'=>'ft-settings','class'=>'has-sub','badge'=>'','badgeClass'=> '', 'isExternalLink'=> false, 'submenu'=>array(),'role_id'=>array(3));
    }

   if($user_type==0){
     $HotelSettings['submenu'][]=array('path'=>'/components/cage-price','title'=>'Price','icon'=>'','class'=>'','badge'=>'','badgeClass'=> '', 'isExternalLink'=> false, 'submenu'=>array(),'role_id'=>array(3));
   }
  
   if($user_type==0){

      $HotelSettings['submenu'][]=array('path'=>'/components/hotel-gallery','title'=>'Gallery','icon'=>'','class'=>'','badge'=>'','badgeClass'=> '', 'isExternalLink'=> false, 'submenu'=>array(),'role_id'=>array(3));

   }

    if($user_type==0){

     $HotelSettings['submenu'][]=array('path'=>'/components/hotel-time','title'=>'Check In Type','icon'=>'','class'=>'','badge'=>'','badgeClass'=> '', 'isExternalLink'=> false, 'submenu'=>array(),'role_id'=>array(3));
   }


 if($user_type==0){

     $HotelSettings['submenu'][]=array('path'=>'/components/hotel-services','title'=>'Services','icon'=>'','class'=>'','badge'=>'','badgeClass'=> '', 'isExternalLink'=> false, 'submenu'=>array(),'role_id'=>array(3));
   }


 if($user_type==0){

     $HotelSettings['submenu'][]=array('path'=>'/components/hotel-facilities','title'=>'Facilities','icon'=>'','class'=>'','badge'=>'','badgeClass'=> '', 'isExternalLink'=> false, 'submenu'=>array(),'role_id'=>array(3));
   }


 if($user_type==0){

     $HotelSettings['submenu'][]=array('path'=>'/components/hotel-cancellation-pilicy','title'=>'Cancellation Policy','icon'=>'','class'=>'','badge'=>'','badgeClass'=> '', 'isExternalLink'=> false, 'submenu'=>array(),'role_id'=>array(3));
   }

     
 
    if(!empty($HotelSettings)){
      $menu[]=$HotelSettings;
    }
  
     // Accounts //
     
       if($user_type==0){
     $Accounts=array('path'=>'','title'=>'Account','icon'=>'ft-user','class'=>'has-sub','badge'=>'','badgeClass'=> '', 'isExternalLink'=> false, 'submenu'=>array(),'role_id'=>array(3));
    }

    if($user_type==0){
        $Accounts['submenu'][]=array('path'=>'/components/booking-transaction','title'=>'Booking Transaction','icon'=>'','class'=>'','badge'=>'','badgeClass'=> '', 'isExternalLink'=> false, 'submenu'=>array(),'role_id'=>array(3));
    }

        if($user_type==0){
        $Accounts['submenu'][]=array('path'=>'/components/booking-commission','title'=>'Commission','icon'=>'','class'=>'','badge'=>'','badgeClass'=> '', 'isExternalLink'=> false, 'submenu'=>array(),'role_id'=>array(3));
    }

        if($user_type==0){
        $Accounts['submenu'][]=array('path'=>'/components/booking-refund','title'=>'Refund','icon'=>'','class'=>'','badge'=>'','badgeClass'=> '', 'isExternalLink'=> false, 'submenu'=>array(),'role_id'=>array(3));
    }

        if($user_type==0){
        $Accounts['submenu'][]=array('path'=>'/components/profit-loss','title'=>'Profit/Loss','icon'=>'','class'=>'','badge'=>'','badgeClass'=> '', 'isExternalLink'=> false, 'submenu'=>array(),'role_id'=>array(3));
    }

    if(!empty($Accounts)){
      $menu[]=$Accounts;
    }

     

     // Reports //
      if($user_type==0 || $user_type==0){
       $Reports=array('path'=>'','title'=>'Reports','icon'=>'ft-printer','class'=>'has-sub','badge'=>'','badgeClass'=> '', 'isExternalLink'=> false, 'submenu'=>array(),'role_id'=>array(1,3));
        }
       
       if($user_type==0 || $user_type==0){
       $Reports['submenu'][]= array('path'=>'/components/booking-report','title'=>'Booking','icon'=>'','class'=>'','badge'=>'','badgeClass'=> '', 'isExternalLink'=> false, 'submenu'=>array(),'role_id'=>array(1,3));
         }

       if($user_type==0 || $user_type==0){     
       $Reports['submenu'][]= array('path'=>'/components/customer-report','title'=>'Customer','icon'=>'','class'=>'','badge'=>'','badgeClass'=> '', 'isExternalLink'=> false, 'submenu'=>array(),'role_id'=>array(1,3));
        }


      if($user_type==0){
        $Reports['submenu'][]=array('path'=>'/components/vendor-report','title'=>'Vendor','icon'=>'','class'=>'','badge'=>'','badgeClass'=> '', 'isExternalLink'=> false, 'submenu'=>array(),'role_id'=>array(1));
 
      }
       
      if($user_type==0){ 
       $Reports['submenu'][]= array('path'=>'/components/commission-report','title'=>'Commission','icon'=>'','class'=>'','badge'=>'','badgeClass'=> '', 'isExternalLink'=> false, 'submenu'=>array(),'role_id'=>array(1));
        }

       if($user_type==0){ 

      $Reports['submenu'][]= array('path'=>'/components/accounts-report','title'=>'Accounts','icon'=>'','class'=>'','badge'=>'','badgeClass'=> '', 'isExternalLink'=> false, 'submenu'=>array(),'role_id'=>array(1));
       }

          if(!empty($Accounts)){
      $menu[]=$Reports;
    }
     
    
    // CMS //

       
        if($user_type==0){ 
       $CMS=array('path'=>'','title'=>'CMS','icon'=>'ft-bar-chart-2','class'=>'has-sub','badge'=>'','badgeClass'=> '', 'isExternalLink'=> false, 'submenu'=>array(),'role_id'=>array(1));
        }

         if($user_type==0){ 
       $CMS['submenu'][]=array('path'=>'/components/pages','title'=>'Pages','icon'=>'','class'=>'','badge'=>'','badgeClass'=> '', 'isExternalLink'=> false, 'submenu'=>array(),'role_id'=>array(1));
       }

        if($user_type==0){ 
       $CMS['submenu'][]=    array('path'=>'/components/manus-setting','title'=>'Menu Settings','icon'=>'','class'=>'','badge'=>'','badgeClass'=> '', 'isExternalLink'=> false, 'submenu'=>array(),'role_id'=>array(1));
        }

       
          if(!empty($CMS)){
      $menu[]=$CMS;
    }
       
        // Enquiry //
        if($user_type==0){ 
       $Enquiry=array('path'=>'','title'=>'Enquiry','icon'=>'ft-copy','class'=>'has-sub','badge'=>'','badgeClass'=> '', 'isExternalLink'=> false, 'submenu'=>array(),'role_id'=>array(1));
        }

       if($user_type==0){ 
       $Enquiry['submenu'][]=array('path'=>'/components/feedback','title'=>'Feedback','icon'=>'','class'=>'','badge'=>'','badgeClass'=> '', 'isExternalLink'=> false, 'submenu'=>array(),'role_id'=>array(1));
        }

        if($user_type==0){  
       $Enquiry['submenu'][]=array('path'=>'/components/contact-us','title'=>'Contact Us','icon'=>'','class'=>'','badge'=>'','badgeClass'=> '', 'isExternalLink'=> false, 'submenu'=>array(),'role_id'=>array(1));
       }

         if($user_type==0){ 

       $Enquiry['submenu'][]=array('path'=>'/components/complaint','title'=>'Complaint','icon'=>'','class'=>'','badge'=>'','badgeClass'=> '', 'isExternalLink'=> false, 'submenu'=>array(),'role_id'=>array(1));
       
       }

      
        if(!empty($Enquiry)){
      $menu[]=$Enquiry;
    }
     


      // Profile Setting //
     
       if($user_type==0){

        $Profile=array('path'=>'/components/profile-setting','title'=>'Profile Setting','icon'=>'','class'=>'','badge'=>'','badgeClass'=> '', 'isExternalLink'=> false, 'submenu'=>array(),'role_id'=>array(1,3));
       
       }

         if(!empty($Profile)){
      $menu[]=$Profile;
    }
     
//      } 
 // }


     foreach($menu as $menus){

         //echo $menus['badge'];
        
        $data=array('path'=>$menus['path'],'title'=>$menus['title'],'icon'=>$menus['icon'],'class'=>$menus['class'],'badge'=>$menus['badge'],'badgeClass'=> $menus['badgeClass'], 'isExternalLink'=> $menus['isExternalLink']);

            
      
        $result=$this->user_model->saveRecord('menu', $data);
         

         foreach($menus['role_id'] as $role_id){
             $role_data=array('menu_id'=>$result,'role_id'=>$role_id,'status'=>1);
             $result_role=$this->user_model->saveRecord('menu_permission', $role_data);
         }

         foreach($menus['submenu'] as $submenus){

            $sub_data=array('parent'=>$result,'path'=>$submenus['path'],'title'=>$submenus['title'],'icon'=>$submenus['icon'],'class'=>$submenus['class'],'badge'=>$submenus['badge'],'badgeClass'=> $submenus['badgeClass'], 'isExternalLink'=> $submenus['isExternalLink']);
 
               $sub_result=$this->user_model->saveRecord('menu', $sub_data);
             
                foreach($submenus['role_id'] as $sub_role_id){

                    $role_data=array('menu_id'=>$sub_result,'role_id'=>$sub_role_id,'status'=>1);
                    $role_result=$this->user_model->saveRecord('menu_permission', $role_data);

                 }

              
                 foreach($submenus['submenu'] as $submenuss){

            $sub_dataa=array('parent'=>$sub_result,'path'=>$submenuss['path'],'title'=>$submenuss['title'],'icon'=>$submenuss['icon'],'class'=>$submenuss['class'],'badge'=>$submenuss['badge'],'badgeClass'=> $submenuss['badgeClass'], 'isExternalLink'=> $submenuss['isExternalLink']);
 
               $sub_results=$this->user_model->saveRecord('menu', $sub_dataa);
             
                foreach($submenuss['role_id'] as $sub_role_id){

                    $role_data=array('menu_id'=>$sub_results,'role_id'=>$sub_role_id,'status'=>1);
                    $this->user_model->saveRecord('menu_permission', $role_data);

                 }

       
         }


           
        }
      }
  
  //  echo json_encode($menu); die();
  
  }

}

?>