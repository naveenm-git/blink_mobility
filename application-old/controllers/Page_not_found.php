<?php 
class Page_not_found extends MY_Controller {
    public function __construct() {
        parent::__construct(); 
    } 

    public function index() { 
		$this->data['page_heading'] = 'Page Not Found';
		$this->load->view('page_not_found',$this->data);
    } 
} 
?> 