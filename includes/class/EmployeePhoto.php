<?php
	include_once 'DB_conn.php';

	class EmployeePhoto {
		protected $db;
		private $PhotoID = "";
		private $EmployeeID = "";
		private $PhotoPath = "";
		private $UploadDate = "";
		private $FileSize = "";

		// Image resizing configuration
		private $maxWidth = 800;
		private $maxHeight = 800;
		private $quality = 85; // JPG quality (1-100)
		private $uploadDir = '../assets/images/employees/';
		private $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif');
		private $maxFileSize = 5242880; // 5MB in bytes
		private $gdEnabled = false; // GD library availability

		public function __construct() {
			$this->db = new DB_conn();
			$this->gdEnabled = extension_loaded('gd');
			$this->ensureUploadDirectory();
		}

		/**
		 * Check if GD library is available
		 * @return boolean
		 */
		private function isGDAvailable() {
			return function_exists('imagecreatefromjpeg') && 
			       function_exists('imagecreatefrompng') && 
			       function_exists('imagecreatefromgif') &&
			       function_exists('imagecreatetruecolor');
		}

		/**
		 * Get GD library status for user information
		 * @return string - Status message
		 */
		public function getGDStatus() {
			if ($this->isGDAvailable()) {
				return 'GD library is available. Images will be resized for optimization.';
			} else {
				return 'GD library is not available. Images will be saved at original size (no resizing).';
			}
		}

		// Setter methods
		function setPhotoID($newValue) { $this->PhotoID = $newValue; }
		function setEmployeeID($newValue) { $this->EmployeeID = $newValue; }
		function setPhotoPath($newValue) { $this->PhotoPath = $newValue; }
		function setUploadDate($newValue) { $this->UploadDate = $newValue; }
		function setFileSize($newValue) { $this->FileSize = $newValue; }

		// Getter methods
		function getPhotoID() { return $this->PhotoID; }
		function getEmployeeID() { return $this->EmployeeID; }
		function getPhotoPath() { return $this->PhotoPath; }
		function getUploadDate() { return $this->UploadDate; }
		function getFileSize() { return $this->FileSize; }

		/**
		 * Ensure the upload directory exists and is writable
		 */
		private function ensureUploadDirectory() {
			$uploadPath = dirname(__FILE__) . '/../../assets/images/employees/';
			if (!is_dir($uploadPath)) {
				@mkdir($uploadPath, 0755, true);
			}
			// Create a .htaccess file to prevent code execution in upload directory
			$htaccess = $uploadPath . '.htaccess';
			if (!file_exists($htaccess)) {
				$htAccessContent = "AddType text/plain .php .phtml .php3 .php4 .php5 .php6 .shtml .pht .phar\n";
				file_put_contents($htaccess, $htAccessContent);
			}
		}

		/**
		 * Validate uploaded file
		 * @return array - array('status' => true/false, 'message' => string)
		 */
		private function validateFile($file) {
			// Check if file was uploaded without errors
			if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
				$errorMessages = array(
					UPLOAD_ERR_INI_SIZE => 'File size exceeds php.ini limit',
					UPLOAD_ERR_FORM_SIZE => 'File size exceeds form limit',
					UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
					UPLOAD_ERR_NO_FILE => 'No file was uploaded',
					UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
					UPLOAD_ERR_CANT_WRITE => 'Cannot write to temporary folder',
					UPLOAD_ERR_EXTENSION => 'File upload extension stopped'
				);
				$error = isset($errorMessages[$file['error']]) ? $errorMessages[$file['error']] : 'Unknown error';
				return array('status' => false, 'message' => $error);
			}

			// Check file size
			if ($file['size'] > $this->maxFileSize) {
				return array('status' => false, 'message' => 'File size exceeds maximum allowed size of 5MB');
			}

			// Check file extension
			$fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
			if (!in_array($fileExtension, $this->allowedExtensions)) {
				return array('status' => false, 'message' => 'Invalid file type. Allowed types: ' . implode(', ', $this->allowedExtensions));
			}

			// Validate file MIME type
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			$mimeType = finfo_file($finfo, $file['tmp_name']);
			finfo_close($finfo);

			$allowedMimeTypes = array('image/jpeg', 'image/png', 'image/gif');
			if (!in_array($mimeType, $allowedMimeTypes)) {
				return array('status' => false, 'message' => 'Invalid image file');
			}

			return array('status' => true, 'message' => 'File validation successful');
		}

		/**
	 * Crop image to perfect square centered on the image
	 * Used to ensure circular profile images display as perfect circles
	 * @param resource $image - GD image resource
	 * @param integer $sourceWidth - Original image width
	 * @param integer $sourceHeight - Original image height
	 * @param integer $imageType - Image type constant (IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF)
	 * @return resource - Cropped square image resource
	 */
	private function cropToSquare($image, $sourceWidth, $sourceHeight, $imageType) {
		// Determine the size of the square (use smaller dimension)
		$squareSize = min($sourceWidth, $sourceHeight);
		
		// Calculate crop offsets to center the crop
		$cropX = intval(($sourceWidth - $squareSize) / 2);
		$cropY = intval(($sourceHeight - $squareSize) / 2);
		
		// Create new square image
		$squareImage = imagecreatetruecolor($squareSize, $squareSize);
		
		// Preserve transparency for PNG and GIF images
		if ($imageType === IMAGETYPE_PNG) {
			imagealphablending($squareImage, false);
			imagesavealpha($squareImage, true);
			$transparent = imagecolorallocatealpha($squareImage, 255, 255, 255, 127);
			imagefill($squareImage, 0, 0, $transparent);
		} elseif ($imageType === IMAGETYPE_GIF) {
			// For GIF, preserve transparency if it exists
			$transparent = imagecolortransparent($image);
			if ($transparent >= 0) {
				imagecolortransparent($squareImage, imagecolorat($image, 0, 0));
			}
		}
		
		// Copy the center square portion of the source image
		imagecopy($squareImage, $image, 0, 0, $cropX, $cropY, $squareSize, $squareSize);
		
		return $squareImage;
	}

	/**
	 * Resize and crop image to perfect square for profile photos
	 * If GD is not available, copies file as-is
	 * @param string $source - Source image path
	 * @param string $dest - Destination image path
	 * @return boolean
	 */
	private function resizeImage($source, $dest) {
		$imageInfo = @getimagesize($source);
		if (!$imageInfo) {
			return false;
		}

		$imageType = $imageInfo[2];

		// If GD is not available, just copy the file
		if (!$this->isGDAvailable()) {
			return @copy($source, $dest);
		}

		$sourceWidth = $imageInfo[0];
		$sourceHeight = $imageInfo[1];

		// Create image resource from source
		switch ($imageType) {
			case IMAGETYPE_JPEG:
				$sourceImage = @imagecreatefromjpeg($source);
				break;
			case IMAGETYPE_PNG:
				$sourceImage = @imagecreatefrompng($source);
				break;
			case IMAGETYPE_GIF:
				$sourceImage = @imagecreatefromgif($source);
				break;
			default:
				return false;
		}

		if (!$sourceImage) {
			return false;
		}

		// Step 1: Crop image to perfect square (centered)
		$squareImage = $this->cropToSquare($sourceImage, $sourceWidth, $sourceHeight, $imageType);
		$squareSize = min($sourceWidth, $sourceHeight);

		// Step 2: Resize the square to final size (800x800)
		$finalSize = $this->maxWidth; // Use maxWidth for square dimensions
		$destImage = imagecreatetruecolor($finalSize, $finalSize);

		// Preserve PNG transparency
		if ($imageType === IMAGETYPE_PNG) {
			imagealphablending($destImage, false);
			imagesavealpha($destImage, true);
			$transparent = imagecolorallocatealpha($destImage, 255, 255, 255, 127);
			imagefill($destImage, 0, 0, $transparent);
		}

		// Resize and copy the square image
		if (!imagecopyresampled($destImage, $squareImage, 0, 0, 0, 0, $finalSize, $finalSize, $squareSize, $squareSize)) {
			imagedestroy($sourceImage);
			imagedestroy($squareImage);
			imagedestroy($destImage);
			return false;
		}

		// Save resized square image
		$success = false;
		switch ($imageType) {
			case IMAGETYPE_JPEG:
				$success = @imagejpeg($destImage, $dest, $this->quality);
				break;
			case IMAGETYPE_PNG:
				$success = @imagepng($destImage, $dest, 8);
				break;
			case IMAGETYPE_GIF:
				$success = @imagegif($destImage, $dest);
				break;
		}

		imagedestroy($sourceImage);
		imagedestroy($squareImage);
		imagedestroy($destImage);

		return $success;
	}

		/**
		 * Upload and save employee photo
		 * @param int $employee_id - Employee ID
		 * @param array $file - $_FILES array element
		 * @return array - array('status' => true/false, 'message' => string, 'path' => string)
		 */
		public function uploadPhoto($employee_id, $file) {
			// Validate employee ID
			$employee_id = $this->db->escape_string(trim($employee_id));
			if (empty($employee_id)) {
				return array('status' => false, 'message' => 'Invalid employee ID');
			}

			// Validate file
			$validation = $this->validateFile($file);
			if (!$validation['status']) {
				return array('status' => false, 'message' => $validation['message']);
			}

			// Delete existing photo if any
			$this->deletePhotoByEmployeeID($employee_id);

			// Generate unique filename
			$fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
			$fileName = 'emp_' . $employee_id . '_' . time() . '.' . $fileExtension;
			
			$uploadPath = dirname(__FILE__) . '/../../assets/images/employees/';
			$fullPath = $uploadPath . $fileName;
			$relativePath = 'assets/images/employees/' . $fileName;

			// Resize and save image
			if (!$this->resizeImage($file['tmp_name'], $fullPath)) {
				return array('status' => false, 'message' => 'Failed to process image');
			}

			// Get file size
			$fileSize = filesize($fullPath);

			// Save to database
			$photoPath = $this->db->escape_string(trim($relativePath));
			$uploadDate = date('Y-m-d H:i:s');
			$fileSize = $this->db->escape_string(trim($fileSize));

			$query = "INSERT INTO employee_photos (employee_id, photo_path, file_size, upload_date) 
					  VALUES ('".$employee_id."', '".$photoPath."', '".$fileSize."', '".$uploadDate."')";

			$result = $this->db->query($query) or die($this->db->error);

			if ($result) {
				$this->setPhotoID($this->db->last_id());
				$this->setEmployeeID($employee_id);
				$this->setPhotoPath($relativePath);
				$this->setUploadDate($uploadDate);
				$this->setFileSize($fileSize);
				return array('status' => true, 'message' => 'Photo uploaded successfully', 'path' => $relativePath);
			} else {
				// Delete the uploaded file if database insertion fails
				@unlink($fullPath);
				return array('status' => false, 'message' => 'Failed to save photo information to database');
			}
		}

		/**
		 * Get photo by employee ID
		 * @param int $employee_id - Employee ID
		 * @return array - Photo data or false
		 */
		public function getPhotoByEmployeeID($employee_id) {
			$employee_id = $this->db->escape_string(trim($employee_id));
			$query = "SELECT * FROM employee_photos WHERE employee_id = '$employee_id' ORDER BY upload_date DESC LIMIT 1";
			$result = $this->db->query($query) or die($this->db->error);
			$count_row = $this->db->num_rows($result);

			if ($count_row == 1) {
				return $row = $this->db->fetch_array($result);
			} else {
				return false;
			}
		}

		/**
		 * Get all photos of an employee
		 * @param int $employee_id - Employee ID
		 * @return array - Array of photos or false
		 */
		public function getAllPhotosByEmployeeID($employee_id) {
			$employee_id = $this->db->escape_string(trim($employee_id));
			$query = "SELECT * FROM employee_photos WHERE employee_id = '$employee_id' ORDER BY upload_date DESC";
			$result = $this->db->query($query) or die($this->db->error);
			$count_row = $this->db->num_rows($result);

			if ($count_row > 0) {
				while ($row = $this->db->fetch_array($result)) {
					$photos[] = $row;
				}
				return $photos;
			} else {
				return false;
			}
		}

		/**
		 * Delete photo by ID
		 * @param int $photo_id - Photo ID
		 * @return boolean
		 */
		public function deletePhoto($photo_id) {
			$photo_id = $this->db->escape_string(trim($photo_id));
			
			// Get photo details before deleting
			$query = "SELECT photo_path FROM employee_photos WHERE photo_id = '$photo_id'";
			$result = $this->db->query($query) or die($this->db->error);
			$count_row = $this->db->num_rows($result);

			if ($count_row == 1) {
				$row = $this->db->fetch_array($result);
				$photoPath = dirname(__FILE__) . '/../../' . $row['photo_path'];

				// Delete from database
				$deleteQuery = "DELETE FROM employee_photos WHERE photo_id = '$photo_id'";
				$deleteResult = $this->db->query($deleteQuery) or die($this->db->error);

				if ($deleteResult) {
					// Delete file from filesystem
					if (file_exists($photoPath)) {
						@unlink($photoPath);
					}
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		}

		/**
		 * Delete all photos of an employee (when deleting the employee)
		 * @param int $employee_id - Employee ID
		 * @return boolean
		 */
		public function deletePhotoByEmployeeID($employee_id) {
			$employee_id = $this->db->escape_string(trim($employee_id));
			
			// Get all photos for this employee
			$query = "SELECT photo_path FROM employee_photos WHERE employee_id = '$employee_id'";
			$result = $this->db->query($query) or die($this->db->error);

			// Delete files from filesystem
			while ($row = $this->db->fetch_array($result)) {
				$photoPath = dirname(__FILE__) . '/../../' . $row['photo_path'];
				if (file_exists($photoPath)) {
					@unlink($photoPath);
				}
			}

			// Delete from database
			$deleteQuery = "DELETE FROM employee_photos WHERE employee_id = '$employee_id'";
			$deleteResult = $this->db->query($deleteQuery) or die($this->db->error);

			return $deleteResult ? true : false;
		}

		/**
		 * Check if employee has photo
		 * @param int $employee_id - Employee ID
		 * @return boolean
		 */
		public function hasPhoto($employee_id) {
			$photo = $this->getPhotoByEmployeeID($employee_id);
			return ($photo !== false) ? true : false;
		}

		/**
		 * Update photo file size (useful for maintenance)
		 * @param int $photo_id - Photo ID
		 * @return boolean
		 */
		public function updatePhotoFileSize($photo_id) {
			$photo_id = $this->db->escape_string(trim($photo_id));
			
			$query = "SELECT photo_path FROM employee_photos WHERE photo_id = '$photo_id'";
			$result = $this->db->query($query) or die($this->db->error);
			$count_row = $this->db->num_rows($result);

			if ($count_row == 1) {
				$row = $this->db->fetch_array($result);
				$photoPath = dirname(__FILE__) . '/../../' . $row['photo_path'];

				if (file_exists($photoPath)) {
					$fileSize = filesize($photoPath);
					$fileSize = $this->db->escape_string(trim($fileSize));

					$updateQuery = "UPDATE employee_photos SET file_size = '$fileSize' WHERE photo_id = '$photo_id'";
					$updateResult = $this->db->query($updateQuery) or die($this->db->error);

					return $updateResult ? true : false;
				} else {
					return false;
				}
			} else {
				return false;
			}
		}
	}
?>
