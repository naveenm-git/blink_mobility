<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Downloads extends MY_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('admin_model');
	}
		
	public function listing($id = ''){
		if ($this->checkLogin('A') == ''){
			$this->setErrorMessage('error','Login required!!!');
			redirect(ADMINURL);
		} else {
			$this->data['heading'] = "Download List";
			$this->load->view('admin/downloads/listing',$this->data);
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
			
			$recordsTotal = $this->admin_model->get_all_counts(DOWNLOADS, $condition);
			$length = $_REQUEST['length'];
			$start = $_REQUEST['start'];
			$column = $columns[$_REQUEST['order'][0]['column']]; 
			$type = $_REQUEST['order'][0]['dir'];
			if(!empty($search)) {
				$recordsFiltered = $this->admin_model->get_all_counts(DOWNLOADS, $condition, $filters);
				$result = $this->admin_model->get_all_details(DOWNLOADS, $condition, [$column=>$type], $length, $start, $filters);
			} else {
				$recordsFiltered = $this->admin_model->get_all_counts(DOWNLOADS, $condition, []);
				$result = $this->admin_model->get_all_details(DOWNLOADS, $condition, [$column=>$type], $length, $start);
			}
			$dataArr = array(); $i = $_REQUEST['start']+1;
			foreach($result->result_array() as $res){
				$nestedData=array(); 
				$nestedData[] = '<input type="checkbox" class="ids" name="ids[]" value="'.(string)$res['_id'].'" />';
				$nestedData[] = $i;
				$nestedData[] = ($res['name']!='')?$res['name']:'-';
				
				$nestedData[] = (strlen($res['description'])>40)?substr($res['description'],0,40).'...':$res['description'];
				
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
				$action = '<a class="action-a" title="View" href="'.base_url().'admin/downloads-view/'.(string)$res['_id'].'"><i class="fa fa-eye"></i></a>';
				$action .= '&nbsp;<a class="action-a" title="Edit" href="'.base_url().'admin/downloads-edit/'.(string)$res['_id'].'" title="Edit"><i class="fa fa-pencil"></i></a>';
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
				$result = $this->admin_model->get_all_details(DOWNLOADS, $condition);
				if($result->num_rows() > 0){
					$this->data['result'] = $result->result_array();
					$form_mode = TRUE;
				} else {
					$this->setErrorMessage('error','Something went wrong!!!');
					redirect(ADMINURL.'/downloads-list');
				}
			}
			$this->data['form_mode'] = $form_mode;
			$this->data['heading'] = "Download Details";
			$this->load->view('admin/downloads/add_edit',$this->data);
		}
	}
	
	public function view($id = ''){
		if ($this->checkLogin('A') == ''){
			$this->setErrorMessage('error','Login required!!!');
			redirect(ADMINURL);
		} else {
			if($id!=''){
				$condition = ['_id' => objectid((string)$id)];
				$result = $this->admin_model->get_all_details(DOWNLOADS, $condition);
				if($result->num_rows() > 0){
					$this->data['result'] = $result->result_array();
					$this->data['heading'] = "Download Details";
					$this->load->view('admin/downloads/view',$this->data);
				} else {
					$this->setErrorMessage('error','Something went wrong!!!');
					redirect(ADMINURL.'/downloads-list');
				}
			} else {
				$this->setErrorMessage('error','Something went wrong!!!');
				redirect(ADMINURL.'/downloads-list');
			}
		}
	}
	
	public function save(){
		if ($this->checkLogin('A') == ''){
			$response['message'] = 'Something went Wrong!';
		} else {
			$objectid = (string)$this->input->post('objectid');
			$config['encrypt_name'] = FALSE;
			$config['overwrite'] = FALSE;
			$config['allowed_types'] = 'jpg|jpeg|gif|png';
			$config['max_size'] = 8000;
			$config['upload_path'] = './uploads/downloads';
			$this->load->library('upload', $config);
			
			if ( $this->upload->do_upload('attachment')){
				$uploaded = $this->upload->data();
				$_POST['attachment'] = $uploaded['file_name'];
			}
			$status = $this->input->post('status');
			$status = ($status!='')?'1':'0'; 
			if($objectid!=''){
				$conditionArr = ['_id'=>objectid((string)$objectid)];
				$result = $this->admin_model->get_all_details(DOWNLOADS, $conditionArr);
				if ($result->num_rows() > 0){
					$remove = $this->input->post('remove');
					$remove = (!empty($remove)) ? $_POST['remove'] : [];
					$photos = (isset($result->row()->photos))?$result->row()->photos:[];
					
					if(count($remove) > 0 && count($photos) > 0){
						$photo = [];
						foreach($photos as $img){
							if(!in_array($img, $remove)) $photo[] = $img;
							else unlink('uploads/downloads/'.$img);
						}
						$photos = $photo;
					}
					
					if(count($_FILES['photos']) > 0){
						$filesArr = $this->file_upload_multiple_local($_FILES['photos'], 'uploads/downloads/');
						if(count($filesArr)>0){
							$photos = array_merge($photos, $filesArr);
						}
					}
					$_POST['photos'] = $photos;
					$this->admin_model->commonInsertUpdate(DOWNLOADS, 'update', ['_id', 'objectid', 'status'], ['dateModified'=>(int)time(), 'status'=>$status], $conditionArr);
					$this->setErrorMessage('success','Download details saved!!!');
					redirect(ADMINURL.'/downloads-list');
				} else {
					$this->setErrorMessage('error','Something went wrong!!!');
					redirect(ADMINURL.'/downloads-list');
				}
			} else {
				$_POST['photos'] = $this->file_upload_multiple_local($_FILES['photos'], 'uploads/downloads/');
				$this->admin_model->commonInsertUpdate(DOWNLOADS,'insert',['_id', 'objectid', 'status'],['dateAdded'=>MongoDATE(time()), 'status'=>$status],[]);
				$this->setErrorMessage('success','Download details saved!!!');
				redirect(ADMINURL.'/downloads-list');
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
			$check = $this->admin_model->get_selected_fields(DOWNLOADS, $condition, ['cms_name']);
			
			if($check->num_rows()>0){
				$this->admin_model->update_details(DOWNLOADS, $dataArr, $condition);
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
			$check = $this->admin_model->get_all_counts(DOWNLOADS, $condition);
			if($check > 0){
				$this->admin_model->commonDelete(DOWNLOADS, $condition);
				echo 'success';
			} else {
				echo 'Something went wrong!!!';
			}
		}
	}
	
	public function bulk_status(){
		$ids = array_map(function($a){ return objectid($a); }, $_POST['ids']);
		$this->change_bulk_status(DOWNLOADS, $_POST['status'], $ids);
	}
	
	public function bulk_delete(){
		$ids = array_map(function($a){ return objectid($a); }, $_POST['ids']);
		$condition = ['_id'=>['$in'=>$ids]];
		$check = $this->admin_model->get_selected_fields(DOWNLOADS, $condition, ['_id']);
		if($check->num_rows()>0){
			$this->admin_model->commonDelete(DOWNLOADS, $condition);
			echo 'success';
		} else {
			echo 'Something went wrong!!!';
		}
	}
	
}
