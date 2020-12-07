<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Awards extends MY_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('admin_model');
	}
		
	public function listing($id = ''){
		if ($this->checkLogin('A') == ''){
			$this->setErrorMessage('error','Login required!!!');
			redirect(ADMINURL);
		} else {
			$this->data['heading'] = "Fisto Awards List";
			$this->data['sports'] = $this->admin_model->get_all_details(SPORTS, []);
			$this->load->view('admin/awards/listing',$this->data);
		}
	}
	
	public function listing_ajax(){
		if ($this->checkLogin('A') != ''){
			$columns = array( 
				0 =>'_id', 
				1 =>'', 
				2 =>'sport', 
				3 =>'category', 
				4 =>'status'
			);
			
			$search = $_REQUEST['search']['value'];
			$filters = [
				'category'=>$search
			];
			$condition = [];
			if($_REQUEST['columns'][2]['search']['value']){
				$condition['sport'] = $_REQUEST['columns'][2]['search']['value'];
			}
			
			$recordsTotal = $this->admin_model->get_all_counts(AWARDS, $condition);
			$length = $_REQUEST['length'];
			$start = $_REQUEST['start'];
			$column = $columns[$_REQUEST['order'][0]['column']]; 
			$type = $_REQUEST['order'][0]['dir'];
			if(!empty($search)) {
				$recordsFiltered = $this->admin_model->get_all_counts(AWARDS, $condition, $filters);
				$result = $this->admin_model->get_all_details(AWARDS, $condition, [$column=>$type], $length, $start, $filters);
			} else {
				$recordsFiltered = $this->admin_model->get_all_counts(AWARDS, $condition, []);
				$result = $this->admin_model->get_all_details(AWARDS, $condition, [$column=>$type], $length, $start);
			}
			$dataArr = array(); $i = $_REQUEST['start']+1;
			foreach($result->result_array() as $res){
				$nestedData=array(); 
				$nestedData[] = '<input type="checkbox" class="ids" name="ids[]" value="'.(string)$res['_id'].'" />';
				$nestedData[] = $i;
				$nestedData[] = ($res['sport']!='')?getrow(SPORTS, $res['sport'])->name:'-';
				$nestedData[] = ($res['category']!='')?$res['category']:'-';
				
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
				$action = '<a class="action-a" title="View" href="'.base_url().'admin/awards-view/'.(string)$res['_id'].'"><i class="fa fa-eye"></i></a>';
				$action .= '&nbsp;<a class="action-a" title="Edit" href="'.base_url().'admin/awards-edit/'.(string)$res['_id'].'" title="Edit"><i class="fa fa-pencil"></i></a>';
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
			$this->data['sports'] = $this->admin_model->get_all_details(SPORTS, ['status'=>'1']);
			$this->data['categories'] = $this->admin_model->get_all_details(CATEGORY, ['status'=>'1']);
			if($id!=''){
				$condition = ['_id' => objectid((string)$id)];
				$result = $this->admin_model->get_all_details(AWARDS, $condition);
				if($result->num_rows() > 0){
					$this->data['result'] = $result->result_array();
					$form_mode = TRUE;
				} else {
					$this->setErrorMessage('error','Something went wrong!!!');
					redirect(ADMINURL.'/awards-list');
				}
			}
			$this->data['form_mode'] = $form_mode;
			$this->data['heading'] = "Fisto Awards Details";
			$this->load->view('admin/awards/add_edit',$this->data);
		}
	}
	
	public function view($id = ''){
		if ($this->checkLogin('A') == ''){
			$this->setErrorMessage('error','Login required!!!');
			redirect(ADMINURL);
		} else {
			if($id!=''){
				$condition = ['_id' => objectid((string)$id)];
				$result = $this->admin_model->get_all_details(AWARDS, $condition);
				if($result->num_rows() > 0){
					$this->data['sport'] = $this->admin_model->get_selected_fields(SPORTS, ['_id'=>objectid($result->row()->sport)], ['name'])->row()->name;
					$this->data['result'] = $result->result_array();
					$this->data['heading'] = "Fisto Awards Details";
					$this->load->view('admin/awards/view',$this->data);
				} else {
					$this->setErrorMessage('error','Something went wrong!!!');
					redirect(ADMINURL.'/awards-list');
				}
			} else {
				$this->setErrorMessage('error','Something went wrong!!!');
				redirect(ADMINURL.'/awards-list');
			}
		}
	}
	
	public function save(){
		if ($this->checkLogin('A') == ''){
			$response['message'] = 'Something went Wrong!';
		} else {
			$objectid = (string)$this->input->post('objectid');
			$postdata = $this->input->post();			
			$config['overwrite'] = FALSE;
			$config['encrypt_name'] = TRUE;
			$config['allowed_types'] = 'jpg|jpeg|gif|png|bmp';
			$config['max_size'] = 5000;
			$config['upload_path'] = 'uploads/awards/';
			$this->load->library('upload', $config);
			
			if($objectid!=''){
				$conditionArr = ['_id'=>objectid((string)$objectid)];
				$adminCheck = $this->admin_model->get_selected_fields(AWARDS, $conditionArr, ['contestants']);
				if ($adminCheck->num_rows() > 0){
					$dataArr = ['dateModified'=>(int)time()];
					for($i=0;$i<count($_POST['contestant']);$i++){
						$uploadedname = '';
						if($_FILES['image']['name'][$i]!=''){
							$_FILES['file'] = [
								'name'=>$_FILES['image']['name'][$i], 
								'type'=>$_FILES['image']['type'][$i], 
								'tmp_name'=>$_FILES['image']['tmp_name'][$i], 
								'error'=>$_FILES['image']['error'][$i],
								'size'=>$_FILES['image']['size'][$i]
							];
							if ($this->upload->do_upload('file')) {
								$bannerDetails = $this->upload->data();
								$uploadedname = $bannerDetails['file_name'];
							}
						} else {
							$uploadedname = $adminCheck->row()->contestants['contestant-'.($i+1)]['image'];
						}
						
						$dataArr['contestants']['contestant-'.($i+1)] = ['name'=>$postdata['contestant'][$i], 'team_name'=>$postdata['team_name'][$i], 'image'=>$uploadedname, 'description'=>$postdata['description'][$i], 'votes'=>$adminCheck->row()->contestants['contestant-'.($i+1)]['votes']];
					}
					$this->admin_model->commonInsertUpdate(AWARDS, 'update', ['_id', 'objectid', 'contestant', 'team_name', 'description'], $dataArr, $conditionArr);
					$this->setErrorMessage('success','Fisto Awards details saved!!!');
					redirect(ADMINURL.'/awards-list');
				} else {
					$this->setErrorMessage('error','Something went wrong!!!');
					redirect(ADMINURL.'/awards-list');
				}
			} else {
				$dataArr = ['status'=>'1', 'dateAdded'=>MongoDATE(time())];
				for($i=0;$i<count($_POST['contestant']);$i++){
					$uploadedname = '';
					if($_FILES['image']['name'][$i]!=''){
						$_FILES['file'] = [
							'name'=>$_FILES['image']['name'][$i], 
							'type'=>$_FILES['image']['type'][$i], 
							'tmp_name'=>$_FILES['image']['tmp_name'][$i], 
							'error'=>$_FILES['image']['error'][$i],
							'size'=>$_FILES['image']['size'][$i]
						];
						if ($this->upload->do_upload('file')) {
							$bannerDetails = $this->upload->data();
							$uploadedname = $bannerDetails['file_name'];
						}
					}
					$dataArr['contestants']['contestant-'.($i+1)] = ['name'=>$postdata['contestant'][$i], 'team_name'=>$postdata['team_name'][$i], 'image'=>$uploadedname, 'description'=>$postdata['description'][$i], 'votes'=>0];
				}
				$this->admin_model->commonInsertUpdate(AWARDS,'insert',['_id', 'objectid', 'contestant', 'team_name', 'description'],$dataArr,[]);
				$this->setErrorMessage('success','Fisto Awards details saved!!!');
				redirect(ADMINURL.'/awards-list');
			}
		}
	}
	
	public function save_page_description(){
		if ($this->checkLogin('A') == ''){
			$response['message'] = 'Something went Wrong!';
		} else {
			$conditionArr = ['type'=>'page-description'];
			$adminCheck = $this->admin_model->get_selected_fields(AWARDS, $conditionArr, ['contestants']);
			if ($adminCheck->num_rows() > 0){
				$this->admin_model->commonInsertUpdate(AWARDS, 'update', [], ['description'=>$this->input->post('description')], $conditionArr);
			} else {
				$this->admin_model->commonInsertUpdate(AWARDS, 'insert', [], ['type'=>'page-description', 'description'=>$this->input->post('description')], $conditionArr);
			}
			$this->setErrorMessage('success','Page description saved!!!');
			redirect(ADMINURL.'/awards-list');
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
			$check = $this->admin_model->get_selected_fields(AWARDS, $condition, ['cms_name']);
			
			if($check->num_rows()>0){
				$this->admin_model->update_details(AWARDS, $dataArr, $condition);
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
			$check = $this->admin_model->get_all_counts(AWARDS, $condition);
			if($check > 0){
				$this->admin_model->commonDelete(AWARDS, $condition);
				echo 'success';
			} else {
				echo 'Something went wrong!!!';
			}
		}
	}
	
	public function bulk_status(){
		$ids = array_map(function($a){ return objectid($a); }, $_POST['ids']);
		$this->change_bulk_status(AWARDS, $_POST['status'], $ids);
	}
	
	public function bulk_delete(){
		$ids = array_map(function($a){ return objectid($a); }, $_POST['ids']);
		$condition = ['_id'=>['$in'=>$ids]];
		$check = $this->admin_model->get_selected_fields(AWARDS, $condition, ['_id']);
		if($check->num_rows()>0){
			$this->admin_model->commonDelete(AWARDS, $condition);
			echo 'success';
		} else {
			echo 'Something went wrong!!!';
		}
	}
	
}
