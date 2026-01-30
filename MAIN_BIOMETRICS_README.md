# Main Department Biometrics Import Feature

This feature provides a **separate import system** for biometric data from the Main department's hardware, which uses a different CSV format than the Dialysis and Admin departments.

## Overview

The existing "Import Biometrics" feature works with Dialysis and Admin department CSV files. This new feature handles the Main department's biometric hardware which exports data in a different format.

## CSV Format (Main Department)

The Main department biometric hardware exports CSV files with the following columns:

| Column | Field | Description |
|--------|-------|-------------|
| 0 | No. | Row number (ignored) |
| 1 | Staff Code | 8-digit code (e.g., `00000001`) - **last 4 digits used, leading zeros removed** |
| 2 | Name | Employee name (ignored - matched via Bio ID) |
| 3 | Department | Department name (stored for reference) |
| 4 | User ID | Duplicate of Staff Code (ignored) |
| 5 | Week | Day of week (stored for reference) |
| 6 | Date | Date in `MM/DD/YYYY` format |
| 7 | Time | Time in `HH:MM:SS` format |
| 8 | Machine ID | Biometric machine ID (ignored) |
| 9 | Remark1 | Empty (ignored) |
| 10 | Remark2 | `IN` or `OUT` punch type |

### Example CSV Row:
```
1,00000001,Mark Redulla,IHOMP,0000000001,Saturday,11/01/2025,05:32:00,1,,IN
```

## Features

### 1. Raw Log Storage
- Stores all biometric punches in a separate `main_biometrics_logs` table
- Preserves original data including department, day of week, and IN/OUT remarks
- Prevents duplicate imports using unique constraint on (staff_code, log_date, log_time, remark)

### 2. Automatic Personnel Matching
- Staff Code is converted to Bio ID by removing leading zeros
- Example: `00000099` → Bio ID `99`
- Automatically links to personnel records with matching `bio_id`
- Tracks matched vs unmatched records for review

### 3. Import History Tracking
- Each import creates a batch record with:
  - Total records in CSV
  - Successfully imported records
  - Matched to personnel count
  - Unmatched count
  - Duplicate records skipped

### 4. Attendance Sync
- Option to automatically sync imported logs to the main `biometrics` attendance table
- Intelligently assigns IN/OUT punches to AM/PM time slots based on time of day
- Supports "Fill" mode (only fill empty slots) or "Override" mode (replace existing)

### 5. Unmatched Staff Management
- Dashboard shows staff codes that don't have matching personnel records
- Quick link to add Bio ID to personnel records
- Re-match feature to link logs after personnel are added

## Database Tables

### `main_biometrics_logs`
Stores raw biometric punch data:
- `staff_code` - Bio ID (integer, leading zeros removed)
- `personnel_id` - Linked personnel ID (if matched)
- `department` - Department from CSV
- `week_day` - Day of week from CSV
- `log_date` - Date of punch
- `log_time` - Time of punch
- `remark` - IN or OUT
- `import_batch` - Batch identifier for tracking

### `main_biometrics_imports`
Tracks import history:
- `batch_id` - Unique batch identifier
- `filename` - Original CSV filename
- `total_records` - Total records in CSV
- `imported_records` - Successfully imported
- `matched_personnel` - Records matched to personnel
- `unmatched_personnel` - Records not matched
- `duplicate_skipped` - Duplicate records skipped

## Installation

1. Run the database migration:
```sql
-- Execute the SQL file
SOURCE db/main_biometrics_migration.sql;
```

Or run manually in phpMyAdmin:
```sql
-- See db/main_biometrics_migration.sql for full script
```

2. The feature is automatically available after migration at:
   - **URL**: `/mainbiometrics`
   - **Sidebar**: Biometrics → Main Department

## Usage

### Importing CSV Files

1. Navigate to **Biometrics → Main Department** in the sidebar
2. Click **Select CSV File** and choose the exported CSV from Main department
3. Optionally check **Auto-sync to Attendance Records** to create/update attendance entries
4. If syncing, optionally check **Override existing attendance records**
5. Click **Import CSV**

### Syncing to Attendance

If you didn't sync during import, you can manually sync later:

1. Go to the Main Biometrics dashboard
2. Under **Actions → Sync to Attendance**
3. Optionally select a specific date
4. Check **Override** if you want to replace existing entries
5. Click **Sync Now**

### Handling Unmatched Staff

When staff codes don't match any personnel:

1. Review the **Unmatched Staff Codes** table on the dashboard
2. Click the **+** button to add the Bio ID to a personnel record
3. After adding personnel, click **Re-match All** to link existing logs

### Viewing Logs

1. Click **View All Logs** on the dashboard
2. Filter by date range and/or staff code
3. View detailed punch history with IN/OUT status

## Time Slot Assignment Logic

When syncing to attendance, IN/OUT punches are assigned to time slots:

| Punch Type | Time Range | Assigned Slot |
|------------|------------|---------------|
| IN | Before 12:00 PM | AM IN |
| IN | 12:00 PM onwards | PM IN |
| OUT | Until 1:00 PM | AM OUT |
| OUT | After 1:00 PM | PM OUT |

## Files Created

- `application/controllers/MainBiometrics.php` - Controller
- `application/models/MainBiometricsModel.php` - Model
- `application/views/main_biometrics/index.php` - Dashboard view
- `application/views/main_biometrics/logs.php` - Logs view
- `db/main_biometrics_migration.sql` - Database migration

## Files Modified

- `application/views/templates/sidebar.php` - Added submenu
- `application/views/bio/modal.php` - Added link to Main department import

## Comparison: Existing vs Main Department Import

| Feature | Existing (Dialysis/Admin) | Main Department |
|---------|---------------------------|-----------------|
| CSV Format | 4 columns | 11 columns |
| Bio ID Format | Direct number | 8-digit with leading zeros |
| IN/OUT Tracking | Time-based inference | Explicit IN/OUT field |
| Raw Log Storage | No | Yes |
| Department Field | No | Yes |
| Import History | No | Yes |

## Troubleshooting

### "Table doesn't exist" error
Run the migration SQL file to create the required tables.

### Staff codes not matching
Ensure personnel have the correct `bio_id` set. The system removes leading zeros, so:
- CSV `00000099` matches personnel with `bio_id = 99`
- CSV `00000001` matches personnel with `bio_id = 1`

### Duplicate records skipped
The system prevents duplicate imports. If you need to re-import, the existing records will be skipped automatically.
