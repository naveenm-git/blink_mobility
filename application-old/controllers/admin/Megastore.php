<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Megastore extends MY_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('admin_model');
	}
		
	public function listing($id = ''){
		if ($this->checkLogin('A') == ''){
			$this->setErrorMessage('error','Login required!!!');
			redirect(ADMINURL);
		} else {
			$this->data['heading'] = "Fisto Megastore List";
			$this->data['min'] = $this->admin_model->get_all_details(MEGASTORE, [], ['price'=>'asc'], 1, 0)->row()->price;
			$this->data['max'] = $this->admin_model->get_all_details(MEGASTORE, [], ['price'=>'desc'], 1, 0)->row()->price;
			$this->load->view('admin/megastore/listing',$this->data);
		}
	}
	
	public function listing_ajax(){
		if ($this->checkLogin('A') != ''){
			$columns = array( 
				0 =>'_id', 
				1 =>'',
				2 =>'name', 
				3 =>'brand_name',
				4 =>'price',
				5 =>'',
				6 =>'status'
			);
			
			$search = $_REQUEST['search']['value'];
			$filters = [
				'name'=>$search,
				'brand_name'=>$search,
				'price'=>$search
			];
			
			$condition = [];
			if($_REQUEST['columns'][6]['search']['value']){
				$condition['status'] = ($_REQUEST['columns'][6]['search']['value']=='active')?'1':'0';
			}
			if($_REQUEST['columns'][4]['search']['value']){
				$price = explode(' - ', $_REQUEST['columns'][4]['search']['value']);
				if($price[0]>0) $pricefilter['$gte'] = (int) $price[0]; 
				if($price[1]>0) $pricefilter['$lte'] = (int) $price[1];
				$condition['price'] = $pricefilter;
			}
			$recordsTotal = $this->admin_model->get_all_counts(MEGASTORE, $condition);
			$length = $_REQUEST['length'];
			$start = $_REQUEST['start'];
			$column = $columns[$_REQUEST['order'][0]['column']]; 
			$type = $_REQUEST['order'][0]['dir'];
			if(!empty($search)) {
				$recordsFiltered = $this->admin_model->get_all_counts(MEGASTORE, $condition, $filters);
				$result = $this->admin_model->get_all_details(MEGASTORE, $condition, [$column=>$type], $length, $start, $filters);
			} else {
				$recordsFiltered = $this->admin_model->get_all_counts(MEGASTORE, $condition, []);
				$result = $this->admin_model->get_all_details(MEGASTORE, $condition, [$column=>$type], $length, $start);
			}
			$dataArr = array(); $i = $_REQUEST['start']+1;
			foreach($result->result_array() as $res){
				$nestedData=array(); 
				$nestedData[] = '<input type="checkbox" class="ids" name="ids[]" value="'.(string)$res['_id'].'" />';
				$nestedData[] = $i;
				$nestedData[] = ($res['name']!='')?$res['name']:'-';
				$nestedData[] = ($res['brand_name']!='')?$res['brand_name']:'-';
				$nestedData[] = ($res['price']!='')?number_format($res['price'], 2):'-';
				$nestedData[] = ($res['images']!='' && $res['images']!='null' && !empty(json_decode($res['images'])))?'<img src="'.base_url().'uploads/megastore/'.json_decode($res['images'])[0].'" class="tbl-img" />':'<img src="'.base_url().'assets/images/user.png" class="tbl-img" />';
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
				$action = '<a class="action-a" title="View" href="'.base_url().'admin/megastore-view/'.(string)$res['_id'].'"><i class="fa fa-eye"></i></a>';
				$action .= '&nbsp;<a class="action-a" title="Edit" href="'.base_url().'admin/megastore-edit/'.(string)$res['_id'].'" title="Edit"><i class="fa fa-pencil"></i></a>';
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
				$result = $this->admin_model->get_all_details(MEGASTORE, $condition);
				if($result->num_rows() > 0){
					$this->data['result'] = $result->result_array();
					$form_mode = TRUE;
				} else {
					$this->setErrorMessage('error','Something went wrong!!!');
					redirect(ADMINURL.'/megastore-list');
				}
			}
			$this->data['form_mode'] = $form_mode;
			$this->data['heading'] = "Fisto Megastore Details";
			$this->load->view('admin/megastore/add_edit',$this->data);
		}
	}
	
	public function view($id = ''){
		if ($this->checkLogin('A') == ''){
			$this->setErrorMessage('error','Login required!!!');
			redirect(ADMINURL);
		} else {
			if($id!=''){
				$condition = ['_id' => objectid((string)$id)];
				$result = $this->admin_model->get_all_details(MEGASTORE, $condition);
				if($result->num_rows() > 0){
					$this->data['sport'] = $this->admin_model->get_selected_fields(SPORTS, ['_id'=>objectid($result->row()->sport)], ['name'])->row()->name;
					$this->data['result'] = $result->result_array();
					$this->data['heading'] = "Fisto Megastore Details";
					$this->load->view('admin/megastore/view',$this->data);
				} else {
					$this->setErrorMessage('error','Something went wrong!!!');
					redirect(ADMINURL.'/megastore-list');
				}
			} else {
				$this->setErrorMessage('error','Something went wrong!!!');
				redirect(ADMINURL.'/megastore-list');
			}
		}
	}
	
	public function save(){
		if ($this->checkLogin('A') == ''){
			$response['message'] = 'Something went Wrong!';
		} else {
			$objectid = (string)$this->input->post('objectid');
			$media_type = $this->input->post('media_type');	
			$price = $this->input->post('price');
			$google_ad_sense = $this->input->post('google_ad_sense');	
			$_POST['google_ad_sense'] = ($google_ad_sense!='')?'1':'0';
			$name = trim($this->input->post('name'));
			$_POST['seourl'] = url_title($name, '-', TRUE);
			
			if($objectid!=''){
				$conditionArr = ['_id'=>objectid((string)$objectid)];
				$result = $this->admin_model->get_all_details(MEGASTORE, $conditionArr);
				if ($result->num_rows() > 0){
					
					$remove = $this->input->post('remove');
					$remove = (!empty($remove)) ? $_POST['remove'] : [];
					$images = (isset($result->row()->images)&& $result->row()->images!='null')?json_decode($result->row()->images):[];
					
					if(count($remove) > 0 && count($images) > 0){
						$photo = [];
						foreach($images as $img){
							if(!in_array($img, $remove)) $photo[] = $img;
							else unlink('uploads/megastore/'.$img);
						}
						$images = $photo;
					}
					
					if(count($_FILES['images']) > 0){
						$filesArr = $this->file_upload_multiple_local($_FILES['images'], 'uploads/megastore/');
						if(count($filesArr)>0){
							$images = array_merge($images, $filesArr);
						}
					}
					$_POST['images'] = json_encode($images);
					
					$this->admin_model->commonInsertUpdate(MEGASTORE, 'update', ['_id', 'objectid', 'date', 'removed', 'price'], ['dateModified'=>(int)time(), 'price'=>(int)$price], $conditionArr);
					$this->setErrorMessage('success','Megastore details saved!!!');
					redirect(ADMINURL.'/megastore-list');
				} else {
					$this->setErrorMessage('error','Something went wrong!!!');
					redirect(ADMINURL.'/megastore-list');
				}
			} else {
				if(count($_FILES['images']) > 0){
					$_POST['images'] = json_encode($this->file_upload_multiple_local($_FILES['images'], 'uploads/megastore/'));
				}
				$this->admin_model->commonInsertUpdate(MEGASTORE,'insert',['_id', 'objectid', 'date', 'price', 'removed'],['status'=>'1', 'dateAdded'=>MongoDATE(time()), 'price'=>(int)$price],[]);
				$this->setErrorMessage('success','Megastore details saved!!!');
				redirect(ADMINURL.'/megastore-list');
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
			$check = $this->admin_model->get_selected_fields(MEGASTORE, $condition, ['cms_name']);
			
			if($check->num_rows()>0){
				$this->admin_model->update_details(MEGASTORE, $dataArr, $condition);
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
			$check = $this->admin_model->get_all_counts(MEGASTORE, $condition);
			if($check > 0){
				$this->admin_model->commonDelete(MEGASTORE, $condition);
				echo 'success';
			} else {
				echo 'Something went wrong!!!';
			}
		}
	}
	
	public function bulk_status(){
		$ids = array_map(function($a){ return objectid($a); }, $_POST['ids']);
		$this->change_bulk_status(MEGASTORE, $_POST['status'], $ids);
	}
	
	public function bulk_delete(){
		$ids = array_map(function($a){ return objectid($a); }, $_POST['ids']);
		$condition = ['_id'=>['$in'=>$ids]];
		$check = $this->admin_model->get_selected_fields(MEGASTORE, $condition, ['_id']);
		if($check->num_rows()>0){
			$this->admin_model->commonDelete(MEGASTORE, $condition);
			echo 'success';
		} else {
			echo 'Something went wrong!!!';
		}
	}
	
}
