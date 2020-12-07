<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Gallery extends MY_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('admin_model');
	}
		
	public function listing($id = ''){
		if ($this->checkLogin('A') == ''){
			$this->setErrorMessage('error','Login required!!!');
			redirect(ADMINURL);
		} else {
			$this->data['heading'] = "Gallery List";
			$this->data['sports'] = $this->admin_model->get_all_details(SPORTS, []);
			$this->data['categories'] = $this->admin_model->get_all_details(CATEGORY, []);
			$this->load->view('admin/gallery/listing',$this->data);
		}
	}
	
	public function listing_ajax(){
		if ($this->checkLogin('A') != ''){
			$columns = array( 
				0 =>'_id', 
				1 =>'', 
				2 =>'name', 
				3 =>'gallery_date', 
				4 =>'status'
			);
			
			$search = $_REQUEST['search']['value'];
			$filters = [
				'name'=>$search
			];
			$condition = [];
			if($_REQUEST['columns'][2]['search']['value']){
				$condition['media_type'] = $_REQUEST['columns'][2]['search']['value'];
			}
			if($_REQUEST['columns'][3]['search']['value']){
				$condition['event_type'] = $_REQUEST['columns'][3]['search']['value'];
			}
			if($_REQUEST['columns'][4]['search']['value']){
				$condition['gender'] = $_REQUEST['columns'][4]['search']['value'];
			}
			
			$recordsTotal = $this->admin_model->get_all_counts(GALLERY, $condition);
			$length = $_REQUEST['length'];
			$start = $_REQUEST['start'];
			$column = $columns[$_REQUEST['order'][0]['column']]; 
			$type = $_REQUEST['order'][0]['dir'];
			if(!empty($search)) {
				$recordsFiltered = $this->admin_model->get_all_counts(GALLERY, $condition, $filters);
				$result = $this->admin_model->get_all_details(GALLERY, $condition, [$column=>$type], $length, $start, $filters);
			} else {
				$recordsFiltered = $this->admin_model->get_all_counts(GALLERY, $condition, []);
				$result = $this->admin_model->get_all_details(GALLERY, $condition, [$column=>$type], $length, $start);
			}
			$dataArr = array(); $i = $_REQUEST['start']+1;
			foreach($result->result_array() as $res){
				$nestedData=array(); 
				$nestedData[] = '<input type="checkbox" class="ids" name="ids[]" value="'.(string)$res['_id'].'" />';
				$nestedData[] = $i;
				
				$nestedData[] = ($res['name']!='')?$res['name']:'-';
				$nestedData[] = ($res['gallery_date']!='')?date('d/m/Y', $res['gallery_date']):'-';
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
				$action = '<a class="action-a" title="View" href="'.base_url().'admin/gallery-view/'.(string)$res['_id'].'"><i class="fa fa-eye"></i></a>';
				$action .= '&nbsp;<a class="action-a" title="Edit" href="'.base_url().'admin/gallery-edit/'.(string)$res['_id'].'" title="Edit"><i class="fa fa-pencil"></i></a>';
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
				$result = $this->admin_model->get_all_details(GALLERY, $condition);
				if($result->num_rows() > 0){
					$this->data['result'] = $result->result_array();
					$form_mode = TRUE;
				} else {
					$this->setErrorMessage('error','Something went wrong!!!');
					redirect(ADMINURL.'/gallery-list');
				}
			}
			$this->data['form_mode'] = $form_mode;
			$this->data['heading'] = "Gallery Details";
			$this->load->view('admin/gallery/add_edit',$this->data);
		}
	}
	
	public function view($id = ''){
		if ($this->checkLogin('A') == ''){
			$this->setErrorMessage('error','Login required!!!');
			redirect(ADMINURL);
		} else {
			if($id!=''){
				$condition = ['_id' => objectid((string)$id)];
				$result = $this->admin_model->get_all_details(GALLERY, $condition);
				if($result->num_rows() > 0){
					$this->data['sport'] = $this->admin_model->get_selected_fields(SPORTS, ['_id'=>objectid($result->row()->sport)], ['name'])->row()->name;
					$this->data['result'] = $result->result_array();
					$this->data['heading'] = "Gallery Details";
					$this->load->view('admin/gallery/view',$this->data);
				} else {
					$this->setErrorMessage('error','Something went wrong!!!');
					redirect(ADMINURL.'/gallery-list');
				}
			} else {
				$this->setErrorMessage('error','Something went wrong!!!');
				redirect(ADMINURL.'/gallery-list');
			}
		}
	}
	
	public function save(){
		if ($this->checkLogin('A') == ''){
			$response['message'] = 'Something went Wrong!';
		} else {
			$objectid = (string)$this->input->post('objectid');
			$media_type = $this->input->post('media_type');	
			$google_ad_sense = $this->input->post('google_ad_sense');	
			$olympics = (string)$this->input->post('olympics');
			$_POST['google_ad_sense'] = ($google_ad_sense!='')?'1':'0';
			$_POST['olympics'] = ($olympics!='')?'1':'0';
			$name = trim($this->input->post('name'));
			$_POST['seourl'] = url_title($name, '-', TRUE);
			$gallery_date = '';
			if($_POST['gallery_date']!=''){
				$explode = @explode('/', $_POST['gallery_date']);
				$gallery_date = $explode[2].'-'.$explode[1].'-'.$explode[0];
			}
			if($objectid!=''){
				$conditionArr = ['_id'=>objectid((string)$objectid)];
				$result = $this->admin_model->get_all_details(GALLERY, $conditionArr);
				if ($result->num_rows() > 0){
					if($media_type=='Photo'){
						$remove = $this->input->post('remove');
						$remove = (!empty($remove)) ? $_POST['remove'] : [];
						$photos = (isset($result->row()->photos))?$result->row()->photos:[];
						
						if(count($remove) > 0 && count($photos) > 0){
							$photo = [];
							foreach($photos as $img){
								if(!in_array($img, $remove)) $photo[] = $img;
								else unlink('uploads/gallery/'.$img);
							}
							$photos = $photo;
						}
						
						if(count($_FILES['photos']) > 0){
							$filesArr = $this->file_upload_multiple_local($_FILES['photos'], 'uploads/gallery/');
							if(count($filesArr)>0){
								$photos = array_merge($photos, $filesArr);
							}
						}
						$_POST['photos'] = $photos;
					} else {
						if($_FILES['thumbnail']['name'][0]!=''){
							$_POST['thumbnail'] = $this->file_upload_multiple_local($_FILES['thumbnail'], 'uploads/gallery/');
						}
					}
					$this->admin_model->commonInsertUpdate(GALLERY, 'update', ['_id', 'objectid', 'gallery_date', 'remove'], ['dateModified'=>(int)time(), 'gallery_date'=>(int)strtotime($gallery_date)], $conditionArr);
					$this->setErrorMessage('success','Gallery details saved!!!');
					redirect(ADMINURL.'/gallery-list');
				} else {
					$this->setErrorMessage('error','Something went wrong!!!');
					redirect(ADMINURL.'/gallery-list');
				}
			} else {
				if($media_type == 'Photo' && count($_FILES['photos']) > 0){
					$_POST['photos'] = $this->file_upload_multiple_local($_FILES['photos'], 'uploads/gallery/');
				} else { 	
					if(count($_FILES['thumbnail']) > 0){
						$_POST['thumbnail'] = $this->file_upload_multiple_local($_FILES['thumbnail'], 'uploads/gallery/');
					}
				}
				$this->admin_model->commonInsertUpdate(GALLERY,'insert',['_id', 'objectid', 'gallery_date', 'remove'],['status'=>'1', 'dateAdded'=>MongoDATE(time()), 'gallery_date'=>(int)strtotime($gallery_date)],[]);
				$this->setErrorMessage('success','Gallery details saved!!!');
				redirect(ADMINURL.'/gallery-list');
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
			$check = $this->admin_model->get_selected_fields(GALLERY, $condition, ['cms_name']);
			
			if($check->num_rows()>0){
				$this->admin_model->update_details(GALLERY, $dataArr, $condition);
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
			$check = $this->admin_model->get_all_counts(GALLERY, $condition);
			if($check > 0){
				$this->admin_model->commonDelete(GALLERY, $condition);
				echo 'success';
			} else {
				echo 'Something went wrong!!!';
			}
		}
	}
	
	public function bulk_status(){
		$ids = array_map(function($a){ return objectid($a); }, $_POST['ids']);
		$this->change_bulk_status(GALLERY, $_POST['status'], $ids);
	}
	
	public function bulk_delete(){
		$ids = array_map(function($a){ return objectid($a); }, $_POST['ids']);
		$condition = ['_id'=>['$in'=>$ids]];
		$check = $this->admin_model->get_selected_fields(GALLERY, $condition, ['_id']);
		if($check->num_rows()>0){
			$this->admin_model->commonDelete(GALLERY, $condition);
			echo 'success';
		} else {
			echo 'Something went wrong!!!';
		}
	}
	
}
