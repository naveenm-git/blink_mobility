<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Partner extends MY_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('admin_model');
	}
		
	public function listing($id = ''){
		if ($this->checkLogin('A') == ''){
			$this->setErrorMessage('error','Login required!!!');
			redirect(ADMINURL);
		} else {
			$this->data['heading'] = "Partner List";
			$this->load->view('admin/partner/listing',$this->data);
		}
	}
	
	public function listing_ajax(){
		if ($this->checkLogin('A') != ''){
			$columns = array( 
				0 =>'_id',
				1 =>'name',
				2 =>'status'
			);
			
			$search = $_REQUEST['search']['value'];
			$filters = [
				'name'=>$search,
				'description'=>$search
			];
			$condition = [];
			if($_REQUEST['columns'][3]['search']['value']){
				$condition['status'] = $_REQUEST['columns'][3]['search']['value'];
			}
			
			$recordsTotal = $this->admin_model->get_all_counts(PARTNER, $condition);
			$length = $_REQUEST['length'];
			$start = $_REQUEST['start'];
			$column = $columns[$_REQUEST['order'][0]['column']]; 
			$type = $_REQUEST['order'][0]['dir'];
			if(!empty($search)) {
				$recordsFiltered = $this->admin_model->get_all_counts(PARTNER, $condition, $filters);
				$result = $this->admin_model->get_all_details(PARTNER, $condition, [$column=>$type], $length, $start, $filters);
			} else {
				$recordsFiltered = $this->admin_model->get_all_counts(PARTNER, $condition, []);
				$result = $this->admin_model->get_all_details(PARTNER, $condition, [$column=>$type], $length, $start);
			}
			$dataArr = array(); $i = $_REQUEST['start']+1;
			foreach($result->result_array() as $res){
				$nestedData=array(); 
				$nestedData[] = '<input type="checkbox" class="ids" name="ids[]" value="'.(string)$res['_id'].'" />';
				$nestedData[] = ($res['name']!='')?$res['name']:'-';
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
				$action = '<a class="action-a" title="View" href="'.base_url().'admin/partner-view/'.(string)$res['_id'].'"><i class="fa fa-eye"></i></a>';
				$action .= '&nbsp;<a class="action-a" title="Edit" href="'.base_url().'admin/partner-edit/'.(string)$res['_id'].'" title="Edit"><i class="fa fa-pencil"></i></a>';
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
	
	public function view($id = ''){
		if ($this->checkLogin('A') == ''){
			$this->setErrorMessage('error','Login required!!!');
			redirect(ADMINURL);
		} else {
			if($id!=''){
				$condition = ['_id' => MongoID((string)$id)];
				$result = $this->admin_model->get_all_details(PARTNER, $condition);
				if($result->num_rows() > 0){
					$this->data['partner'] = $result->result_array();
					$this->data['heading'] = "Partner Details";
					$this->load->view('admin/partner/view_partner',$this->data);
				} else {
					$this->setErrorMessage('error','Something went wrong!!!');
					redirect(ADMINURL.'/partner-list');
				}
			} else {
				$this->setErrorMessage('error','Something went wrong!!!');
				redirect(ADMINURL.'/partner-list');
			}
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
				$result = $this->admin_model->get_all_details(PARTNER, $condition);
				if($result->num_rows() > 0){
					$this->data['result'] = $result->result_array();
					$form_mode = TRUE;
				} else {
					$this->setErrorMessage('error','Something went wrong!!!');
					redirect(ADMINURL.'/partner-list');
				}
			}
			$this->data['form_mode'] = $form_mode;
			$this->data['heading'] = "Partner Details";
			$this->load->view('admin/partner/add_edit',$this->data);
		}
	}
	
	public function save(){
		$response['status'] = 'error';
		$response['message'] = '';
		if ($this->checkLogin('A') == ''){
			$response['message'] = 'Something went Wrong!';
		} else {
			$objectid = (string)$this->input->post('objectid');
			$name = $this->input->post('name');
			$status = $this->input->post('status');
			$status = ($status!='')?'1':'0';
         
         $partnerCond = ['name' => $name];
         if($objectid !='') $partnerCond['_id'] = ['$ne' => MongoID($objectid)];
         $partner = $this->admin_model->get_all_counts(PARTNER, $partnerCond);
         if($partner > 0){
            $this->setErrorMessage('error','Partner name already exist!!!');
            redirect(ADMINURL.'/partner-list');
         }
         
			if($objectid!=''){
				$conditionArr = ['_id'=>MongoID((string)$objectid)];
				$adminCheck = $this->admin_model->get_all_counts(PARTNER, $conditionArr);
				if ($adminCheck > 0){
					$dataArr = ['dateModified'=>MongoDATE(time()), 'status'=>$status];
					$this->admin_model->commonInsertUpdate(PARTNER, 'update', ['_id', 'objectid', 'status'], $dataArr, $conditionArr);
					$this->setErrorMessage('success','Partner details saved!!!');
				} else {
					$this->setErrorMessage('error','Something went wrong!!!');
				}
			} else {
				$dataArr = ['dateAdded'=>MongoDATE(time()), 'status'=>$status];
				$this->admin_model->commonInsertUpdate(PARTNER,'insert',['_id', 'objectid', 'status'],$dataArr,[]);
				$this->setErrorMessage('success','Partner details saved!!!');
			}
		}
      redirect(ADMINURL.'/partner-list');
	}
	
	public function get_details(){
		$response['status'] = 'error';
		$response['message'] = '';
		if ($this->checkLogin('A') == ''){
			$response['message'] = 'Something went wrong!!!';
		} else {
			$objectid = $this->input->post('objectid');
			$condition = ['_id'=>MongoID($objectid)];
			$result = $this->admin_model->get_selected_fields(PARTNER, $condition, ['name', 'description', 'meta_title', 'meta_description', 'meta_keyword']);
			if($result->num_rows()>0){
				$response['status'] = 'success';
				$response['message'] = $result->result_array()[0];
			} else {
				$response['message'] = 'Something went wrong!!!';
			}
		}
		echo json_encode($response); exit;
	}
	
	public function change_status(){
		if ($this->checkLogin('A') == ''){
			echo 'Something went wrong!!!';
		} else {
			$status = $this->input->post('status');
			$id = $this->input->post('id');
			$dataArr = ['status'=>$status];
			$condition = ['_id'=>MongoID($id)];
			$check = $this->admin_model->get_selected_fields(PARTNER, $condition, ['partner_name']);
			
			if($check->num_rows()>0){
				$this->admin_model->update_details(PARTNER, $dataArr, $condition);
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
			$check = $this->admin_model->get_all_counts(PARTNER, $condition);
			if($check > 0){
				$this->admin_model->commonDelete(PARTNER, $condition);
				echo 'success';
			} else {
				echo 'Something went wrong!!!';
			}
		}
	}
	
}
