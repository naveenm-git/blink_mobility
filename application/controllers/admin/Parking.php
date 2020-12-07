<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Parking extends MY_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('admin_model');
	}
		
	public function listing($id = ''){
		if ($this->checkLogin('A') == ''){
			$this->setErrorMessage('error','Login required!!!');
			redirect(ADMINURL);
		} else {
			$this->data['heading'] = "Parking List";
         $this->data['stations'] = $this->admin_model->get_all_details(STATION, ['status'=>'1']);
			$this->load->view('admin/parking/listing',$this->data);
		}
	}
	
	public function listing_ajax(){
		if ($this->checkLogin('A') != ''){
			$columns = array( 
				0 =>'_id', 
				1 =>'', 
				2 =>'name',
				3 =>'station_id',
				4 =>'status'
			);
			
			$search = $_REQUEST['search']['value'];
			$filters = [
				'name'=>$search
			];
			$condition = [];
         if($_REQUEST['columns'][3]['search']['value']){
				$condition['station_id'] = $_REQUEST['columns'][3]['search']['value'];
			}
         
			$recordsTotal = $this->admin_model->get_all_counts(PARKING, $condition);
			$length = $_REQUEST['length'];
			$start = $_REQUEST['start'];
			$column = $columns[$_REQUEST['order'][0]['column']]; 
			$type = $_REQUEST['order'][0]['dir'];
			if(!empty($search)) {
				$recordsFiltered = $this->admin_model->get_all_counts(PARKING, $condition, $filters);
				$result = $this->admin_model->get_all_details(PARKING, $condition, [$column=>$type], $length, $start, $filters);
			} else {
				$recordsFiltered = $this->admin_model->get_all_counts(PARKING, $condition, []);
				$result = $this->admin_model->get_all_details(PARKING, $condition, [$column=>$type], $length, $start);
			}
			$dataArr = array(); $i = $_REQUEST['start']+1;
			foreach($result->result_array() as $res){
				$nestedData=array(); 
				$nestedData[] = '<input type="checkbox" class="ids" name="ids[]" value="'.(string)$res['_id'].'" />';
				$nestedData[] = $i;
				$nestedData[] = ($res['name']!='')?$res['name']:'-';
				$nestedData[] = ($res['station_id']!='')?(getrow(STATION, $res['station_id'])->name):'-';
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
				$action = '<a class="action-a" title="View" href="'.base_url().'admin/parking-view/'.(string)$res['_id'].'"><i class="fa fa-eye"></i></a>';
				$action .= '&nbsp;<a class="action-a" title="Edit" href="'.base_url().'admin/parking-edit/'.(string)$res['_id'].'" title="Edit"><i class="fa fa-pencil"></i></a>';
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
				$result = $this->admin_model->get_all_details(PARKING, $condition);
				if($result->num_rows() > 0){
					$this->data['result'] = $result->result_array();
					$form_mode = TRUE;
				} else {
					$this->setErrorMessage('error','Something went wrong!!!');
					redirect(ADMINURL.'/parking-list');
				}
			}
         $this->data['stations'] = $this->admin_model->get_all_details(STATION, ['status'=>'1']);
			$this->data['form_mode'] = $form_mode;
			$this->data['heading'] = "Parking Details";
			$this->load->view('admin/parking/add_edit',$this->data);
		}
	}
	
	public function view($id = ''){
		if ($this->checkLogin('A') == ''){
			$this->setErrorMessage('error','Login required!!!');
			redirect(ADMINURL);
		} else {
			if($id!=''){
				$condition = ['_id' => MongoID((string)$id)];
				$result = $this->admin_model->get_all_details(PARKING, $condition);
				if($result->num_rows() > 0){
					$this->data['result'] = $result->result_array();
					$this->data['heading'] = "Parking Details";
					$this->load->view('admin/parking/view',$this->data);
				} else {
					$this->setErrorMessage('error','Something went wrong!!!');
					redirect(ADMINURL.'/parking-list');
				}
			} else {
				$this->setErrorMessage('error','Something went wrong!!!');
				redirect(ADMINURL.'/parking-list');
			}
		}
	}
	
	public function save(){
		if ($this->checkLogin('A') == ''){
			$response['message'] = 'Something went Wrong!';
		} else {
			$objectid = (string)$this->input->post('objectid');
			$status = $this->input->post('status');
			$dob = $this->input->post('dob');
			$subscription_date = $this->input->post('subscription_date');
			$activation_date = $this->input->post('activation_date');
			$expiry_date = $this->input->post('expiry_date');
         
         $dates = [];
         foreach(['dob', 'subscription_date', 'activation_date', 'expiry_date'] as $key){
            $date = @explode('/',$_POST[$key]);
            $date = $date[2].'-'.$date[1].'-'.$date[0];
            $dates[$key] = strtotime($date);
         }
         extract($dates);
                  
			$status = ($status!='')?'1':'0';
			if($objectid!=''){
				$conditionArr = ['_id'=>MongoID((string)$objectid)];
				$adminCheck = $this->admin_model->get_all_counts(PARKING, $conditionArr);
				if ($adminCheck > 0){
					$dataArr = ['subscription_date'=>MongoDATE($subscription_date), 'activation_date'=>MongoDATE($activation_date), 'expiry_date'=>MongoDATE($expiry_date), 'dob'=>MongoDATE($dob), 'dateModified'=>MongoDATE(time()), 'status'=>$status];
					$password = $this->input->post('password');
					if($password!='')	$dataArr['password'] = md5(trim($password));
					$this->admin_model->commonInsertUpdate(PARKING, 'update', ['_id', 'objectid', 'status', 'password', 'dob', 'subscription_date', 'activation_date', 'expiry_date'], $dataArr, $conditionArr);
					$this->setErrorMessage('success','Parking details saved!!!');
					redirect(ADMINURL.'/parking-list');
				} else {
					$this->setErrorMessage('error','Something went wrong!!!');
					redirect(ADMINURL.'/parking-list');
				}
			} else {
				$dataArr = ['subscription_date'=>MongoDATE(strtotime($subscription_date)), 'activation_date'=>MongoDATE(strtotime($activation_date)), 'expiry_date'=>MongoDATE(strtotime($expiry_date)), 'dob'=>MongoDATE(strtotime($dob)), 'dateAdded'=>MongoDATE(time()), 'status'=>$status];
				$password = $this->input->post('password');
				if($password!='')	$dataArr['password'] = md5(trim($password));
				$this->admin_model->commonInsertUpdate(PARKING,'insert',['_id', 'objectid', 'status', 'password', 'dob', 'subscription_date', 'activation_date', 'expiry_date'], $dataArr,[]);
				$this->setErrorMessage('success','Parking details saved!!!');
				redirect(ADMINURL.'/parking-list');
			}
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
			$check = $this->admin_model->get_selected_fields(PARKING, $condition, ['cms_name']);
			
			if($check->num_rows()>0){
				$this->admin_model->update_details(PARKING, $dataArr, $condition);
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
			$check = $this->admin_model->get_all_counts(PARKING, $condition);
			if($check > 0){
				$this->admin_model->commonDelete(PARKING, $condition);
				echo 'success';
			} else {
				echo 'Something went wrong!!!';
			}
		}
	}
	
	public function bulk_status(){
		$ids = array_map(function($a){ return MongoID($a); }, $_POST['ids']);
		$this->change_bulk_status(PARKING, $_POST['status'], $ids);
	}
	
	public function bulk_delete(){
		$ids = array_map(function($a){ return MongoID($a); }, $_POST['ids']);
		$condition = ['_id'=>['$in'=>$ids]];
		$check = $this->admin_model->get_selected_fields(PARKING, $condition, ['_id']);
		if($check->num_rows()>0){
			$this->admin_model->commonDelete(PARKING, $condition);
			echo 'success';
		} else {
			echo 'Something went wrong!!!';
		}
	}
	
}
