<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Faq extends MY_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('admin_model');
	}
		
	public function listing($id = ''){
		if ($this->checkLogin('A') == ''){
			$this->setErrorMessage('error','Login required!!!');
			redirect(ADMINURL);
		} else {
			$this->data['heading'] = "FAQ List";
			$this->load->view('admin/faq/listing',$this->data);
		}
	}
	
	public function listing_ajax(){
		if ($this->checkLogin('A') != ''){
			$columns = array( 
				0 =>'_id', 
				1 =>'', 
				2 =>'name', 
				3 =>'description',
				4 =>'status'
			);
			
			$search = $_REQUEST['search']['value'];
			$filters = [
				'name'=>$search,
				'description'=>$search
			];
			$condition = [];
			if($_REQUEST['columns'][4]['search']['value']){
				$condition['status'] = $_REQUEST['columns'][4]['search']['value'];
			}
			
			$recordsTotal = $this->admin_model->get_all_counts(FAQ, $condition);
			$length = $_REQUEST['length'];
			$start = $_REQUEST['start'];
			$column = $columns[$_REQUEST['order'][0]['column']]; 
			$type = $_REQUEST['order'][0]['dir'];
			if(!empty($search)) {
				$recordsFiltered = $this->admin_model->get_all_counts(FAQ, $condition, $filters);
				$result = $this->admin_model->get_all_details(FAQ, $condition, [$column=>$type], $length, $start, $filters);
			} else {
				$recordsFiltered = $this->admin_model->get_all_counts(FAQ, $condition, []);
				$result = $this->admin_model->get_all_details(FAQ, $condition, [$column=>$type], $length, $start);
			}
			$dataArr = array(); $i = $_REQUEST['start']+1;
			foreach($result->result_array() as $res){
				$nestedData=array(); 
				$nestedData[] = '<input type="checkbox" class="ids" name="ids[]" value="'.(string)$res['_id'].'" />';
				$nestedData[] = $i;
				$nestedData[] = (strlen($res['name'])>35)?substr($res['name'],0,35).'...':$res['name'];
				$description = '-';
				if($res['description']!=''){
					$description = (strlen($res['description'])>50)?substr($res['description'],0,50).'...':$res['description'];
				}
				$nestedData[] = $description;
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
				$action = '<a class="action-a" title="View" href="'.base_url().'admin/faq-view/'.(string)$res['_id'].'"><i class="fa fa-eye"></i></a>';
				$action .= '&nbsp;<a class="action-a" title="Edit" href="'.base_url().'admin/faq-edit/'.(string)$res['_id'].'" title="Edit"><i class="fa fa-pencil"></i></a>';
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
				$result = $this->admin_model->get_all_details(FAQ, $condition);
				if($result->num_rows() > 0){
					$this->data['result'] = $result->result_array();
					$form_mode = TRUE;
				} else {
					$this->setErrorMessage('error','Something went wrong!!!');
					redirect(ADMINURL.'/faq-list');
				}
			}
			$this->data['form_mode'] = $form_mode;
			$this->data['heading'] = "FAQ Details";
			$this->load->view('admin/faq/add_edit',$this->data);
		}
	}
	
	public function view($id = ''){
		if ($this->checkLogin('A') == ''){
			$this->setErrorMessage('error','Login required!!!');
			redirect(ADMINURL);
		} else {
			if($id!=''){
				$condition = ['_id' => MongoID((string)$id)];
				$result = $this->admin_model->get_all_details(FAQ, $condition);
				if($result->num_rows() > 0){
					$this->data['result'] = $result->result_array();
					$this->data['heading'] = "FAQ Details";
					$this->load->view('admin/faq/view',$this->data);
				} else {
					$this->setErrorMessage('error','Something went wrong!!!');
					redirect(ADMINURL.'/faq-list');
				}
			} else {
				$this->setErrorMessage('error','Something went wrong!!!');
				redirect(ADMINURL.'/faq-list');
			}
		}
	}
	
	public function save(){
		if ($this->checkLogin('A') == ''){
			$response['message'] = 'Something went Wrong!';
		} else {
			$objectid = (string)$this->input->post('objectid');
			if($objectid!=''){
				$conditionArr = ['_id'=>MongoID((string)$objectid)];
				$adminCheck = $this->admin_model->get_all_counts(FAQ, $conditionArr);
				if ($adminCheck > 0){
					$this->admin_model->commonInsertUpdate(FAQ, 'update', ['_id', 'objectid'], ['dateModified'=>(int)time()], $conditionArr);
					$this->setErrorMessage('success','FAQ details saved!!!');
					redirect(ADMINURL.'/faq-list');
				} else {
					$this->setErrorMessage('error','Something went wrong!!!');
					redirect(ADMINURL.'/faq-list');
				}
			} else {
				$this->admin_model->commonInsertUpdate(FAQ,'insert',['_id', 'objectid'],['status'=>'1', 'dateAdded'=>MongoDATE(time()), 'dateModified'=>(int)time()],[]);
				$this->setErrorMessage('success','FAQ details saved!!!');
				redirect(ADMINURL.'/faq-list');
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
			$check = $this->admin_model->get_selected_fields(FAQ, $condition, ['cms_name']);
			
			if($check->num_rows()>0){
				$this->admin_model->update_details(FAQ, $dataArr, $condition);
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
			$check = $this->admin_model->get_all_counts(FAQ, $condition);
			if($check > 0){
				$this->admin_model->commonDelete(FAQ, $condition);
				echo 'success';
			} else {
				echo 'Something went wrong!!!';
			}
		}
	}
	
	public function bulk_status(){
		$ids = array_map(function($a){ return MongoID($a); }, $_POST['ids']);
		$this->change_bulk_status(FAQ, $_POST['status'], $ids);
	}
	
	public function bulk_delete(){
		$ids = array_map(function($a){ return MongoID($a); }, $_POST['ids']);
		$condition = ['_id'=>['$in'=>$ids]];
		$check = $this->admin_model->get_selected_fields(FAQ, $condition, ['_id']);
		if($check->num_rows()>0){
			$this->admin_model->commonDelete(FAQ, $condition);
			echo 'success';
		} else {
			echo 'Something went wrong!!!';
		}
	}
	
}
