<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Article extends MY_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('admin_model');
	}
		
	public function listing($id = ''){
		if ($this->checkLogin('A') == ''){
			$this->setErrorMessage('error','Login required!!!');
			redirect(ADMINURL);
		} else {
			$this->data['heading'] = "Article List";
			$this->data['categories'] = $this->admin_model->get_selected_fields(CATEGORY, [], ['name']);
			$this->data['authors'] = $this->admin_model->get_selected_fields(AUTHOR, [], ['name']);
			$this->data['sports'] = $this->admin_model->get_selected_fields(SPORTS, [], ['name']);
			$this->load->view('admin/article/listing',$this->data);
		}
	}
	
	public function listing_ajax(){
		// error_reporting(-1);
		if ($this->checkLogin('A') != ''){
			$columns = array( 
				0 =>'_id', 
				1 =>'dateAdded', 
				2 =>'category', 
				3 =>'name', 
				4 =>'author',
				5 =>'sport',
				6 =>'dateAdded',
				7 =>'status'
			);
			
			$search = $_REQUEST['search']['value'];
			$filters = [
				'name'=>$search,
				'category'=>$search
			];
			$condition = [];
			if($_REQUEST['columns'][2]['search']['value']){
				$condition['category'] = $_REQUEST['columns'][2]['search']['value'];
			}
			if($_REQUEST['columns'][4]['search']['value']){
				$condition['author'] = $_REQUEST['columns'][4]['search']['value'];
			}
			if($_REQUEST['columns'][5]['search']['value']){
				$condition['sport'] = $_REQUEST['columns'][5]['search']['value'];
			}
			
			$recordsTotal = $this->admin_model->get_all_counts(ARTICLE, $condition);
			$length = $_REQUEST['length'];
			$start = $_REQUEST['start'];
			$column = $columns[$_REQUEST['order'][0]['column']]; 
			$type = $_REQUEST['order'][0]['dir'];
			if(!empty($search)) {
				$recordsFiltered = $this->admin_model->get_all_counts(ARTICLE, $condition, $filters);
				$result = $this->admin_model->get_all_details(ARTICLE, $condition, [$column=>$type], $length, $start, $filters);
			} else {
				$recordsFiltered = $this->admin_model->get_all_counts(ARTICLE, $condition, []);
				$result = $this->admin_model->get_all_details(ARTICLE, $condition, [$column=>$type], $length, $start);
			}
			$dataArr = array(); $i = $_REQUEST['start']+1;
			
			foreach($result->result_array() as $res){
				$nestedData=array(); 
				$nestedData[] = '<input type="checkbox" class="ids" name="ids[]" value="'.(string)$res['_id'].'" />';
				$nestedData[] = $i;
				$nestedData[] = ($res['category']!='')?getrow(CATEGORY, $res['category'])->name:'-';
				$nestedData[] = (strlen($res['name'])>40)?preg_replace("/[^a-zA-Z0-9:',-_]/", " ", substr($res['name'],0,40).'...'):preg_replace("/[^a-zA-Z0-9:',-_]/", " ", $res['name']);
				$nestedData[] = ($res['author']!='')?getrow(AUTHOR, $res['author'])->name:'-';
				if($res['image']!=''){
					if(strpos($res['image'], 'fistosports.com/wp-content') > 1){
						$img=$res['image'];
					} else {
						$img=base_url().'uploads/article/'.$res['image'];
					}
					$nestedData[] = '<img src="'.$img.'" class="tbl-img" />';
				} else {
					$nestedData[] = '<img src="'.base_url().'assets/images/user.png" class="tbl-img" />';
				}
				$nestedData[] = date('d/m/Y', $res['dateAdded']);
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
				$action = '<a class="action-a" title="View" href="'.base_url().'admin/article-view/'.(string)$res['_id'].'"><i class="fa fa-eye"></i></a>';
				$action .= '&nbsp;<a class="action-a" title="Edit" href="'.base_url().'admin/article-edit/'.(string)$res['_id'].'" title="Edit"><i class="fa fa-pencil"></i></a>';
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
			// echo '<pre>'; print_r($dataArr); die;
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
			$this->data['categories'] = $this->admin_model->get_selected_fields(CATEGORY, [], ['name']);
			$this->data['authors'] = $this->admin_model->get_selected_fields(AUTHOR, [], ['name']);
			$this->data['sports'] = $this->admin_model->get_selected_fields(SPORTS, [], ['name'], ['name'=>'asc']);
			$this->data['tags'] = $this->admin_model->get_selected_fields(COMMON, ['type'=>'tags'], ['tags'])->row();
			$relatedCondition = ['status'=>'1'];
			if($id!=''){
				$relatedCondition['_id'] = ['$ne'=>objectid((string)$id)];
				$condition = ['_id' => objectid((string)$id)];
				$result = $this->admin_model->get_all_details(ARTICLE, $condition);
				if($result->num_rows() > 0){
					$this->data['result'] = $result->result_array();
					$form_mode = TRUE;
				} else {
					$this->setErrorMessage('error','Something went wrong!!!');
					redirect(ADMINURL.'/article-list');
				}
			}
			$this->data['form_mode'] = $form_mode;
			$this->data['heading'] = "Article Details";
			$this->load->view('admin/article/add_edit',$this->data);
		}
	}
	
	public function view($id = ''){
		if ($this->checkLogin('A') == ''){
			$this->setErrorMessage('error','Login required!!!');
			redirect(ADMINURL);
		} else {
			if($id!=''){
				$condition = ['_id' => objectid((string)$id)];
				$result = $this->admin_model->get_all_details(ARTICLE, $condition);
				if($result->num_rows() > 0){
					$this->data['sport'] = $this->admin_model->get_selected_fields(SPORTS, ['_id'=>objectid($result->row()->sport)], ['name'])->row()->name;
					$this->data['author'] = $this->admin_model->get_selected_fields(AUTHOR, ['_id'=>objectid($result->row()->author)], ['name'])->row()->name;
					$this->data['result'] = $result->result_array();
					$this->data['heading'] = "Article Details";
					$this->load->view('admin/article/view',$this->data);
				} else {
					$this->setErrorMessage('error','Something went wrong!!!');
					redirect(ADMINURL.'/article-list');
				}
			} else {
				$this->setErrorMessage('error','Something went wrong!!!');
				redirect(ADMINURL.'/article-list');
			}
		}
	}
	
	public function save(){
		if ($this->checkLogin('A') == ''){
			$this->setErrorMessage('error','Login required!!!');
			redirect(ADMINURL);
		} else {
			$objectid = (string)$this->input->post('objectid');
			$_POST['content'] = (string)$this->input->post('content', false);
			// print_r(str_replace('/',':|:', $_POST['content'])); die;
			$dataArr = [
				'tags' => $_POST['tags']
			];
			if(count($_FILES) > 0){
				$config['encrypt_name'] = TRUE;
				$config['overwrite'] = FALSE;
				$config['allowed_types'] = 'jpg|jpeg|gif|png';
				$config['max_size'] = 8000;
				$config['upload_path'] = './uploads/article';
				$this->load->library('upload', $config);
				if ( $this->upload->do_upload('cover_image')){
					$uploaded = $this->upload->data();
					$dataArr['cover_image'] = $uploaded['file_name'];
				}
				if ( $this->upload->do_upload('image')){
					$uploaded = $this->upload->data();
					$dataArr['image'] = $uploaded['file_name'];
				}
			}			
			
			$dataArr['olympics'] = (isset($_POST['olympics']))?'1':'0';
			$dataArr['homepage'] = (isset($_POST['homepage']))?'1':'0';
			$dataArr['banner'] = (isset($_POST['banner']))?'1':'0';
			
			$name = trim($this->input->post('name'));	
			$seourl = trim($this->input->post('seourl'));	
			$_POST['seourl'] = ($seourl != '')?url_title($seourl, '-', TRUE):url_title($name, '-', TRUE);				
			if($_POST['meta_title']=='') $_POST['meta_title'] = $name;
			if($_POST['meta_description']=='') $_POST['meta_description'] = $_POST['description'];
			
			$article_date = '';
			if($_POST['article_date']!=''){
				$explode = @explode('/', $_POST['article_date']);
				$article_date = $explode[2].'-'.$explode[1].'-'.$explode[0];
			}
			if($objectid!=''){
				$conditionArr = ['_id'=>objectid((string)$objectid)];
				$adminCheck = $this->admin_model->get_all_details(ARTICLE, $conditionArr);
				if ($adminCheck->num_rows() > 0){
					if(count($_POST['polling_answers']) > 0){
						if(isset($adminCheck->row()->polling_answers) && count($adminCheck->row()->polling_answers) > 0){
							for($i=0;$i<count($_POST['polling_answers']);$i++){
								$votes = (isset($adminCheck->row()->polling_answers['answer-'.($i+1)]))?$adminCheck->row()->polling_answers['answer-'.($i+1)]['votes']:0;
								$dataArr['polling_answers']['answer-'.($i+1)] = ['name'=>$_POST['polling_answers'][$i], 'votes'=>(int)$votes];
							}
						} else {
							for($i=0;$i<count($_POST['polling_answers']);$i++){
								$dataArr['polling_answers']['answer-'.($i+1)] = ['name'=>$_POST['polling_answers'][$i], 'votes'=>0];
							}
						}
					}
					
					$dataArr = array_merge($dataArr, ['dateModified'=>(int)time(), 'article_date'=>(int)strtotime($article_date)]);
					$this->admin_model->commonInsertUpdate(ARTICLE, 'update', ['_id', 'objectid'], $dataArr , $conditionArr);
					$this->save_tags($_POST['tags']);
					$this->setErrorMessage('success','Article details saved!!!');
					redirect(ADMINURL.'/article-list');
				} else {
					$this->setErrorMessage('error','Something went wrong!!!');
					redirect(ADMINURL.'/article-list');
				}
			} else {
				if(!empty($_POST['polling_answers'])){
					for($i=0;$i<count($_POST['polling_answers']);$i++){
						$dataArr['polling_answers']['answer-'.($i+1)] = ['name'=>$_POST['polling_answers'][$i], 'votes'=>0];
					}
				}
				$dataArr = array_merge($dataArr, ['status'=>'1', 'dateAdded' => (int)time(), 'article_date'=>(int)strtotime($article_date)]);
				$this->admin_model->commonInsertUpdate(ARTICLE,'insert',['_id', 'objectid'], $dataArr, []);
				$this->save_tags($_POST['tags']);
				$this->setErrorMessage('success','Article details saved!!!');
				redirect(ADMINURL.'/article-list');
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
			$check = $this->admin_model->get_selected_fields(ARTICLE, $condition, ['cms_name']);
			
			if($check->num_rows()>0){
				$this->admin_model->update_details(ARTICLE, $dataArr, $condition);
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
			$check = $this->admin_model->get_all_counts(ARTICLE, $condition);
			if($check > 0){
				$this->admin_model->commonDelete(ARTICLE, $condition);
				echo 'success';
			} else {
				echo 'Something went wrong!!!';
			}
		}
	}
	
	public function bulk_status(){
		$ids = array_map(function($a){ return objectid($a); }, $_POST['ids']);
		$this->change_bulk_status(ARTICLE, $_POST['status'], $ids);
	}
	
	public function bulk_delete(){
		$ids = array_map(function($a){ return objectid($a); }, $_POST['ids']);
		$condition = ['_id'=>['$in'=>$ids]];
		$check = $this->admin_model->get_selected_fields(ARTICLE, $condition, ['_id']);
		if($check->num_rows()>0){
			$this->admin_model->commonDelete(ARTICLE, $condition);
			echo 'success';
		} else {
			echo 'Something went wrong!!!';
		}
	}
	
}
