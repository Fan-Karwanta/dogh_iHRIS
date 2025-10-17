# CSV Import Format Guide

## New CSV Format (October 2025)

The biometrics import feature now supports the **Attendance Record** CSV format with date columns.

### CSV Structure

```
Row 1: Attendance Record (Title)
Row 2: (Empty)
Row 3: Create Time:2025/10/15 16:13:13
Row 4: Made Date:2025/10/01-2025/10/15
Row 5: Employee ID, Name, Department, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15...
Row 6: (Empty)
Row 7+: Employee data rows
```

### Column Layout

- **Column A**: Employee ID (bio_id) - numeric value that matches the bio_id in the personnel database
- **Column B**: Name - employee name (optional, not used in import)
- **Column C**: Department - department name (optional, not used in import)
- **Column D onwards**: Date columns (1, 2, 3, ... 31) representing days of the month

### Time Entry Format

Each cell in the date columns can contain:
- **Empty cell**: No attendance for that day
- **Single or multiple time entries**: One time entry per line in HH:MM format

Example cell content:
```
07:54
12:09
12:54
17:25
```

This represents 4 time logs for that employee on that specific day.

### Import Algorithm

1. **Parse Date Range**: Extracts year and month from Row 4 (Made Date field)
2. **Map Date Columns**: Identifies which columns represent which days of the month
3. **Process Employee Rows**: For each employee (bio_id):
   - Reads time entries from each date column
   - Splits multiple time entries by newline
   - Constructs full datetime (YYYY-MM-DD HH:MM:SS)
   - Groups entries by employee and date

4. **Smart Time Assignment**: The existing algorithm assigns times to slots:
   - **am_in**: Morning arrival (before 8:00 AM or earliest time)
   - **am_out**: Morning departure (around 12:00 PM)
   - **pm_in**: Afternoon arrival (around 1:00 PM)
   - **pm_out**: Afternoon departure (after 5:00 PM or latest time)

5. **Duplicate Removal**: Removes duplicate entries within 1-minute intervals

6. **Undertime Calculation**: Automatically calculates undertime based on:
   - Standard schedule: 8:00 AM - 12:00 PM, 1:00 PM - 5:00 PM
   - Late arrivals and early departures

### Key Features

✅ **Automatic bio_id Matching**: Imports are automatically linked to employees with matching bio_id in the database

✅ **Multiple Time Entries**: Supports multiple time logs per day (e.g., multiple in/out entries)

✅ **Date Filtering**: Can filter imports by specific date if needed

✅ **Smart Time Slot Assignment**: Intelligently assigns times to AM IN/OUT and PM IN/OUT slots

✅ **Duplicate Detection**: Removes duplicate entries within 1-minute intervals

✅ **Personnel Validation**: Skips entries for bio_ids that don't exist in the database

### Example Data

**CSV Content:**
```csv
Attendance Record,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
Create Time:2025/10/15 16:13:13,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
Made Date:2025/10/01-2025/10/15,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
Employee ID,Name,Department,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15
,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
353,,,07:54
12:09
12:54
17:25,07:54
12:54
12:56
17:12,07:48
12:09
17:23,,,...
```

**Parsed Result:**
- Bio ID: 353
- Date: 2025-10-01
- Times: 07:54, 12:09, 12:54, 17:25
- Assignment: am_in=07:54, am_out=12:09, pm_in=12:54, pm_out=17:25

### Testing

Run the test script to validate the parsing:
```bash
c:\xampp\php\php.exe test_new_csv_format.php
```

### Notes

- The algorithm maintains backward compatibility with the existing smart time assignment logic
- Empty cells (no attendance) are handled correctly and won't create blank entries
- The system will skip employees whose bio_id doesn't exist in the personnel database
- All existing features (undertime calculation, audit trail, etc.) continue to work as before

### Migration from Old Format

**Old Format:**
```csv
bio_id,biometrics_time
307,8/1/2025 7:14
254,8/1/2025 7:15
```

**New Format:**
```csv
Employee ID,Name,Department,1,2,3,...
307,,,07:14,07:15,...
254,,,07:14,07:15,...
```

Both formats are now supported by the import system.
