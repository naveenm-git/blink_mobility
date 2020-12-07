<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Author extends MY_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('admin_model');
	}
		
	public function listing($id = ''){
		if ($this->checkLogin('A') == ''){
			$this->setErrorMessage('error','Login required!!!');
			redirect(ADMINURL);
		} else {
			$this->data['heading'] = "Author List";
			$this->load->view('admin/author/listing',$this->data);
		}
	}
	
	public function listing_ajax(){
		if ($this->checkLogin('A') != ''){
			$columns = array( 
				0 =>'_id', 
				1 =>'', 
				2 =>'', 
				3 =>'name', 
				4 =>'description',
				// 5 =>'status'
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
			
			$recordsTotal = $this->admin_model->get_all_counts(AUTHOR, $condition);
			$length = $_REQUEST['length'];
			$start = $_REQUEST['start'];
			$column = $columns[$_REQUEST['order'][0]['column']]; 
			$type = $_REQUEST['order'][0]['dir'];
			if(!empty($search)) {
				$recordsFiltered = $this->admin_model->get_all_counts(AUTHOR, $condition, $filters);
				$result = $this->admin_model->get_all_details(AUTHOR, $condition, [$column=>$type], $length, $start, $filters);
			} else {
				$recordsFiltered = $this->admin_model->get_all_counts(AUTHOR, $condition, []);
				$result = $this->admin_model->get_all_details(AUTHOR, $condition, [$column=>$type], $length, $start);
			}
			$dataArr = array(); $i = $_REQUEST['start']+1;
			foreach($result->result_array() as $res){
				$nestedData=array(); 
				$nestedData[] = '<input type="checkbox" class="ids" name="ids[]" value="'.(string)$res['_id'].'" />';
				$nestedData[] = $i;
				$image = '<img src="'.base_url().'assets/images/user.png" class="tbl-img" />';
				if($res['image']!=''){
					if(in_array(@explode('.', $res['image'])[1], ['zip', 'rar'])) $image = '<a target="_blank" href="'.base_url().'uploads/author/'.$res['image'].'" class="tbl-img" style="color: #555; font-size: 60px;padding: 3px 12px"><i class="fa fa-file-zip-o"></i></a>';
					else $image = '<img src="'.base_url().'uploads/author/'.$res['image'].'" class="tbl-img" />';
				}
				$nestedData[] = $image;
				$nestedData[] = ($res['name']!='')?$res['name']:'-';
				$description = '-';
				if($res['description']!=''){
					$description = (strlen($res['description'])>60)?substr($res['description'],0,60).'...':$res['description'];
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
				// $nestedData[] = $status;
				$action = '<a class="action-a" title="View" href="'.base_url().'admin/author-view/'.(string)$res['_id'].'"><i class="fa fa-eye"></i></a>';
				$action .= '&nbsp;<a class="action-a" title="Edit" href="'.base_url().'admin/author-edit/'.(string)$res['_id'].'" title="Edit"><i class="fa fa-pencil"></i></a>';
				// $action .= '&nbsp;<a class="action-a act_status" title="Remove" onclick="delete_record(\''.(string)$res['_id'].'\')" data-rid="'.(string)$res['_id'].'"><i class="fa fa-trash"></i></a>';
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
				$result = $this->admin_model->get_all_details(AUTHOR, $condition);
				if($result->num_rows() > 0){
					$this->data['result'] = $result->result_array();
					$form_mode = TRUE;
				} else {
					$this->setErrorMessage('error','Something went wrong!!!');
					redirect(ADMINURL.'/author-list');
				}
			}
			$this->data['form_mode'] = $form_mode;
			$this->data['heading'] = "Author Details";
			$this->load->view('admin/author/add_edit',$this->data);
		}
	}
	
	public function view($id = ''){
		if ($this->checkLogin('A') == ''){
			$this->setErrorMessage('error','Login required!!!');
			redirect(ADMINURL);
		} else {
			if($id!=''){
				$condition = ['_id' => objectid((string)$id)];
				$result = $this->admin_model->get_all_details(AUTHOR, $condition);
				if($result->num_rows() > 0){
					$this->data['result'] = $result->result_array();
					$this->data['heading'] = "Author Details";
					$this->load->view('admin/author/view',$this->data);
				} else {
					$this->setErrorMessage('error','Something went wrong!!!');
					redirect(ADMINURL.'/author-list');
				}
			} else {
				$this->setErrorMessage('error','Something went wrong!!!');
				redirect(ADMINURL.'/author-list');
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
			$config['upload_path'] = './uploads/author';
			$this->load->library('upload', $config);
			
			if ( $this->upload->do_upload('image')){
				$uploaded = $this->upload->data();
				$_POST['image'] = $uploaded['file_name'];
			}
			if($objectid!=''){
				$conditionArr = ['_id'=>objectid((string)$objectid)];
				$adminCheck = $this->admin_model->get_all_counts(AUTHOR, $conditionArr);
				if ($adminCheck > 0){
					$this->admin_model->commonInsertUpdate(AUTHOR, 'update', ['_id', 'objectid'], ['dateModified'=>(int)time()], $conditionArr);
					$this->setErrorMessage('success','Author details saved!!!');
					redirect(ADMINURL.'/author-list');
				} else {
					$this->setErrorMessage('error','Something went wrong!!!');
					redirect(ADMINURL.'/author-list');
				}
			} else {
				$code = (string)$this->get_rand_number(5);
				$condition = array('code' => $code);
				$res = $this->admin_model->get_all_counts(AUTHOR, $condition);
				if ($res > 0) {
					$check = 0;
					$code = (string)$this->get_rand_number(5);
					while ($check == 0) {
						$condition = array('code' => $code);
						$res = $this->admin_model->get_all_counts(AUTHOR, $condition);
						if ($res > 0) {
							$code = $this->get_rand_number(5);
						} else {
							$check = 1;
						}
					}
				}
				$_POST['code'] = $code;
				
				$this->admin_model->commonInsertUpdate(AUTHOR,'insert',['_id', 'objectid'],['status'=>'1', 'dateAdded'=>MongoDATE(time())],[]);
				$this->setErrorMessage('success','Author details saved!!!');
				redirect(ADMINURL.'/author-list');
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
			$check = $this->admin_model->get_selected_fields(AUTHOR, $condition, ['cms_name']);
			
			if($check->num_rows()>0){
				$this->admin_model->update_details(AUTHOR, $dataArr, $condition);
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
			$check = $this->admin_model->get_all_counts(AUTHOR, $condition);
			if($check > 0){
				$this->admin_model->commonDelete(AUTHOR, $condition);
				echo 'success';
			} else {
				echo 'Something went wrong!!!';
			}
		}
	}
	
	public function bulk_status(){
		$ids = array_map(function($a){ return objectid($a); }, $_POST['ids']);
		$this->change_bulk_status(AUTHOR, $_POST['status'], $ids);
	}
	
	public function bulk_delete(){
		$ids = array_map(function($a){ return objectid($a); }, $_POST['ids']);
		$condition = ['_id'=>['$in'=>$ids]];
		$check = $this->admin_model->get_selected_fields(AUTHOR, $condition, ['_id']);
		if($check->num_rows()>0){
			$this->admin_model->commonDelete(AUTHOR, $condition);
			echo 'success';
		} else {
			echo 'Something went wrong!!!';
		}
	}
	
}
