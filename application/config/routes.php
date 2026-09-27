<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Each route below replaces one direct .php file access in the legacy app, which had no router and dispatched on the session role inside
// index.php. That role dispatch is now HomeController/index.

$route['default_controller'] = 'HomeController';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// Authentication, reachable while signed out.
$route['auth'] = 'AuthController/index';
$route['auth/login'] = 'AuthController/login';
$route['auth/logout'] = 'AuthController/logout';
$route['auth/change_password'] = 'AuthController/change_password';

// Staff area: admin and moderator. Guards live in each controller.
$route['dashboard'] = 'DashboardController/index';
$route['candidates'] = 'CandidateController/index';
$route['candidates/save'] = 'CandidateController/save';
$route['candidates/delete'] = 'CandidateController/delete';
$route['reports'] = 'ReportController/index';
$route['reports/election/(:num)'] = 'ReportController/index/$1';

// Admin only.
$route['users'] = 'UserController/index';
$route['users/save'] = 'UserController/save';
$route['users/delete'] = 'UserController/delete';
$route['elections'] = 'ElectionController/index';
$route['elections/save'] = 'ElectionController/save';
$route['elections/close'] = 'ElectionController/close';
$route['elections/delete'] = 'ElectionController/delete';

// Student area. election ids moved from ?election_id= to URI segments.
$route['student'] = 'StudentController/index';
$route['student/ballot/(:num)'] = 'StudentController/ballot/$1';
$route['student/ballot/submit'] = 'StudentController/submit_ballot';
$route['student/receipt/(:num)'] = 'StudentController/receipt/$1';
