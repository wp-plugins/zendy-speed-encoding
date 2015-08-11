<?php
/*
Plugin Name: Zendy Speed: Encoding
Plugin URI: https://kauai.zendy.net/wordpress/plugins/zendy-speed/
Description: Specify a "Vary: Accept-Encoding" Header; improve your website performance and your performace grade on YSlow, Google PageSpeed, and Pingdom.
Author: Zendy Web Studio
Version: 2.0
Author URI: https://kauai.zendy.net
*/


// Register the activation hook to install
register_activation_hook( __FILE__, 'zendy_speed_encoding_install' );
register_deactivation_hook( __FILE__, 'zendy_speed_encoding_uninstall' );

// Plugin install functions
// Right now there is only one
// TODO: add notification about backup files that are automatically created in case the htaccess file is corrupted
if( !function_exists( 'zendy_speed_encoding_install' ) ) {
	function zendy_speed_encoding_install() {
		zendy_speed_encoding_install_htaccess();
	}
}

// Plugin uninstall functions
// Right now there is only one
// TODO: add notification about backup files that are automatically created in case the htaccess file is corrupted
if( !function_exists( 'zendy_speed_encoding_uninstall' ) ) {
	function zendy_speed_encoding_uninstall() {
		zendy_speed_encoding_uninstall_htaccess();
	}
}

// Update htaccess
if( !function_exists( 'zendy_speed_encoding_install_htaccess' ) ){
	
	function zendy_speed_encoding_install_htaccess(){
	
		// Keep track of operation status
		$is_install_ok = true;
		
		// We don't mess around with .htaccess
		$backup_filename = 'zendy_speed_encoding_install_backup' . time() . '.htaccess';
		
		// Create htaccess if it's not there
		if( !file_exists( ABSPATH . '.htaccess') ){
			$newHtaccess = fopen(ABSPATH . '.htaccess', "w");
			fclose($newHtaccess);
		}
		
		// If there already is an .htaccess file
		if(file_exists( ABSPATH . '.htaccess') ) {
		
			// Try to copy the .htaccess for backup
			// If copy fails
			if(!copy ( ABSPATH . '.htaccess' , ABSPATH . $backup_filename )) {
			
				// Operation not ok
				$is_install_ok = false;

			}

		}
		
		// So far so good, let's keep going
		if( $is_install_ok ){
					
			// Add new rules to .htaccess
			$is_install_ok = write_htaccess_encoding_directives(ABSPATH . '.htaccess');

		}

		// So far so good, let's keep going		
		if( $is_install_ok ){
			
			// Erase backup
			zendy_speed_encoding_erase_file(false, $backup_filename);
			
		}

		// Log whether the plugin was installed successfully
		if($is_install_ok) {
			update_option( 'zendy_speed_encoding_status', 1);
		} else {
			update_option( 'zendy_speed_encoding_status', 0);
		}
		
	}

}

/**
 * Write htaccess directives
 * @param string $file_path
 * @return bool whether the write operation was successful or not.
 */
if( !function_exists( 'write_htaccess_encoding_directives' ) ){	

	function write_htaccess_encoding_directives( $file_path ){

		// Keep track of operation status
		$is_write_operation_ok = true;
		
		// If the file is writable
		if (is_writable($file_path)) {
			
			// Open file for writing
			$file_handle = fopen($file_path, "a");
			
			// Lock file; if file lock is obtained
			if (flock($file_handle, LOCK_EX)) {

				// THIS IS WHERE WE WRITE THE HTACCESS CODE 
				// TRIPLE-CHECK SYNTAX PLEASE!!!!
				fwrite($file_handle, "\n");
				fwrite($file_handle, "# START - Zendy Speed - Encoding\n");
				fwrite($file_handle, "<IfModule mod_headers.c> \n");
				fwrite($file_handle, "<FilesMatch \"\.(js|css|xml|gz)$\"> \n");
				fwrite($file_handle, "Header append Vary: Accept-Encoding \n");
				fwrite($file_handle, "</FilesMatch> \n");
				fwrite($file_handle, "</IfModule> \n");
				fwrite($file_handle, "# END - Zendy Speed - Encoding\n");
				fwrite($file_handle, "\n");
				
				// Force write of all buffered output
				fflush($file_handle);
				
				// Release file lock
				flock($file_handle, LOCK_UN);
			
			// Poop; didn't obtain lock on htaccess so let's not even try writing anything	
			}else{
				
				// Log failure
				$is_write_operation_ok = false;
				
			}
			
			// Close file
			fclose($file_handle);
		
		// Poop; file is not writeable	
		} else {
		
			// Log failure
			$is_write_operation_ok = false;
		}
		
		// It's a wrap
		return $status;
		
	}
	
}
	

if( !function_exists('zendy_speed_encoding_uninstall_htaccess') ){	
	
	function zendy_speed_encoding_uninstall_htaccess() {
	
		// Don't mess with htaccess files; make a backup
		$backup_filename = 'zendy_speed_encoding_uninstall_backup' . time() . '.htaccess';
		
		// Keep track of operation status
		$is_operation_ok = true;
		
		// Try to remove htaccess directives
		// If it works
		if( zendy_speed_encoding_remove_htaccess_directives($backup_filename) ) {
		
			// Mark plugin as deactivated
			update_option( 'zendy_speed_encoding_status', 0);

			// Keep track of operation status
			$is_operation_ok = false;
		
		// Oops! Could not remove htaccess files	
		} else {
			
			// Mark plugin as still active
			update_option( 'zendy_speed_encoding_status', 1);
	
		}
		
		
		return $is_operation_okstatus;
	}

}	

if( !function_exists('zendy_speed_encoding_remove_htaccess_directives') ){	

	function zendy_speed_encoding_remove_htaccess_directives($backup_filename){
		
		// Keep track of operation status
		$is_operation_ok = true;
		
		// Copy htaccess for backup
		// If backup failed
		if( !copy ( ABSPATH . '.htaccess' , ABSPATH . $backup_filename )) {
		
			// Keep track of operation status
			$is_operation_ok = false;
		
		}
		
		// All good, let's keep going.
		if($is_operation_ok) {
		
			// Get file handle for writing
			$file_handle = fopen(ABSPATH . '.htaccess', "w");
			
			// Get file lines as array
			$lines = file( ABSPATH . $backup_filename );
			
			// Lock htaccess 
			if (flock($file_handle, LOCK_EX)) {
			
				// Truncate file to 0 (erase everything)
				ftruncate($file_handle, 0);
				
				// Keep track of whether the line we're working on is a Zendy Speed line from this plugin
				$inZendySpeedDirectives = false;
				
				// Lopp over lines
				foreach($lines as $line) {
				
					// When we find the first line of our directives
					if(strpos($line, 'START - Zendy Speed - Encoding') !== false) {
						
						// Take note of this
						$inZendySpeedDirectives = true;

					}

					// We're rewriting the file line by line from scratch,
					// except for lines within our plugin directives
					// effectively deleting the lines we added during the plugin install process.
					// If at this point, we're not within our plugin directive
					if(!$inZendySpeedDirectives) {
						
						// Write line
						fwrite($file_handle, $line);
						
						// Force output of buffered writes
						fflush($file_handle);
						
					}

					// When we find the last line of our directives
					if(strpos($line, 'END - Zendy Speed - Encoding') !== false) {

						// Take note of this
						$inZendySpeedDirectives = false;

					}
					
				}
				
				// Release file lock on htaccess
				flock($file_handle, LOCK_UN);
			
			// Poop; could not get lock on htaccess	
			} else {
			
				$is_operation_ok = false;
				
			}
			
			// Close file
			fclose($file_handle);
			
		}
		
		// Let's call this a day.
		return $is_operation_ok;
		
	}

}