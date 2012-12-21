<?php 
/**
 * A File handling class
 */
class fslider_file
{
	/**
	 * Gets the extension of a file name
	 *
	 * @param   string  $file  The file name
	 *
	 * @return  string  The file extension
	 */
	public function getExt($file)
	{
		$dot = strrpos($file, '.') + 1;
	
		return substr($file, $dot);
	}
	
	/**
	 * Wrapper for the standard file_exists function
	 *
	 * @param   string  $file  File path
	 *
	 * @return  boolean  True if path is a file
	 */
	public function exists($file)
	{
		return is_file($file);
	}
	
	/**
	 * Copies a file
	 *
	 * @param   string   $src          The path to the source file
	 * @param   string   $dest         The path to the destination file
	 *
	 * @return  boolean  True on success
	 */
	public function copy($src, $dest)
	{
		// Check src path
		if (!is_readable($src))
		{
			trigger_error("JLIB_FILESYSTEM_ERROR_JFILE_FIND_COPY", E_USER_ERROR);
	
			return false;
		}
			
		if (!@ copy($src, $dest))
		{
			trigger_error("JLIB_FILESYSTEM_ERROR_COPY_FAILED", E_USER_ERROR);
	
			return false;
		}
			
		return true;
		
	}
	
	/**
	 * Moves an uploaded file to a destination folder
	 *
	 * @param   string   $src          The name of the php (temporary) uploaded file
	 * @param   string   $dest         The path (including filename) to move the uploaded file to
	 *
	 * @return  boolean  True on success
	 */
	public function upload($src, $dest)
	{
		// Create the destination directory if it does not exist
		$baseDir = dirname($dest);
	
		if (is_writeable($baseDir) && move_uploaded_file($src, $dest))
		{
			return true;
		}
		else
		{
			trigger_error("JLIB_FILESYSTEM_ERROR_WARNFS_ERR02", E_USER_ERROR);
		}
	
		return false;
	}
	
	/**
	 * Create a folder -- and all necessary parent folders.
	 *
	 * @param   string   $path  A path to create from the base path.
	 *
	 * @return  boolean  True if successful.
	 */
	public function create($path)
	{
		if (@mkdir($path))
		{
			return false;
		}
		return true;
	}
	
	/**
	 * Utility function: Make image's thumb
	 * 
	 * @param 	string	$srcImage source image (allow image type: jpg,gif,png)
	 * @param 	string	$destImage destination image
	 * @param 	string	$ext image ext
	 * @param	number	$width
	 * @param 	number	$height
	 */
	public function makethumbimage($srcImage, $destImage, $ext, $width = 100, $height = 100)
	{
		switch ($ext)
		{
			case 'jpeg':
			case 'jpg':
				$image = imagecreatefromjpeg($srcImage);
				break;
			case 'gif':
				$image = imagecreatefromgif($srcImage);
				break;
			case 'png':
				$image = imagecreatefrompng($srcImage);
				break;
			default:
				$image = imagecreatefromjpeg($srcImage);
		}
	
		$thumb_width = $width;
		$thumb_height = $height;
	
		$width = imagesx($image);
		$height = imagesy($image);
	
		$original_aspect = $width / $height;
		$thumb_aspect = $thumb_width / $thumb_height;
	
		if ( $original_aspect >= $thumb_aspect )
		{
			// If image is wider than thumbnail (in aspect ratio sense)
			$new_height = $thumb_height;
			$new_width = $width / ($height / $thumb_height);
		}
		else
		{
			// If the thumbnail is wider than the image
			$new_width = $thumb_width;
			$new_height = $height / ($width / $thumb_width);
		}
	
		$thumb = imagecreatetruecolor( $thumb_width, $thumb_height );
	
		// Resize and crop
		imagecopyresampled( $thumb,
			$image,
			0 - ($new_width - $thumb_width) / 2, // Center the image horizontally
			0 - ($new_height - $thumb_height) / 2, // Center the image vertically
			0, 0,
			$new_width, $new_height,
			$width, $height
		);
		imagejpeg($thumb, $destImage, 100);
	}
	
	/**
	 * Delete a file or array of files
	 *
	 * @param   mixed  $file  The file name or an array of file names
	 *
	 * @return  boolean  True on success
	 */
	public function delete($file)
	{
		if (is_array($file))
		{
			$files = $file;
		}
		else
		{
			$files[] = $file;
		}
		
		foreach ($files as $file)
		{
			// Try making the file writable first. If it's read-only, it can't be deleted
			// on Windows, even if the parent folder is writable
			@chmod($file, 0777);
		
			// In case of restricted permissions we zap it one way or the other
			// as long as the owner is either the webserver or the ftp
			if (@unlink($file))
			{
				// Do nothing
			}
			else
			{
				trigger_error("JLIB_FILESYSTEM_DELETE_FAILED", E_USER_ERROR);
		
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * Delete a folder.
	 *
	 * @param   string  $dir  The path to the folder to delete.
	 *
	 * @return  boolean  True on success.
	 */
	public function deleteDir($dir) 
	{
        if(is_file($dir))
        {
            return @unlink($dir);
        }
        elseif(is_dir($dir))
        {
            $scan = glob(rtrim($dir,'/').'/*');
            foreach($scan as $index=>$path)
            {
                $this->deleteDir($path);
            }
            return @rmdir($dir);
        }
    }
}
?>