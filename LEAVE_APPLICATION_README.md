# Leave Application Module (CS Form No. 6)

This module implements a complete leave application system based on Civil Service Form No. 6 (Revised 2020) for the DOGH DTR system.

## Features

### User Features
- **Leave Application Form**: Exact replica of CS Form No. 6 with all fields
- **My Leave Applications**: View all submitted leave applications with status tracking
- **Leave Credits Display**: View current leave credit balances
- **Print Form**: Print-ready CS Form No. 6 format

### Admin Features
- **Leave Management Dashboard**: Overview of all applications with statistics
- **Pending Applications**: Quick access to applications requiring action
- **Multi-step Approval Workflow**:
  1. **Certification** (HR): Verify and certify leave credits
  2. **Recommendation** (Supervisor): Recommend for approval/disapproval
  3. **Final Approval** (Medical Center Chief): Approve or disapprove
- **Leave Credits Management**: Initialize, view, and edit leave credits for all personnel
- **Reports**: Monthly summaries, by leave type, by department

## Installation

### 1. Run Database Migration

Execute the SQL migration file to create the required tables:

```sql
-- Run this in your MySQL database
SOURCE c:/xampp/htdocs/dogh_dtr/db/leave_applications_migration.sql;
```

Or import the file via phpMyAdmin:
1. Open phpMyAdmin
2. Select your database
3. Go to Import tab
4. Select `db/leave_applications_migration.sql`
5. Click Go

### 2. Initialize Leave Credits

After running the migration, initialize leave credits for all personnel:

1. Go to **Leave Management** > **Leave Credits**
2. Click **Initialize All Credits** button
3. This will create default leave credits (15 VL, 15 SL, etc.) for all active personnel

## Database Tables

### `leave_applications`
Main table storing all leave application data including:
- Applicant information
- Leave type and details
- Inclusive dates and working days
- Certification data
- Recommendation data
- Approval/disapproval data
- Status tracking

### `leave_credits`
Tracks leave credits per personnel per year:
- Vacation Leave (15 days default)
- Sick Leave (15 days default)
- Special Privilege Leave (3 days default)
- Solo Parent Leave (7 days default)
- VAWC Leave (10 days default)

### `leave_application_logs`
Audit trail for all actions on leave applications.

## Leave Types Supported

1. Vacation Leave
2. Mandatory/Forced Leave
3. Sick Leave
4. Maternity Leave
5. Paternity Leave
6. Special Privilege Leave
7. Solo Parent Leave
8. Study Leave
9. 10-Day VAWC Leave
10. Rehabilitation Privilege
11. Special Leave Benefits for Women
12. Special Emergency (Calamity) Leave
13. Adoption Leave
14. Others (with specification)

## Workflow

### For Employees:
1. Navigate to **My Leave Applications**
2. Click **New Leave Application**
3. Fill out the CS Form No. 6 fields
4. Save as Draft or Submit directly
5. Track application status

### For HR (Certification):
1. Go to **Leave Management** > **Pending Applications**
2. Click **Process** on a pending application
3. Review leave credits and click **Certify Leave Credits**

### For Supervisor (Recommendation):
1. View certified applications
2. Select **For Approval** or **For Disapproval**
3. Submit recommendation

### For Medical Center Chief (Final Approval):
1. View recommended applications
2. Specify days with pay/without pay
3. Click **Approve** or **Disapprove**

## Office/Department Options

The form includes 4 department options as specified:
- Medical
- Nursing
- Ancillary
- Administrative

## Salary Grade

Dropdown selection from SG 1 to SG 33.

## Navigation

- **My Leave Applications**: `leave_application/`
- **Leave Management Dashboard**: `leaves/`
- **Pending Applications**: `leaves/pending`
- **All Applications**: `leaves/all`
- **Leave Credits**: `leaves/credits`
- **Reports**: `leaves/reports`

## Files Created

### Controllers
- `application/controllers/LeaveApplication.php` - User-facing controller
- `application/controllers/Leaves.php` - Admin controller

### Models
- `application/models/LeaveModel.php` - Database operations

### Views (User)
- `application/views/leave/application_form.php` - CS Form No. 6 form
- `application/views/leave/my_leaves.php` - User's leave list
- `application/views/leave/view_application.php` - View single application
- `application/views/leave/print_form.php` - Print-ready form

### Views (Admin)
- `application/views/leaves/dashboard.php` - Admin dashboard
- `application/views/leaves/list.php` - All applications list
- `application/views/leaves/pending.php` - Pending applications
- `application/views/leaves/view.php` - View/process application
- `application/views/leaves/credits.php` - Leave credits management
- `application/views/leaves/reports.php` - Reports and analytics

### Database
- `db/leave_applications_migration.sql` - Database migration

## Configuration

The LeaveModel is auto-loaded via `application/config/autoload.php`.

## Logos

The form uses the following logos:
- DOH Logo (left): `assets/img/doh_logo1.png`
- DOGH Logo (right): `assets/img/dogh_logo.png`

## Notes

- Personnel must be linked to user accounts via email for the leave application to work
- Leave credits are automatically deducted upon approval
- All actions are logged for audit trail purposes
- The form is print-ready and matches the official CS Form No. 6 format
