# DTR Inline Edit Feature

## Overview
This feature allows you to edit the DTR (Daily Time Record) preview directly before printing. You can modify time entries, undertime values, and add special labels like ABSENT, OFFICIAL BUSINESS, OFF, etc.

## Features

### 1. **Edit Mode Toggle**
- Click the **"Edit Mode"** button to activate editing
- All editable cells will be highlighted in yellow
- A visual indicator appears showing "EDIT MODE ACTIVE"
- Click again to switch back to "View Mode"

### 2. **Editable Fields**

#### Time Entries
- **Morning In/Out**: Click any time cell to edit
- **Afternoon In/Out**: Click any time cell to edit
- Enter time in **HH:MM format** (e.g., 08:30, 13:00)
- Press **Enter** or click outside to save

#### Undertime
- **Hours**: Click to edit (0-8)
- **Minutes**: Click to edit (0-59)
- Enter numeric values only

#### Special Labels
- Click on weekend/holiday labels (SATURDAY, SUNDAY, HOLIDAY)
- Select from dropdown:
  - ABSENT
  - OFFICIAL BUSINESS
  - OFFICIAL TIME
  - OFF
  - LEAVE
  - SICK LEAVE
  - VACATION LEAVE
  - TRAINING
  - CONFERENCE
  - Or leave blank to convert to time entry cells

### 3. **Weekend/Holiday Work**
- If a person worked on a weekend or holiday, click the label cell
- Select blank option from dropdown
- The row will convert to individual time entry cells
- Enter the actual work times

### 4. **Blank Dates**
- For dates with no attendance data
- Click any cell to add time entries
- Useful for adding ABSENT, OFFICIAL BUSINESS, or actual times

### 5. **Automatic Synchronization**
- Both DTR copies (left and right) are automatically synchronized
- Changes made to one copy instantly reflect on the other
- Ensures consistency across both printed copies

### 6. **Save Changes**
- After making edits, click **"Save Changes"** button
- Confirm the save operation
- Changes are saved to the database
- Page reloads to show updated data

## How to Use

### Basic Workflow
1. Navigate to **Generate DTR** page
2. Select the month and personnel
3. Click **"Edit Mode"** button
4. Click on any yellow-highlighted cell to edit
5. Make your changes:
   - For time: Enter HH:MM format
   - For undertime: Enter numbers
   - For labels: Select from dropdown
6. Press Enter or click outside to confirm each edit
7. Click **"Save Changes"** when done
8. Confirm the save operation
9. Print the DTR using the **"Print"** button

### Example Scenarios

#### Scenario 1: Mark a day as ABSENT
1. Enable Edit Mode
2. Find the blank date row
3. Click on the first time cell (or label cell if weekend)
4. If it's a label cell, select "ABSENT" from dropdown
5. If it's a time cell, you may need to add a label feature (currently stores in notes)

#### Scenario 2: Add weekend work hours
1. Enable Edit Mode
2. Find the weekend row (shows SATURDAY or SUNDAY)
3. Click the label cell
4. Select blank option from dropdown
5. Row converts to time entry cells
6. Enter work hours (e.g., 08:00, 12:00, 13:00, 17:00)
7. Enter undertime if applicable

#### Scenario 3: Correct undertime minutes
1. Enable Edit Mode
2. Find the day with incorrect undertime
3. Click the undertime minutes cell
4. Enter correct value (0-59)
5. Press Enter

#### Scenario 4: Add OFFICIAL BUSINESS
1. Enable Edit Mode
2. Find the date
3. Click the label cell (if weekend/holiday) or time cell
4. Select "OFFICIAL BUSINESS" from dropdown

## Technical Details

### Files Modified
1. **View**: `application/views/attendance/generate_dtr.php`
   - Added editable cell classes
   - Added data attributes for tracking
   - Added JavaScript for edit functionality
   - Added CSS for visual feedback

2. **Controller**: `application/controllers/Attendance.php`
   - Added `save_dtr_edits()` method
   - Handles AJAX save requests
   - Updates biometrics and attendance tables

### Database Updates
- **Biometrics table**: Stores time entries and undertime
  - Fields: `am_in`, `am_out`, `pm_in`, `pm_out`, `undertime_hours`, `undertime_minutes`
- **Attendance table**: Can store special labels in `notes` field

### CSS Classes
- `.editable-cell`: Time and undertime cells
- `.editable-label`: Weekend/holiday label cells
- `.edit-mode`: Applied when edit mode is active
- `.editing`: Applied to cell being edited

### Data Attributes
- `data-date`: ISO date (YYYY-MM-DD)
- `data-copy`: DTR copy number (1 or 2)
- `data-field`: Field name (morning_in, afternoon_out, etc.)

## Browser Compatibility
- Modern browsers (Chrome, Firefox, Edge, Safari)
- JavaScript must be enabled
- Tested on desktop browsers

## Security
- Requires user authentication
- AJAX requests validated server-side
- SQL injection protection via CodeIgniter query builder

## Future Enhancements
- Add audit trail for edits
- Bulk edit multiple dates
- Undo/Redo functionality
- Export edited DTR to PDF
- Email notification on changes
- More label options
- Custom label input

## Troubleshooting

### Changes not saving
- Check browser console for errors
- Verify you're logged in
- Ensure database connection is active
- Check server error logs

### Cells not editable
- Make sure Edit Mode is enabled (yellow highlighting)
- Check if JavaScript is enabled
- Refresh the page and try again

### Synchronization issues
- Both copies should update together
- If not, try refreshing the page
- Check browser console for errors

## Support
For issues or questions, contact the system administrator.
