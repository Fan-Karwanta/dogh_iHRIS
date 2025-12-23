# Profile Image Feature Implementation Guide

## Overview
This document describes the implementation of the profile image feature for personnel in the iHRIS DTR system. The feature allows users to upload, update, and delete profile images for each personnel record.

## Implementation Approach

### Storage Method: **Filesystem-based Storage**

**Why Filesystem over BLOB?**
- **Performance**: No database bloat, faster image serving
- **Scalability**: Easier to integrate with CDN and caching
- **Simplicity**: Leverages existing web server capabilities
- **Maintenance**: Simpler backup and migration processes
- **Existing Infrastructure**: Already have upload directory structure in place

## Database Changes

### Migration File
**Location**: `db/profile_image_migration.sql`

**Changes**:
```sql
ALTER TABLE `personnels` 
ADD COLUMN `profile_image` VARCHAR(255) NULL COMMENT 'Profile image filename' AFTER `bio_id`;

ALTER TABLE `personnels` 
ADD INDEX `idx_profile_image` (`profile_image`);
```

**To Apply Migration**:
1. Open phpMyAdmin or MySQL client
2. Select your database
3. Run the SQL script: `db/profile_image_migration.sql`

## Directory Structure

### Upload Directory
**Location**: `assets/uploads/profile_images/`

**Files Created**:
- `.htaccess` - Allows image access while protecting directory listing
- `index.html` - Prevents directory browsing

**Permissions**: Ensure the directory is writable by the web server (chmod 755 or 775)

## Code Changes

### 1. PersonnelModel (`application/models/PersonnelModel.php`)

**New Methods**:
- `update_profile_image($personnel_id, $image_filename)` - Updates profile image filename
- `get_profile_image($personnel_id)` - Retrieves current profile image filename
- `delete_profile_image($personnel_id)` - Deletes profile image file and database record

### 2. Personnel Controller (`application/controllers/Personnel.php`)

**New Methods**:
- `upload_profile_image()` - Handles image upload with validation
- `delete_profile_image($personnel_id)` - Handles image deletion

**Upload Specifications**:
- **Allowed formats**: JPG, JPEG, PNG, GIF
- **Max file size**: 2MB (2048 KB)
- **Max dimensions**: 2000x2000 pixels
- **File naming**: Encrypted/hashed filenames for security
- **Old image handling**: Automatically deletes old image when uploading new one

### 3. Personnel Profile View (`application/views/personnel/personnel_profile.php`)

**Features Added**:
- Dynamic profile image display (uses default `person.png` if no image)
- Upload button with modal dialog
- Delete button (only shown when image exists)
- Image preview before upload
- Responsive design

**UI Components**:
- Upload modal with file picker and preview
- Confirmation dialog for deletion
- Success/error flash messages

### 4. Personnel Management View (`application/views/personnel/manage.php`)

**Features Added**:
- Profile image column in personnel table
- Small avatar thumbnails (avatar-sm class)
- Circular image display with proper object-fit
- Default image fallback

## Usage Instructions

### For Administrators

#### Uploading a Profile Image
1. Navigate to Personnel Management
2. Click on a personnel name to view their profile
3. Click the "Upload Profile Image" or "Change Profile Image" button
4. Select an image file (JPG, PNG, or GIF)
5. Preview the image in the modal
6. Click "Upload Image" to save

#### Deleting a Profile Image
1. Navigate to the personnel profile page
2. Click the "Remove Profile Image" button (red button)
3. Confirm the deletion
4. The profile will revert to the default person icon

### For Developers

#### Accessing Profile Images in Views
```php
<?php 
$profile_image_url = $personnel->profile_image 
    ? site_url('assets/uploads/profile_images/' . $personnel->profile_image) 
    : site_url('assets/img/person.png');
?>
<img src="<?= $profile_image_url ?>" alt="<?= $personnel->firstname ?>" />
```

#### Adding Profile Image to Other Views
```php
// In controller - ensure profile_image is selected
$this->db->select('*');
$personnel = $this->db->get('personnels')->result();

// In view - display image
foreach ($personnel as $person) {
    $img = $person->profile_image 
        ? site_url('assets/uploads/profile_images/' . $person->profile_image)
        : site_url('assets/img/person.png');
    echo '<img src="' . $img . '" />';
}
```

## Security Considerations

1. **File Type Validation**: Only image files (JPG, JPEG, PNG, GIF) are allowed
2. **File Size Limits**: Maximum 2MB to prevent abuse
3. **Encrypted Filenames**: Original filenames are encrypted to prevent path traversal attacks
4. **Directory Protection**: `.htaccess` prevents directory listing
5. **Authentication Required**: All upload/delete operations require login
6. **Old File Cleanup**: Previous images are automatically deleted to prevent storage bloat

## File Size and Performance

### Recommendations
- **Optimal size**: 500x500 pixels or less
- **File format**: JPG for photos, PNG for graphics with transparency
- **Compression**: Use moderate compression to balance quality and file size

### Storage Estimates
- Average image size: ~100-200 KB
- 1000 personnel: ~100-200 MB storage
- Recommend monitoring disk space regularly

## Troubleshooting

### Images Not Uploading
1. Check directory permissions: `chmod 755 assets/uploads/profile_images/`
2. Verify PHP upload settings in `php.ini`:
   - `upload_max_filesize = 2M`
   - `post_max_size = 8M`
3. Check error logs for specific issues

### Images Not Displaying
1. Verify file exists in `assets/uploads/profile_images/`
2. Check `.htaccess` allows image access
3. Verify correct file path in database
4. Check browser console for 404 errors

### Database Migration Issues
1. Ensure you have ALTER privileges on the database
2. Check if column already exists: `DESCRIBE personnels;`
3. Run migration manually if automated process fails

## Future Enhancements

### Potential Improvements
1. **Image Cropping**: Add client-side image cropping tool
2. **Thumbnail Generation**: Create multiple sizes for different contexts
3. **Bulk Upload**: Allow uploading multiple images at once
4. **Image Optimization**: Automatic compression and resizing
5. **Cloud Storage**: Integrate with AWS S3 or similar services
6. **User Self-Upload**: Allow personnel to upload their own images via user portal

## Testing Checklist

- [ ] Database migration applied successfully
- [ ] Upload directory created with proper permissions
- [ ] Can upload JPG image
- [ ] Can upload PNG image
- [ ] Can upload GIF image
- [ ] File size validation works (reject >2MB)
- [ ] File type validation works (reject non-images)
- [ ] Image preview shows before upload
- [ ] Old image deleted when uploading new one
- [ ] Can delete profile image
- [ ] Default image shows when no profile image
- [ ] Profile images display in personnel list
- [ ] Profile images display in personnel profile
- [ ] Flash messages show success/error correctly

## Support

For issues or questions regarding the profile image feature:
1. Check this documentation first
2. Review error logs in `application/logs/`
3. Verify database schema matches migration
4. Check file permissions on upload directory

## Version History

- **v1.0** (December 2024): Initial implementation with filesystem storage
  - Upload, update, and delete functionality
  - Integration with personnel profile and list views
  - Security validations and error handling
