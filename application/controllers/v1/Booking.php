<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Booking extends Api_Controller {
	public function __construct(){
		parent::__construct();
		$this->load->model('admin_model');
	}

   public function get_nearest_station() {
		$lat = $this->input->post('lat');
		$lng = $this->input->post('lng');
      $unit = $this->input->post('unit');
      $distance = $this->input->post('distance');
      
      if($this->_user_id != ''){
         $user = $this->admin_model->get_all_details(USERS, ['_id'=>MongoID($this->_user_id)]);
         if($user->num_rows() > 0){
            $profile_status = (isset($user->row()->profile_status) && !in_array('0', $user->row()->profile_status)) ? '1' : '0';
            if($lat != '' && $lng != ''){
               $distanceMultiplier = 0.001;
               if($unit == 'km'){
                  $distanceMultiplier = 0.001;
               } else if($unit == 'mi'){
                  $distanceMultiplier = 0.000621371;
               } else if($unit == 'm'){
                  $distanceMultiplier = 1;
               }
               $distance = ($distance!='') ? $distance : 10;
               
               $option = [
                  [
                     '$geoNear' => [
                        'near' => [ 'type' => 'Point', 'coordinates' => [floatval($lat), floatval($lng)]],
                        'spherical' => true,
                        'maxDistance' => (intval($distance) * 1000),
                        'includeLocs' => 'location',
                        'distanceField' => 'distance',
                        'distanceMultiplier' => $distanceMultiplier,
                        'num' => intval(1000),
                        'query' => ['status' => '1']
                     ],
                  ],
                  [
                     '$project' => [
                        'name' => 1,
                        'street' => 1,
                        'city' => 1,
                        'zipcode' => 1,
                        'location' => 1
                     ]
                  ],
                  ['$limit' => 5]
               ];
               $result = $this->mongo_db->aggregate(STATION, $option);
               $response = [];
               if(count($result['result']) > 0){
                  foreach($result['result'] as $res){
                     $station = [
                        'station_id' => (string) $res['_id'],
                        'name' => $res['name'],
                        'street' => $res['street'],
                        'city' => $res['city'],
                        'zipcode' => $res['zipcode'],
                        'location' => $res['location'],
                        'address' => $res['street'].', '.$res['city'].', '.$res['zipcode']
                     ];
                     $station['reservation'] = $this->admin_model->get_all_counts(PARKING, ['status'=>'1', 'station_id' => $station['station_id'], 'parking_status'=>['$in'=>['operational']], 'parking_type'=>['$in'=>['reservation']]]);
                     
                     $station['charging_point'] = $this->admin_model->get_all_counts(PARKING, ['status'=>'1', 'station_id' => $station['station_id'], 'parking_status'=>['$in'=>['operational']], 'parking_type'=>['$in'=>['charging_point']]]);
                        
                     $parkings = $this->admin_model->get_all_details(PARKING, ['status'=>'1', 'station_id' => $station['station_id'], 'parking_status'=>['$in'=>['operational']]]);
                     $parking = [];
                     
                     if($parkings->num_rows() > 0){
                        foreach($parkings->result_array() as $park){
                           
                           $vehicle = [];
                           if($park['parking_type'] == 'charging_point'){
                              $vehicle = getrow(VEHICLE, $park['vehicle_id']);
                              $make = getrow(MAKE, $vehicle->make_id)->make;
                              $model = getrow(MODEL, $vehicle->model_id)->model;
                              $vehicle_name = $make.' '.$model;
                              $mileage = ($vehicle->remaining_charge / 100) * $vehicle->mileage;
                              $car_icon = (isset($vehicle->car_icon) && $vehicle->car_icon!='')?base_url('uploads/vehicles/'.$vehicle->car_icon):base_url('assets/images/default-car-icon.png');
                              $vehicle = [
                                 'id' => $park['vehicle_id'],
                                 'name' => $vehicle_name,
                                 'year' => $vehicle->year,
                                 'license_plate' => $vehicle->license_plate,
                                 'mileage' => (string) round(floatval($mileage)).' mi',
                                 'remaining_charge' => (string) $vehicle->remaining_charge.'%',
                                 'capacity' => $vehicle->seats.' Seat(s)',
                                 'image' => $car_icon
                              ];
                           }
                              
                           $parking[] = [
                              'parking_id' => (string) $park['_id'],
                              'station_id' => $station['station_id'],
                              'vehicle' => $vehicle,
                              'name' => $park['name'],
                              'order' => $park['order'],
                              'parking_type' => $park['parking_type'],
                              'parking_status' => $park['parking_status'],
                              'cable_status' => $this->data['cable_status'][$park['cable_status']],
                              'light_color' => $this->data['light_color'][$park['light_color']],
                              'status' => $park['status']
                           ];
                        }
                        usort($parking, function($a, $b) { return $a['order'] - $b['order']; });
                        $station['parkings'] = $parking;
                        $response[] = $station;
                     }
                  }
               }
               
               $userArr['id'] = $this->_user_id;
               $userArr['user_type'] = 'user';
               $this->_response['status'] = '1';
               $this->_response['response'] = $response;
               $this->_response['profile_status'] = $profile_status;
               $this->_response['commonArr'] = array('token'=>$this->getjwt($userArr));
               $this->_response['long_message'] = 'Nearby Station Successfully Fetched.';
               $this->json_response($this->_response); exit;
            } else {
               if($lat == ''){
                  $errArr[] = array('field'=>'lat', 'error_msg'=>'Latitude Is Required.');
               }
               if($lng == ''){
                  $errArr[] = array('field'=>'lng', 'error_msg'=>'Longitude Is Required.');
               }
               $this->_response['response_code'] = '201';
               $this->_response['message'] = 'failed';
               $this->_response['status'] = '0';
               $this->_response['long_message'] = 'Required parameters missing.';
               $this->_response['errArr'] = $errArr;
               $this->json_response($this->_response);exit;
            }
         } else {
            $this->_response['response_code'] = '202';
            $this->_response['message'] = 'failed';
            $this->_response['status'] = '0';
            $this->_response['long_message'] = 'User Does Not Exist.';
            $this->json_response($this->_response);exit;
         }
      } else {
         $this->_response['response_code'] = '202';
         $this->_response['message'] = 'failed';
         $this->_response['status'] = '0';
         $this->_response['long_message'] = 'Invalid user.';
         $this->json_response($this->_response);exit;
      }
   }
   
   public function reserve_parking(){
      $station_id = $this->input->post('station_id');
		$parking_id = $this->input->post('parking_id');
		$address = $this->input->post('address');
		$lat = $this->input->post('lat');
		$lng = $this->input->post('lng');

      if($this->_user_id == ''){
         $this->_response['response_code'] = '202';
         $this->_response['message'] = 'failed';
         $this->_response['status'] = '0';
         $this->_response['long_message'] = 'Invalid user.';
         $this->json_response($this->_response);exit;
      }
      
      $user = $this->admin_model->get_all_details(USERS, ['_id'=>MongoID($this->_user_id)]);
      if($user->num_rows() == 0){
         $this->_response['response_code'] = '202';
         $this->_response['message'] = 'failed';
         $this->_response['status'] = '0';
         $this->_response['long_message'] = 'Invalid user.';
         $this->json_response($this->_response);exit;
      }
      
		if($station_id=='' || $parking_id=='' || $address=='' || $lat=='' || $lng==''){
         $this->_response['response_code'] = '201';
			$this->_response['message'] = 'failed';
			$this->_response['status'] = '0';
			$this->_response['long_message'] = 'Required parameters missing.';
			$this->json_response($this->_response);exit;
      }
      
      $station = $this->admin_model->get_all_details(STATION, ['_id' => MongoID($station_id), 'status'=>'1']);
      if($station->num_rows() == 0){
         $this->_response['response_code'] = '202';
         $this->_response['message'] = 'failed';
         $this->_response['status'] = '0';
         $this->_response['long_message'] = 'Station Not Available.';
         $this->json_response($this->_response);exit;
      }
      $station = $station->row();
      
      $parking = $this->admin_model->get_all_details(PARKING, ['_id' => MongoID($parking_id), 'station_id'=>$station_id, 'status'=>'1', 'light_color'=>['$in' => ['available', 'ready_for_rental']]]);
      if($parking->num_rows() == 0){
         $this->_response['response_code'] = '202';
         $this->_response['message'] = 'failed';
         $this->_response['status'] = '0';
         $this->_response['long_message'] = 'Parking Not Available.';
         $this->json_response($this->_response);exit;
      }
      $parking = $parking->row();
      
      if($parking->light_color != 'available'){
         $this->_response['response_code'] = '202';
         $this->_response['message'] = 'failed';
         $this->_response['status'] = '0';
         $this->_response['long_message'] = 'Parking Not Available.';
         $this->json_response($this->_response);exit;
      }
      
      $rental_id = $this->admin_model->get_rental_id();
      
      $rentalsArr = [
         'rental_id' => $rental_id,
         'user' => [
            'id' => $this->_user_id,
            'first_name' => $user->row()->first_name,
            'last_name' => $user->row()->last_name,
            'email' => $user->row()->email,
            'phone_number' => $user->row()->phone_number
         ],
         'origin' => [
            'station_id' => $station_id,
            'station' => $station->name,
            'parking_id' => $parking_id,
            'parking' => $parking->name,
            'street' => $station->street,
            'zipcode' => $station->zipcode,
            'city' => $station->city,
            'location' => [
               'lat' => floatval($station->location['lat']),
               'lng' => floatval($station->location['lng'])
            ]
         ],
         'booked_location' => [
            'address' => $address,
            'lat' => floatval($lat),
            'lng' => floatval($lng)
         ],
         'history' => [
            'reserved' => [
               'message' => 'Parking Has Been Reserved.',
               'booked_at' => MongoDATE(time()),
               'platform' => 'App'
            ]
         ],
         'created_at' => MongoDATE(time()),
         'type' => 'Parking',
         'status' => 'Reserved',
         'platform' => 'App'
      ];
      
      $this->admin_model->simple_insert(RENTALS, $rentalsArr);
      $this->admin_model->update_details(PARKING, ['rental_id' => $rental_id, 'light_color'=>'reserved'], ['_id'=>MongoID($parking_id)]);
      
      $userArr['id'] = $this->_user_id;
      $userArr['user_type'] = 'user';
      $this->_response['status'] = '1';
      $this->_response['response'] = $rental_id;
      $this->_response['long_message'] = 'Parking Has Been Reserved.';
      $this->_response['commonArr'] = array('token'=>$this->getjwt($userArr));
      $this->json_response($this->_response); exit;
   }
   
   public function reserve_car(){
      $station_id = $this->input->post('station_id');
		$parking_id = $this->input->post('parking_id');
		$vehicle_id = $this->input->post('vehicle_id');
		$address = $this->input->post('address');
		$lat = $this->input->post('lat');
		$lng = $this->input->post('lng');
      
      if($this->_user_id == ''){
         $this->_response['response_code'] = '202';
         $this->_response['message'] = 'failed';
         $this->_response['status'] = '0';
         $this->_response['long_message'] = 'Invalid user.';
         $this->json_response($this->_response);exit;
      }
      
      $user = $this->admin_model->get_all_details(USERS, ['_id'=>MongoID($this->_user_id)]);
      if($user->num_rows() == 0){
         $this->_response['response_code'] = '202';
         $this->_response['message'] = 'failed';
         $this->_response['status'] = '0';
         $this->_response['long_message'] = 'Invalid parameters given.';
         $this->json_response($this->_response);exit;
      }
      $user = $user->row();
      
		if($station_id=='' || $parking_id=='' || $vehicle_id=='' || $address=='' || $lat=='' || $lng==''){
         $this->_response['response_code'] = '201';
			$this->_response['message'] = 'failed';
			$this->_response['status'] = '0';
			$this->_response['long_message'] = 'Required parameters missing.';
			$this->json_response($this->_response);exit;
      }
      
      $station = $this->admin_model->get_all_details(STATION, ['_id' => MongoID($station_id), 'status'=>'1']);
      if($station->num_rows() == 0){
         $this->_response['response_code'] = '202';
         $this->_response['message'] = 'failed';
         $this->_response['status'] = '0';
         $this->_response['long_message'] = 'Station Not Available.';
         $this->json_response($this->_response);exit;
      }
      $station = $station->row();
      
      $parking = $this->admin_model->get_all_details(PARKING, ['_id' => MongoID($parking_id), 'status'=>'1', 'light_color'=>['$in' => ['available', 'ready_for_rental']]]);
      if($parking->num_rows() == 0){
         $this->_response['response_code'] = '202';
         $this->_response['message'] = 'failed';
         $this->_response['status'] = '0';
         $this->_response['long_message'] = 'Parking Not Available.';
         $this->json_response($this->_response);exit;
      }
      $parking = $parking->row();
      
      $vehicle = $this->admin_model->get_all_details(VEHICLE, ['_id' => MongoID($vehicle_id), 'status'=>'1', 'car_status' => ['$in' => ['plugged_in', 'ready_for_rental']]]);
      if($vehicle->num_rows() == 0){
         $this->_response['response_code'] = '202';
         $this->_response['message'] = 'failed';
         $this->_response['status'] = '0';
         $this->_response['long_message'] = 'Vehicle Not Available.';
         $this->json_response($this->_response);exit;
      }
      $vehicle = $vehicle->row();
      
      if($vehicle->light_color != 'available' && $vehicle->rental_id!=''){
         $rental = $this->admin_model->get_all_counts(RENTALS, ['_id'=>MongoID($vehicle->rental_id), 'vehicle.id'=>$parking_id]);
         if($rental > 0){
            $this->_response['response_code'] = '202';
            $this->_response['message'] = 'failed';
            $this->_response['status'] = '0';
            $this->_response['long_message'] = 'Already Reserved.';
            $this->json_response($this->_response);exit;
         }
      }
      
      $rental_id = $this->admin_model->get_rental_id();
      
      $make = getrow(MAKE, $vehicle->make_id)->make;
      $model = getrow(MODEL, $vehicle->model_id)->model;
      
      $rentalsArr = [
         'rental_id' => $rental_id,
         'user' => [
            'id' => $this->_user_id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'phone_number' => $user->phone_number
         ],
         'vehicle' => [
            'id' => $vehicle_id,
            'name' => $make .' '. $model .' '. $vehicle->year,
            'license_plate' => $vehicle->license_plate,
            'vin_number' => $vehicle->vin_number
         ],
         'booked_location' => [
            'address' => $address,
            'lat' => floatval($lat),
            'lng' => floatval($lng)
         ],
         'origin' => [
            'station_id' => $station_id,
            'station' => $station->name,
            'parking_id' => $parking_id,
            'parking' => $parking->name,
            'street' => $station->street,
            'zipcode' => $station->zipcode,
            'city' => $station->city,
            'location' => [
               'lat' => floatval($station->location['lat']),
               'lng' => floatval($station->location['lng'])
            ]
         ],
         'history' => [
            'reserved' => [
               'message' => 'Car Has Been Reserved.',
               'booked_at' => MongoDATE(time()),
               'platform' => 'App'
            ]
         ],
         'created_at' => MongoDATE(time()),
         'type' => 'Car',
         'status' => 'Reserved',
         'platform' => 'App'
      ];
      
      $this->admin_model->simple_insert(RENTALS, $rentalsArr);
      $this->admin_model->update_details(PARKING, ['rental_id' => $rental_id, 'light_color'=>'reserved'], ['_id'=>MongoID($parking_id)]);
      
      $userArr['id'] = $this->_user_id;
      $userArr['user_type'] = 'user';
      $this->_response['status'] = '1';
      $this->_response['response'] = $rental_id;
      $this->_response['long_message'] = 'Car Has Been Reserved.';
      $this->_response['commonArr'] = array('token'=>$this->getjwt($userArr));
      $this->json_response($this->_response); exit;
   }
   
   public function save_pre_inspection(){
		$rental_id = $this->input->post('rental_id');
      $exterior_damage = $this->input->post('exterior_damage');
      $exterior_images = $this->input->post('exterior_images');
      $exterior_damage_image = $this->input->post('exterior_damage_image');
      $exterior_damage_description = $this->input->post('exterior_damage_description');
      $interior_damage = $this->input->post('interior_damage');
      $interior_images = $this->input->post('interior_images');
      $interior_damage_image = $this->input->post('interior_damage_image');
      $interior_damage_description = $this->input->post('interior_damage_description');
      
      if($this->_user_id == ''){
         $this->_response['response_code'] = '202';
         $this->_response['message'] = 'failed';
         $this->_response['status'] = '0';
         $this->_response['long_message'] = 'Invalid user.';
         $this->json_response($this->_response);exit;
      }
      
      $paramsErr=0;
		if(empty($exterior_images) || empty($interior_images) || $exterior_damage=='' || $interior_damage==''){
         $paramsErr++;
      }
      
      if($exterior_damage=='1'){
         if(empty($exterior_damage_image) || $exterior_damage_description==''){
            $paramsErr++;
         }
      }
      
      if($interior_damage=='1'){
         if(empty($interior_damage_image) || $interior_damage_description==''){
            $paramsErr++;
         }
      }
      
      if($paramsErr>0){
         $this->_response['response_code'] = '201';
         $this->_response['message'] = 'failed';
         $this->_response['status'] = '0';
         $this->_response['long_message'] = 'Required parameters missing.';
         $this->json_response($this->_response);exit;
      }
      
      $user = $this->admin_model->get_all_details(USERS, ['_id' => MongoID($this->_user_id)]);
      if($user->num_rows() == 0){
         $this->_response['response_code'] = '202';
         $this->_response['message'] = 'failed';
         $this->_response['status'] = '0';
         $this->_response['long_message'] = 'Invalid parameters given.';
         $this->json_response($this->_response);exit;
      }
      
      $rental = $this->admin_model->get_all_details(RENTALS, ['rental_id' => $rental_id]);
      if($rental->num_rows() == 0){
         $this->_response['response_code'] = '202';
         $this->_response['message'] = 'failed';
         $this->_response['status'] = '0';
         $this->_response['long_message'] = 'Rental Detail Not Found.';
         $this->json_response($this->_response);exit;
      }
      
      $dataArr = [
         'pre_inspection' => [
            'exterior' => [
               'images' => $exterior_images,
               'damage' => [
                  'status' => $exterior_damage,
                  'images' => $exterior_damage_image,
                  'description' => $exterior_damage_description
               ]
            ],
            'interior' => [
               'images' => $interior_images,
               'damage' => [
                  'status' => $interior_damage,
                  'images' => $interior_damage_image,
                  'description' => $interior_damage_description
               ]
            ]
         ],
         'history.pre_inspection' => [
            'message' => 'Pre Inspection Saved.',
            'booked_at' => MongoDATE(time()),
            'platform' => 'App'
         ]
      ];
      
      $this->admin_model->update_details(RENTALS, $dataArr, ['rental_id' => $rental_id]);
      
      $userArr['id'] = $this->_user_id;
      $userArr['user_type'] = 'user';
      $this->_response['status'] = '1';
      $this->_response['response'] = $rental_id;
      $this->_response['long_message'] = 'Pre Inspection Details Saved.';
      $this->_response['commonArr'] = array('token'=>$this->getjwt($userArr));
      $this->json_response($this->_response); exit;
   }
	
	public function start_rental(){
		$rental_id = $this->input->post('rental_id');
      
      if($this->_user_id == ''){
         $this->_response['response_code'] = '202';
         $this->_response['message'] = 'failed';
         $this->_response['status'] = '0';
         $this->_response['long_message'] = 'Invalid user.';
         $this->json_response($this->_response);exit;
      }
      
		if($rental_id == ''){
         $this->_response['response_code'] = '201';
			$this->_response['message'] = 'failed';
			$this->_response['status'] = '0';
			$this->_response['long_message'] = 'Required parameters missing.';
			$this->json_response($this->_response);exit;		
      }
      
      $user = $this->admin_model->get_all_details(USERS, ['_id'=>MongoID($this->_user_id)]);
      if($user->num_rows() == 0){
         $this->_response['response_code'] = '202';
         $this->_response['message'] = 'failed';
         $this->_response['status'] = '0';
         $this->_response['long_message'] = 'Invalid parameters given.';
         $this->json_response($this->_response);exit;
      }      
      
      $dataArr = [
         'status' => 'Ongoing',
         'history.rental_start' => [
            'message' => 'Rental Has Been Started.',
            'booked_at' => MongoDATE(time()),
            'platform' => 'App'
         ]
      ];
      
      $this->admin_model->update_details(RENTALS, $dataArr, ['rental_id' => $rental_id]);
      
      $userArr['id'] = $this->_user_id;
      $userArr['user_type'] = 'user';
      $this->_response['status'] = '1';
      $this->_response['long_message'] = 'Your Rental Has Been Started.';
      $this->_response['commonArr'] = array('token'=>$this->getjwt($userArr));
      $this->json_response($this->_response); exit;
	}
   
   public function ongoing_rental(){
      
   }
   
   public function save_final_inspection(){
      
   }
   
   public function finish_rental(){
      
   }
   
}
