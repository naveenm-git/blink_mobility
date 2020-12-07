<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Vehicle extends MY_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('admin_model');
	}
		
	public function listing($id = ''){
		if ($this->checkLogin('A') == ''){
			$this->setErrorMessage('error','Login required!!!');
			redirect(ADMINURL);
		} else {
			$this->data['heading'] = "Vehicle List";
         $this->data['makes'] = $this->admin_model->get_all_details(MAKE, ['status'=>'1'], ['make'=>'asc']);
         $this->data['models'] = $this->admin_model->get_all_details(MODEL, ['status'=>'1'], ['model'=>'asc']);
			$this->load->view('admin/vehicle/listing',$this->data);
		}
	}
	
	public function listing_ajax(){
		if ($this->checkLogin('A') != ''){
			$columns = array('_id', 'vin_number', 'license_plate', 'make', 'model', 'year', 'car_status', 'status');
			$search = $_REQUEST['search']['value'];
			$filters = [
				'vin_number'=>$search,
				'license_plate'=>$search
			];
			$condition = [];
         if($_REQUEST['columns'][3]['search']['value']){
				$condition['make_id'] = $_REQUEST['columns'][3]['search']['value'];
			}
         if($_REQUEST['columns'][4]['search']['value']){
				$condition['model_id'] = $_REQUEST['columns'][4]['search']['value'];
			}
         if($_REQUEST['columns'][5]['search']['value']){
				$condition['year'] = $_REQUEST['columns'][5]['search']['value'];
			}
         if($_REQUEST['columns'][6]['search']['value']){
				$condition['car_status'] = $_REQUEST['columns'][6]['search']['value'];
			}
         
			$recordsTotal = $this->admin_model->get_all_counts(VEHICLE, $condition);
			$length = $_REQUEST['length'];
			$start = $_REQUEST['start'];
			$column = $columns[$_REQUEST['order'][0]['column']]; 
			$type = $_REQUEST['order'][0]['dir'];
			if(!empty($search)) {
				$recordsFiltered = $this->admin_model->get_all_counts(VEHICLE, $condition, $filters);
				$result = $this->admin_model->get_all_details(VEHICLE, $condition, [$column=>$type], $length, $start, $filters);
			} else {
				$recordsFiltered = $this->admin_model->get_all_counts(VEHICLE, $condition, []);
				$result = $this->admin_model->get_all_details(VEHICLE, $condition, [$column=>$type], $length, $start);
			}
			$dataArr = array(); $i = $_REQUEST['start']+1;
			foreach($result->result_array() as $res){
				$nestedData=array(); 
				$nestedData[] = '<input type="checkbox" class="ids" name="ids[]" value="'.(string)$res['_id'].'" />';
				$nestedData[] = ($res['vin_number']!='')?$res['vin_number']:'-';
				$nestedData[] = ($res['license_plate']!='')?$res['license_plate']:'-';
				$nestedData[] = ($res['make_id']!='')?getrow(MAKE, $res['make_id'])->make:'-';
				$nestedData[] = ($res['model_id']!='')?getrow(MODEL, $res['model_id'])->model:'-';
				$nestedData[] = ($res['year']!='')?$res['year']:'-';
				$nestedData[] = ($res['car_status']!='')?$this->data['car_status'][$res['car_status']]:'-';
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
				$action = '<a class="action-a" title="View" href="'.base_url().'admin/vehicle-view/'.(string)$res['_id'].'"><i class="fa fa-eye"></i></a>';
				$action .= '&nbsp;<a class="action-a" title="Edit" href="'.base_url().'admin/vehicle-edit/'.(string)$res['_id'].'" title="Edit"><i class="fa fa-pencil"></i></a>';
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
				$result = $this->admin_model->get_all_details(VEHICLE, $condition);
				if($result->num_rows() > 0){
					$this->data['result'] = $result->result_array();
					$form_mode = TRUE;
				} else {
					$this->setErrorMessage('error','Something went wrong!!!');
					redirect(ADMINURL.'/vehicle-list');
				}
			}
         $this->data['makes'] = $this->admin_model->get_all_details(MAKE, ['status'=>'1']);
         $this->data['models'] = $this->admin_model->get_all_details(MODEL, ['status'=>'1']);
			$this->data['form_mode'] = $form_mode;
			$this->data['heading'] = "Vehicle Details";
			$this->load->view('admin/vehicle/add_edit',$this->data);
		}
	}
	
	public function view($id = ''){
		if ($this->checkLogin('A') == ''){
			$this->setErrorMessage('error','Login required!!!');
			redirect(ADMINURL);
		} else {
			if($id!=''){
				$condition = ['_id' => MongoID((string)$id)];
				$result = $this->admin_model->get_all_details(VEHICLE, $condition);
				if($result->num_rows() > 0){
					$this->data['result'] = $result->result_array();
					$this->data['heading'] = "Vehicle Details";
					$this->load->view('admin/vehicle/view',$this->data);
				} else {
					$this->setErrorMessage('error','Something went wrong!!!');
					redirect(ADMINURL.'/vehicle-list');
				}
			} else {
				$this->setErrorMessage('error','Something went wrong!!!');
				redirect(ADMINURL.'/vehicle-list');
			}
		}
	}
	
	public function save(){
		if ($this->checkLogin('A') == ''){
			$response['message'] = 'Something went Wrong!';
		} else {
			$objectid = (string)$this->input->post('objectid');
			$status = $this->input->post('status');
			$vin_number = $this->input->post('vin_number');
			$license_plate = $this->input->post('license_plate');
			$status = ($status!='')?'1':'0';
         
         $condition = ['$or'=>[['vin_number'=>$vin_number], ['license_plate'=>$license_plate]]];
         if($objectid!='') $condition['_id'] = ['$ne'=>MongoID((string)$objectid)];
         $getDuplicate = $this->admin_model->get_all_counts(VEHICLE, $condition);
         
         $car_icon = '';
         if(!empty($_FILES) && $_FILES['car_icon']['name']!=''){
            $config['upload_path']          = './uploads/vehicles/';
            $config['encrypt_name']     	  = TRUE;
            $config['allowed_types']        = 'gif|jpg|png';
            $config['max_size']             = 5000;
            $this->load->library('upload', $config);
            if ($this->upload->do_upload('car_icon')) {
               $response = $this->upload->data();
               $car_icon = $response['file_name'];
            }
         }
			
         if($getDuplicate == 0){
            if($objectid!=''){
               $conditionArr = ['_id'=>MongoID((string)$objectid)];
               $adminCheck = $this->admin_model->get_all_counts(VEHICLE, $conditionArr);
               if ($adminCheck > 0){
                  $dataArr = ['dateModified'=>MongoDATE(time()), 'status'=>$status];
                  if($car_icon!=''){
                     $dataArr['car_icon'] = $car_icon;
                  }
                  $password = $this->input->post('password');
                  if($password!='')	$dataArr['password'] = md5(trim($password));
                  $this->admin_model->commonInsertUpdate(VEHICLE, 'update', ['_id', 'objectid', 'status'], $dataArr, $conditionArr);
                  $this->setErrorMessage('success','Vehicle details saved!!!');
               } else {
                  $this->setErrorMessage('error','Something went wrong!!!');
               }
            } else {
               $dataArr = ['dateAdded'=>MongoDATE(time()), 'status'=>$status];
               if($car_icon!=''){
                  $dataArr['car_icon'] = $car_icon;
               }
               $this->admin_model->commonInsertUpdate(VEHICLE,'insert',['_id', 'objectid', 'status'], $dataArr,[]);
               $this->setErrorMessage('success','Vehicle details saved!!!');
            }
         } else {
            $this->setErrorMessage('error','License Plate / VIN number already exist!!!');
         }
         redirect(ADMINURL.'/vehicle-list');
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
			$check = $this->admin_model->get_selected_fields(VEHICLE, $condition, ['cms_name']);
			
			if($check->num_rows()>0){
				$this->admin_model->update_details(VEHICLE, $dataArr, $condition);
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
			$check = $this->admin_model->get_all_counts(VEHICLE, $condition);
			if($check > 0){
				$this->admin_model->commonDelete(VEHICLE, $condition);
				echo 'success';
			} else {
				echo 'Something went wrong!!!';
			}
		}
	}
	
	public function bulk_status(){
		$ids = array_map(function($a){ return MongoID($a); }, $_POST['ids']);
		$this->change_bulk_status(VEHICLE, $_POST['status'], $ids);
	}
	
	public function bulk_delete(){
		$ids = array_map(function($a){ return MongoID($a); }, $_POST['ids']);
		$condition = ['_id'=>['$in'=>$ids]];
		$check = $this->admin_model->get_selected_fields(VEHICLE, $condition, ['_id']);
		if($check->num_rows()>0){
			$this->admin_model->commonDelete(VEHICLE, $condition);
			echo 'success';
		} else {
			echo 'Something went wrong!!!';
		}
	}
   
   public function get_maker_models(){
      $response['status'] = '0';
      $response['response'] = '';
      $make_id = $this->input->post('make_id');
      
      $make = $this->admin_model->get_all_counts(MAKE, ['_id'=>MongoID($make_id), 'status'=>'1']);
      if($make > 0){
         $models = $this->admin_model->get_all_details(MODEL, ['make_id'=>$make_id, 'status'=>'1']);
         if($models->num_rows() > 0){
            $response['status'] = '1';
            $modelArr = [];
            foreach($models->result() as $model){
               $modelArr[] = ['_id'=>(string)$model->_id, 'model'=>$model->model];
            }
            $response['response'] = $modelArr;
         } else {
            $response['response'] = "Model not found";
         }
      } else {
         $response['response'] = "Make not found";
      }
      echo json_encode($response, JSON_PRETTY_PRINT);
   }
	
}
