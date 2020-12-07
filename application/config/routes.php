<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'admin/admin';
$route['404_override'] = 'page_not_found';
$route['translate_uri_dashes'] = FALSE;

# ADMIN PANEL
$route['do-login'] = 'admin/admin/do_login';
$route['logout'] = 'admin/admin/logout';
$route[ADMINURL] = 'admin/admin';
$route[ADMINURL.'/forgot-password'] = 'admin/admin/forgot_password_form';
$route[ADMINURL.'/submit-forgot-password'] = 'admin/admin/forgot_password';
$route[ADMINURL.'/verify-code'] = 'admin/admin/verification_code';
$route[ADMINURL.'/dashboard'] = 'admin/admin/dashboard';
$route[ADMINURL.'/change-password'] = 'admin/admin/admin_change_password';
$route[ADMINURL.'/settings'] = 'admin/admin/admin_settings';
$route[ADMINURL.'/email-template-list'] = 'admin/newsletter/listing';
$route[ADMINURL.'/email-template-add'] = 'admin/newsletter/add_edit';
$route[ADMINURL.'/email-template-edit/(:any)'] = 'admin/newsletter/add_edit/$1';
$route[ADMINURL.'/email-template-view/(:any)'] = 'admin/newsletter/view/$1';
$route[ADMINURL.'/sms-template-list'] = 'admin/newsletter/sms_listing';
$route[ADMINURL.'/sms-template-add'] = 'admin/newsletter/sms_add_edit';
$route[ADMINURL.'/sms-template-edit/(:any)'] = 'admin/newsletter/sms_add_edit/$1';
$route[ADMINURL.'/sms-template-view/(:any)'] = 'admin/newsletter/sms_view/$1';
$modules = ['achiever', 'article', 'category', 'cms', 'faq', 'subscription', 'payments', 'users', 'sms', 'station', 'make', 'model', 'vehicle', 'parking', 'partner'];
foreach($modules as $module){
	$route[ADMINURL.'/'.$module.'-list'] = 'admin/'.$module.'/listing';
	$route[ADMINURL.'/'.$module.'-add'] = 'admin/'.$module.'/add_edit';
	$route[ADMINURL.'/'.$module.'-edit/(:any)'] = 'admin/'.$module.'/add_edit/$1';
	$route[ADMINURL.'/'.$module.'-view/(:any)'] = 'admin/'.$module.'/view/$1';
}

# USER PANEL
$route[USERURL.'/(:any)/dashboard'] = 'users/common/dashboard/$1';
$route[USERURL.'/(:any)/account'] = 'users/common/account/$1';
$route[USERURL.'/(:any)/account/edit'] = 'users/common/account_edit/$1';
$route[USERURL.'/(:any)/account/save'] = 'users/common/account_save/$1';
$route[USERURL.'/(:any)/documents'] = 'users/documents/listing/$1';
$route[USERURL.'/(:any)/documents/add'] = 'users/documents/add/$1';
$route[USERURL.'/(:any)/documents/save'] = 'users/documents/save/$1';
$route[USERURL.'/(:any)/documents/edit/(:any)'] = 'users/documents/edit/$1/$1';
$route[USERURL.'/(:any)/documents/view/(:any)'] = 'users/documents/view/$1/$1';
$route[USERURL.'/(:any)/documents/status/(:any)'] = 'users/documents/change_status/$1/$1';
$route[USERURL.'/(:any)/subscription'] = 'users/subscription/listing/$1';
$route[USERURL.'/(:any)/subscription/get'] = 'users/subscription/listing_ajax/$1';
$route[USERURL.'/(:any)/subscription/view/(:any)'] = 'users/subscription/view/$1/$1';
$route[USERURL.'/(:any)/subscription/status'] = 'users/subscription/status/$1';
$route[USERURL.'/(:any)/favorite-address'] = 'users/favorite_address/listing/$1';
$route[USERURL.'/(:any)/favorite-address/get'] = 'users/favorite_address/listing_ajax/$1';
$route[USERURL.'/(:any)/favorite-address/add'] = 'users/favorite_address/add_edit/$1';
$route[USERURL.'/(:any)/favorite-address/edit/(:any)'] = 'users/favorite_address/add_edit/$1/$1';
$route[USERURL.'/(:any)/favorite-address/view/(:any)'] = 'users/favorite_address/view/$1/$1';
$route[USERURL.'/(:any)/favorite-address/save'] = 'users/favorite_address/save/$1';
$route[USERURL.'/(:any)/favorite-address/remove'] = 'users/favorite_address/remove/$1';
$route[USERURL.'/(:any)/favorite-address/status'] = 'users/favorite_address/status/$1';

# STATION PANEL
$route[STATIONURL.'/(:any)/dashboard'] = 'station/common/dashboard/$1';
$route[STATIONURL.'/(:any)/parking'] = 'station/parking/listing/$1';
$route[STATIONURL.'/(:any)/parking/get'] = 'station/parking/listing_ajax/$1';
$route[STATIONURL.'/(:any)/parking/add'] = 'station/parking/add_edit/$1';
$route[STATIONURL.'/(:any)/parking/edit/(:any)'] = 'station/parking/add_edit/$1/$1';
$route[STATIONURL.'/(:any)/parking/view/(:any)'] = 'station/parking/view/$1/$1';
$route[STATIONURL.'/(:any)/parking/save'] = 'station/parking/save/$1';

# API WEB SERVICES
$route[API_VERSION.'api/user/login'] = API_VERSION.'user/login';
$route[API_VERSION.'api/user/forgot-password'] = API_VERSION.'user/forgot_password';
$route[API_VERSION.'api/user/membership/signup'] = API_VERSION.'user/signup';
$route[API_VERSION.'api/user/profile'] = API_VERSION.'user/profile';
$route[API_VERSION.'api/user/verification/code'] = API_VERSION.'user/verification_code';
$route[API_VERSION.'api/user/file/upload'] = API_VERSION.'user/upload_file';
$route[API_VERSION.'api/user/save/membership/type'] = API_VERSION.'user/save_membership_type';
$route[API_VERSION.'api/user/save/documents'] = API_VERSION.'user/save_verification_documents';
$route[API_VERSION.'api/user/save/payment/method'] = API_VERSION.'user/save_payment_method';
$route[API_VERSION.'api/user/save/preferences'] = API_VERSION.'user/save_preferences';
$route[API_VERSION.'api/user/save/favorite/address'] = API_VERSION.'user/save_favorite_address';
$route[API_VERSION.'api/user/get/favorite/address'] = API_VERSION.'user/get_favorite_address';
$route[API_VERSION.'api/booking/get/nearest/stations'] = API_VERSION.'booking/get_nearest_station';
$route[API_VERSION.'api/booking/reserve/car'] = API_VERSION.'booking/reserve_car';
$route[API_VERSION.'api/booking/reserve/parking'] = API_VERSION.'booking/reserve_parking';
$route[API_VERSION.'api/booking/rental/start'] = API_VERSION.'booking/start_rental';
$route[API_VERSION.'api/booking/rental/ongoing'] = API_VERSION.'booking/ongoing_rental';
$route[API_VERSION.'api/booking/save/pre/inspection'] = API_VERSION.'booking/save_pre_inspection';
$route[API_VERSION.'api/booking/save/final/inspection'] = API_VERSION.'booking/save_final_inspection';
