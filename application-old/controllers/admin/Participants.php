<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Participants extends MY_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('admin_model');
	}
		
	public function listing($id = ''){
		if ($this->checkLogin('A') == ''){
			$this->setErrorMessage('error','Login required!!!');
			redirect(ADMINURL);
		} else {
			$this->data['heading'] = "Participants List";
			$this->load->view('admin/participants/listing',$this->data);
		}
	}
	
	public function listing_ajax(){
		if ($this->checkLogin('A') != ''){
			$columns = array( 
				0 =>'_id',
				1 =>'',
				2 =>'name',
				3 =>'email',
				4 =>'mobile',
				5 =>'team_name',
				6 =>'event',
				7 =>'status'
			);
			
			$search = $_REQUEST['search']['value'];
			$filters = [
				'name'=>$search,
				'email'=>$search,
				'mobile'=>$search,
				'team_name'=>$search,
				'event'=>$search
			];
			$condition = [];
			if($_REQUEST['columns'][4]['search']['value']){
				$condition['status'] = $_REQUEST['columns'][4]['search']['value'];
			}
			
			$recordsTotal = $this->admin_model->get_all_counts(PARTICIPANTS, $condition);
			$length = $_REQUEST['length'];
			$start = $_REQUEST['start'];
			$column = $columns[$_REQUEST['order'][0]['column']]; 
			$type = $_REQUEST['order'][0]['dir'];
			if(!empty($search)) {
				$recordsFiltered = $this->admin_model->get_all_counts(PARTICIPANTS, $condition, $filters);
				$result = $this->admin_model->get_all_details(PARTICIPANTS, $condition, [$column=>$type], $length, $start, $filters);
			} else {
				$recordsFiltered = $this->admin_model->get_all_counts(PARTICIPANTS, $condition, []);
				$result = $this->admin_model->get_all_details(PARTICIPANTS, $condition, [$column=>$type], $length, $start);
			}
			$dataArr = array(); $i = $_REQUEST['start']+1;
			foreach($result->result_array() as $res){
				$nestedData=array(); 
				$nestedData[] = '<input type="checkbox" class="ids" name="ids[]" value="'.(string)$res['_id'].'" />';
				$nestedData[] = $i;
				$nestedData[] = ($res['name']!='')?$res['name']:'-';
				$nestedData[] = ($res['email']!='')?$res['email']:'-';
				$nestedData[] = ($res['mobile']!='')?$res['mobile']:'-';
				$nestedData[] = ($res['team_name']!='')?$res['team_name']:'-';
				$nestedData[] = ($res['event']!='')?$res['event']:'-';
				// $nestedData[] = ($res['date_time']!='')?date('d/m/Y H:i A', $res['date_time']):'-';
				
				if($this->data['user_type']=='superadmin'){
					if($res['status'] == '0'){
						$status = '<a class="btn btn-xs btn-success" onclick="change_status(\''.(string)$res['_id'].'\', \'1\')" title="Click to make inactive">Approve</a>';
						$status .= '&nbsp;<a class="btn btn-xs btn-danger" onclick="change_status(\''.(string)$res['_id'].'\', \'2\')" title="Click to make inactive">Unapprove</a>';
					} else { 
						$btn = 'danger'; $stat = 'Unapproved';
						if($res['status']=='1'){ 
							$btn = 'success'; 
							$stat = 'Approved'; 
						}
						$status = '<a class="btn btn-xs btn-'.$btn.'" title="Can\'t able to change status">'.$stat.'</a>';
					} 
				} else {
					$btn = 'default'; $stat = 'Unapproved';
					if($res['status']=='1'){ 
						$btn = 'success'; 
						$stat = 'Approved'; 
					}
					$status = '<a class="btn btn-xs btn-bordered btn-'.$btn.'" title="Can\'t able to change status">'.$stat.'</a>';
				}
				$nestedData[] = $status;
				
				$action = '<a class="action-a" title="View" href="'.base_url().'admin/participants-view/'.(string)$res['_id'].'"><i class="fa fa-eye"></i></a>';
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
				$result = $this->admin_model->get_all_details(PARTICIPANTS, $condition);
				if($result->num_rows() > 0){
					$this->data['result'] = $result->result_array();
					$form_mode = TRUE;
				} else {
					$this->setErrorMessage('error','Something went wrong!!!');
					redirect(ADMINURL.'/participants-list');
				}
			}
			$this->data['form_mode'] = $form_mode;
			$this->data['heading'] = "Participants Details";
			$this->load->view('admin/participants/add_edit',$this->data);
		}
	}
	
	public function view($id = ''){
		if ($this->checkLogin('A') == ''){
			$this->setErrorMessage('error','Login required!!!');
			redirect(ADMINURL);
		} else {
			if($id!=''){
				$condition = ['_id' => objectid((string)$id)];
				$result = $this->admin_model->get_all_details(PARTICIPANTS, $condition);
				if($result->num_rows() > 0){
					$this->data['result'] = $result->result_array();
					$this->data['heading'] = "Participants Details";
					$this->load->view('admin/participants/view',$this->data);
				} else {
					$this->setErrorMessage('error','Something went wrong!!!');
					redirect(ADMINURL.'/participants-list');
				}
			} else {
				$this->setErrorMessage('error','Something went wrong!!!');
				redirect(ADMINURL.'/participants-list');
			}
		}
	}
	
	public function save(){
		if ($this->checkLogin('A') == ''){
			$response['message'] = 'Something went Wrong!';
		} else {
			$objectid = (string)$this->input->post('objectid');
			
			$status = $this->input->post('status');
			$status = ($status!='')?'1':'0'; 
			if($objectid!=''){
				$conditionArr = ['_id'=>objectid((string)$objectid)];
				$adminCheck = $this->admin_model->get_all_counts(PARTICIPANTS, $conditionArr);
				if ($adminCheck > 0){
					$this->admin_model->commonInsertUpdate(PARTICIPANTS, 'update', ['_id', 'objectid', 'status'], ['dateModified'=>(int)time(), 'status'=>$status], $conditionArr);
					$this->setErrorMessage('success','Participants details saved!!!');
					redirect(ADMINURL.'/participants-list');
				} else {
					$this->setErrorMessage('error','Something went wrong!!!');
					redirect(ADMINURL.'/participants-list');
				}
			} else {
				$this->admin_model->commonInsertUpdate(PARTICIPANTS,'insert',['_id', 'objectid', 'status'],['dateAdded'=>MongoDATE(time()), 'status'=>$status],[]);
				$this->setErrorMessage('success','Participants details saved!!!');
				redirect(ADMINURL.'/participants-list');
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
			$check = $this->admin_model->get_selected_fields(PARTICIPANTS, $condition, ['cms_name']);
			
			if($check->num_rows()>0){
				$this->admin_model->update_details(PARTICIPANTS, $dataArr, $condition);
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
			$check = $this->admin_model->get_all_counts(PARTICIPANTS, $condition);
			if($check > 0){
				$this->admin_model->commonDelete(PARTICIPANTS, $condition);
				echo 'success';
			} else {
				echo 'Something went wrong!!!';
			}
		}
	}
	
	public function bulk_status(){
		$ids = array_map(function($a){ return objectid($a); }, $_POST['ids']);
		$this->change_bulk_status(PARTICIPANTS, $_POST['status'], $ids);
	}
	
	public function bulk_delete(){
		$ids = array_map(function($a){ return objectid($a); }, $_POST['ids']);
		$condition = ['_id'=>['$in'=>$ids]];
		$check = $this->admin_model->get_selected_fields(PARTICIPANTS, $condition, ['_id']);
		if($check->num_rows()>0){
			$this->admin_model->commonDelete(PARTICIPANTS, $condition);
			echo 'success';
		} else {
			echo 'Something went wrong!!!';
		}
	}
	
}
