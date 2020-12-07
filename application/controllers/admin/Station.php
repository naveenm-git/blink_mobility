<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Station extends MY_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('admin_model');
	}
		
	public function listing($id = ''){
		if ($this->checkLogin('A') == ''){
			$this->setErrorMessage('error','Login required!!!');
			redirect(ADMINURL);
		} else {
			$this->data['heading'] = "Station List";
         $zipcodes = $this->admin_model->get_selected_fields(STATION, ['status'=>'1'], ['zipcode']);
         $zipcodes = array_unique(array_column($zipcodes->result(), 'zipcode')); sort($zipcodes);
         $this->data['zipcodes'] = $zipcodes;
         $cities = $this->admin_model->get_selected_fields(STATION, ['status'=>'1'], ['city']);
         $cities = array_unique(array_column($cities->result(), 'city')); sort($cities);
         $this->data['cities'] = $cities;
			$this->load->view('admin/station/listing',$this->data);
		}
	}
	
	public function listing_ajax(){
		if ($this->checkLogin('A') != ''){
			$columns = ['_id', 'name', 'street', 'zipcode', 'city', 'status'];
			
			$search = $_REQUEST['search']['value'];
			$filters = [
				'name'=>$search,
				'street'=>$search,
				'zipcode'=>$search,
				'city'=>$search
			];
			$condition = [];
         if($_REQUEST['columns'][3]['search']['value']){
				$condition['zipcode'] = $_REQUEST['columns'][3]['search']['value'];
			}
         
			$recordsTotal = $this->admin_model->get_all_counts(STATION, $condition);
			$length = $_REQUEST['length'];
			$start = $_REQUEST['start'];
			$column = $columns[$_REQUEST['order'][0]['column']]; 
			$type = $_REQUEST['order'][0]['dir'];
			if(!empty($search)) {
				$recordsFiltered = $this->admin_model->get_all_counts(STATION, $condition, $filters);
				$result = $this->admin_model->get_all_details(STATION, $condition, [$column=>$type], $length, $start, $filters);
			} else {
				$recordsFiltered = $this->admin_model->get_all_counts(STATION, $condition, []);
				$result = $this->admin_model->get_all_details(STATION, $condition, [$column=>$type], $length, $start);
			}
			$dataArr = array(); $i = $_REQUEST['start']+1;
			foreach($result->result_array() as $res){
				$nestedData=array(); 
				$nestedData[] = '<input type="checkbox" class="ids" name="ids[]" value="'.(string)$res['_id'].'" />';
				$nestedData[] = ($res['name']!='')?$res['name']:'-';
				$nestedData[] = ($res['street']!='')?$res['street']:'-';
				$nestedData[] = ($res['zipcode']!='')?$res['zipcode']:'-';
				$nestedData[] = ($res['city']!='')?$res['city']:'-';
				$nestedData[] = $this->admin_model->get_all_counts(PARKING, ['station_id' => (string) $res['_id'], 'parking_type'=>'reservation', 'parking_status' => 'operational']);
				$nestedData[] = $this->admin_model->get_all_counts(PARKING, ['station_id' => (string) $res['_id'], 'parking_type'=>'charging_point', 'parking_status' => 'operational']);
				if($this->data['user_type']=='superadmin'){
					if($res['status'] == '1'){
						$status = '<a class="btn btn-xs btn-success" onclick="change_status(\''.(string)$res['_id'].'\', \'0\')" title="Click to make inactive">Active</a>';
					} else { 
						$status = '<a class="btn btn-xs btn-default" onclick="change_status(\''.(string)$res['_id'].'\', \'1\')" title="Click to make active">In Active</a>';
					} 
				} else {
					$btn = 'default'; $stat = 'In Active';
					if($res['status']=='1'){ 
						$btn = 'success'; 
						$stat = 'Active'; 
					}
					$status = '<a class="btn btn-xs btn-'.$btn.'" title="Can\'t able to change status">'.$stat.'</a>';
				}
				$nestedData[] = $status;
				$action = '<a class="action-a" title="View" target="_blank" href="'.base_url().STATIONURL.'/'.(string) $res['_id'].'/dashboard"><i class="fa fa-eye"></i></a>';
				$action .= '&nbsp;<a class="action-a" title="Edit" href="'.base_url().'admin/station-edit/'.(string)$res['_id'].'" title="Edit"><i class="fa fa-pencil"></i></a>';
				$action .= '&nbsp;<a class="action-a act_status" title="Remove" onclick="delete_record(\''.(string)$res['_id'].'\')" data-rid="'.(string)$res['_id'].'"><i class="fa fa-trash"></i></a>';
				$nestedData[] = $action;
				$dataArr[] = $nestedData;
				$i++;
			}
			$jsonDataArr = [ 
				"draw" => intval($_REQUEST['draw']),
				"recordsTotal" => intval($recordsTotal), 
				"recordsFiltered" => intval($recordsFiltered), 
				"data" => $dataArr
			];
			echo json_encode($jsonDataArr);
		}
	}
	
	public function add_edit($id = ''){
		if ($this->checkLogin('A') == ''){
			$this->setErrorMessage('error','Login required!!!');
			redirect(ADMINURL);
		} else {
			$form_mode = FALSE;
			$this->data['result'] = [];
			if($id!=''){
				$condition = ['_id' => MongoID((string)$id)];
				$result = $this->admin_model->get_all_details(STATION, $condition);
				if($result->num_rows() > 0){
					$this->data['result'] = $result->result_array();
					$form_mode = TRUE;
				} else {
					$this->setErrorMessage('error','Something went wrong!!!');
					redirect(ADMINURL.'/station-list');
				}
			}
         $this->data['partners'] = $this->admin_model->get_all_details(PARTNER, ['status'=>'1']);
         $this->data['subscription'] = $this->admin_model->get_all_details(SUBSCRIPTION, ['status'=>'1']);
         $makeArr = [];
         $makes = $this->admin_model->get_all_details(MAKE, ['status'=>'1'])->result_array();
         foreach($makes as $make){
            $makeArr[(string)$make['_id']] = $make['make'];
         }
         $modelArr = [];
         $models = $this->admin_model->get_all_details(MODEL, ['status'=>'1'])->result_array();
         foreach($models as $model){
            $modelArr[(string)$model['_id']] = $model['model'];
         }
         $this->data['makes'] = $makeArr;
         $this->data['models'] = $modelArr;
         $vehicleCondition = ['status'=>'1', '$or'=>[['station_id'=>['$exists'=>false]], ['station_id'=>''], ['station_id'=>['$eq'=>$id]]]];
         $this->data['vehicles'] = $this->admin_model->get_all_details(VEHICLE, $vehicleCondition);
			$this->data['form_mode'] = $form_mode;
			$this->data['heading'] = "Station Details";
			$this->load->view('admin/station/add_edit',$this->data);
		}
	}
	
	public function view($id = ''){
		if ($this->checkLogin('A') == ''){
			$this->setErrorMessage('error','Login required!!!');
			redirect(ADMINURL);
		} else {
			if($id!=''){
				$condition = ['_id' => MongoID((string)$id)];
				$result = $this->admin_model->get_all_details(STATION, $condition);
				if($result->num_rows() > 0){
					$this->data['result'] = $result->result_array();
					$this->data['heading'] = "Station Details";
					$this->load->view('admin/station/view',$this->data);
				} else {
					$this->setErrorMessage('error','Something went wrong!!!');
					redirect(ADMINURL.'/station-list');
				}
			} else {
				$this->setErrorMessage('error','Something went wrong!!!');
				redirect(ADMINURL.'/station-list');
			}
		}
	}
	
	public function save(){
		if ($this->checkLogin('A') == ''){
			$response['message'] = 'Something went Wrong!';
		} else {
			$objectid = (string)$this->input->post('objectid');
			$status = $this->input->post('status');
			$street = trim($this->input->post('street'));
			$zipcode = trim($this->input->post('zipcode'));
			$city = trim($this->input->post('city'));
         $address = $street.', '.$city.', '.$zipcode;
			$vehicles = $this->input->post('vehicles');
         $vehicleids = array_map(function($a){ return MongoID($a); }, $vehicles);
			$status = ($status!='')?'1':'0';
			if($objectid!=''){
				$conditionArr = ['_id'=>MongoID((string)$objectid)];
				$adminCheck = $this->admin_model->get_all_counts(STATION, $conditionArr);
				if ($adminCheck > 0){
               $location = $this->admin_model->get_location_details($address);
               if($location['status']=='1'){
                  $dataArr = ['location'=>$location['response'], 'dateModified'=>MongoDATE(time()), 'status'=>$status];
                  $this->admin_model->commonInsertUpdate(STATION, 'update', ['_id', 'objectid', 'status'], $dataArr, $conditionArr);
                  $this->admin_model->update_details(VEHICLE, ['station_id' => ''], ['station_id'=>$objectid]);
                  $this->admin_model->update_details(VEHICLE, ['station_id' => $objectid], ['_id'=>['$in' => $vehicleids]]);
                  $this->setErrorMessage('success','Station details saved!!!');
               } else {
                  $this->setErrorMessage('error','Address not fetched, Enter valid address!!!');
               }
				} else {
					$this->setErrorMessage('error','Something went wrong!!!');
				}
			} else {
            $location = $this->admin_model->get_location_details($address);
            if($location['status']=='1'){
               $dataArr = ['location'=>$location['response'], 'dateAdded'=>MongoDATE(time()), 'status'=>$status];
               $this->admin_model->commonInsertUpdate(STATION, 'insert', ['_id', 'objectid', 'status'], $dataArr,[]);
               $objectid = $this->admin_model->get_last_insert_id();
               $this->admin_model->update_details(VEHICLE, ['station_id' => ''], ['station_id'=>$objectid]);
               $this->admin_model->update_details(VEHICLE, ['station_id' => $objectid], ['_id'=>['$in' => $vehicleids]]);
               $this->setErrorMessage('success','Station details saved!!!');
            } else {
               $this->setErrorMessage('error','Address not fetched, Enter valid address!!!');
            }
			}
         redirect(ADMINURL.'/station-list');
		}
	}
	
	public function change_status(){
		if ($this->checkLogin('A') == ''){
			echo 'Something went wrong!!!';
		} else {
			$status = $this->input->post('status');
			$id = $this->input->post('id');
			$dataArr = ['status'=>$status];
			$condition = ['_id'=>MongoID($id)];
			$check = $this->admin_model->get_selected_fields(STATION, $condition, ['cms_name']);
			
			if($check->num_rows()>0){
				$this->admin_model->update_details(STATION, $dataArr, $condition);
				echo 'success';
			} else {
				echo 'Something went wrong!!!';
			}
		}
	}

	public function remove(){
		if ($this->checkLogin('A') == ''){
			echo 'Something went wrong!!!';
		} else {
			$id = $this->input->post('id');
			$condition = ['_id'=>MongoID($id)];
			$check = $this->admin_model->get_all_counts(STATION, $condition);
			if($check > 0){
				$this->admin_model->commonDelete(STATION, $condition);
				echo 'success';
			} else {
				echo 'Something went wrong!!!';
			}
		}
	}
	
	public function bulk_status(){
		$ids = array_map(function($a){ return MongoID($a); }, $_POST['ids']);
		$this->change_bulk_status(STATION, $_POST['status'], $ids);
	}
	
	public function bulk_delete(){
		$ids = array_map(function($a){ return MongoID($a); }, $_POST['ids']);
		$condition = ['_id'=>['$in'=>$ids]];
		$check = $this->admin_model->get_selected_fields(STATION, $condition, ['_id']);
		if($check->num_rows()>0){
			$this->admin_model->commonDelete(STATION, $condition);
			echo 'success';
		} else {
			echo 'Something went wrong!!!';
		}
	}
	
}
