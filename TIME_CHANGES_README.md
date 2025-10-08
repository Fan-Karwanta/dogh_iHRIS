# Time Changes Feature - Implementation Guide

## Overview
The "Failure to Clock and Time Changes" page allows administrators to efficiently manage and correct personnel biometric time records with individual and bulk editing capabilities.

## Access
Navigate to: **Time Changes** menu item in the sidebar (with clock icon)

## Features

### 1. Personnel Selection Page
- **URL**: `/admin/timechanges`
- Searchable table of all personnel
- Click "View Time Records" to access individual personnel's biometric data

### 2. Personnel Biometrics Page
- **URL**: `/timechanges/personnel_biometrics/{bio_id}`
- Displays all biometric time records for selected personnel
- Default: 30 rows per page
- Month filter for date range selection
- Breadcrumb navigation for easy return

### 3. Individual Edit
- Click edit icon (pencil) on any row
- Modify time entries (AM In/Out, PM In/Out)
- Adjust undertime hours/minutes
- Add reason for changes (tracked in audit trail)
- Auto-calculation of undertime based on standard schedule

### 4. Bulk Edit
- Select multiple records using checkboxes
- "Select All" checkbox for convenience
- Visual alert shows number of selected records
- Update same field across all selected records at once
- Requires reason for audit trail
- Confirmation before applying changes

### 5. Add New Record
- Click "Add Time Record" button
- Manually create time entry for any date
- Useful for missed clock-ins or corrections

## Files Created

### Controllers
- `application/controllers/TimeChanges.php`

### Models
- `application/models/TimeChangesModel.php`

### Views
- `application/views/time_changes/index.php` - Personnel selection page
- `application/views/time_changes/personnel_biometrics.php` - Individual personnel records
- `application/views/time_changes/modal.php` - Add, Edit, and Bulk Edit modals

### JavaScript
- `assets/js/timeChanges.js` - All client-side functionality

### Configuration
- Updated `application/config/routes.php` with new routes
- Updated `application/views/templates/sidebar.php` with menu item
- Updated `application/views/templates/footer.php` to include timeChanges.js

## Routes
```php
$route['admin/timechanges'] = 'timechanges/index';
$route['timechanges/personnel_biometrics/(:num)'] = 'timechanges/personnel_biometrics/$1';
```

## Security & Audit Trail
- All changes are logged in the audit trail
- Requires authentication (redirects to login if not authenticated)
- Reason field for accountability
- CSRF protection via CodeIgniter

## Technical Details

### DataTables Configuration
- Server-side processing for efficient data loading
- Default page length: 30 rows
- Sortable columns (except checkboxes and actions)
- Search functionality built-in

### Undertime Calculation
- Standard schedule: 8:00 AM - 5:00 PM (8 hours)
- Morning: 8:00 AM - 12:00 PM (4 hours)
- Lunch: 12:00 PM - 1:00 PM (excluded)
- Afternoon: 1:00 PM - 5:00 PM (4 hours)
- Auto-calculated when time fields are modified
- Can be manually overridden if needed

### Bulk Edit Fields
- Morning In
- Morning Out
- Afternoon In
- Afternoon Out

## Usage Tips

1. **Quick Corrections**: Use individual edit for single record fixes
2. **Mass Updates**: Use bulk edit when multiple people have the same issue (e.g., system downtime)
3. **Always Document**: Provide clear reasons for changes in the audit trail
4. **Verify Changes**: Check the updated records after bulk edits
5. **Month Filter**: Use to focus on specific time periods

## Troubleshooting

### No Data Showing
- Check if personnel has biometric records in the database
- Verify the month filter is set correctly
- Check browser console for JavaScript errors

### Bulk Edit Not Working
- Ensure records are selected (checkboxes checked)
- Verify all required fields are filled
- Check network tab for AJAX errors

### Select2 Not Loading
- Ensure jQuery is loaded before timeChanges.js
- Check that Select2 library is included in footer.php

## Future Enhancements (Optional)
- Export selected records to CSV
- Date range filter instead of just month
- Bulk delete functionality
- Copy time from one date to another
- Template-based bulk updates (e.g., "Standard 8-5 shift")
