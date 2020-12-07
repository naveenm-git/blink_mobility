<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Parking extends MY_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('admin_model');
		$this->data['_station_id'] = $this->uri->segment(2);
	}
		
	public function listing(){
		if ($this->checkLogin('A') != '' && $this->data['_station_id']!=''){
			$station = $this->admin_model->get_selected_fields(STATION, ['_id'=>MongoID($this->data['_station_id'])]);
			$this->data['heading'] = $station->row()->name. ' Parking';
			$this->load->view('station/parking/listing', $this->data);
		} else {
			$this->setErrorMessage('error','Login required!!!');
			redirect(ADMINURL);
		}
	}
   
	public function listing_ajax(){
      if ($this->checkLogin('A') != '' && $this->data['_station_id']!=''){
			$columns = ['name', 'parking_type', 'order', 'light_color', 'cable_status', 'status'];
			$search = $_REQUEST['search']['value'];
			$filters = [
				'order'=>$search,
				'name'=>$search
			];
         
			$condition = ['station_id' => $this->data['_station_id']];
         if($_REQUEST['columns'][1]['search']['value']){
				$condition['parking_type'] = $_REQUEST['columns'][1]['search']['value'];
			}
         if($_REQUEST['columns'][3]['search']['value']){
				$condition['light_color'] = $_REQUEST['columns'][3]['search']['value'];
			}
         if($_REQUEST['columns'][4]['search']['value']){
				$condition['cable_status'] = $_REQUEST['columns'][4]['search']['value'];
			}
         if($_REQUEST['columns'][5]['search']['value']){
				$condition['parking_status'] = $_REQUEST['columns'][5]['search']['value'];
			}
         
			$recordsTotal = $this->admin_model->get_all_counts(PARKING, $condition);
			$length = $_REQUEST['length'];
			$start = $_REQUEST['start'];
			$column = $columns[$_REQUEST['order'][0]['column']]; 
			$type = $_REQUEST['order'][0]['dir'];
			if(!empty($search)) {
				$recordsFiltered = $this->admin_model->get_all_counts(PARKING, $condition, $filters);
				$result = $this->admin_model->get_all_details(PARKING, $condition, [$column=>$type], $length, $start, $filters);
			} else {
				$recordsFiltered = $this->admin_model->get_all_counts(PARKING, $condition, []);
				$result = $this->admin_model->get_all_details(PARKING, $condition, [$column=>$type], $length, $start);
			}
			$dataArr = array(); $i = $_REQUEST['start']+1;
			foreach($result->result_array() as $res){
				$nestedData=array(); 
				$nestedData[] = ($res['name']!='')?$res['name']:'-';
				$nestedData[] = ($res['parking_type']!='')?$this->data['parking_type'][$res['parking_type']]:'-';
				$nestedData[] = ($res['order']!='')?'# '.sprintf("%02d", $res['order']):'-';
            
            if(in_array($res['light_color'], ['available', 'ready_for_rental'])){
               $lightcolor = 'success';
            } else if(in_array($res['light_color'], ['reserved'])){
               $lightcolor = 'warning';
            } else {
               $lightcolor = 'danger';
            }
               
            
				$nestedData[] = ($res['light_color']!='')?'<p class="light-color bg-'.$lightcolor.'">'.$this->data['light_color'][$res['light_color']].'</p>':'-';
				$nestedData[] = ($res['cable_status']!='')?$this->data['cable_status'][$res['cable_status']]:'-';
            
				$nestedData[] = ($res['parking_status']!='')?'<span class="text-'.(in_array($res['parking_status'], ['operational'])?'success':'danger').'">'.$this->data['parking_status'][$res['parking_status']].'</span>':'-';
				
            $action = '<a class="action-a" title="View" href="'.base_url().STATIONURL.'/'.$this->data['_station_id'].'/parking/view/'.(string)$res['_id'].'"><i class="fa fa-eye"></i></a>';
				$action .= '&nbsp;<a class="action-a" title="Edit" href="'.base_url().STATIONURL.'/'.$this->data['_station_id'].'/parking/edit/'.(string)$res['_id'].'" title="Edit"><i class="fa fa-pencil"></i></a>';
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
		if ($this->checkLogin('A')!='' && $this->data['_station_id']!=''){
			$form_mode = FALSE;
			$this->data['result'] = [];
         $_id = $this->uri->segment(5);
         
         $vehicleCondition = ['status'=>'1', 'station_id'=>$this->data['_station_id'], '$or'=>[['parking_id'=>['$exists'=>false]], ['parking_id'=>['$eq'=>'']]]];
         if($_id!=''){
            $vehicleCondition['$or'][] = ['parking_id'=>$_id];
				$condition = ['_id' => MongoID((string)$_id)];
				$result = $this->admin_model->get_all_details(PARKING, $condition);
				if($result->num_rows() > 0){
					$this->data['result'] = $result->result_array();
					$form_mode = TRUE;
				} else {
					$this->setErrorMessage('error','Something went wrong!!!');
					redirect(ADMINURL.'/station-list');
				}
			}
         
         $this->data['vehicles'] = $this->admin_model->get_all_details(VEHICLE, $vehicleCondition);
			$this->data['form_mode'] = $form_mode;
			$this->data['heading'] = ($form_mode)?"Edit Parking":"Add Parking";
			$this->load->view('station/parking/add_edit',$this->data);
		} else {
			$this->setErrorMessage('error','Login required!!!');
			redirect(ADMINURL);
		}
	}
   
	public function save(){
		if ($this->checkLogin('A') != '' && $this->data['_station_id']!=''){
			$_station_id = $this->data['_station_id'];
			$objectid = $this->input->post('objectid');
			$vehicle_id = $this->input->post('vehicle_id');
			$status = $this->input->post('status');
			$status = ($status!='')?'1':'0';
         
			if($_station_id!=''){
            if($objectid!=''){
               $condition = ['_id'=>MongoID((string)$objectid)];
               $check = $this->admin_model->get_all_details(PARKING, $condition);
               if ($check->num_rows() > 0){
                  $dataArr['status'] = $status;
                  $dataArr['modified_at'] = MongoDATE(time());
                  $this->admin_model->commonInsertUpdate(PARKING, 'update', ['_id', 'objectid', 'status'], $dataArr, $condition);
                  $this->admin_model->update_details(VEHICLE, ['parking_id'=>''], ['parking_id'=>$objectid]);
                  $this->admin_model->update_details(VEHICLE, ['parking_id'=>$objectid], ['_id'=>MongoID($vehicle_id)]);
                  $this->setErrorMessage('success','Address saved!!!');
               } else {
                  $this->setErrorMessage('error','Something went wrong!!!');
               }
            } else {
               $dataArr['status'] = $status;
               $dataArr['created_at'] = MongoDATE(time());
               $this->admin_model->commonInsertUpdate(PARKING,'insert',['_id', 'objectid', 'status'], $dataArr,[]);
               $objectid = $this->admin_model->get_last_insert_id();
               $this->admin_model->update_details(VEHICLE, ['parking_id'=>''], ['parking_id'=>$objectid]);
               $this->admin_model->update_details(VEHICLE, ['parking_id'=>$objectid], ['_id'=>MongoID($vehicle_id)]);
               $this->setErrorMessage('success','Address saved!!!');
            }
			} else {
				$this->setErrorMessage('error','User ID Empty!!!');
			}
         redirect(STATIONURL.'/'.$_station_id.'/parking');
		} else {
			$this->setErrorMessage('error','Login required!!!');
			redirect(ADMINURL);
		}
	}
	
	public function view(){
		if ($this->checkLogin('A') != '' && $this->data['_station_id']!=''){
         $_id = $this->uri->segment(5);
			$this->data['heading'] = 'Parking View';
			$this->data['result'] = $this->admin_model->get_all_details(PARKING, ['_id'=>MongoID($_id)])->result_array();;
			$this->load->view('station/parking/view', $this->data);
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
			$check = $this->admin_model->get_selected_fields(PARKING, $condition, ['cms_name']);
			
			if($check->num_rows()>0){
				$this->admin_model->update_details(PARKING, $dataArr, $condition);
				echo 'success';
			} else {
				echo 'Something went wrong!!!';
			}
		}
	}

	public function remove(){
		if ($this->checkLogin('A') == '' && $this->data['_station_id']!=''){
			echo 'Something went wrong!!!';
		} else {
			$id = $this->input->post('id');
			$condition = ['_id'=>MongoID($id)];
			$check = $this->admin_model->get_all_counts(PARKING, $condition);
			if($check > 0){
				$this->admin_model->commonDelete(PARKING, $condition);
				echo 'success';
			} else {
				echo 'Something went wrong!!!';
			}
		}
	}
}
