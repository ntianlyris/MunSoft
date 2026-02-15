-- Create employee_photos table
-- This table stores references to employee photos/images
-- The images are stored in /assets/images/employees/ directory

CREATE TABLE IF NOT EXISTS `employee_photos` (
  `photo_id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `photo_path` varchar(255) NOT NULL COMMENT 'Relative path to the photo file',
  `file_size` int(11) NOT NULL COMMENT 'File size in bytes',
  `upload_date` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`photo_id`),
  FOREIGN KEY (`employee_id`) REFERENCES `employees_tbl` (`employee_id`) ON DELETE CASCADE,
  KEY `idx_employee_id` (`employee_id`),
  KEY `idx_upload_date` (`upload_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Add index for faster queries
CREATE INDEX idx_employee_photo ON employee_photos(employee_id, upload_date DESC);
