# Schedule Compliance Report Feature

## Overview

The **Schedule Compliance Report** is a comprehensive reporting module that tracks and displays employee attendance compliance based on the standard work schedule (8:00 AM - 5:00 PM with 12:00 PM - 1:00 PM lunch break).

## Features

### 1. Top Performers Tracking
- Identifies employees with **100% complete schedule compliance**
- Ranks employees by compliance rate
- Trophy badges for top 3 performers

### 2. Department-Based Analysis
- Filter by department
- Visual chart comparing department compliance rates
- Department color-coded badges

### 3. Compliance Metrics
A "complete schedule" requires all 4 clock entries:
- **AM IN** - Morning arrival (expected: 8:00 AM)
- **AM OUT** - Lunch break out (expected: 12:00 PM)
- **PM IN** - Lunch break return (expected: 1:00 PM)
- **PM OUT** - End of day departure (expected: 5:00 PM)

### 4. Visual Dashboard
- **Statistics Cards**: Perfect attendance count, total employees, average compliance, missing entries
- **Department Chart**: Bar chart with compliance rates and perfect employee counts
- **Top Performers Panel**: Quick view of 100% compliance employees

### 5. Interactive Data Table
- Sortable columns
- Search functionality
- Compliance rate filter (Perfect, Good, Needs Improvement, Critical)
- Employee quick lookup dropdown
- Color-coded missing entries (red highlighting)
- Progress bar visualization for compliance rates

### 6. Employee Detail Modal
- Click any employee row to view detailed failure report
- Shows complete/incomplete days breakdown
- Lists all dates with missing entries
- Visual breakdown of which time slots are missing

### 7. Export Options
- **CSV Export**: Download complete report as CSV file
- **Print View**: Clean, printer-friendly report layout

## Access

Navigate to: **Reports > Schedule Compliance** in the sidebar menu

Or directly access: `/reports/schedule_compliance`

## Filters

### Date Range
- Start Date / End Date selection
- Quick filters: Current Month, Last Month, Current Year, Last 7 Days

### Department
- Filter by specific department
- Shows personnel count per department

### Compliance Level
- **Perfect (100%)**: All days have complete schedules
- **Good (80-99%)**: Most days complete
- **Needs Improvement (50-79%)**: Significant missing entries
- **Critical (<50%)**: Majority of days incomplete

## Technical Details

### Files Created

1. **Model**: `application/models/AttendanceComplianceModel.php`
   - Compliance calculations
   - Working days counting (excludes weekends/holidays)
   - Department summary generation
   - Employee failure details

2. **Controller**: `application/controllers/AttendanceCompliance.php`
   - Main page rendering
   - AJAX endpoints for dynamic data
   - CSV export functionality
   - Print report generation

3. **Views**:
   - `application/views/reports/schedule_compliance.php` - Main view
   - `application/views/reports/schedule_compliance_print.php` - Print view

4. **Routes**: Added to `application/config/routes.php`

### Database Requirements

Uses existing tables:
- `personnels` - Employee information
- `biometrics` - Time clock records
- `departments` - Department information

No database migrations required.

## Usage Tips

1. **Identifying Problem Areas**: Look for employees with high missing entry counts in specific time slots (e.g., many missing PM OUT entries might indicate early departures)

2. **Department Comparison**: Use the chart to quickly identify which departments need attendance improvement

3. **Individual Follow-up**: Click on any employee to see their specific failure dates and patterns

4. **Regular Monitoring**: Use the "Current Month" quick filter for ongoing monitoring

5. **Historical Analysis**: Use custom date ranges to analyze trends over time

## Compliance Rate Calculation

```
Compliance Rate = (Complete Days / Working Days) * 100

Where:
- Complete Days = Days with all 4 time entries (AM IN, AM OUT, PM IN, PM OUT)
- Working Days = Days with at least one clock-in entry (AM IN or PM IN)
```

### Dynamic Working Days (Updated)

**Previous Behavior**: Working days were calculated as a static count of calendar weekdays (excluding weekends and holidays) for the entire period. This caused issues where employees who didn't work certain days would have inflated "absent days" and lower compliance rates.

**New Behavior**: Working days are now calculated **dynamically per employee** based on actual attendance:
- A day is counted as a "working day" if the employee has at least **one clock-in entry (AM IN or PM IN)**
- This provides more accurate compliance rates that reflect actual work patterns
- The system still tracks "Calendar Working Days" for reference (shown in parentheses)

**Example**:
- If November has 20 calendar working days but an employee only clocked in on 15 days
- Working Days = 15 (based on actual attendance)
- If they had complete entries on 12 of those days: Compliance Rate = (12/15) × 100 = 80%

## Philippine Holidays Excluded

The system automatically excludes these holidays from working day calculations:
- New Year's Day (Jan 1)
- EDSA Revolution Anniversary (Feb 25)
- Araw ng Kagitingan (Apr 9)
- Labor Day (May 1)
- Independence Day (Jun 12)
- Ninoy Aquino Day (Aug 21)
- National Heroes Day (Aug 25)
- Davao Occidental Araw (Oct 28)
- All Souls' Evening (Oct 31)
- Bonifacio Day (Nov 30)
- Christmas Day (Dec 25)
- Rizal Day (Dec 30)
- New Year's Eve (Dec 31)
- Maundy Thursday & Good Friday (variable dates)
