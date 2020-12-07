<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sample extends MY_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('admin_model');
      // $this->mongo_db->ensure_index(STATION, array('location'=>'2dsphere'));
	}
   
   public function test() {
      $distance = 10;
      
		$distanceMultiplier = 0.001;
		if($distance_unit == 'km'){
			$distanceMultiplier = 0.001;
		} else if($distance_unit == 'mi'){
			$distanceMultiplier = 0.000621371;
		} else if($distance_unit == 'm'){
			$distanceMultiplier = 1;
		}
      
      $option = [
         [
            '$geoNear' => [
               "near" => [ "type" => "Point", "coordinates" => [12.909955, 80.094859] ],
               "spherical" => true,
               "maxDistance" => (intval($distance) * $multiply),
               "includeLocs" => 'location',
               "distanceField" => "distance",
               "distanceMultiplier" => $distanceMultiplier,
               'num' => intval(10000),
               "query" => ['status' => '1']
            ],
         ],
         [
            '$project' => [
               'name' => 1,
               'street' => 1,
               'zipcode' => 1,
               'city' => 1,
               'location' => 1
            ]
         ],
         ['$limit' => 5]
      ];
		#echo'<pre>';print_r($matchArr); die;
		$res = $this->mongo_db->aggregate(STATION, $option);
		
		echo'<pre>'; print_r($res); die;
   }
		
	public function getdetail($collection){
		$result = $this->admin_model->get_all_details($collection, []);
		echo '<pre>'; print_r($result->result()); die; 
	}
	
	public function updatedetail(){
		// $this->admin_model->update_details(VEHICLE, ['remaining_charge'=>75], ['_id'=>MongoID('5fc4ee6b4c7f334cd6212222')]);
	}
	
	public function trash(){
		// $this->admin_model->commonDelete(RENTALS, []);
		echo 'Hi, Collection(s) successfully trashed!';
	}	
	
	public function importdata(){
		$this->load->library('csvimport');
		$file_data = $this->csvimport->get_array('uploads/posts.csv');
		// echo '<pre>'; print_r($file_data); die; 
		foreach($file_data as $post) {
			$dataArr = [
				'category' => '5dfb651a4c7f336e07538be2',
				'name' => $post['Title'],
				'author' => '5e4247424c7f33263a39a1c2',
				'homepage' => '0',
				'article_date' => (int)strtotime($post['Date']),
				'content' => $post['Conte'],
				'description' => '',
				'seourl' => $post['Permalink'],
				'image' => $post['Featured'],
				'status' => '1',
				'dateAdded' => (int)strtotime($post['Date'])
			];
			// $this->admin_model->simple_insert(ARTICLE, $dataArr);
		}
		echo '<pre>'; print_r($dataArr); die; 
		
	}
	
	public function import_users(){ 
		$this->load->library(['excel', 'zip']);
		$obj = PHPExcel_IOFactory::createReader('Excel2007');
		$obj->setReadDataOnly(true);
		PHPExcel_Settings::setZipClass(PHPExcel_Settings::PCLZIP);
		$object = PHPExcel_IOFactory::load('uploads/BlueLA_ActiveCustomers_20201007.xlsx');
		$data = [];
		echo '<pre>';
		date_default_timezone_set('UTC');
		foreach($object->getWorksheetIterator() as $worksheet) {
			$highestRow = $worksheet->getHighestRow();
			$highestColumn = $worksheet->getHighestColumn();
			for($row=2; $row<=$highestRow; $row++){
				$col1 = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
				$col2 = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
				$col3 = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
				$col4 = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
				$col5 = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
				$col7 = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
				$col8 = $worksheet->getCellByColumnAndRow(7, $row)->getValue();
				$col9 = $worksheet->getCellByColumnAndRow(8, $row)->getValue();
				// $col10 = $worksheet->getCellByColumnAndRow(9, $row)->getValue();
            
            if ($worksheet->getCellByColumnAndRow(9, $row)->getValue() instanceof PHPExcel_RichText)
				$col10 = $worksheet->getCellByColumnAndRow(9, $row)->getValue()->getPlainText();
				else 
				$col10 = $worksheet->getCellByColumnAndRow(9, $row)->getValue();
         
				$col11 = $worksheet->getCellByColumnAndRow(10, $row)->getValue()->getPlainText();
				$col12 = $worksheet->getCellByColumnAndRow(11, $row)->getValue();
				$col13 = $worksheet->getCellByColumnAndRow(12, $row)->getValue();
				$col14 = $worksheet->getCellByColumnAndRow(13, $row)->getValue();
				$col15 = $worksheet->getCellByColumnAndRow(14, $row)->getValue();
				$col16 = $worksheet->getCellByColumnAndRow(15, $row)->getValue();
				$col17 = $worksheet->getCellByColumnAndRow(16, $row)->getValue();
				$col18 = $worksheet->getCellByColumnAndRow(17, $row)->getValue();
				$col19 = $worksheet->getCellByColumnAndRow(18, $row)->getValue();
				$col20 = $worksheet->getCellByColumnAndRow(19, $row)->getValue();
				$col21 = $worksheet->getCellByColumnAndRow(20, $row)->getValue();
				
				$dataArr = array(
					'fleet' => $col1,
					'customer_id' => $col2,
					'subscription_id' => $col3,
					'offer' => $col4,
					'subscription_status' => $col5,
					'civility' => $col6,
					'first_name' => $col7,
					'last_name' => $col8,
					'username' => $col9,
					'dob' => $col10,
					'email' => $col11,
					'mobile' => $col12,
					'marketing_id' => $col13,
					'subscription_date' => $col14,
					'activation_date' => $col15,
					'expiry_date' => $col16,
					'street' => $col17,
					'building' => $col18,
					'zipcode' => $col19,
					'city' => $col20,
					'third_party_badge' => $col21
				);
				print_r($dataArr); 
				// $this->admin_model->simple_insert(USERS, $dataArr);
				
			}
		}
		/* print_r($data); 
		die; */
		// $this->excel_import_model->insert($data);
		echo 'Articles Imported successfully';
	}
	
	public function import_interviews(){ die;
		$this->load->library(['excel', 'zip']);
		$obj = PHPExcel_IOFactory::createReader('Excel2007');
		$obj->setReadDataOnly(true);
		PHPExcel_Settings::setZipClass(PHPExcel_Settings::PCLZIP);
		$object = PHPExcel_IOFactory::load('uploads/Interviews.xlsx');
		$data = [];
		echo '<pre>';
		date_default_timezone_set('UTC');
		foreach($object->getWorksheetIterator() as $worksheet) {
			$highestRow = $worksheet->getHighestRow();
			$highestColumn = $worksheet->getHighestColumn();
			for($row=2; $row<=$highestRow; $row++){
				if ($worksheet->getCellByColumnAndRow(0, $row)->getValue() instanceof PHPExcel_RichText)
				$name = $worksheet->getCellByColumnAndRow(0, $row)->getValue()->getPlainText();
				else 
				$name = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
				if(trim($name) == '')break;
				
				$article_date = date('Y-m-d H:i:s', PHPExcel_Shared_Date::ExcelToPHP($worksheet->getCellByColumnAndRow(1, $row)->getValue()));
				
				if ($worksheet->getCellByColumnAndRow(2, $row)->getValue() instanceof PHPExcel_RichText)
				$content = $worksheet->getCellByColumnAndRow(2, $row)->getValue()->getPlainText();
				else
				$content = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
				
				if ($worksheet->getCellByColumnAndRow(3, $row)->getValue() instanceof PHPExcel_RichText)
				$description = $worksheet->getCellByColumnAndRow(3, $row)->getValue()->getPlainText();
				else 
				$description = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
				
				$seourl = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
				
				$image = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
				
				$dataArr = array(
					'article_date' => (int)strtotime($article_date),
					'seourl' => $seourl,
					'dateAdded' => (int)strtotime($article_date)
				);
				print_r($dataArr); 
				// $this->admin_model->simple_insert(ARTICLE, $dataArr);
				$this->admin_model->update_details(ARTICLE, $dataArr, ['category' => '5dfb65144c7f336e026d65f2', 'name' => $name]);
			}
		}
		/* print_r($data); 
		die; */
		// $this->excel_import_model->insert($data);
		echo 'Interviews Imported successfully';
	}
	
	public function import_news(){ die;
		$this->load->library(['excel', 'zip']);
		$obj = PHPExcel_IOFactory::createReader('Excel2007');
		$obj->setReadDataOnly(true);
		PHPExcel_Settings::setZipClass(PHPExcel_Settings::PCLZIP);
		$object = PHPExcel_IOFactory::load('uploads/News.xlsx');
		$data = [];
		echo '<pre>';
		date_default_timezone_set('UTC');
		foreach($object->getWorksheetIterator() as $worksheet) {
			$highestRow = $worksheet->getHighestRow();
			$highestColumn = $worksheet->getHighestColumn();
			for($row=2; $row<=$highestRow; $row++){
				if ($worksheet->getCellByColumnAndRow(0, $row)->getValue() instanceof PHPExcel_RichText)
				$name = $worksheet->getCellByColumnAndRow(0, $row)->getValue()->getPlainText();
				else 
				$name = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
				if(trim($name) == '')break;
				
				$article_date = date('Y-m-d H:i:s', PHPExcel_Shared_Date::ExcelToPHP($worksheet->getCellByColumnAndRow(1, $row)->getValue()));
				
				if ($worksheet->getCellByColumnAndRow(2, $row)->getValue() instanceof PHPExcel_RichText)
				$content = $worksheet->getCellByColumnAndRow(2, $row)->getValue()->getPlainText();
				else
				$content = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
				
				if ($worksheet->getCellByColumnAndRow(3, $row)->getValue() instanceof PHPExcel_RichText)
				$description = $worksheet->getCellByColumnAndRow(3, $row)->getValue()->getPlainText();
				else 
				$description = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
				
				$seourl = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
				
				$image = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
				
				$dataArr = array(
					'article_date' => (int)strtotime($article_date),
					'seourl' => $seourl,
					'dateAdded' => (int)strtotime($article_date)
				);
				print_r($dataArr); 
				// $this->admin_model->simple_insert(ARTICLE, $dataArr);
				$this->admin_model->update_details(ARTICLE, $dataArr, ['category' => '5dfb650c4c7f336dec2b3662', 'name' => $name]);
			}
		}
		/* print_r($data); 
		die; */
		// $this->excel_import_model->insert($data);
		echo 'News Imported successfully';
	}
	
	public function import_news_1(){ die;
		$this->load->library(['excel', 'zip']);
		$obj = PHPExcel_IOFactory::createReader('Excel2007');
		$obj->setReadDataOnly(true);
		PHPExcel_Settings::setZipClass(PHPExcel_Settings::PCLZIP);
		$object = PHPExcel_IOFactory::load('uploads/News-05-03-2020-1.xlsx');
		$data = [];
		echo '<pre>';
		date_default_timezone_set('UTC');
		foreach($object->getWorksheetIterator() as $worksheet) {
			$highestRow = $worksheet->getHighestRow();
			$highestColumn = $worksheet->getHighestColumn();
			for($row=2; $row<=$highestRow; $row++){
				if ($worksheet->getCellByColumnAndRow(0, $row)->getValue() instanceof PHPExcel_RichText)
				$name = $worksheet->getCellByColumnAndRow(0, $row)->getValue()->getPlainText();
				else 
				$name = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
				if(trim($name) == '')break;
				
				$article_date = date('Y-m-d H:i:s', PHPExcel_Shared_Date::ExcelToPHP($worksheet->getCellByColumnAndRow(1, $row)->getValue()));
				
				if ($worksheet->getCellByColumnAndRow(2, $row)->getValue() instanceof PHPExcel_RichText)
				$content = $worksheet->getCellByColumnAndRow(2, $row)->getValue()->getPlainText();
				else
				$content = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
				
				if ($worksheet->getCellByColumnAndRow(3, $row)->getValue() instanceof PHPExcel_RichText)
				$description = $worksheet->getCellByColumnAndRow(3, $row)->getValue()->getPlainText();
				else 
				$description = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
				
				$seourl = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
				
				$image = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
				
				$dataArr = array(
					'article_date' => (int) strtotime($article_date),
					'seourl' => $seourl,
					'dateAdded' => (int) strtotime($article_date)
				);
				print_r($dataArr); 
				// $this->admin_model->simple_insert(ARTICLE, $dataArr);
				// $this->admin_model->update_details(ARTICLE, $dataArr, ['category' => '5dfb650c4c7f336dec2b3662', 'name' => $name]);
			}
		}
		/* print_r($data); 
		die; */
		// $this->excel_import_model->insert($data);
		echo 'News Imported successfully';
	}
	
	public function email_templates(){
		$templates = [
			[	
				'name'=>"Organizer Credentials",
				'email_subject'=>"Thank you for joining with us!",
				'sender_name'=>"Fistosports",
				'sender_email'=>"info@fistosports.com",
				'content'=>"<p>Hi \{$organizer_name\},</p><p>Password: \{$password\}</p><p>Your will now login to your account, Click here to <a href='\{$login_url\}>login</a></p>",
				'status'=>"1",
				'template'=>"organizer-registration",
				'type'=>"custom"
			],[	
				'name'=>"Organizer Registration Successful",
				'email_subject'=>"Thank you for joining with us!",
				'sender_name'=>"Fistosports",
				'sender_email'=>"info@fistosports.com",
				'content'=>"<p>Welcome \{$organizer_name\},</p><p>You are successfully registered to fistosports.</p>",
				'status'=>"1",
				'template'=>"organizer-registered",
				'type'=>"custom"
			],[	
				'name'=>"Organizer Verification Code",
				'email_subject'=>"Thank you for joining with us!",
				'sender_name'=>"Fistosports",
				'sender_email'=>"info@fistosports.com",
				'content'=>"<p>Hi \{$organizer_name\},</p><p>Your \{$siteTitle\} password change request has been accepted,</p><p>Here is your verification code : \{$verification_code\}</p>",
				'status'=>"1",
				'template'=>"organizer-forgot-password",
				'type'=>"custom"
			],[	
				'name'=>"Organizer Password Changed",
				'email_subject'=>"Thank you for joining with us!",
				'sender_name'=>"Fistosports",
				'sender_email'=>"info@fistosports.com",
				'content'=>"<p>Hi \{$organizer_name\},</p><p>Your password has been changed successfully.</p>",
				'status'=>"1",
				'template'=>"organizer-password-changed",
				'type'=>"custom"
			],[	
				'name'=>"Scorer Credentials",
				'email_subject'=>"Thank you for joining with us!",
				'sender_name'=>"Fistosports",
				'sender_email'=>"info@fistosports.com",
				'content'=>"<p>Hi \{$user_name\},</p><p>Password: \{$password\}</p><p>Your will now login to your account, Click here to <a href='\{$login_url\}'>login</a></p>",
				'status'=>"1",
				'template'=>"scorer-registration",
				'type'=>"custom"
			],[	
				'name'=>"Scorer Verification Code",
				'email_subject'=>"Thank you for joining with us!",
				'sender_name'=>"Fistosports",
				'sender_email'=>"info@fistosports.com",
				'content'=>"<p>Hi \{$user_name\},</p><p>Your \{$siteTitle\} password change request has been accepted,</p><p>Here is your verification code : \{$verification_code\}</p>",
				'status'=>"1",
				'template'=>"scorer-forgot-password",
				'type'=>"custom"
			],[	
				'name'=>"Scorer Password Changed",
				'email_subject'=>"Thank you for joining with us!",
				'sender_name'=>"Fistosports",
				'sender_email'=>"info@fistosports.com",
				'content'=>"<p>Hi \{$user_name\},</p><p>Your password has been changed successfully.</p>",
				'status'=>"1",
				'template'=>"scorer-password-changed",
				'type'=>"custom"
			]
		];
		
		foreach($templates as $template){
			$template['status'] = '1'; 
			$template['type'] = 'custom'; 
			$template['dateAdded'] = time();
			$check = $this->admin_model->get_all_counts(NEWSLETTER, ['template'=>$template['template']]);
			if($check == 0){
				$this->admin_model->simple_insert(NEWSLETTER, $template);
			}
		}
		echo 'Templates Configured!!!'; exit;
	}

	public function import_xml(){

		$this->load->library('csvimport');
		$file_data = $this->csvimport->get_array('uploads/posts.csv');
		// echo '<pre>'; print_r($file_data); die; 
		$featuredArr = [];
		foreach($file_data as $post) {
			$featuredArr[str_replace('/', '', str_replace('https://fistosports.com/', '', $post['Permalink']))] = $post['Featured'];
		}
		// echo '<pre>'; print_r($featuredArr); die; 

		// $xml=simplexml_load_file("uploads/fisto.xml");
		// $xml=$this->xml2array($xml);
		// $postsArr = $xml['channel']['item'];
		// foreach($postsArr as $posts){
		// 	echo '<pre>';print_r($this->xml2array($posts));
		// }

		$xml = simplexml_load_file("uploads/fisto.xml");
		$posts = array();

		foreach($xml->channel->item as $item){
			$content = $item->children('http://purl.org/rss/1.0/modules/content/');
			
			$posts[] = array(
				"title"=>(string)$item->title,
				"link"=>str_replace('/', '', str_replace("https://fistosports.com/", "", (string)$item->link)),
				"content"=>(string)$content->encoded,
				"pubDate"=>str_replace(" +0000", "", (string)$item->pubDate)
			);
		}

		foreach($posts as $post){
			$dataArr = [
				'category' => '5dfb651a4c7f336e07538be2',
			 	'name' => $post['title'],
			 	'author' => '5e43dbe055af3f5fce12efa2',
			 	'homepage' => '0',
				'article_date' => (int)strtotime($post['pubDate']),
				'content' => $post['content'],
			 	'description' => '',
			 	'seourl' => $post['link'],
			 	'image' => $featuredArr[$post['link']],
			 	'status' => '1',
				'dateAdded' => (int)strtotime($post['pubDate']),
				'import_from' => 'xml'
			];
			$count = $this->admin_model->get_all_counts(ARTICLE, ['seourl'=>$post['link']]);
			if($count == 0){
				// echo '/---Inserted----/<br>';
				// $this->admin_model->simple_insert(ARTICLE, $dataArr);
			} else {
				// $this->admin_model->update_details(ARTICLE, $dataArr, ['seourl'=>$post['link']]);
				// echo $featuredArr[$post['link']].'/---Already Present----/<br>';
			}
			echo "https://fistosports.com/".$post['link'].'<br>';
		}
	}

	public function import_meta(){

		$this->load->library('csvimport');
		$file_data = $this->csvimport->get_array('uploads/posts_meta.csv');
		// echo '<pre>'; print_r($file_data); die; 
		$featuredArr = [];
		foreach($file_data as $post) {
			$featuredArr[str_replace('/', '', str_replace('https://fistosports.com/', '', $post['Permalink']))] = $post['_yoast_wpseo_metadesc'];
		}
		// echo '<pre>'; print_r($featuredArr); die; 

		// $xml=simplexml_load_file("uploads/fisto.xml");
		// $xml=$this->xml2array($xml);
		// $postsArr = $xml['channel']['item'];
		// foreach($postsArr as $posts){
		// 	echo '<pre>';print_r($this->xml2array($posts));
		// }

		$xml = simplexml_load_file("uploads/fisto.xml");
		$posts = array();

		foreach($xml->channel->item as $item){
			$content = $item->children('http://purl.org/rss/1.0/modules/content/');
			
			$posts[] = array(
				"title"=>(string)$item->title,
				"link"=>str_replace('/', '', str_replace("https://fistosports.com/", "", (string)$item->link)),
				"content"=>(string)$content->encoded,
				"pubDate"=>str_replace(" +0000", "", (string)$item->pubDate)
			);
		}

		foreach($posts as $post){
			$dataArr = [
				'meta_title' => $post['title'],
				'meta_description' => $featuredArr[$post['link']],
			];
			$count = $this->admin_model->get_all_counts(ARTICLE, ['seourl'=>$post['link']]);
			if($count == 0){
				// echo '/---Inserted----/<br>';
				// $this->admin_model->simple_insert(ARTICLE, $dataArr);
			} else {
				$this->admin_model->update_details(ARTICLE, $dataArr, ['seourl'=>$post['link']]);
				// echo $featuredArr[$post['link']].'/---Already Present----/<br>';
				echo '<pre>'; print_r($dataArr);
			}
			
		}
	}

	public function xml2array ( $xmlObject, $out = array () ){
		foreach ( (array) $xmlObject as $index => $node )
			$out[$index] = ( is_object ( $node ) ) ? $this->xml2array ( $node ) : $node;

		return $out;
	}
}
