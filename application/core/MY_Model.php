<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
* 
* This model contains all common db related functions
* @author Casperon
*
**/

class My_Model extends CI_Model {

	/**
	* 
	* This function connect the database and load the functions from CI_Model
	*
	**/
    public function __construct() {
        parent::__construct();
    }
	
	/**
	* 
	* Simple function for inserting data into a collection
	* @param String $collection
	* @param Array $data
	*
	**/
    public function simple_insert($collection = '', $data = '') {
        return $this->mongo_db->insert($collection, $data);
    }

	/**
	*
	* This functions updates the collection details using @param 
	* @param String $collection
	* @param Array $data
	* @param Array $condition
	*
	**/
    public function update_details($collection = '', $data = '', $condition = '') {
        if (!empty($collection)) {
            $this->mongo_db->where($condition);
            $this->mongo_db->set($data);
            return $this->mongo_db->update_all($collection);
        }
    }

	/**
	* 
	* This function deletes the document based upon the condition
	* @param String $collection
	* @param Array $condition
	**/
    public function commonDelete($collection = '', $condition = '') {
        $this->mongo_db->where($condition);
        return $this->mongo_db->delete_all($collection);
    }

	/**
	*
	* This functions returns all the collection details using @param 
	* @param String $collection
	* @param Array $sortArr
	* @param Array $condition
	* @param Numeric $limit
	* @param Numeric $offset
	* @param Array $likearr
	*
	**/
    public function get_all_details($collection, $condition = array(), $sortArr = array(), $limit = FALSE, $offset = FALSE, $likearr = array()) {
        $this->mongo_db->select();
        if (!empty($condition)) {
            $this->mongo_db->where($condition);
        }
        if (!empty($likearr)) {
            if (count($likearr) > 0) {
                foreach ($likearr as $key => $val) {
                    $this->mongo_db->or_like($key, $val);
                }
            } else {
                $this->mongo_db->like($key, $val);
            }
        }
				
        if ($sortArr != '' && is_array($sortArr) && !empty($sortArr)) {
            $this->mongo_db->order_by($sortArr);
        }
        if ($limit !== FALSE && is_numeric($limit) && $offset !== FALSE && is_numeric($offset)) {
            $this->mongo_db->limit($limit);
            $this->mongo_db->offset($offset);
        } 
        $res = $this->mongo_db->get($collection);
				// echo '<pre>'; print_r($res->result_array()); echo '</pre>';
        return $res;
    }
	
	/**
	*
	* This functions returns all the collection details using @param 
	* @param String $collection
	* @param Array $sortArr
	* @param Array $fields
	* @param Array $condition
	* @param Numeric $limit
	* @param Numeric $offset
	* @param Array $likearr
	*
	**/
    public function get_selected_fields($collection, $condition = array(), $fields = array(), $sortArr = array(), $limit = FALSE, $offset = FALSE, $likearr = array()) {
        $this->mongo_db->select($fields);
        if (!empty($condition)) {
            $this->mongo_db->where($condition);
        }
        if (!empty($likearr)) {
            if (count($likearr) > 0) {
                foreach ($likearr as $key => $val) {
                    $this->mongo_db->or_like($key, $val);
                }
            } else {
                $this->mongo_db->like($key, $val);
            }
        }
        if ($sortArr != '' && is_array($sortArr) && !empty($sortArr)) {
            $this->mongo_db->order_by($sortArr);
        }
        if ($limit !== FALSE && is_numeric($limit) && $offset !== FALSE && is_numeric($offset)) {
            $this->mongo_db->limit($limit);
            $this->mongo_db->offset($offset);
        } 
        $res = $this->mongo_db->get($collection);
        return $res;
    }
	
	/**
	* 
	* Common select base on the where in conditions
	*
	* @param $condition = array('field','where_in Array');
	**/
    public function get_selected_fields_where_in($collection, $condition = array(), $fields = array(), $sortArr = array(), $limit = FALSE, $offset = FALSE, $likearr = array()) {
        $this->mongo_db->select($fields);

        if (!empty($condition)) {
            $field = $condition[0];
            $data = $condition[1];
            $condition = $condition[2];

            if (!empty($condition)) {
                $this->mongo_db->where($condition);
            }
            if ($field != '' && !empty($data)) {
                if ($field == '_id') {
                    $datanew = $data;
                    $data = array();
                    $k = 0;
                    foreach ($datanew as $key => $value) {
                        $data[$k] = MongoID($value);
                        $k++;
                    }
                }
                $newdata = array_values($data);
                $this->mongo_db->where_in($field, $newdata);
            }
        }

        if (!empty($likearr)) {
            if (count($likearr) > 0) {
                foreach ($likearr as $key => $val) {
                    $this->mongo_db->or_like($key, $val);
                }
            } else {
                $this->mongo_db->like($key, $val);
            }
        }
        if ($sortArr != '' && is_array($sortArr) && !empty($sortArr)) {
            $this->mongo_db->order_by($sortArr);
        }
        if ($limit !== FALSE && is_numeric($limit) && $offset !== FALSE && is_numeric($offset)) {
            $this->mongo_db->limit($limit);
            $this->mongo_db->offset($offset);
        } 
        $res = $this->mongo_db->get($collection);
        
        return $res;
    }

	/**
	* 
	* This function do all insert and edit operations
	* @param String $collection	   -->	Collection name
	* @param String $mode		   -->	Insert, Update
	* @param Array $excludeArr	   -->   To avoid post inputs
	* @param Array $dataArr         -->   Add additional inputs with posted inputs
	* @param Array $condition      -->  Applicable only for updates
	*
	**/
    public function commonInsertUpdate($collection = '', $mode = '', $excludeArr = '', $dataArr = '', $condition = '') {
        $inputArr = array();
        foreach ($this->input->post() as $key => $val) {
            if (!in_array($key, $excludeArr)) {
                /* if (is_numeric($val)) {
                    $inputArr[$key] = floatval($val);
                } else */ if (is_object($val)) {
                    $inputArr[$key] = MongoID((string)$val);
                } else {
                    $inputArr[$key] = $val;
                }
            }
        }
        $finalArr = array_merge($inputArr, $dataArr);
		
        if ($mode == 'insert') {
            return $this->mongo_db->insert($collection, $finalArr);
        } else if ($mode == 'update') {
            $this->mongo_db->where($condition);
            $this->mongo_db->set($finalArr);
            return $this->mongo_db->update($collection);
        }
    }

	/**
	*
	* Common function for executing mongoDB query
	* @param String $Query	->	mongoDB Query
	*
	**/
    public function ExecuteQuery($Query) {
        $res = $this->mongo_db->command($Query);
        return $res;
    }

	/**
	*
	* Common function for get last inserted _id
	*
	**/
    public function get_last_insert_id() {
        $last_insert_id = $this->mongo_db->insert_id();
        return $last_insert_id;
    }

	/**
	* 
	* This function change the status of records and delete the records
	* @param String $collection
	* @param String $field
	* 
	**/
    public function activeInactiveCommon($collection = '', $field = '', $delete = TRUE) {
        $data = $_POST['checkbox_id'];
        $mode = $this->input->post('statusMode');
        for ($i = 0; $i <= count($data); $i++) {
            if ($data[$i] == 'on') {
                unset($data[$i]);
            }
        }
        if ($field == '_id') {
            $datanew = $data;
            $data = array();
            $k = 0;
            foreach ($datanew as $key => $value) {
                $data[$k] = MongoID($value);
                $k++;
            }
        }
        $newdata = array_values($data);
        $this->mongo_db->where_in($field, $newdata);
        if (strtolower($mode) == 'delete') {
            if ($delete === TRUE) {
                $this->mongo_db->delete_all($collection);
            } else if ($delete === FALSE) {
                $statusArr = array('status' => 'Deleted');
                $this->mongo_db->set($statusArr);
                $this->mongo_db->update_all($collection);
            }
        } else {
            $statusArr = array('status' => $mode);
            $this->mongo_db->set($statusArr);
            $this->mongo_db->update_all($collection);
        }
    }

   

	

	/**
	* 
	* This function return the count of particular records
	* @param String $collection
	* @param Array $condition
	* @param Array $filterarr
	*
	**/
    public function get_all_counts($collection = '', $condition = array(), $filterarr = array(), $limit = FALSE, $offset = FALSE) {
        if (!empty($condition)) {
            $this->mongo_db->where($condition);
        }
        if (!empty($filterarr)) {
            if (count($filterarr) > 0) {
                foreach ($filterarr as $key => $val) {
                    $this->mongo_db->or_like($key, $val);
                }
            } else {
                $this->mongo_db->like($key, $val);
            }
        }
        if ($limit !== FALSE && is_numeric($limit) && $offset !== FALSE && is_numeric($offset)) {
            $this->mongo_db->limit($limit);
            $this->mongo_db->offset($offset);
        }
        return $this->mongo_db->count($collection);
    }

	/**
	* 
	* This function push the data in to a field
	* @param String $collection
	* @param Array $condition
	* @param Array/String $pushdata
	*
	**/
    public function simple_push($collection = '', $condition = array(), $pushdata = array()) {
        if (!empty($condition)) {
            $this->mongo_db->where($condition);
        }
        $this->mongo_db->push($pushdata);
        return $this->mongo_db->update_all($collection);
    }

	/**
	* 
	* This function removes the data in a field
	* @param String $collection
	* @param Array $condition
	* @param Array/String $pushdata
	*
	**/
    public function simple_pull($collection = '', $condition = array(), $pulldata, $value = array()) {
        if (!empty($condition)) {
            $this->mongo_db->where($condition);
        }
        if (is_array($pulldata)) {
            foreach ($pulldata as $field => $value) {
                $this->mongo_db->pull($field, $value);
            }
        } elseif (is_string($pulldata)) {
            $this->mongo_db->pull($pulldata, $value);
        }
        return $this->mongo_db->update_all($collection);
    }

	/**
	* 
	* This function add to set data in a field
	* @param String $collection
	* @param Array $condition
	* @param Array $setdata
	*
	**/
    public function set_to_field($collection = '', $condition = array(), $setdata = array()) {
        if (!empty($condition)) {
            $this->mongo_db->where($condition);
        }
        if (is_array($setdata)) {
            $this->mongo_db->set($setdata);
        }
        return $this->mongo_db->update_all($collection);
    }

	/**
	* 
	* This function return the admin settings details
	*
	**/
    public function getAdminSettings() {
        $this->mongo_db->select();
        $this->mongo_db->where(array('admin_id' => '1'));
        $result = $this->mongo_db->get(ADMIN);
        unset($result->row()->admin_password);
        return $result;
    }
	
	/**
	* 
	* Common Email send funciton 
	* @param Array $email_vaues
	* @return 1
	*
	**/
    public function common_email_send($email_vaues = array()) {
        $server_ip = $this->input->ip_address();
        $mail_id = 'set';
				
				if ($mail_id != '') {
            $config['smtp_user']=$this->config->item('smtp_email');
            $config['smtp_pass']=$this->config->item('smtp_password');
            $config['smtp_host']=$this->config->item('smtp_host');
            $config['smtp_port']=$this->config->item('smtp_port');
            // Set SMTP Configuration
            if ($config['smtp_user'] != '' && $config['smtp_pass'] != '') {
                $emailConfig = array(
                    'protocol' => 'smtp',
                    'smtp_host' => $config['smtp_host'],
                    'smtp_port' => $config['smtp_port'],
                    'smtp_user' => $config['smtp_user'],
                    'smtp_pass' => $config['smtp_pass'],
                    'auth' => true,
                );
            }
					
            // Set your email information
            $from = array('email' => $email_vaues['from_mail_id'], 'name' => $email_vaues['mail_name']);
            $to = $email_vaues['to_mail_id'];
            $subject = $email_vaues['subject_message'];
            $message = stripslashes($email_vaues['body_messages']);
            // Load CodeIgniter Email library   ##  iso-8859-1
            if ($config['smtp_user'] != '' && $config['smtp_pass'] != '') {
                $this->load->library('email', $emailConfig);
						
            } else {
							 
                $headers = 'MIME-Version: 1.0' . "\r\n";
                $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
                // Additional headers
                $headers .= 'From: ' . $email_vaues['mail_name'] . ' <' . $email_vaues['from_mail_id'] . '>' . "\r\n";
                if (array_key_exists('cc_mail_id', $email_vaues)) {
                    if ($email_vaues['cc_mail_id'] != '') {
                        $headers .= 'Cc: ' . $email_vaues['cc_mail_id'] . "\r\n";
                    }
                }
                if (array_key_exists('bcc_mail_id', $email_vaues)) {
                    if ($email_vaues['bcc_mail_id'] != '') {
                        $headers .= 'Bcc: ' . $email_vaues['bcc_mail_id'] . "\r\n";
                    }
                }
                // Mail it
                mail($email_vaues['to_mail_id'], trim(stripslashes($email_vaues['subject_message'])), trim(stripslashes($email_vaues['body_messages'])), $headers);
                return 1;
            }
					$this->email->initialize($emailConfig);
            // Sometimes you have to set the new line character for better result
            $this->email->set_newline("\r\n");
            // Set email preferences
            $this->email->set_mailtype($email_vaues['mail_type']);
            $this->email->from($from['email'], $from['name']);
            $this->email->to($to);
            if (array_key_exists('cc_mail_id', $email_vaues)) {
                if ($email_vaues['cc_mail_id'] != '') {
                    $this->email->cc($email_vaues['cc_mail_id']);
                }
            }
            if (array_key_exists('bcc_mail_id', $email_vaues)) {
                if ($email_vaues['bcc_mail_id'] != '') {
                    $this->email->bcc($email_vaues['bcc_mail_id']);
                }
            }
            $this->email->subject($subject);
            $this->email->message($message);
            if (!empty($email_vaues['attachments'])) {
                foreach ($email_vaues['attachments'] as $attach) {
                    if ($attach != '') {
                        $this->email->attach($attach);
                    }
                }
            }
            // Ready to send email and check whether the email was successfully sent;
						
            if (!$this->email->send()) {
								// echo '<pre>'; print_r($this->email->print_debugger()); die;
                $headers = 'MIME-Version: 1.0' . "\r\n";
                $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
                // Additional headers
                $headers .= 'From: ' . $email_vaues['mail_name'] . ' <' . $email_vaues['from_mail_id'] . '>' . "\r\n";
                if (array_key_exists('cc_mail_id', $email_vaues)) {
                    if ($email_vaues['cc_mail_id'] != '') {
                        $headers .= 'Cc: ' . $email_vaues['cc_mail_id'] . "\r\n";
                    }
                }
                if (array_key_exists('bcc_mail_id', $email_vaues)) {
                    if ($email_vaues['bcc_mail_id'] != '') {
                        $headers .= 'Bcc: ' . $email_vaues['bcc_mail_id'] . "\r\n";
                    }
                }

                // Mail it
                mail($email_vaues['to_mail_id'], trim(stripslashes($email_vaues['subject_message'])), trim(stripslashes($email_vaues['body_messages'])), $headers);

                return 1;
            } else {
                // Show success notification or other things here
                //echo 'Success to send email';
                return 1;
            }
        } else {
            return 1;
        }
    }
    
    
	public function get_location_details($address=''){
		$returnArr['status'] = '0';
      $returnArr['response'] = '';
      if($address != ''){
			$google_maps_api_key = $this->data['google_maps_api_key'];
			$origin_city='';
			$origin_country='';
			$origin_lat='';
			$origin_lon='';
			$encoded_address = str_replace(" ", "+", $address);
			$json = file_get_contents("https://maps.google.com/maps/api/geocode/json?address=".$encoded_address."&sensor=false".$google_maps_api_key);
			$json = json_decode($json);
			$origin_lat = $json->results[0]->geometry->location->lat;
			$origin_lon = $json->results[0]->geometry->location->lng;
			
			if($origin_lat != '' && $origin_lon != ''){
            $returnArr['status'] = '1';
            $location_record = [
               'lat'=> floatval($origin_lat),
               'lng'=> floatval($origin_lon)
            ];
            $returnArr['response'] = $location_record;
			} else {
				$returnArr['message'] = "No location found.";
			}
		} else {
			$returnArr['message'] = "Some Parameter Missing";
		}		
		return $returnArr;
	}
   
   public function get_rental_id() {
		$digits = 6;
		$rental_id = str_pad(rand(0, pow(10, $digits)-1), $digits, '0', STR_PAD_LEFT);		
      $condition = array('rental_id' => $rental_id);
      $this->mongo_db->select(array('rental_id'));
      $this->mongo_db->where($condition);
      $res = $this->mongo_db->get(RENTALS);
      if ($res->num_rows() > 0) {
         $check = 0;
         $rental_id = str_pad(rand(0, pow(10, $digits)-1), $digits, '0', STR_PAD_LEFT);
         while ($check == 0) {
            $condition = array('rental_id' => $rental_id);
            $duplicate_id = $this->get_all_details(RENTALS, $condition);
            if ($duplicate_id->num_rows() > 0) {
               $rental_id = str_pad(rand(0, pow(10, $digits)-1), $digits, '0', STR_PAD_LEFT);
             } else {
               $check = 1;
            }
         }
      }
      return $rental_id;
   }

}
?>