<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Documents extends MY_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('admin_model');
		$this->data['_user_id'] = $this->uri->segment(2);
	}
	
	public function listing(){
		if ($this->checkLogin('A') != '' && $this->data['_user_id']){
			$this->data['heading'] = "Customer's Documents";
			$this->data['result'] = $this->admin_model->get_all_details(USERS, ['_id'=>MongoID($this->data['_user_id'])]);
			$this->load->view('users/documents/listing', $this->data);
		} else {
			$this->setErrorMessage('error','Login required!!!');
			redirect(ADMINURL);
		}
	}
	
	public function add(){
		if ($this->checkLogin('A') != '' && $this->data['_user_id']){
			$this->data['heading'] = 'Add New Document';
			$this->data['result'] = $this->admin_model->get_all_details(USERS, ['_id'=>MongoID($this->data['_user_id'])]);
			$this->load->view('users/documents/add', $this->data);
		} else {
			$this->setErrorMessage('error','Login required!!!');
			redirect(ADMINURL);
		}
	}
	public function save(){
		if ($this->checkLogin('A') != '' && $this->data['_user_id']){
			$_user_id = $this->data['_user_id'];
			$document_type = $this->input->post('document_type');
			$dataArr = [];
			
			
			if($_user_id!=''){
				$conditionArr = ['_id'=>MongoID((string)$_user_id)];
				$check = $this->admin_model->get_all_details(USERS, $conditionArr);
				if ($check->num_rows() > 0){
               $documents_to_verify = $check->row()->documents_to_verify;
               if(isset($check->row()->verified_documents) && !array_key_exists($document_type, $check->row()->verified_documents)){
                  $config['upload_path']    = './uploads/';
                  $config['encrypt_name']   = TRUE;
                  $config['allowed_types']  = 'gif|jpg|png';
                  $config['max_size']       = 5000;
                  $this->load->library('upload', $config);
                  
                  $err = 0;
                  if($document_type=='license'){
                     if ($this->upload->do_upload('license_front')) {
                        $response = $this->upload->data();
                        $dataArr['front'] = $response['file_name'];
                     } else $err++;
                     if ($this->upload->do_upload('license_back')) {
                        $response = $this->upload->data();
                        $dataArr['back'] = $response['file_name'];
                     } else $err++;
                  }
                  if($document_type=='photo_of_yourself' || $document_type=='proof_of_qualification'){
                     if ($this->upload->do_upload($document_type)) {
                        $response = $this->upload->data();
                        $dataArr['value'] = $response['file_name'];
                     } else $err++;
                  }
                  
                  if($err==0){
                     $dataArr['created_at'] = MongoDATE(time());
                  }
                  $documents_to_verify[$document_type] = $dataArr;
                  $this->admin_model->update_details(USERS, ['documents_to_verify' => $documents_to_verify], $conditionArr);
                  $this->setErrorMessage('success','Document Saved!!!');
                  redirect(USERURL.'/'.$_user_id.'/documents');
               } else {
                  $doctype = ucwords(str_replace('_',' ',$document_type));
                  $this->setErrorMessage('error', $doctype.' Already Verified!!!');
                  redirect(USERURL.'/'.$_user_id.'/documents');
               }
				} else {
					$this->setErrorMessage('error','Something went wrong!!!');
					redirect(USERURL.'/'.$_user_id.'/documents');
				}
			} else {
				$this->setErrorMessage('error','User ID Empty!!!');
				redirect(USERURL.'/'.$_user_id.'/documents');
			}
		} else {
			$this->setErrorMessage('error','Login required!!!');
			redirect(ADMINURL);
		}
	}
	
	public function edit(){
		if ($this->checkLogin('A') != '' && $this->data['_user_id']){
         $this->data['document'] = $document = $this->uri->segment(5);
         $this->data['query'] = $this->input->get('q');
			$this->data['heading'] = ucwords(str_replace('_',' ',$document)).' Edit';
			$this->data['result'] = $this->admin_model->get_all_details(USERS, ['_id'=>MongoID($this->data['_user_id'])]);
			$this->load->view('users/documents/edit', $this->data);
		} else {
			$this->setErrorMessage('error','Login required!!!');
			redirect(ADMINURL);
		}
	}
	
	public function view(){
		if ($this->checkLogin('A') != '' && $this->data['_user_id']){
         $this->data['document'] = $document = $this->uri->segment(5);
			$this->data['heading'] = ucwords(str_replace('_',' ',$document)).' View';
			$this->data['result'] = $this->admin_model->get_all_details(USERS, ['_id'=>MongoID($this->data['_user_id'])]);
			$this->load->view('users/documents/view', $this->data);
		} else {
			$this->setErrorMessage('error','Login required!!!');
			redirect(ADMINURL);
		}
	}
	
	public function change_status(){
		if ($this->checkLogin('A') != '' && $this->data['_user_id']){
         $status = $this->input->post('status');
			$document = $this->input->post('id');
			$document_type = $this->input->post('document_type');
			$dataArr = [];
         
			$condition = ['_id'=>MongoID($this->data['_user_id'])];
			$check = $this->admin_model->get_selected_fields(USERS, $condition, ['_id', 'documents_to_verify', 'verified_documents', 'rejected_documents']);
			if($check->num_rows()>0){
            $documents_to_verify = $check->result_array()[0][$document_type];
            $arr = $documents_to_verify[$document];
            $arr['status'] = $status;
            $arr['modified_at'] = MongoDATE(time());
            if($status=='Verified'){
               $dataArr['verified_documents.'.$document] = $arr;
            } else {
               $dataArr['rejected_documents.'.$document] = $arr;
            }
            unset($documents_to_verify[$document]);
            $dataArr[$document_type] = $documents_to_verify;
				$this->admin_model->update_details(USERS, $dataArr, $condition);
				echo 'success';
			} else {
				echo 'Something went wrong!!!';
			}
		} else {
			echo 'Something went wrong!!!';
		}
	}
	
}
