# User Profile Analytics Enhancement

## Overview
The User Profile has been completely redesigned to provide comprehensive DTR (Daily Time Record) analytics and performance insights. This enhancement transforms the simple profile page into a powerful analytics dashboard that helps users understand their attendance patterns, performance metrics, and compare with department averages.

## Features Implemented

### 1. **Comprehensive Profile Information**
- User avatar and basic information
- Personnel details (position, department, employment type)
- Performance grade badge (A+ to D) based on attendance rate
- Quick edit profile modal

### 2. **Monthly Statistics Dashboard**
The profile displays detailed monthly statistics including:
- **Present Days**: Total days with attendance records
- **Absent Days**: Days without any attendance record
- **Late Arrivals**: Number of times arrived after 8:15 AM
- **Complete DTR**: Days with all 4 time slots filled (AM In/Out, PM In/Out)
- **Total Hours Worked**: Cumulative hours for the month
- **Average Arrival Time**: Typical check-in time
- **Average Departure Time**: Typical check-out time

### 3. **Performance Grading System**
Users receive a performance grade based on their attendance rate:
- **A+ (Outstanding)**: 95%+ attendance rate
- **A (Excellent)**: 90-94% attendance rate
- **B+ (Very Good)**: 85-89% attendance rate
- **B (Good)**: 80-84% attendance rate
- **C+ (Satisfactory)**: 75-79% attendance rate
- **C (Fair)**: 70-74% attendance rate
- **D (Needs Improvement)**: Below 70% attendance rate

### 4. **Attendance Trends Chart**
Interactive line chart showing 6-month trends for:
- Present days
- Late days
- Absent days

Uses Chart.js for beautiful, responsive visualizations.

### 5. **Recent Activity Table**
Displays the last 10 attendance records with:
- Date
- All 4 time slots (AM In/Out, PM In/Out)
- Status badge (Complete/Incomplete)

### 6. **Department Comparison**
Compare individual performance with department averages:
- Your attendance rate vs department average
- Visual difference indicator (positive/negative)
- Department statistics (total personnel, avg late days, avg complete days)

### 7. **Audit Trail**
Timeline view of recent changes to user's DTR records:
- Action type (UPDATE, CREATE, DELETE)
- Timestamp and user who made the change
- Reason for the change (if provided)

### 8. **Month/Year Selector**
Filter all statistics by selecting specific month and year from dropdown menus.

## Technical Implementation

### New Files Created

#### 1. `UserProfileModel.php`
Location: `application/models/UserProfileModel.php`

**Key Methods:**
- `getUserProfile($user_id)` - Get user profile with personnel data
- `getMonthlyStats($email, $month, $year)` - Calculate monthly DTR statistics
- `getYearlyStats($email, $year)` - Get statistics for all 12 months
- `getAttendanceTrends($email, $months)` - Get trend data for charts
- `getRecentAttendance($email, $limit)` - Fetch recent attendance records
- `getDepartmentComparison($email, $month, $year)` - Compare with department
- `getPerformanceSummary($email, $month, $year)` - Calculate performance grade
- `getUserAuditTrail($email, $limit)` - Get audit trail records

### Modified Files

#### 1. `Auth.php` Controller
Location: `application/controllers/Auth.php`

**Changes:**
- Enhanced `user_profile($id)` method to load UserProfileModel
- Added data collection for all analytics features
- Implemented month/year filtering from query parameters
- Passes comprehensive data to the view

#### 2. `user_profile.php` View
Location: `application/views/user/user_profile.php`

**Complete Redesign:**
- Responsive 3-column layout (profile card + main dashboard)
- Statistics cards with hover effects
- Tabbed interface for different sections
- Chart.js integration for trends visualization
- Edit profile modal for cleaner UX
- Timeline component for audit trail

## Database Requirements

The system uses existing database tables:
- `users` - User account information
- `personnels` - Personnel details and department assignment
- `departments` - Department information
- `attendance` - Manual attendance records
- `biometrics` - Biometric attendance records
- `audit_trail` - Change tracking records

**Note:** Ensure the following migrations have been run:
- `personnel_enhancement_migration.sql` (for employment_type, schedule_type)
- `department_migration.sql` (for departments table)
- `audit_trail_migration.sql` (for audit trail tracking)

## Usage

### Accessing User Profile
1. Navigate to: `/auth/user_profile/{user_id}`
2. Users can only access their own profile unless they are admin
3. Admins can view any user's profile

### Filtering by Month/Year
- Use the dropdown selectors at the top-right of the page
- Select month and year, changes apply automatically
- All statistics update based on selected period

### Viewing Different Sections
Click on the tabs to switch between:
- **Trends**: Visual chart of attendance patterns
- **Recent Activity**: Table of recent attendance records
- **Department Comparison**: Performance vs department average
- **Audit Trail**: History of DTR changes

### Editing Profile
1. Click "Edit Profile" button
2. Update avatar, name, password in modal
3. Admins can also change user roles
4. Click "Save Changes" to update

## Analytics Calculations

### Working Days Calculation
- Excludes weekends (Saturday and Sunday)
- Does not exclude holidays (can be enhanced)
- Counts only weekdays in the selected month

### Attendance Rate
```
Attendance Rate = (Present Days / Total Working Days) × 100
```

### Punctuality Rate
```
Punctuality Rate = ((Present Days - Late Days) / Present Days) × 100
```

### Completion Rate
```
Completion Rate = (Complete Days / Present Days) × 100
```

### Hours Worked
Calculated by summing:
- Morning hours: (AM Out - AM In)
- Afternoon hours: (PM Out - PM In)

## Features for Future Enhancement

### Suggested Improvements
1. **Export Functionality**
   - Export monthly statistics to PDF
   - Download attendance records as CSV
   - Generate performance reports

2. **Goal Setting**
   - Set personal attendance goals
   - Track progress towards goals
   - Receive notifications for milestones

3. **Predictive Analytics**
   - Forecast attendance patterns
   - Suggest optimal work schedules
   - Identify potential attendance issues

4. **Mobile Responsiveness**
   - Optimize charts for mobile devices
   - Touch-friendly interface
   - Progressive Web App (PWA) support

5. **Notifications**
   - Email summaries of monthly performance
   - Alerts for incomplete DTR records
   - Reminders for late arrivals

6. **Gamification**
   - Achievement badges
   - Leaderboards (optional)
   - Streak tracking for perfect attendance

7. **Leave Integration**
   - Display approved leaves
   - Exclude leave days from absent count
   - Show leave balance

8. **Holiday Calendar**
   - Mark holidays on the calendar
   - Exclude holidays from working days
   - Display upcoming holidays

## Customization

### Changing Performance Grade Thresholds
Edit `UserProfileModel.php`, method `getPerformanceSummary()`:
```php
if ($performance['attendance_rate'] >= 95) {
    $performance['grade'] = 'A+';
    $performance['status'] = 'Outstanding';
}
// Modify thresholds as needed
```

### Adjusting Chart Colors
Edit `user_profile.php`, Chart.js configuration:
```javascript
borderColor: '#1abc9c',  // Change line color
backgroundColor: 'rgba(26, 188, 156, 0.1)',  // Change fill color
```

### Modifying Statistics Cards
Edit `user_profile.php`, statistics section to add/remove cards or change metrics.

## Troubleshooting

### Issue: No data showing
**Solution:** Ensure user has a linked personnel record with valid email

### Issue: Department comparison not available
**Solution:** User must be assigned to a department in personnels table

### Issue: Chart not rendering
**Solution:** Check browser console for JavaScript errors, ensure Chart.js CDN is accessible

### Issue: Audit trail empty
**Solution:** Audit trail only shows records after the audit_trail system was implemented

### Issue: Performance grade showing incorrectly
**Solution:** Verify attendance records exist for the selected month/year

## Security Considerations

1. **Access Control**: Users can only view their own profile unless admin
2. **Data Validation**: All inputs are validated before processing
3. **SQL Injection Prevention**: Uses CodeIgniter's Query Builder
4. **XSS Protection**: All output is properly escaped
5. **CSRF Protection**: Form submissions use CSRF tokens

## Performance Optimization

1. **Database Queries**: Optimized with proper indexes
2. **Caching**: Consider implementing query caching for frequently accessed data
3. **Lazy Loading**: Charts load only when tab is active
4. **Pagination**: Recent activity limited to 10 records

## Support and Maintenance

For issues or feature requests related to User Profile Analytics:
1. Check this documentation first
2. Review the code comments in UserProfileModel.php
3. Test with sample data to isolate issues
4. Check database for proper data relationships

## Conclusion

The enhanced User Profile provides a comprehensive view of attendance performance with actionable insights. Users can track their progress, identify patterns, and compare with peers, while administrators gain visibility into individual performance metrics.
