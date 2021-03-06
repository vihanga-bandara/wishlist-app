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
$route['default_controller'] = 'Home';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;


$route['API'] = 'Rest_server';

// Item API Routes
switch ($_SERVER['REQUEST_METHOD'])
{
	case 'GET':
		// fetch single item
		$route['api/list/item/(:num)'] = 'api/wishlist/fetch_item';
		// fetch all items
		$route['api/list/item'] = 'api/wishlist/fetch_all_items';
		//fetch all items with userid
		$route['api/list/item/(:num)'] = 'api/wishlist/fetch_all_items_user';
		break;
	case 'POST':
		// add single item
		$route['api/list/item'] = 'api/wishlist/add_item';
		break;
	case 'PUT':
		//update a single item
		$route['api/list/item/(:num)'] = 'api/wishlist/item';
	case 'DELETE':
		//delete a single item
		$route['api/list/item/(:num)'] = 'api/wishlist/item';
}

//User API Routes

//Registration
$route['api/user/register'] = 'api/user/register_user';

//Login
$route['api/user/login'] = 'api/user/login_user';

//Logout
$route['api/user/logout'] = 'api/user/logout';

//get single user details
$route['api/user/(:num)'] = 'api/user/single_user';

//update user list name and description
$route['api/user/update/(:num)'] = 'api/user/create_list';


