<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Events extends MY_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('admin_model');
	}
		
	public function listing($id = ''){
		if ($this->checkLogin('A') == ''){
			$this->setErrorMessage('error','Login required!!!');
			redirect(ADMINURL);
		} else {
			$this->data['heading'] = "Events List";
			$this->data['sports'] = $this->admin_model->get_all_details(SPORTS, []);
			$this->data['categories'] = $this->admin_model->get_all_details(CATEGORY, []);
			$this->load->view('admin/events/listing',$this->data);
		}
	}
	
	public function listing_ajax(){
		if ($this->checkLogin('A') != ''){
			$columns = array( 
				0 =>'_id', 
				1 =>'', 
				2 =>'event_type', 
				3 =>'name', 
				4 =>'date', 
				5 =>'last_date',
				6 =>'featured',
				7 =>'status'
			);
			
			$search = $_REQUEST['search']['value'];
			$filters = [
				'event_type'=>$search,
				'name'=>$search
			];
			$condition = ['status'=>['$ne'=>'2'], 'deleted'=>'0'];
			if($_REQUEST['columns'][2]['search']['value']){
				$condition['category'] = $_REQUEST['columns'][2]['search']['value'];
			}
			if($_REQUEST['columns'][3]['search']['value']){
				$condition['sport'] = ['$in' => $_REQUEST['columns'][3]['search']['value']];
			}
			
			$recordsTotal = $this->admin_model->get_all_counts(EVENTS, $condition);
			$length = $_REQUEST['length'];
			$start = $_REQUEST['start'];
			$column = $columns[$_REQUEST['order'][0]['column']]; 
			$type = $_REQUEST['order'][0]['dir'];
			if(!empty($search)) {
				$recordsFiltered = $this->admin_model->get_all_counts(EVENTS, $condition, $filters);
				$result = $this->admin_model->get_all_details(EVENTS, $condition, [$column=>$type], $length, $start, $filters);
			} else {
				$recordsFiltered = $this->admin_model->get_all_counts(EVENTS, $condition, []);
				$result = $this->admin_model->get_all_details(EVENTS, $condition, [$column=>$type], $length, $start);
			}
			$dataArr = array(); $i = $_REQUEST['start']+1;
			foreach($result->result_array() as $res){
				$nestedData=array(); 
				$nestedData[] = '<input type="checkbox" class="ids" name="ids[]" value="'.(string)$res['_id'].'" />';
				$nestedData[] = $i;
				
				$nestedData[] = ($res['event_type']!='')?$res['event_type']:'-';				
				$nestedData[] = ($res['name']!='')?$res['name']:'-';				
				$nestedData[] = ($res['date']!='')?date('d/m/Y', $res['date']):'-';
				$nestedData[] = ($res['last_date']!='')?date('d/m/Y', $res['last_date']):'-';
				
				if($res['featured'] == '1'){
					$featured = '<a class="btn btn-xs btn-success" onclick="make_featured(\''.(string)$res['_id'].'\', \'0\')" title="Click to make featured">Yes</a>';
				} else { 
					$featured = '<a class="btn btn-xs btn-default" onclick="make_featured(\''.(string)$res['_id'].'\', \'1\')" title="Click to make not featured">No</a>';
				} 
				$nestedData[] = $featured;
				
				if($res['status'] == '1'){
					$status = '<a class="btn btn-xs btn-success" onclick="change_status(\''.(string)$res['_id'].'\', \'0\')" title="Click to make inactive">Approved</a>';
				} else { 
					$status = '<a class="btn btn-xs btn-danger" onclick="change_status(\''.(string)$res['_id'].'\', \'1\')" title="Click to make active">Unapproved</a>';
				} 
				if($res['status'] == '1' && $res['end_date']<time()){
					$status = '<a class="btn btn-xs btn-info" title="This event has been expired">Expired</a>';
				}
				
				$nestedData[] = $status;
				$action = '';
				if($res['status'] == '1' || $res['status'] == '2'){
					$action = '<a class="action-a" href="'.base_url().'admin/events-participants/'.(string)$res['_id'].'" title="Participants"><i class="fa fa-users"></i></a>&nbsp;';
				}
				$action .= '<a class="action-a" title="View" href="'.base_url().'admin/events-view/'.(string)$res['_id'].'"><i class="fa fa-eye"></i></a>';
				if($res['date']<time()){
					$action .= '&nbsp;<a class="action-a act_status" title="Remove" onclick="deleted_status(\''.(string)$res['_id'].'\')" data-rid="'.(string)$res['_id'].'"><i class="fa fa-trash"></i></a>';
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
			$this->data['sports'] = $this->admin_model->get_all_details(SPORTS, ['status'=>'1']);
			$this->data['categories'] = $this->admin_model->get_all_details(CATEGORY, ['status'=>'1']);
			if($id!=''){
				$condition = ['_id' => objectid((string)$id)];
				$result = $this->admin_model->get_all_details(EVENTS, $condition);
				if($result->num_rows() > 0){
					$this->data['result'] = $result->result_array();
					$form_mode = TRUE;
				} else {
					$this->setErrorMessage('error','Something went wrong!!!');
					redirect(ADMINURL.'/events-list');
				}
			}
			$this->data['form_mode'] = $form_mode;
			$this->data['heading'] = "Events Details";
			$this->load->view('admin/events/add_edit',$this->data);
		}
	}
	
	public function view($id = ''){
		if ($this->checkLogin('A') == ''){
			$this->setErrorMessage('error','Login required!!!');
			redirect(ADMINURL);
		} else {
			if($id!=''){
				$condition = ['_id' => objectid((string)$id)];
				$result = $this->admin_model->get_all_details(EVENTS, $condition);
				if($result->num_rows() > 0){
					$this->data['package'] = $this->admin_model->get_all_details(PACKAGE, ['_id'=>objectid($result->row()->package)], ['name']);
					$this->data['sport'] = $this->admin_model->get_selected_fields(SPORTS, ['_id'=>objectid($result->row()->sport)], ['name'])->row()->name;
					$this->data['result'] = $result->result_array();
					$this->data['heading'] = "Events Details";
					$this->load->view('admin/events/view',$this->data);
				} else {
					$this->setErrorMessage('error','Something went wrong!!!');
					redirect(ADMINURL.'/events-list');
				}
			} else {
				$this->setErrorMessage('error','Something went wrong!!!');
				redirect(ADMINURL.'/events-list');
			}
		}
	}
	
	public function save(){
		if ($this->checkLogin('A') == ''){
			$response['message'] = 'Something went Wrong!';
		} else {
			$objectid = (string)$this->input->post('objectid');
			$config['encrypt_name'] = TRUE;
			$config['overwrite'] = FALSE;
			$config['allowed_types'] = 'jpg|jpeg|gif|png';
			$config['max_size'] = 8000;
			$config['upload_path'] = './uploads/events';
			$this->load->library('upload', $config);
			
			if ( $this->upload->do_upload('cover_image')){
				$uploaded = $this->upload->data();
				$_POST['cover_image'] = $uploaded['file_name'];
			}
			$event_date = @explode('/', $_POST['event_date']);
			$event_date = strtotime($event_date[2].'-'.$event_date[1].'-'.$event_date[0]);
			if($objectid!=''){
				$conditionArr = ['_id'=>objectid((string)$objectid)];
				$adminCheck = $this->admin_model->get_all_counts(EVENTS, $conditionArr);
				if ($adminCheck > 0){
					$this->admin_model->commonInsertUpdate(EVENTS, 'update', ['_id', 'objectid', 'event_date'], ['dateModified'=>(int)time(), 'event_date'=>(int)$event_date], $conditionArr);
					$this->setErrorMessage('success','Event details saved!!!');
					redirect(ADMINURL.'/events-list');
				} else {
					$this->setErrorMessage('error','Something went wrong!!!');
					redirect(ADMINURL.'/events-list');
				}
			} else {
				$this->admin_model->commonInsertUpdate(EVENTS,'insert',['_id', 'objectid', 'event_date'],['featured'=>'0', 'status'=>'1', 'dateAdded'=>MongoDATE(time()), 'event_date'=>(int)$event_date],[]);
				$this->setErrorMessage('success','Event details saved!!!');
				redirect(ADMINURL.'/events-list');
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
			$check = $this->admin_model->get_selected_fields(EVENTS, $condition, ['cms_name']);
			
			if($check->num_rows()>0){
				$this->admin_model->update_details(EVENTS, $dataArr, $condition);
				echo 'success';
			} else {
				echo 'Something went wrong!!!';
			}
		}
	}
	
	public function make_featured(){
		if ($this->checkLogin('A') == ''){
			echo 'Something went wrong!!!';
		} else {
			$status = $this->input->post('status');
			$id = $this->input->post('id');
			$dataArr = ['featured'=>$status];
			$condition = ['_id'=>objectid($id)];
			$check = $this->admin_model->get_selected_fields(EVENTS, $condition, ['cms_name']);
			
			if($check->num_rows()>0){
				$this->admin_model->update_details(EVENTS, $dataArr, $condition);
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
			$check = $this->admin_model->get_all_counts(EVENTS, $condition);
			if($check > 0){
				$this->admin_model->commonDelete(EVENTS, $condition);
				echo 'success';
			} else {
				echo 'Something went wrong!!!';
			}
		}
	}

	public function deleted_status(){
		if ($this->checkLogin('A') == ''){
			echo 'Something went wrong!!!';
		} else {
			$id = $this->input->post('id');
			$condition = ['_id'=>objectid($id)];
			$check = $this->admin_model->get_all_counts(EVENTS, $condition);
			if($check > 0){
				$this->admin_model->update_details(EVENTS, ['deleted'=>'1'], $condition);
				echo 'success';
			} else {
				echo 'Something went wrong!!!';
			}
		}
	}
	
	public function bulk_status(){
		$ids = array_map(function($a){ return objectid($a); }, $_POST['ids']);
		$this->change_bulk_status(EVENTS, $_POST['status'], $ids);
	}
	
	public function bulk_delete(){
		$ids = array_map(function($a){ return objectid($a); }, $_POST['ids']);
		$condition = ['_id'=>['$in'=>$ids]];
		$check = $this->admin_model->get_selected_fields(EVENTS, $condition, ['_id']);
		if($check->num_rows()>0){
			$this->admin_model->update_details(EVENTS, ['deleted'=>'1'], $condition);
			// $this->admin_model->commonDelete(EVENTS, $condition);
			echo 'success';
		} else {
			echo 'Something went wrong!!!';
		}
	}
	
	
	public function participants($id = ''){
		if ($this->checkLogin('A') == ''){
			$this->setErrorMessage('error','Login required!!!');
			redirect(ADMINURL);
		} else {
			$event = $this->admin_model->get_selected_fields(EVENTS, ['_id' => objectid($id)]);
			$this->data['heading'] = "Event Participants List - ".$event->row()->name;
			$this->data['sports'] = $this->admin_model->get_all_details(SPORTS, []);
			$this->data['categories'] = $this->admin_model->get_all_details(CATEGORY, []);
			$this->load->view('admin/events/participants_listing',$this->data);
		}
	}
	
	public function participants_ajax($id=''){
		if ($this->checkLogin('A') != ''){
			$columns = array( 
				0 =>'_id', 
				1 =>'', 
				2 =>'name', 
				3 =>'email', 
				4 =>'mobile', 
				5 =>'team_name',
				6 =>'dateAdded',
				7 =>'status'
			);
			
			$search = $_REQUEST['search']['value'];
			$filters = [
				'name'=>$search,
				'team_name'=>$search,
				'description'=>$search
			];
			$condition = ['event' => $id];
			if($_REQUEST['columns'][2]['search']['value']){
				$condition['category'] = $_REQUEST['columns'][2]['search']['value'];
			}
			if($_REQUEST['columns'][3]['search']['value']){
				$condition['sport'] = ['$in' => $_REQUEST['columns'][3]['search']['value']];
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
				$nestedData[] = ($res['dateAdded']!='')?date('d/m/Y h:i A', $res['dateAdded']):'-';
				
				if($res['status'] == '1'){
					$status = 'Approved';
				} else { 
					$status = 'Unapproved';
				} 
				
				$nestedData[] = $status;
								
				$action = '<a class="action-a" title="View" href="'.base_url().'admin/events-participants-view/'.(string)$res['_id'].'"><i class="fa fa-eye"></i></a>';
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
	
	public function participants_view($id = ''){
		if ($this->checkLogin('A') == ''){
			$this->setErrorMessage('error','Login required!!!');
			redirect(ADMINURL);
		} else {
			if($id!=''){
				$condition = ['_id' => objectid((string)$id)];
				$result = $this->admin_model->get_all_details(PARTICIPANTS, $condition);
				if($result->num_rows() > 0){
					$this->data['result'] = $result->result_array();
					$this->data['heading'] = "Participant Details";
					$this->load->view('admin/events/participants_view',$this->data);
				} else {
					$this->setErrorMessage('error','Something went wrong!!!');
					redirect(ADMINURL.'/events-list');
				}
			} else {
				$this->setErrorMessage('error','Something went wrong!!!');
				redirect(ADMINURL.'/events-list');
			}
		}
	}
	
}
