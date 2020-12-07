<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends MY_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('admin_model');
	}
		
	public function listing($id = ''){
		if ($this->checkLogin('A') == ''){
			$this->setErrorMessage('error','Login required!!!');
			redirect(ADMINURL);
		} else {
			$this->data['heading'] = "Users List";
			$this->load->view('admin/users/listing',$this->data);
		}
	}
	
	public function listing_ajax(){
		if ($this->checkLogin('A') != ''){
			$columns = array( 
				0 =>'_id', 
				1 =>'', 
				2 =>'first_name', 
				3 =>'email',
				4 =>'mobile',
				5 =>'user_name',
				6 =>'status'
			);
			
			$search = $_REQUEST['search']['value'];
			$filters = [
				'first_name'=>$search,
				'email'=>$search,
				'mobile'=>$search,
				'user_name'=>$search
			];
			$condition = [];
			$recordsTotal = $this->admin_model->get_all_counts(USERS, $condition);
			$length = $_REQUEST['length'];
			$start = $_REQUEST['start'];
			$column = $columns[$_REQUEST['order'][0]['column']]; 
			$type = $_REQUEST['order'][0]['dir'];
			if(!empty($search)) {
				$recordsFiltered = $this->admin_model->get_all_counts(USERS, $condition, $filters);
				$result = $this->admin_model->get_all_details(USERS, $condition, [$column=>$type], $length, $start, $filters);
			} else {
				$recordsFiltered = $this->admin_model->get_all_counts(USERS, $condition, []);
				$result = $this->admin_model->get_all_details(USERS, $condition, [$column=>$type], $length, $start);
			}
			$dataArr = array(); $i = $_REQUEST['start']+1;
			foreach($result->result_array() as $res){
				$nestedData=array(); 
				$nestedData[] = '<input type="checkbox" class="ids" name="ids[]" value="'.(string)$res['_id'].'" />';
				$nestedData[] = $i;
				$nestedData[] = ($res['first_name']!='' || $res['first_name']!='')?$res['first_name'].' '.$res['last_name']:'-';
				$nestedData[] = ($res['email']!='')?$res['email']:'-';
				$nestedData[] = ($res['mobile']!='')?$res['mobile']:'-';
				$nestedData[] = ($res['user_name']!='')?$res['user_name']:'-';
				
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
				$action = '<a class="action-a" title="View" href="'.base_url().'admin/users-view/'.(string)$res['_id'].'"><i class="fa fa-eye"></i></a>';
				$action .= '&nbsp;<a class="action-a" title="Edit" href="'.base_url().'admin/users-edit/'.(string)$res['_id'].'" title="Edit"><i class="fa fa-pencil"></i></a>';
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
				$condition = ['_id' => objectid((string)$id)];
				$result = $this->admin_model->get_all_details(USERS, $condition);
				if($result->num_rows() > 0){
					$this->data['result'] = $result->result_array();
					$form_mode = TRUE;
				} else {
					$this->setErrorMessage('error','Something went wrong!!!');
					redirect(ADMINURL.'/users-list');
				}
			}
         $this->data['subscription'] = $this->admin_model->get_all_details(SUBSCRIPTION, ['status'=>'1']);
			$this->data['form_mode'] = $form_mode;
			$this->data['heading'] = "Users Details";
			$this->load->view('admin/users/add_edit',$this->data);
		}
	}
	
	public function view($id = ''){
		if ($this->checkLogin('A') == ''){
			$this->setErrorMessage('error','Login required!!!');
			redirect(ADMINURL);
		} else {
			if($id!=''){
				$condition = ['_id' => objectid((string)$id)];
				$result = $this->admin_model->get_all_details(USERS, $condition);
				if($result->num_rows() > 0){
					$this->data['result'] = $result->result_array();
					$this->data['heading'] = "Users Details";
					$this->load->view('admin/users/view',$this->data);
				} else {
					$this->setErrorMessage('error','Something went wrong!!!');
					redirect(ADMINURL.'/users-list');
				}
			} else {
				$this->setErrorMessage('error','Something went wrong!!!');
				redirect(ADMINURL.'/users-list');
			}
		}
	}
	
	public function save(){
		if ($this->checkLogin('A') == ''){
			$response['message'] = 'Something went Wrong!';
		} else {
			$objectid = (string)$this->input->post('objectid');
			$status = $this->input->post('status');
			$third_party_badge = $this->input->post('third_party_badge');
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
			$third_party_badge = ($third_party_badge!='')?'1':'0';
			if($objectid!=''){
				$conditionArr = ['_id'=>objectid((string)$objectid)];
				$adminCheck = $this->admin_model->get_all_counts(USERS, $conditionArr);
				if ($adminCheck > 0){
					$dataArr = ['subscription_date'=>MongoDATE($subscription_date), 'activation_date'=>MongoDATE($activation_date), 'expiry_date'=>MongoDATE($expiry_date), 'dob'=>MongoDATE($dob), 'third_party_badge'=>$third_party_badge, 'dateModified'=>MongoDATE(time()), 'status'=>$status];
					$this->admin_model->commonInsertUpdate(USERS, 'update', ['_id', 'objectid', 'status', 'password', 'dob', 'subscription_date', 'activation_date', 'expiry_date'], $dataArr, $conditionArr);
					$this->setErrorMessage('success','Users details saved!!!');
					redirect(ADMINURL.'/users-list');
				} else {
					$this->setErrorMessage('error','Something went wrong!!!');
					redirect(ADMINURL.'/users-list');
				}
			} else {
				$dataArr = ['subscription_date'=>MongoDATE($subscription_date), 'activation_date'=>MongoDATE($activation_date), 'expiry_date'=>MongoDATE($expiry_date), 'dob'=>MongoDATE($dob), 'third_party_badge'=>$third_party_badge, 'dateAdded'=>MongoDATE(time()), 'status'=>$status];
				$password = $this->input->post('password');
				if($password!='')	$dataArr['password'] = password_hash(trim($password), PASSWORD_DEFAULT);
				$this->admin_model->commonInsertUpdate(USERS,'insert',['_id', 'objectid', 'status', 'password', 'dob', 'subscription_date', 'activation_date', 'expiry_date'], $dataArr,[]);
				$this->setErrorMessage('success','Users details saved!!!');
				redirect(ADMINURL.'/users-list');
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
			$condition = ['_id'=>objectid($id)];
			$check = $this->admin_model->get_selected_fields(USERS, $condition, ['cms_name']);
			
			if($check->num_rows()>0){
				$this->admin_model->update_details(USERS, $dataArr, $condition);
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
			$condition = ['_id'=>objectid($id)];
			$check = $this->admin_model->get_all_counts(USERS, $condition);
			if($check > 0){
				$this->admin_model->commonDelete(USERS, $condition);
				echo 'success';
			} else {
				echo 'Something went wrong!!!';
			}
		}
	}
	
	public function bulk_status(){
		$ids = array_map(function($a){ return objectid($a); }, $_POST['ids']);
		$this->change_bulk_status(USERS, $_POST['status'], $ids);
	}
	
	public function bulk_delete(){
		$ids = array_map(function($a){ return objectid($a); }, $_POST['ids']);
		$condition = ['_id'=>['$in'=>$ids]];
		$check = $this->admin_model->get_selected_fields(USERS, $condition, ['_id']);
		if($check->num_rows()>0){
			$this->admin_model->commonDelete(USERS, $condition);
			echo 'success';
		} else {
			echo 'Something went wrong!!!';
		}
	}
	
}
