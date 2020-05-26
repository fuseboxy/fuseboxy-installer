<?php
namespace Fuseboxy\Composer;


class Helper {


	// recursively delete a directory and its contents
	public static function rrmdir($dir) {
		// only handle directory
		if ( is_dir($dir) ) {
			$objects = scandir($dir);
			// go through each child of this directory
			foreach ( $objects as $object ) {
				// ignore current/parent directory indicator
				if ( $object != "." && $object != ".." ) {
					// if sub-directory
					// ===> proceed to remove its contents 
					if ( is_dir($dir.DIRECTORY_SEPARATOR.$object) && !is_link($dir."/".$object) ) {
						self::rrmdir($dir.DIRECTORY_SEPARATOR.$object);
					// if file
					// ===> simply remove
					} else {
						unlink($dir.DIRECTORY_SEPARATOR.$object);
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