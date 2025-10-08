<?php
defined('BASEPATH') or exit('No direct script access allowed');

$route['default_controller'] = 'auth';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

$route['admin'] = 'auth/login';
$route['admin/users'] = 'auth/users';
$route['admin/user_profile'] = 'auth/user_profile';

$route['admin/dashboard'] = 'dashboard/index';

$route['admin/personnel'] = 'personnel/index';
$route['admin/personnel_attendace/(:num)'] = 'personnel/personnel_attendance/$1';
$route['create_personnel'] = 'personnel/create';

$route['admin/attendance'] = 'attendance/index';
$route['admin/generate_dtr'] = 'attendance/generate_dtr';
$route['admin/generate_dtr/(:num)'] = 'attendance/generate_dtr/$1';

$route['admin/biometrics'] = 'biometrics/index';
$route['admin/generate_bio'] = 'biometrics/generate_bioreport';

$route['admin/timechanges'] = 'timechanges/index';
$route['timechanges/personnel_biometrics/(:num)'] = 'timechanges/personnel_biometrics/$1';

$route['admin/audit_trail'] = 'AuditTrail/index';
$route['admin/audit_trail/get_audit_data'] = 'AuditTrail/get_audit_data';
$route['admin/audit_trail/get_personnel_audit_data'] = 'AuditTrail/get_personnel_audit_data';
$route['admin/audit_trail/personnel/(:num)'] = 'AuditTrail/personnel/$1';
$route['admin/audit_trail/personnel_by_email/(.+)'] = 'AuditTrail/personnel_by_email/$1';
$route['admin/audit_trail/personnel_by_bio_id/(:num)'] = 'AuditTrail/personnel_by_bio_id/$1';

$route['admin/audit_trail/get_audit_details'] = 'AuditTrail/get_audit_details';
$route['admin/audit_trail/export_csv'] = 'AuditTrail/export_csv';

$route['admin/audit_reports'] = 'auditreport/index';
$route['admin/audit_reports/personnel_analysis'] = 'auditreport/personnel_analysis';
