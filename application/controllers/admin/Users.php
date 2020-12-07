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
			$columns = array('_id', 'first_name', 'email', 'phone_number', 'status');
			$search = $_REQUEST['search']['value'];
			$filters = [
				'first_name'=>$search,
				'last_name'=>$search,
				'email'=>$search,
				'phone_number'=>$search,
				'user_name'=>$search
			];
         
			$condition = [];
         if($_REQUEST['columns'][5]['search']['value']){
				$condition['documents_to_verify'] = ($_REQUEST['columns'][5]['search']['value']=='Pending') ? ['$ne'=>[]] : ['$eq'=>[]];
			}
         if($_REQUEST['columns'][4]['search']['value']){
				$condition['status'] = ($_REQUEST['columns'][4]['search']['value']=='Active')?'1':'0';
			}
         
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
				$nestedData[] = ($res['first_name']!='' || $res['first_name']!='')?$res['first_name'].' '.$res['last_name']:'-';
				$nestedData[] = ($res['email']!='')?$res['email']:'-';
				$nestedData[] = ($res['phone_number']!='')?$res['phone_number']:'-';
				
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
				$action = '<a class="action-a" target="_blank" title="View" href="'.base_url().USERURL.'/'.(string)$res['_id'].'/dashboard"><i class="fa fa-eye"></i></a>';
				$action .= '&nbsp;<a class="action-a" target="_blank" title="Edit" href="'.base_url().USERURL.'/'.(string)$res['_id'].'/account/edit" title="Edit"><i class="fa fa-pencil"></i></a>';
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
				$condition = ['_id' => MongoID((string)$id)];
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
			$status = $this->input->post('status');
			$dob = $this->input->post('dob');
         if($dob!=''){
            $date = @explode('/', $dob);
            $date = $date[2].'-'.$date[1].'-'.$date[0];
            $dob = MongoDATE(strtotime($date));
         }
			$status = ($status!='')?'1':'0';
         $profile_status = ['personal'=>'0', 'contact'=>'0', 'address'=>'0', 'profile_validation'=>'0', 'pin_code'=>'0'];
         $dataArr = ['dob'=>$dob, 'profile_status'=>$profile_status, 'dateAdded'=>MongoDATE(time()), 'status'=>$status];
         $password = $this->input->post('password');
         if($password!='')	$dataArr['password'] = password_hash(trim($password), PASSWORD_DEFAULT);
         $this->admin_model->commonInsertUpdate(USERS,'insert',['_id', 'objectid', 'status', 'password', 'dob', 'subscription_date', 'activation_date', 'expiry_date'], $dataArr,[]);
         $this->setErrorMessage('success','Users details saved!!!');
         redirect(ADMINURL.'/users-list');
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
			$condition = ['_id'=>MongoID($id)];
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
		$ids = array_map(function($a){ return MongoID($a); }, $_POST['ids']);
		$this->change_bulk_status(USERS, $_POST['status'], $ids);
	}
	
	public function bulk_delete(){
		$ids = array_map(function($a){ return MongoID($a); }, $_POST['ids']);
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
