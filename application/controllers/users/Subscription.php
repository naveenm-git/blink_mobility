<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Subscription extends MY_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('admin_model');
		$this->data['_user_id'] = $this->uri->segment(2);
	}
		
	public function listing(){
		if ($this->checkLogin('A') != '' && $this->data['_user_id']!=''){
			$this->data['heading'] = 'Users Subscription';
			$this->data['userdetail'] = $userdetail = $this->admin_model->get_all_details(USERS, ['_id'=>MongoID($this->data['_user_id'])]);
			$this->data['result'] = $this->admin_model->get_all_details(USERS_SUBSCRIPTION, ['user_id'=>$this->data['_user_id']]);
			$this->load->view('users/subscription/listing', $this->data);
		} else {
			$this->setErrorMessage('error','Login required!!!');
			redirect(ADMINURL);
		}
	}
   
	public function listing_ajax(){
      if ($this->checkLogin('A') != '' && $this->data['_user_id']!=''){
			$columns = array('name', 'created_at', '', 'platform', 'status');
			$search = $_REQUEST['search']['value'];
			$filters = [
				'name'=>$search,
				'platform'=>$search
			];
			$condition = ['user_id' => $this->data['_user_id']];         
			$recordsTotal = $this->admin_model->get_all_counts(USERS_SUBSCRIPTION, $condition);
			$length = $_REQUEST['length'];
			$start = $_REQUEST['start'];
			$column = $columns[$_REQUEST['order'][0]['column']]; 
			$type = $_REQUEST['order'][0]['dir'];
			if(!empty($search)) {
				$recordsFiltered = $this->admin_model->get_all_counts(USERS_SUBSCRIPTION, $condition, $filters);
				$result = $this->admin_model->get_all_details(USERS_SUBSCRIPTION, $condition, [$column=>$type], $length, $start, $filters);
			} else {
				$recordsFiltered = $this->admin_model->get_all_counts(USERS_SUBSCRIPTION, $condition, []);
				$result = $this->admin_model->get_all_details(USERS_SUBSCRIPTION, $condition, [$column=>$type], $length, $start);
			}
			$dataArr = array(); $i = $_REQUEST['start']+1;
			foreach($result->result_array() as $res){
            $userdetail = getrow(USERS, $res['user_id']);
				$nestedData=array(); 
				$nestedData[] = ($res['name']!='')?$res['name']:'-';
				$nestedData[] = ($res['created_at']!='')?date('M d,Y h:m:s A', MongoEPOCH($res['created_at'])):'-';
				$nestedData[] = $userdetail->first_name.' '.$userdetail->last_name;
				$nestedData[] = ($res['platform']!='')?$res['platform']:'-';
				if($this->data['user_type']=='superadmin'){
					if($res['status'] == '1'){
						$status = '<a class="btn btn-xs btn-success" onclick="change_status(\''.(string)$res['_id'].'\', \'0\')" title="Click to make invalid">Valid</a>';
					} else { 
						$status = '<a class="btn btn-xs btn-danger" onclick="change_status(\''.(string)$res['_id'].'\', \'1\')" title="Click to make valid">Invalid</a>';
					}
               if($userdetail->subscription_id!=(string)$res['_id']){
                  $status = '<a class="btn btn-xs btn-default" title="Already expired">Expired</a>';
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
            
            $docs = '';
            $needed_documents = ['license', 'photo_of_yourself'];
            if($res['proof_of_qualification'] == '1') $needed_documents[] = 'proof_of_qualification';
            $i = 1;
            foreach($needed_documents as $needed){
               $document_type = ucwords(str_replace('_', ' ', $needed));
               $status = 'Pending'; $text = 'danger';
               if(isset($userdetail->verified_documents) && isset($userdetail->verified_documents[$needed]) && $userdetail->verified_documents[$needed]['status']=='Verified'){ 
                  $status = 'Validated';
                  $text = 'success';
               }
               $docs .= '<p class="text-left">'.$i.'. '.$document_type.' - <span class="text-'.$text.'">'.$status.'</span></p>';
               $i++;
            }
				$nestedData[] = $docs;
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
				$result = $this->admin_model->get_all_details(USERS_SUBSCRIPTION, $condition);
				if($result->num_rows() > 0){
					$this->data['result'] = $result->result_array();
					$form_mode = TRUE;
				} else {
					$this->setErrorMessage('error','Something went wrong!!!');
					redirect(ADMINURL.'/station-list');
				}
			}
			$this->data['form_mode'] = $form_mode;
			$this->data['heading'] = "Subscription Address";
			$this->load->view(USERURL.'/subscription/add_edit',$this->data);
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
               $check = $this->admin_model->get_all_details(USERS_SUBSCRIPTION, $condition);
               if ($check->num_rows() > 0){
                  $dataArr['modified_at'] = MongoDATE(time());
                  $this->admin_model->commonInsertUpdate(USERS_SUBSCRIPTION, 'update', ['_id', 'objectid', 'status'], $dataArr, $condition);
                  $this->setErrorMessage('success','Address saved!!!');
               } else {
                  $this->setErrorMessage('error','Something went wrong!!!');
               }
            } else {
               $dataArr['created_at'] = MongoDATE(time());
               $this->admin_model->commonInsertUpdate(USERS_SUBSCRIPTION,'insert',['_id', 'objectid', 'status'], $dataArr,[]);
               $this->setErrorMessage('success','Address saved!!!');
            }
			} else {
				$this->setErrorMessage('error','User ID Empty!!!');
			}
         redirect(USERURL.'/'.$_user_id.'/favorite-address');
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
			$this->load->view(USERURL.'/subscription/edit', $this->data);
		} else {
			$this->setErrorMessage('error','Login required!!!');
			redirect(ADMINURL);
		}
	}
	
	public function view(){
		if ($this->checkLogin('A') != '' && $this->data['_user_id']!=''){
         $_id = $this->uri->segment(5);
			$this->data['heading'] = 'Subscription Address View';
			$this->data['result'] = $this->admin_model->get_all_details(USERS_SUBSCRIPTION, ['_id'=>MongoID($_id)])->result_array();;
			$this->load->view(USERURL.'/subscription/view', $this->data);
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
			$check = $this->admin_model->get_selected_fields(USERS_SUBSCRIPTION, $condition, ['cms_name']);
			
			if($check->num_rows()>0){
				$this->admin_model->update_details(USERS_SUBSCRIPTION, $dataArr, $condition);
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
			$check = $this->admin_model->get_all_counts(USERS_SUBSCRIPTION, $condition);
			if($check > 0){
				$this->admin_model->commonDelete(USERS_SUBSCRIPTION, $condition);
				echo 'success';
			} else {
				echo 'Something went wrong!!!';
			}
		}
	}
}
