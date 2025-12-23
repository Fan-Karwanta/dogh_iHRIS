# Print Feature Guide - Personnel Profile Analytics

## Overview
The comprehensive profile dashboard now includes a professional print feature that allows you to generate physical reports of DTR analytics for reporting and documentation purposes.

## How to Use

### Accessing the Print Feature

1. **Navigate to a Profile:**
   - Go to Personnel Management page
   - Click on any personnel name or profile icon
   - You'll be redirected to their comprehensive profile

2. **Click the Print Button:**
   - Look for the **"Print Report"** button at the top-right of the page
   - Click the button to open the print dialog
   - Alternatively, use keyboard shortcut: `Ctrl+P` (Windows) or `Cmd+P` (Mac)

### Print Button Location
```
[Print Report] [Month Selector] [Year Selector]
```

## What Gets Printed

The print feature automatically includes ALL sections in a well-organized format:

### 1. **Report Header**
- Report title: "Personnel DTR Analytics Report"
- Generation date and time
- Selected period (Month and Year)

### 2. **Personnel Information**
- Full name
- Email address
- Position
- Department (if assigned)
- Employment type
- Salary grade
- Performance grade and status

### 3. **Monthly Statistics**
- Present days
- Absent days
- Late arrivals
- Complete DTR records
- Total hours worked
- Average arrival time
- Average departure time

### 4. **Performance Grade**
- Letter grade (A+ to D)
- Performance status
- Attendance rate percentage

### 5. **Attendance Trends**
- 6-month trend chart
- Visual representation of attendance patterns
- Present, late, and absent days over time

### 6. **Recent Attendance Records**
- Table of last 10 attendance entries
- All time slots (AM In/Out, PM In/Out)
- Completion status for each record

### 7. **Department Comparison** (User Profile Only)
- Individual vs department average
- Performance metrics comparison
- Department statistics

### 8. **Audit Trail**
- Recent changes to DTR records
- Who made the changes
- When changes were made
- Reason for modifications

## Print Layout Features

### Automatic Optimization
The print feature automatically:
- **Hides unnecessary elements** (navigation, buttons, tabs)
- **Shows all tab content** (no need to click through tabs)
- **Adjusts layout** for paper size
- **Preserves colors** for badges and charts
- **Prevents page breaks** in important sections
- **Adds section headers** for clarity

### Page Organization
```
Page 1:
├── Report Header
├── Personnel Information
├── Monthly Statistics
└── Performance Grade

Page 2:
└── Attendance Trends Chart

Page 3:
└── Recent Attendance Records Table

Page 4:
└── Department Comparison (if available)

Page 5:
└── Audit Trail
```

### Print-Friendly Formatting
- **Font Size:** Optimized at 11pt for readability
- **Tables:** Bordered with clear headers
- **Charts:** Maintained at readable size
- **Spacing:** Compact but not cramped
- **Borders:** Added to cards and sections for clarity

## Print Settings Recommendations

### Recommended Browser Settings
1. **Orientation:** Portrait (recommended) or Landscape for wide tables
2. **Paper Size:** A4 or Letter
3. **Margins:** Default or Custom (0.5 inches)
4. **Scale:** 100% (adjust if content doesn't fit)
5. **Background Graphics:** ON (to preserve colors)
6. **Headers/Footers:** Optional

### Chrome Print Settings
```
Destination: [Your Printer] or "Save as PDF"
Pages: All
Layout: Portrait
Color: Color (recommended)
More settings:
  ☑ Background graphics
  Margins: Default
  Scale: Default (100%)
```

### Firefox Print Settings
```
Destination: [Your Printer] or "Save as PDF"
Orientation: Portrait
Pages: All
☑ Print backgrounds (colors & images)
Scale: 100%
```

## Saving as PDF

### To Save Instead of Print:
1. Click "Print Report" button
2. In print dialog, select **"Save as PDF"** or **"Microsoft Print to PDF"**
3. Click "Save" or "Print"
4. Choose location and filename
5. Click "Save"

### Recommended PDF Filename Format:
```
DTR_Report_[PersonnelName]_[Month]_[Year].pdf

Example:
DTR_Report_JuanDelaCruz_December_2024.pdf
```

## Use Cases

### 1. **Monthly Performance Reviews**
- Print monthly reports for each employee
- Compare performance across months
- Document attendance patterns

### 2. **HR Documentation**
- Archive employee attendance records
- Maintain physical copies for compliance
- Support disciplinary actions with data

### 3. **Payroll Processing**
- Verify hours worked
- Check for undertime or overtime
- Cross-reference with payroll calculations

### 4. **Management Reports**
- Present department performance to leadership
- Identify attendance trends
- Support staffing decisions

### 5. **Employee Self-Service**
- Employees can print their own records
- Personal attendance tracking
- Performance self-assessment

## Troubleshooting

### Issue: Chart not showing in print
**Solution:** 
- Ensure "Background graphics" is enabled in print settings
- Wait for chart to fully load before printing
- Try using "Save as PDF" instead of direct printing

### Issue: Content cut off or doesn't fit
**Solution:**
- Adjust scale to 90% or 85%
- Change to Landscape orientation
- Use smaller margins (0.3 inches)
- Check paper size matches your printer

### Issue: Colors not printing
**Solution:**
- Enable "Background graphics" or "Print backgrounds"
- Check printer color settings
- Ensure printer has color ink/toner

### Issue: Multiple pages when expecting fewer
**Solution:**
- This is normal - comprehensive report spans multiple pages
- Each section gets its own page for clarity
- Use "Save as PDF" to combine all pages

### Issue: Tabs still showing
**Solution:**
- Refresh the page and try again
- Clear browser cache
- Update browser to latest version

### Issue: Print button not working
**Solution:**
- Use keyboard shortcut (Ctrl+P or Cmd+P)
- Check browser JavaScript is enabled
- Try different browser (Chrome, Firefox, Edge)

## Browser Compatibility

### Fully Supported:
- ✅ Google Chrome (recommended)
- ✅ Mozilla Firefox
- ✅ Microsoft Edge
- ✅ Safari (Mac)

### Partially Supported:
- ⚠️ Internet Explorer 11 (some styling may differ)

## Tips for Best Results

### Before Printing:
1. **Select the correct month/year** you want to report
2. **Wait for all data to load** (especially charts)
3. **Preview the print** before sending to printer
4. **Check page count** in print preview

### For Professional Reports:
1. Use **color printing** for better readability
2. Print on **quality paper** (20lb or higher)
3. Consider **two-sided printing** to save paper
4. Add **cover page** with company logo if needed

### For Archival:
1. **Save as PDF** for digital archiving
2. Use consistent **naming convention**
3. Store in organized **folder structure**
4. Keep **backup copies** in cloud storage

## Customization Options

### For Developers:
The print styles can be customized by editing the `@media print` section in:
- `application/views/user/user_profile.php`
- `application/views/personnel/personnel_profile.php`

### Common Customizations:
```css
/* Change font size */
@media print {
    body { font-size: 10pt; } /* Smaller text */
}

/* Adjust chart height */
@media print {
    .chart-container { height: 200px !important; }
}

/* Modify page breaks */
@media print {
    .tab-pane { page-break-before: auto; } /* Less page breaks */
}

/* Hide specific sections */
@media print {
    #audit { display: none !important; } /* Hide audit trail */
}
```

## Security Considerations

### What's Included:
- ✅ Personnel name and basic info
- ✅ Attendance statistics
- ✅ Performance metrics
- ✅ Audit trail (who made changes)

### What's NOT Included:
- ❌ Passwords or sensitive credentials
- ❌ Other employees' data
- ❌ System configuration details
- ❌ Database information

### Access Control:
- Users can only print their own profile
- Admins can print any personnel profile
- Print respects same permissions as view

## Best Practices

### For HR Departments:
1. Print monthly reports for all employees
2. File in personnel folders
3. Use for performance reviews
4. Maintain for audit purposes

### For Managers:
1. Print team member reports before 1-on-1s
2. Track attendance trends
3. Identify training needs
4. Support promotion decisions

### For Employees:
1. Print personal records quarterly
2. Keep for personal documentation
3. Review before performance reviews
4. Track own improvement

## Future Enhancements

Potential improvements to the print feature:
1. **Custom date ranges** - Select specific date periods
2. **Batch printing** - Print multiple employees at once
3. **Template selection** - Choose different report layouts
4. **Export to Excel** - Download as spreadsheet
5. **Email reports** - Send directly to email
6. **Scheduled reports** - Automatic monthly generation
7. **Comparison reports** - Side-by-side employee comparison
8. **Summary page** - Executive summary on first page

## Support

For issues with the print feature:
1. Check this documentation first
2. Verify browser compatibility
3. Test with different browsers
4. Contact IT support if problems persist

## Summary

The print feature transforms the digital analytics dashboard into professional, print-ready reports suitable for:
- HR documentation
- Performance reviews
- Compliance requirements
- Personal records
- Management reporting

Simply click "Print Report" and all comprehensive analytics are automatically formatted for professional printing or PDF export.
