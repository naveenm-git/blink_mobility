<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Newsletter extends MY_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('admin_model');
	}
		
	public function listing($id = ''){
		if ($this->checkLogin('A') == ''){
			$this->setErrorMessage('error','Login required!!!');
			redirect(ADMINURL);
		} else {
			$this->data['heading'] = "Email Template";
			$this->load->view('admin/newsletter/listing',$this->data);
		}
	}
	
	public function listing_ajax(){
		if ($this->checkLogin('A') != ''){
			$columns = array( 
				0 =>'_id', 
				1 =>'dateAdded', 
				2 =>'name', 
				3 =>'email_subject',
				5 =>'status'
			);
			
			$search = $_REQUEST['search']['value'];
			$filters = [
				'name'=>$search,
				'category'=>$search
			];
			$condition = [];
			
			$recordsTotal = $this->admin_model->get_all_counts(NEWSLETTER, $condition);
			$length = $_REQUEST['length'];
			$start = $_REQUEST['start'];
			$column = $columns[$_REQUEST['order'][0]['column']]; 
			$type = $_REQUEST['order'][0]['dir'];
			if(!empty($search)) {
				$recordsFiltered = $this->admin_model->get_all_counts(NEWSLETTER, $condition, $filters);
				$result = $this->admin_model->get_all_details(NEWSLETTER, $condition, [$column=>$type], $length, $start, $filters);
			} else {
				$recordsFiltered = $this->admin_model->get_all_counts(NEWSLETTER, $condition, []);
				$result = $this->admin_model->get_all_details(NEWSLETTER, $condition, [$column=>$type], $length, $start);
			}
			$dataArr = array(); $i = $_REQUEST['start']+1;
			foreach($result->result_array() as $res){
				$nestedData=array(); 
				$nestedData[] = '<input type="checkbox" class="ids" name="ids[]" value="'.(string)$res['_id'].'" />';
				$nestedData[] = $i;
				$nestedData[] = ($res['name']!='')?$res['name']:'-';
				$nestedData[] = ($res['email_subject']!='')?$res['email_subject']:'-';
				
				if($res['type']!='custom'){
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
				} else {
					$status = '<a class="btn btn-xs btn-default" title="Can\'t able to change status">Default</a>';
				}
				
				$nestedData[] = $status;
				$action = '<a class="action-a" title="View" href="'.base_url().'admin/email-template-view/'.(string)$res['_id'].'"><i class="fa fa-eye"></i></a>';
				if($res['type']!='custom'){
					$action .= '&nbsp;<a class="action-a" title="Edit" href="'.base_url().'admin/email-template-edit/'.(string)$res['_id'].'" title="Edit"><i class="fa fa-pencil"></i></a>';
					$action .= '&nbsp;<a class="action-a act_status" title="Remove" onclick="delete_record(\''.(string)$res['_id'].'\')" data-rid="'.(string)$res['_id'].'"><i class="fa fa-trash"></i></a>';
				}
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
				$result = $this->admin_model->get_all_details(NEWSLETTER, $condition);
				if($result->num_rows() > 0){
					$this->data['result'] = $result->result_array();
					$form_mode = TRUE;
				} else {
					$this->setErrorMessage('error','Something went wrong!!!');
					redirect(ADMINURL.'/email-template-list');
				}
			}
			$this->data['form_mode'] = $form_mode;
			$this->data['heading'] = "Newsletter Details";
			$this->load->view('admin/newsletter/add_edit',$this->data);
		}
	}
	
	public function view($id = ''){
		if ($this->checkLogin('A') == ''){
			$this->setErrorMessage('error','Login required!!!');
			redirect(ADMINURL);
		} else {
			if($id!=''){
				$condition = ['_id' => MongoID((string)$id)];
				$result = $this->admin_model->get_all_details(NEWSLETTER, $condition);
				if($result->num_rows() > 0){
					$this->data['sport'] = $this->admin_model->get_selected_fields(SPORTS, ['_id'=>MongoID($result->row()->sport)], ['name'])->row()->name;
					$this->data['author'] = $this->admin_model->get_selected_fields(AUTHOR, ['_id'=>MongoID($result->row()->author)], ['name'])->row()->name;
					$this->data['result'] = $result->result_array();
					$this->data['heading'] = "Newsletter Details";
					$this->load->view('admin/newsletter/view',$this->data);
				} else {
					$this->setErrorMessage('error','Something went wrong!!!');
					redirect(ADMINURL.'/email-template-list');
				}
			} else {
				$this->setErrorMessage('error','Something went wrong!!!');
				redirect(ADMINURL.'/email-template-list');
			}
		}
	}
	
	public function save(){
		if ($this->checkLogin('A') == ''){
			$response['message'] = 'Something went Wrong!';
		} else {
			$objectid = (string)$this->input->post('objectid');
			$dataArr = ['type' => 'email'];
			
			if($objectid!=''){
				$conditionArr = ['_id'=>MongoID((string)$objectid), 'type'=>'email'];
				$adminCheck = $this->admin_model->get_all_counts(NEWSLETTER, $conditionArr);
				if ($adminCheck > 0){
					$dataArr = array_merge($dataArr, ['dateModified'=>(int)time()]);
					$this->admin_model->commonInsertUpdate(NEWSLETTER, 'update', ['_id', 'objectid'], $dataArr , $conditionArr);
					$this->setErrorMessage('success','Email template saved!!!');
					redirect(ADMINURL.'/email-template-list');
				} else {
					$this->setErrorMessage('error','Something went wrong!!!');
					redirect(ADMINURL.'/email-template-list');
				}
			} else {
				$dataArr = array_merge($dataArr, ['status'=>'1', 'dateAdded' => (int)time()]);
				$this->admin_model->commonInsertUpdate(NEWSLETTER,'insert',['_id', 'objectid'], $dataArr, []);
				$this->setErrorMessage('success','Email template saved!!!');
				redirect(ADMINURL.'/email-template-list');
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
			$check = $this->admin_model->get_selected_fields(NEWSLETTER, $condition, ['cms_name']);
			
			if($check->num_rows()>0){
				$this->admin_model->update_details(NEWSLETTER, $dataArr, $condition);
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
			$check = $this->admin_model->get_all_counts(NEWSLETTER, $condition);
			if($check > 0){
				$this->admin_model->commonDelete(NEWSLETTER, $condition);
				echo 'success';
			} else {
				echo 'Something went wrong!!!';
			}
		}
	}
	
	public function bulk_status(){
		$ids = array_map(function($a){ return MongoID($a); }, $_POST['ids']);
		$this->change_bulk_status(NEWSLETTER, $_POST['status'], $ids);
	}
	
	public function bulk_delete(){
		$ids = array_map(function($a){ return MongoID($a); }, $_POST['ids']);
		$condition = ['_id'=>['$in'=>$ids]];
		$check = $this->admin_model->get_selected_fields(NEWSLETTER, $condition, ['_id']);
		if($check->num_rows()>0){
			$this->admin_model->commonDelete(NEWSLETTER, $condition);
			echo 'success';
		} else {
			echo 'Something went wrong!!!';
		}
	}
	
	public function sms_listing($id = ''){
		if ($this->checkLogin('A') == ''){
			$this->setErrorMessage('error','Login required!!!');
			redirect(ADMINURL);
		} else {
			$this->data['heading'] = "SMS Template";
			$this->load->view('admin/newsletter/sms_listing',$this->data);
		}
	}
	
	public function sms_listing_ajax(){
		if ($this->checkLogin('A') != ''){
			$columns = array( 
				0 =>'_id', 
				1 =>'dateAdded', 
				2 =>'name', 
				3 =>'dateModified',
				4 =>'status'
			);
			
			$search = $_REQUEST['search']['value'];
			$filters = [
				'name'=>$search,
				'category'=>$search
			];
			$condition = ['type'=>'sms'];
			
			$recordsTotal = $this->admin_model->get_all_counts(NEWSLETTER, $condition);
			$length = $_REQUEST['length'];
			$start = $_REQUEST['start'];
			$column = $columns[$_REQUEST['order'][0]['column']]; 
			$type = $_REQUEST['order'][0]['dir'];
			if(!empty($search)) {
				$recordsFiltered = $this->admin_model->get_all_counts(NEWSLETTER, $condition, $filters);
				$result = $this->admin_model->get_all_details(NEWSLETTER, $condition, [$column=>$type], $length, $start, $filters);
			} else {
				$recordsFiltered = $this->admin_model->get_all_counts(NEWSLETTER, $condition, []);
				$result = $this->admin_model->get_all_details(NEWSLETTER, $condition, [$column=>$type], $length, $start);
			}
			$dataArr = array(); $i = $_REQUEST['start']+1;
			foreach($result->result_array() as $res){
				$nestedData=array(); 
				$nestedData[] = '<input type="checkbox" class="ids" name="ids[]" value="'.(string)$res['_id'].'" />';
				$nestedData[] = $i;
				$nestedData[] = ($res['name']!='')?$res['name']:'-';
				$nestedData[] = ($res['dateModified']!='')?date('d/m/Y h:i A', $res['dateModified']):'-';
				
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
				$action = '<a class="action-a" title="View" href="'.base_url().'admin/sms-template-view/'.(string)$res['_id'].'"><i class="fa fa-eye"></i></a>';
				$action .= '&nbsp;<a class="action-a" title="Edit" href="'.base_url().'admin/sms-template-edit/'.(string)$res['_id'].'" title="Edit"><i class="fa fa-pencil"></i></a>';
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
	
	public function sms_add_edit($id = ''){
		if ($this->checkLogin('A') == ''){
			$this->setErrorMessage('error','Login required!!!');
			redirect(ADMINURL);
		} else {
			$form_mode = FALSE;
			$this->data['result'] = [];
			if($id!=''){
				$condition = ['_id' => MongoID((string)$id), 'type'=>'sms'];
				$result = $this->admin_model->get_all_details(NEWSLETTER, $condition);
				if($result->num_rows() > 0){
					$this->data['result'] = $result->result_array();
					$form_mode = TRUE;
				} else {
					$this->setErrorMessage('error','Something went wrong!!!');
					redirect(ADMINURL.'/sms-template-list');
				}
			}
			$this->data['form_mode'] = $form_mode;
			$this->data['heading'] = "Newsletter Details";
			$this->load->view('admin/newsletter/sms_add_edit',$this->data);
		}
	}
	
	public function sms_view($id = ''){
		if ($this->checkLogin('A') == ''){
			$this->setErrorMessage('error','Login required!!!');
			redirect(ADMINURL);
		} else {
			if($id!=''){
				$condition = ['_id' => MongoID((string)$id)];
				$result = $this->admin_model->get_all_details(NEWSLETTER, $condition);
				if($result->num_rows() > 0){
					$this->data['sport'] = $this->admin_model->get_selected_fields(SPORTS, ['_id'=>MongoID($result->row()->sport)], ['name'])->row()->name;
					$this->data['author'] = $this->admin_model->get_selected_fields(AUTHOR, ['_id'=>MongoID($result->row()->author)], ['name'])->row()->name;
					$this->data['result'] = $result->result_array();
					$this->data['heading'] = "Newsletter Details";
					$this->load->view('admin/newsletter/sms_view',$this->data);
				} else {
					$this->setErrorMessage('error','Something went wrong!!!');
					redirect(ADMINURL.'/email-template-list');
				}
			} else {
				$this->setErrorMessage('error','Something went wrong!!!');
				redirect(ADMINURL.'/email-template-list');
			}
		}
	}
	
	public function sms_save(){
		if ($this->checkLogin('A') == ''){
			$response['message'] = 'Something went Wrong!';
		} else {
			$objectid = (string)$this->input->post('objectid');
			$dataArr = ['type' => 'sms'];
			
			if($objectid!=''){
				$conditionArr = ['_id'=>MongoID((string)$objectid), 'type'=>'sms'];
				$adminCheck = $this->admin_model->get_all_counts(NEWSLETTER, $conditionArr);
				if ($adminCheck > 0){
					$dataArr = array_merge($dataArr, ['dateModified'=>(int)time()]);
					$this->admin_model->commonInsertUpdate(NEWSLETTER, 'update', ['_id', 'objectid'], $dataArr , $conditionArr);
					$this->save_tags($_POST['tags']);
					$this->setErrorMessage('success','SMS template saved!!!');
					redirect(ADMINURL.'/sms-template-list');
				} else {
					$this->setErrorMessage('error','Something went wrong!!!');
					redirect(ADMINURL.'/sms-template-list');
				}
			} else {
				$dataArr = array_merge($dataArr, ['status'=>'1', 'dateAdded' => (int)time(), 'dateModified'=>(int)time()]);
				$this->admin_model->commonInsertUpdate(NEWSLETTER,'insert',['_id', 'objectid'], $dataArr, []);
				$this->save_tags($_POST['tags']);
				$this->setErrorMessage('success','SMS template saved!!!');
				redirect(ADMINURL.'/sms-template-list');
			}
		}
	}
	
	public function sms_change_status(){
		if ($this->checkLogin('A') == ''){
			echo 'Something went wrong!!!';
		} else {
			$status = $this->input->post('status');
			$id = $this->input->post('id');
			$dataArr = ['status'=>$status];
			$condition = ['_id'=>MongoID($id)];
			$check = $this->admin_model->get_selected_fields(NEWSLETTER, $condition, ['cms_name']);
			
			if($check->num_rows()>0){
				$this->admin_model->update_details(NEWSLETTER, $dataArr, $condition);
				echo 'success';
			} else {
				echo 'Something went wrong!!!';
			}
		}
	}

	public function sms_remove(){
		if ($this->checkLogin('A') == ''){
			echo 'Something went wrong!!!';
		} else {
			$id = $this->input->post('id');
			$condition = ['_id'=>MongoID($id)];
			$check = $this->admin_model->get_all_counts(NEWSLETTER, $condition);
			if($check > 0){
				$this->admin_model->commonDelete(NEWSLETTER, $condition);
				echo 'success';
			} else {
				echo 'Something went wrong!!!';
			}
		}
	}
	
	public function sms_bulk_status(){
		$ids = array_map(function($a){ return MongoID($a); }, $_POST['ids']);
		$this->change_bulk_status(NEWSLETTER, $_POST['status'], $ids);
	}
	
	public function sms_bulk_delete(){
		$ids = array_map(function($a){ return MongoID($a); }, $_POST['ids']);
		$condition = ['_id'=>['$in'=>$ids]];
		$check = $this->admin_model->get_selected_fields(NEWSLETTER, $condition, ['_id']);
		if($check->num_rows()>0){
			$this->admin_model->commonDelete(NEWSLETTER, $condition);
			echo 'success';
		} else {
			echo 'Something went wrong!!!';
		}
	}
	
}
