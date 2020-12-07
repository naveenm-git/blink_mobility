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

/*** ADMIN ***/
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
$modules = ['achiever', 'article', 'category', 'cms', 'faq', 'subscription', 'payments', 'users', 'sms', 'station', 'make', 'model', 'vehicle', 'parking'];
foreach($modules as $module){
	$route[ADMINURL.'/'.$module.'-list'] = 'admin/'.$module.'/listing';
	$route[ADMINURL.'/'.$module.'-add'] = 'admin/'.$module.'/add_edit';
	$route[ADMINURL.'/'.$module.'-edit/(:any)'] = 'admin/'.$module.'/add_edit/$1';
	$route[ADMINURL.'/'.$module.'-view/(:any)'] = 'admin/'.$module.'/view/$1';
}
/*** ADMIN ***/

/*** API ***/
$route[API_VERSION.'api/user/login'] = API_VERSION.'user/login';
$route[API_VERSION.'api/user/register'] = API_VERSION.'user/register';
$route[API_VERSION.'api/user/profile'] = API_VERSION.'user/profile';
$route[API_VERSION.'api/user/forgot-password'] = API_VERSION.'user/forgot_password';
/*** API ***/