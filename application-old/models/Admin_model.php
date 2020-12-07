<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 
 * This model contains all db functions related to admin management
 * @author Teamtweaks
 *
 */
class Admin_model extends My_Model {	
	/**
    * 
    * This function save the driver details in a file
    */
   public function saveAdminSettings(){
		$getAdminSettingsDetails = $this->getAdminSettings();
		$config = '<?php ';
		foreach($getAdminSettingsDetails->row() as $key => $val){
			if($key!='admin_password' && $key!='privileges' && $key!='smtp' && $key!='country' && $key!='countryId' && $key!='currency'){
				if(is_array($val)){
					foreach($val as $ikey => $ival){
						$value = addslashes($ival);
						$config .= "\n\$config['$ikey'] = '$value'; ";
					}
				}else{				
					$value = addslashes($val);
					$config .= "\n\$config['$key'] = '$value'; ";
				}
			}
		}
		$config .= "\n\$config['base_url'] = '".base_url()."';\n ";
		$config .= ' ?>';
		$file = 'app-config/settings.php';
		file_put_contents($file, $config);
   }
	 
	 public function lookup_details($collection='', $from='', $condition=[], $LField='', $FField='', $projection=[]){
		 $array = [
			[ '$lookup' => 
				[	'from' => $from,
					'localField' => $LField,
					'foreignField' => $FField,
					'as' => 'parent'
				]
			],
			[ '$project' => ['parent'=>1]],
			[ '$unwind' => '$parent' ]
		];
		
		$check = $this->mongo_db->aggregate($collection, $array);		
		return $check;
	}
}