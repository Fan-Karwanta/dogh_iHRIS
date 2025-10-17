# Changelog - CSV Import Update

## Date: October 16, 2025

### Summary
Updated the biometrics CSV import functionality to support the new **Attendance Record** format with date columns instead of the previous row-based format.

### Changes Made

#### 1. Updated `Biometrics.php` Controller
**File:** `application/controllers/Biometrics.php`
**Method:** `importCSV()`

**Key Changes:**
- Added parsing logic for the new CSV structure with date columns
- Extracts year-month from "Made Date" field (Row 4)
- Maps column indices to day numbers from header row (Row 5)
- Processes employee data starting from Row 7
- Handles multiple time entries per cell (newline-separated)
- Maintains all existing functionality:
  - Smart time slot assignment
  - Duplicate removal (1-minute intervals)
  - Personnel validation
  - Undertime calculation
  - Audit trail logging

**Lines Modified:** Lines 201-307 (importCSV method)

### New CSV Format Specifications

**Structure:**
```
Row 1: Title ("Attendance Record")
Row 3: Create Time metadata
Row 4: Made Date range (YYYY/MM/DD-YYYY/MM/DD)
Row 5: Headers (Employee ID, Name, Department, 1, 2, 3, ...)
Row 7+: Employee data with time entries
```

**Time Entry Format:**
- Multiple entries per cell separated by newlines
- Format: HH:MM (e.g., 07:54, 12:09)
- Automatically converted to HH:MM:SS for database storage

### Algorithm Flow

1. **Parse Metadata**
   - Extract year-month from "Made Date" field
   - Map column indices to day numbers

2. **Process Employee Rows**
   - Read bio_id from Column A
   - For each date column:
     - Extract time entries (split by newline)
     - Construct full date (YYYY-MM-DD)
     - Parse time (HH:MM)
     - Add to import array

3. **Apply Existing Logic**
   - Group by employee and date
   - Remove duplicates within 1-minute intervals
   - Smart time slot assignment (am_in, am_out, pm_in, pm_out)
   - Calculate undertime
   - Update or create biometrics records

### Testing

**Test File Created:** `test_new_csv_format.php`

**Test Results:**
- ✅ Successfully parsed CSV with 15 date columns
- ✅ Extracted 81 time entries from 3 employees
- ✅ Correctly grouped by employee and date
- ✅ Proper date construction (2025-10-01, etc.)
- ✅ Time format conversion (07:54 → 07:54:00)

**Sample Test Output:**
```
Bio ID 353 on 2025-10-01: 4 time entries
  - 07:54:00 (am_in)
  - 12:09:00 (am_out)
  - 12:54:00 (pm_in)
  - 17:25:00 (pm_out)
```

### Backward Compatibility

The system maintains support for the previous CSV format:
- Old format: `bio_id, biometrics_time`
- New format: Date columns with multiple entries

Both formats can be imported through the same interface.

### Files Added/Modified

**Modified:**
- `application/controllers/Biometrics.php` (importCSV method)

**Added:**
- `test_new_csv_format.php` - Test script for new format
- `CSV_IMPORT_FORMAT_GUIDE.md` - Documentation
- `CHANGELOG_CSV_IMPORT.md` - This file

**Sample Data:**
- `csv_files/AttendanceRecord_oct.csv` - Sample CSV file

### Usage Instructions

1. **Prepare CSV File:**
   - Export from biometrics system in "Attendance Record" format
   - Ensure bio_ids match personnel database

2. **Import via Web Interface:**
   - Navigate to Biometrics Management
   - Click "Import CSV"
   - Select the CSV file
   - (Optional) Filter by specific date
   - Click "Import"

3. **Review Results:**
   - System displays number of records processed
   - Shows duplicates removed
   - Lists skipped entries (personnel not found)

### Technical Notes

- **CSV Buffer Size:** Increased from 1000 to 10000 bytes to handle larger rows
- **Date Format:** Supports YYYY/MM/DD format in metadata
- **Time Parsing:** Uses regex `/^(\d{1,2}):(\d{2})$/` for HH:MM format
- **Newline Handling:** Uses `preg_split('/[\r\n]+/')` for cross-platform compatibility

### Benefits

✅ **Efficiency:** Import entire month of data in one file
✅ **Accuracy:** Maintains all existing validation and calculation logic
✅ **Flexibility:** Supports multiple time entries per day
✅ **Reliability:** Automatic duplicate detection and removal
✅ **Traceability:** Full audit trail maintained

### Next Steps

- Test with production data
- Monitor import performance with larger files
- Consider adding progress indicator for large imports
- Document any edge cases discovered during use

---

**Developer:** Cascade AI Assistant
**Date:** October 16, 2025
**Version:** 2.0
