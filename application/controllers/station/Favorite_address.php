<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Favorite_address extends MY_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('admin_model');
		$this->data['_user_id'] = $this->uri->segment(2);
	}
		
	public function listing(){
		if ($this->checkLogin('A') != '' && $this->data['_user_id']!=''){
			$this->data['heading'] = 'Favorite Address';
			$this->data['userdetail'] = $userdetail = $this->admin_model->get_all_details(USERS, ['_id'=>MongoID($this->data['_user_id'])]);
			$this->data['result'] = $this->admin_model->get_all_details(FAVORITE_ADDRESS, ['user_id'=>$this->data['_user_id']]);
			$this->load->view('users/favorite_address/listing', $this->data);
		} else {
			$this->setErrorMessage('error','Login required!!!');
			redirect(ADMINURL);
		}
	}
   
	public function listing_ajax(){
      if ($this->checkLogin('A') != '' && $this->data['_user_id']!=''){
			$columns = array( 
				0 =>'_id', 
				1 =>'', 
				2 =>'title', 
				3 =>'address',
				4 =>'status'
			);
			
			$search = $_REQUEST['search']['value'];
			$filters = [
				'title'=>$search,
				'address'=>$search
			];
			$condition = [];         
			$recordsTotal = $this->admin_model->get_all_counts(FAVORITE_ADDRESS, $condition);
			$length = $_REQUEST['length'];
			$start = $_REQUEST['start'];
			$column = $columns[$_REQUEST['order'][0]['column']]; 
			$type = $_REQUEST['order'][0]['dir'];
			if(!empty($search)) {
				$recordsFiltered = $this->admin_model->get_all_counts(FAVORITE_ADDRESS, $condition, $filters);
				$result = $this->admin_model->get_all_details(FAVORITE_ADDRESS, $condition, [$column=>$type], $length, $start, $filters);
			} else {
				$recordsFiltered = $this->admin_model->get_all_counts(FAVORITE_ADDRESS, $condition, []);
				$result = $this->admin_model->get_all_details(FAVORITE_ADDRESS, $condition, [$column=>$type], $length, $start);
			}
			$dataArr = array(); $i = $_REQUEST['start']+1;
			foreach($result->result_array() as $res){
				$nestedData=array(); 
				$nestedData[] = '<input type="checkbox" class="ids" name="ids[]" value="'.(string)$res['_id'].'" />';
				$nestedData[] = $i;
				$nestedData[] = ($res['title']!='')?$res['title']:'-';
				$nestedData[] = ($res['address']!='')?$res['address']:'-';
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
				$action = '<a class="action-a" title="View" href="'.base_url().USERURL.'/'.$this->data['_user_id'].'/favorite-address/view/'.(string)$res['_id'].'"><i class="fa fa-eye"></i></a>';
				$action .= '&nbsp;<a class="action-a" title="Edit" href="'.base_url().USERURL.'/'.$this->data['_user_id'].'/favorite-address/edit/'.(string)$res['_id'].'" title="Edit"><i class="fa fa-pencil"></i></a>';
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
	
	public function add_edit(){
		if ($this->checkLogin('A')!='' && $this->data['_user_id']!=''){
			$form_mode = FALSE;
			$this->data['result'] = [];
         $_id = $this->uri->segment(5);
			if($_id!=''){
				$condition = ['_id' => MongoID((string)$_id)];
				$result = $this->admin_model->get_all_details(FAVORITE_ADDRESS, $condition);
				if($result->num_rows() > 0){
					$this->data['result'] = $result->result_array();
					$form_mode = TRUE;
				} else {
					$this->setErrorMessage('error','Something went wrong!!!');
					redirect(ADMINURL.'/station-list');
				}
			}
			$this->data['form_mode'] = $form_mode;
			$this->data['heading'] = "Favorite Address";
			$this->load->view(USERURL.'/favorite_address/add_edit',$this->data);
		} else {
			$this->setErrorMessage('error','Login required!!!');
			redirect(ADMINURL);
		}
	}
   
	public function save(){
		if ($this->checkLogin('A') != '' && $this->data['_user_id']!=''){
			$_user_id = $this->data['_user_id'];
			$objectid = $this->input->post('objectid');
			$title = $this->input->post('title');
			$address = $this->input->post('address');
			$lat = $this->input->post('lat');
			$lng = $this->input->post('lng');
			$status = $this->input->post('status');
			$status = ($status!='')?'1':'0';
         
         $dataArr = [
            'user_id' => $this->_user_id,
            'title' => $title,
            'address' => $address,
            'location' => [
               'lat' => floatval($lat),
               'lng' => floatval($lng)
            ]
         ];
			if($_user_id!=''){
            if($objectid!=''){
               $condition = ['_id'=>MongoID((string)$objectid), 'user_id'=>$_user_id];
               $check = $this->admin_model->get_all_details(FAVORITE_ADDRESS, $condition);
               if ($check->num_rows() > 0){
                  $dataArr['modified_at'] = MongoDATE(time());
                  $this->admin_model->commonInsertUpdate(FAVORITE_ADDRESS, 'update', ['_id', 'objectid', 'status'], $dataArr, $condition);
                  $this->setErrorMessage('success','Address saved!!!');
               } else {
                  $this->setErrorMessage('error','Something went wrong!!!');
                  redirect(USERURL.'/'.$_user_id.'/favorite-address');
               }
            } else {
               $dataArr['created_at'] = MongoDATE(time());
               $this->admin_model->commonInsertUpdate(FAVORITE_ADDRESS,'insert',['_id', 'objectid', 'status'], $dataArr,[]);
               $this->setErrorMessage('success','Address saved!!!');
            }
			} else {
				$this->setErrorMessage('error','User ID Empty!!!');
				redirect(USERURL.'/'.$_user_id.'/favorite-address');
			}
		} else {
			$this->setErrorMessage('error','Login required!!!');
			redirect(ADMINURL);
		}
	}
	
	public function edit(){
		if ($this->checkLogin('A') != '' && $this->data['_user_id']!=''){
         $this->data['document'] = $document = $this->uri->segment(5);
         $this->data['query'] = $this->input->get('q');
			$this->data['heading'] = ucwords(str_replace('_',' ',$document)).' Edit';
			$this->data['result'] = $this->admin_model->get_all_details(USERS, ['_id'=>MongoID($this->data['_user_id'])]);
			$this->load->view(USERURL.'/favorite_address/edit', $this->data);
		} else {
			$this->setErrorMessage('error','Login required!!!');
			redirect(ADMINURL);
		}
	}
	
	public function view(){
		if ($this->checkLogin('A') != '' && $this->data['_user_id']!=''){
         $_id = $this->uri->segment(5);
			$this->data['heading'] = 'Favorite Address View';
			$this->data['result'] = $this->admin_model->get_all_details(FAVORITE_ADDRESS, ['_id'=>MongoID($_id)])->result_array();;
			$this->load->view(USERURL.'/favorite_address/view', $this->data);
		} else {
			$this->setErrorMessage('error','Login required!!!');
			redirect(ADMINURL);
		}
	}
	
	public function status(){
		if ($this->checkLogin('A') == ''){
			echo 'Something went wrong!!!';
		} else {
			$status = $this->input->post('status');
			$id = $this->input->post('id');
			$dataArr = ['status'=>$status];
			$condition = ['_id'=>MongoID($id)];
			$check = $this->admin_model->get_selected_fields(FAVORITE_ADDRESS, $condition, ['cms_name']);
			
			if($check->num_rows()>0){
				$this->admin_model->update_details(FAVORITE_ADDRESS, $dataArr, $condition);
				echo 'success';
			} else {
				echo 'Something went wrong!!!';
			}
		}
	}

	public function remove(){
		if ($this->checkLogin('A') == '' && $this->data['_user_id']!=''){
			echo 'Something went wrong!!!';
		} else {
			$id = $this->input->post('id');
			$condition = ['_id'=>MongoID($id)];
			$check = $this->admin_model->get_all_counts(FAVORITE_ADDRESS, $condition);
			if($check > 0){
				$this->admin_model->commonDelete(FAVORITE_ADDRESS, $condition);
				echo 'success';
			} else {
				echo 'Something went wrong!!!';
			}
		}
	}
}
