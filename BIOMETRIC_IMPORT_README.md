# Biometric CSV Import Feature

## Overview
This feature allows importing biometric attendance data from CSV files exported by biometric hardware devices.

## Database Changes
1. **Added `device_code` column** to the `biometrics` table
   - Run the migration script: `db/add_device_code_migration.sql`
   - Execute: `ALTER TABLE biometrics ADD COLUMN device_code VARCHAR(10) DEFAULT NULL AFTER bio_id;`

## CSV Format Requirements
The system now supports the following CSV structure from biometric devices:

| Column | Description | Example |
|--------|-------------|---------|
| 1 | Employee No. | 70 |
| 2 | Name | Marites Carillo |
| 3 | Attendance log | 07/01/2025 06:49 |
| 4 | Device Code | 222 |

## Features Implemented

### Backend Changes
- **Modified `Biometrics::importCSV()`** to handle new CSV structure
- **Updated `BiometricsModel`** to include device_code in search/display columns
- **Enhanced data parsing** to support MM/DD/YYYY HH:MM datetime format
- **Improved error handling** with detailed success messages

### Frontend Changes
- **Added Device Code column** to the biometrics table display
- **Enhanced import modal** with format requirements and instructions
- **Updated table headers** to include Device Code information

### Key Improvements
1. **Smart Date Parsing**: Automatically converts MM/DD/YYYY HH:MM to database format
2. **Flexible Date Filtering**: Optional date filter for importing specific date ranges
3. **Device Code Tracking**: Records which biometric device logged each entry
4. **Better User Experience**: Clear instructions and format requirements in the UI
5. **Robust Error Handling**: Detailed feedback on import success/failure

## Usage Instructions
1. Export CSV from your biometric device
2. Click "Import Biometrics" button
3. Optionally select a specific date to filter imports
4. Upload the CSV file
5. System will process and import all valid records

## File Structure Changes
```
application/
├── controllers/Biometrics.php (Updated)
├── models/BiometricsModel.php (Updated)
└── views/bio/
    ├── manage.php (Updated - Added Device Code column)
    └── modal.php (Updated - Enhanced import modal)
db/
└── add_device_code_migration.sql (New)
```

## Testing
The system has been updated to handle the provided CSV format with:
- Employee No: 70
- Name: Marites Carillo  
- Attendance logs with MM/DD/YYYY HH:MM format
- Device Code: 222

All records will be properly parsed and imported into the biometrics table with the correct date/time formatting.
