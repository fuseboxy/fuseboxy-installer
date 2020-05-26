<?php
namespace Fuseboxy\Composer;


class Helper {

	// recursively remove a folder with contents (or simply remove a file)
	public static function rrmdir($dir) {
		if ( is_dir($dir) ) {
			$objects = scandir($dir);
			foreach ( $objects as $obj ) if ( !in_array($obj, ['.','..']) ) self::rrmdir($dir.'/'.$obj);
			rmdir($dir);
		} elseif ( is_file($dir) or is_link($dir) ) {
			unlink($dir);
		}
		return true;
	}

} // class