<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cms extends MY_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('admin_model');
	}
		
	public function listing($id = ''){
		if ($this->checkLogin('A') == ''){
			$this->setErrorMessage('error','Login required!!!');
			redirect(ADMINURL);
		} else {
			$this->data['heading'] = "Static Pages";
			$this->load->view('admin/cms/listing',$this->data);
		}
	}
	
	public function listing_ajax(){
		if ($this->checkLogin('A') != ''){
			$columns = array( 
				0 =>'_id', 
				1 =>'name', 
				2 =>'seourl',
				3 =>'status'
			);
			
			$search = $_REQUEST['search']['value'];
			$filters = [
				'name'=>$search,
				'seourl'=>$search,
				'content'=>$search,
				'meta_title'=>$search,
				'meta_keyword'=>$search,
				'meta_description'=>$search				
			];
			$condition = ['type'=>['$ne'=>'terms_conditions']];
			if($_REQUEST['columns'][3]['search']['value']){
				$condition['status'] = $_REQUEST['columns'][3]['search']['value'];
			}
			
			$recordsTotal = $this->admin_model->get_all_counts(CMS, $condition);
			$length = $_REQUEST['length'];
			$start = $_REQUEST['start'];
			$column = $columns[$_REQUEST['order'][0]['column']]; 
			$type = $_REQUEST['order'][0]['dir'];
			if(!empty($search)) {
				$recordsFiltered = $this->admin_model->get_all_counts(CMS, $condition, $filters);
				$result = $this->admin_model->get_all_details(CMS, $condition, [$column=>$type], $length, $start, $filters);
			} else {
				$recordsFiltered = $this->admin_model->get_all_counts(CMS, $condition, []);
				$result = $this->admin_model->get_all_details(CMS, $condition, [$column=>$type], $length, $start);
			}
			$dataArr = array(); $i = $_REQUEST['start']+1;
			foreach($result->result_array() as $res){
				$nestedData=array(); 
				$nestedData[] = $i;
				$nestedData[] = ($res['name']!='')?$res['name']:'-';
				$nestedData[] = ($res['seourl']!='')?base_url().$res['seourl']:'-';
				
				if($this->data['user_type']=='superadmin'){
					if($res['status'] == '1'){
						$status = '<a class="btn btn-xs btn-success cms_status" onclick="status_change(\''.(string)$res['_id'].'\', \'0\')" title="Click to make inactive">Active</a>';
					} else { 
						$status = '<a class="btn btn-xs btn-default cms_status" onclick="status_change(\''.(string)$res['_id'].'\', \'1\')" title="Click to make active">In Active</a>';
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
				$action = '<a class="action-a" title="View" href="'.base_url().'admin/cms-view/'.(string)$res['_id'].'"><i class="fa fa-eye"></i></a>';
				$action .= '&nbsp;<a class="action-a" title="Edit" href="'.base_url().'admin/cms-edit/'.(string)$res['_id'].'" title="Edit"><i class="fa fa-pencil"></i></a>';
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
				$condition = ['_id' => objectid((string)$id)];
				$result = $this->admin_model->get_all_details(CMS, $condition);
				if($result->num_rows() > 0){
					$this->data['cms'] = $result->result_array();
					$this->data['heading'] = "Static Page Details";
					$this->load->view('admin/cms/view',$this->data);
				} else {
					$this->setErrorMessage('error','Something went wrong!!!');
					redirect(ADMINURL.'/cms-list');
				}
			} else {
				$this->setErrorMessage('error','Something went wrong!!!');
				redirect(ADMINURL.'/cms-list');
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
				$condition = ['_id' => objectid((string)$id)];
				$result = $this->admin_model->get_all_details(CMS, $condition);
				if($result->num_rows() > 0){
					$this->data['result'] = $result->result_array();
					$form_mode = TRUE;
				} else {
					$this->setErrorMessage('error','Something went wrong!!!');
					redirect(ADMINURL.'/cms-list');
				}
			}
			$this->data['form_mode'] = $form_mode;
			$this->data['heading'] = "Static Page Details";
			$this->load->view('admin/cms/add_edit',$this->data);
		}
	}
	
	public function save(){
		if ($this->checkLogin('A') == ''){
			$this->setErrorMessage('error','Login required!!!');
			redirect(ADMINURL);
		} else {
			$objectid = (string)$this->input->post('objectid');
			$config['encrypt_name'] = TRUE;
			$config['overwrite'] = FALSE;
			$config['allowed_types'] = 'jpg|jpeg|gif|png';
			$config['max_size'] = 8000;
			$config['upload_path'] = './uploads/cms';
			$this->load->library('upload', $config);
			
			if ( $this->upload->do_upload('cms_banner')){
				$logoDetails = $this->upload->data();
				$_POST['cms_banner'] = $logoDetails['file_name'];
			}
				
			$name = trim($this->input->post('name'));
			$_POST['seourl'] = url_title($name, '-', TRUE);
			if($objectid!=''){
				if(!isset($_POST['cms_banner'])){
					$remove = $this->input->post('remove');
					if($remove!='') $_POST['cms_banner'] = '';
				}
				
				$conditionArr = ['_id'=>objectid((string)$objectid)];
				$adminCheck = $this->admin_model->get_all_counts(CMS, $conditionArr);
				if ($adminCheck > 0){
					$this->admin_model->commonInsertUpdate(CMS, 'update', ['_id', 'objectid'], ['dateAdded'=>MongoDATE(time())], $conditionArr);
					$this->setErrorMessage('success','Static page details saved!!!');
					redirect(ADMINURL.'/cms-list');
				} else {
					$this->setErrorMessage('error','Something went wrong!!!');
					redirect(ADMINURL.'/cms-list');
				}
			} else {
				$this->admin_model->commonInsertUpdate(CMS,'insert',['_id', 'objectid'],['status'=>'1', 'dateAdded' => time()],[]);
				$this->setErrorMessage('success','Static page details saved!!!');
				redirect(ADMINURL.'/cms-list');
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
			$check = $this->admin_model->get_selected_fields(CMS, $condition, ['cms_name']);
			
			if($check->num_rows()>0){
				$this->admin_model->update_details(CMS, $dataArr, $condition);
				echo 'success';
			} else {
				echo 'Something went wrong!!!';
			}
		}
	}

	public function remove_cms(){
		if ($this->checkLogin('A') == ''){
			echo 'Something went wrong!!!';
		} else {
			$id = $this->input->post('id');
			$condition = ['_id'=>objectid($id)];
			$check = $this->admin_model->get_all_counts(CMS, $condition);
			if($check > 0){
				$this->admin_model->commonDelete(CMS, $condition);
				echo 'success';
			} else {
				echo 'Something went wrong!!!';
			}
		}
	}
	
}
