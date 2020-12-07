<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Polling extends MY_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('admin_model');
	}
		
	public function listing($id = ''){
		if ($this->checkLogin('A') == ''){
			$this->setErrorMessage('error','Login required!!!');
			redirect(ADMINURL);
		} else {
			$this->data['heading'] = "Polling List";
			$this->load->view('admin/polling/listing',$this->data);
		}
	}
	
	public function listing_ajax(){
		if ($this->checkLogin('A') != ''){
			$columns = array( 
				0 =>'_id', 
				1 =>'', 
				2 =>'', 
				3 =>'question',
				4 =>'status'
			);
			
			$search = $_REQUEST['search']['value'];
			$filters = [
				'question'=>$search
			];
			$condition = [];
			if($_REQUEST['columns'][4]['search']['value']){
				$condition['status'] = $_REQUEST['columns'][4]['search']['value'];
			}
			
			$recordsTotal = $this->admin_model->get_all_counts(POLLING, $condition);
			$length = $_REQUEST['length'];
			$start = $_REQUEST['start'];
			$column = $columns[$_REQUEST['order'][0]['column']]; 
			$type = $_REQUEST['order'][0]['dir'];
			if(!empty($search)) {
				$recordsFiltered = $this->admin_model->get_all_counts(POLLING, $condition, $filters);
				$result = $this->admin_model->get_all_details(POLLING, $condition, [$column=>$type], $length, $start, $filters);
			} else {
				$recordsFiltered = $this->admin_model->get_all_counts(POLLING, $condition, []);
				$result = $this->admin_model->get_all_details(POLLING, $condition, [$column=>$type], $length, $start);
			}
			$dataArr = array(); $i = $_REQUEST['start']+1;
			foreach($result->result_array() as $res){
				$nestedData=array(); 
				$nestedData[] = '<input type="checkbox" class="ids" name="ids[]" value="'.(string)$res['_id'].'" />';
				$nestedData[] = $i;
				$image = '<img src="'.base_url().'assets/images/user.png" class="tbl-img" />';
				if($res['image']!=''){
					if(in_array(@explode('.', $res['image'])[1], ['zip', 'rar'])) $image = '<a target="_blank" href="'.base_url().'uploads/polling/'.$res['image'].'" class="tbl-img" style="color: #555; font-size: 60px;padding: 3px 12px"><i class="fa fa-file-zip-o"></i></a>';
					else $image = '<img src="'.base_url().'uploads/polling/'.$res['image'].'" class="tbl-img" />';
				}
				$nestedData[] = $image;
				$nestedData[] = (strlen($res['question'])>50)?substr($res['question'],0,50).'...':$res['question'];
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
				$action = '<a class="action-a" title="View" href="'.base_url().'admin/polling-view/'.(string)$res['_id'].'"><i class="fa fa-eye"></i></a>';
				$action .= '&nbsp;<a class="action-a" title="Edit" href="'.base_url().'admin/polling-edit/'.(string)$res['_id'].'" title="Edit"><i class="fa fa-pencil"></i></a>';
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
				$result = $this->admin_model->get_all_details(POLLING, $condition);
				if($result->num_rows() > 0){
					$this->data['result'] = $result->result_array();
					$form_mode = TRUE;
				} else {
					$this->setErrorMessage('error','Something went wrong!!!');
					redirect(ADMINURL.'/polling-list');
				}
			}
			$this->data['form_mode'] = $form_mode;
			$this->data['heading'] = "Polling Details";
			$this->load->view('admin/polling/add_edit',$this->data);
		}
	}
	
	public function view($id = ''){
		if ($this->checkLogin('A') == ''){
			$this->setErrorMessage('error','Login required!!!');
			redirect(ADMINURL);
		} else {
			if($id!=''){
				$condition = ['_id' => objectid((string)$id)];
				$result = $this->admin_model->get_all_details(POLLING, $condition);
				if($result->num_rows() > 0){
					$this->data['result'] = $result->result_array();
					$this->data['heading'] = "Polling Details";
					$this->load->view('admin/polling/view',$this->data);
				} else {
					$this->setErrorMessage('error','Something went wrong!!!');
					redirect(ADMINURL.'/polling-list');
				}
			} else {
				$this->setErrorMessage('error','Something went wrong!!!');
				redirect(ADMINURL.'/polling-list');
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
			$config['upload_path'] = './uploads/polling';
			$this->load->library('upload', $config);
			
			if ( $this->upload->do_upload('image')){
				$uploaded = $this->upload->data();
				$_POST['image'] = $uploaded['file_name'];
			}
			$postdata = $this->input->post();
			if($objectid!=''){
				$conditionArr = ['_id'=>objectid((string)$objectid)];
				$adminCheck = $this->admin_model->get_all_details(POLLING, $conditionArr);
				if ($adminCheck->num_rows() > 0){
					$dataArr['dateModified'] = (int)time();
					for($i=0;$i<count($_POST['answers']);$i++){
						$votes = $adminCheck->row()->answers['answer-'.($i+1)]['votes'];
						$dataArr['answers']['answer-'.($i+1)] = ['name'=>$postdata['answers'][$i], 'votes'=>(int)$votes];
					}
					
					$this->admin_model->commonInsertUpdate(POLLING, 'update', ['_id', 'objectid'], $dataArr, $conditionArr);
					$this->setErrorMessage('success','Polling details saved!!!');
					redirect(ADMINURL.'/polling-list');
				} else {
					$this->setErrorMessage('error','Something went wrong!!!');
					redirect(ADMINURL.'/polling-list');
				}
			} else {
				$check = $this->admin_model->get_all_counts(POLLING, ['status'=>'1']);
				$status = ($check > 0)?'0':'1';
				for($i=0;$i<count($postdata['answers']);$i++){
					$dataArr['answers']['answer-'.($i+1)] = ['name'=>$postdata['answers'][$i], 'votes'=>0];
				}
				$dataArr['status'] = $status; 
				$dataArr['dateAdded'] = (int)time();
				
				$this->admin_model->commonInsertUpdate(POLLING,'insert',['_id', 'objectid'], $dataArr,[]);
				$this->setErrorMessage('success','Polling details saved!!!');
				redirect(ADMINURL.'/polling-list');
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
			$condition = ['_id'=>['$ne' => objectid($id)], 'status'=>'1'];
			$check1 = $this->admin_model->get_selected_fields(POLLING, $condition, ['cms_name']);
			if($check1->num_rows()==0){
				$condition = ['_id'=>objectid($id)];
				$check2 = $this->admin_model->get_selected_fields(POLLING, $condition, ['cms_name']);
				if($check2->num_rows()>0){
					$this->admin_model->update_details(POLLING, $dataArr, $condition);
					echo 'success';
				} else {
					echo 'Something went wrong!!!';
				}
			} else {
				echo 'Sorry, currently another polling is active!!!';
			}
		}
	}

	public function remove(){
		if ($this->checkLogin('A') == ''){
			echo 'Something went wrong!!!';
		} else {
			$id = $this->input->post('id');
			$condition = ['_id'=>objectid($id)];
			$check = $this->admin_model->get_all_counts(POLLING, $condition);
			if($check > 0){
				$this->admin_model->commonDelete(POLLING, $condition);
				echo 'success';
			} else {
				echo 'Something went wrong!!!';
			}
		}
	}
	
	public function bulk_status(){
		$ids = array_map(function($a){ return objectid($a); }, $_POST['ids']);
		$this->change_bulk_status(POLLING, $_POST['status'], $ids);
	}
	
	public function bulk_delete(){
		$ids = array_map(function($a){ return objectid($a); }, $_POST['ids']);
		$condition = ['_id'=>['$in'=>$ids]];
		$check = $this->admin_model->get_selected_fields(POLLING, $condition, ['_id']);
		if($check->num_rows()>0){
			$this->admin_model->commonDelete(POLLING, $condition);
			echo 'success';
		} else {
			echo 'Something went wrong!!!';
		}
	}
	
}
