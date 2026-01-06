<?php
defined('BASEPATH') or exit('No direct script access allowed');

$route['default_controller'] = 'userauth';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// User Type Selection and User Portal Routes
$route['userauth'] = 'userauth/index';
$route['userauth/login'] = 'userauth/login';
$route['userauth/register'] = 'userauth/register';
$route['userauth/logout'] = 'userauth/logout';
$route['userauth/check_status'] = 'userauth/check_status';

// User Portal Routes
$route['user/dashboard'] = 'user/dashboard';
$route['user/dtr'] = 'user/dtr';
$route['user/profile'] = 'user/profile';
$route['user/update_profile'] = 'user/update_profile';
$route['user/change_password'] = 'user/change_password';
$route['user/notifications'] = 'user/notifications';
$route['user/mark_notification_read/(:num)'] = 'user/mark_notification_read/$1';
$route['user/attendance_history'] = 'user/attendance_history';

// User Leave Application Routes
$route['user/leave_applications'] = 'user/leave_applications';
$route['user/create_leave'] = 'user/create_leave';
$route['user/save_leave'] = 'user/save_leave';
$route['user/view_leave/(:num)'] = 'user/view_leave/$1';
$route['user/edit_leave/(:num)'] = 'user/edit_leave/$1';
$route['user/submit_leave/(:num)'] = 'user/submit_leave/$1';
$route['user/cancel_leave/(:num)'] = 'user/cancel_leave/$1';
$route['user/print_leave/(:num)'] = 'user/print_leave/$1';

// User Management Routes (Admin)
$route['usermanagement'] = 'usermanagement/index';
$route['usermanagement/pending'] = 'usermanagement/pending';
$route['usermanagement/approved'] = 'usermanagement/approved';
$route['usermanagement/blocked'] = 'usermanagement/blocked';
$route['usermanagement/view/(:num)'] = 'usermanagement/view/$1';
$route['usermanagement/add'] = 'usermanagement/add';
$route['usermanagement/edit/(:num)'] = 'usermanagement/edit/$1';
$route['usermanagement/approve/(:num)'] = 'usermanagement/approve/$1';
$route['usermanagement/disapprove/(:num)'] = 'usermanagement/disapprove/$1';
$route['usermanagement/block/(:num)'] = 'usermanagement/block/$1';
$route['usermanagement/unblock/(:num)'] = 'usermanagement/unblock/$1';
$route['usermanagement/delete/(:num)'] = 'usermanagement/delete/$1';

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
$route['attendance/bulk_generate_dtr'] = 'attendance/bulk_generate_dtr';
$route['attendance/get_personnel_by_department'] = 'attendance/get_personnel_by_department';

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

// Department Management Routes
$route['settings/departments'] = 'settings/departments';
$route['settings/assign_personnel'] = 'settings/assign_personnel';
$route['settings/bulk_assign_personnel'] = 'settings/bulk_assign_personnel';
$route['settings/search_personnel'] = 'settings/search_personnel';
$route['settings/create_department'] = 'settings/create_department';
$route['settings/update_department'] = 'settings/update_department';
$route['settings/delete_department'] = 'settings/delete_department';
$route['settings/get_department_stats'] = 'settings/get_department_stats';
$route['settings/get_department_personnel'] = 'settings/get_department_personnel';

// Schedule Compliance Report Routes
$route['reports/schedule_compliance'] = 'AttendanceCompliance/index';
$route['reports/schedule_compliance/get_compliance_data'] = 'AttendanceCompliance/get_compliance_data';
$route['reports/schedule_compliance/get_employee_details'] = 'AttendanceCompliance/get_employee_details';
$route['reports/schedule_compliance/get_department_summary'] = 'AttendanceCompliance/get_department_summary';
$route['reports/schedule_compliance/get_overall_stats'] = 'AttendanceCompliance/get_overall_stats';
$route['reports/schedule_compliance/export_csv'] = 'AttendanceCompliance/export_csv';
$route['reports/schedule_compliance/print_report'] = 'AttendanceCompliance/print_report';

// Bulk Print Complete Schedule Personnel Routes
$route['reports/schedule_compliance/bulk_print_complete'] = 'AttendanceCompliance/bulk_print_complete';
$route['reports/schedule_compliance/bulk_print_complete_print'] = 'AttendanceCompliance/bulk_print_complete_print';
$route['reports/schedule_compliance/get_complete_personnel'] = 'AttendanceCompliance/get_complete_personnel';
$route['reports/schedule_compliance/bulk_print_dtr'] = 'AttendanceCompliance/bulk_print_dtr';
