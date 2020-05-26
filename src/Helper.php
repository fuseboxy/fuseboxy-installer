<?php
namespace Fuseboxy\Composer;


class Helper {


	// recursively delete a directory and its contents
	public static function rrmdir($dir) {
		// only handle directory
		if ( is_dir($dir) ) {
			$objects = scandir($dir);
			// go through each child of this directory
			foreach ( $objects as $obj ) {
				// ignore current/parent directory indicator
				if ( !in_array($obj, ['.','..']) ) {
					// if sub-directory
					// ===> proceed to remove its contents 
					if ( is_dir($dir.DIRECTORY_SEPARATOR.$obj) && !is_link($dir.'/'.$obj) ) {
						self::rrmdir($dir.DIRECTORY_SEPARATOR.$obj);
					// if file
					// ===> simply remove
					} else {
						unlink($dir.DIRECTORY_SEPARATOR.$obj);
					}
				}
			}
			// remove the (empty) directory
			rmdir($dir);
		}
		// done!
		return true;
	}


} // class