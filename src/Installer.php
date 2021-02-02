<?php
namespace Fuseboxy\Composer;
use Composer\Installer\LibraryInstaller;
use Composer\Package\PackageInterface;
use Composer\Repository\InstalledRepositoryInterface;



// custom installer for fuseboxy
class Installer extends LibraryInstaller {


	// list of directories to remove after install or update
	private static $dir2remove = array( '*' => ['.git'] );


	// list of files to copy after install (only)
	private static $file2copy = array(
		'fuseboxy/fuseboxy-core' => [
			'app/config/fusebox_config.php',
			'app/controller/error_controller.php',
			'app/controller/home_controller.php',
			'.htaccess',
			'index.php',
		],
	);


	// list of files to remove after install or update
	private static $file2remove = array(
		'fuseboxy-core' => [
			'app/config/fusebox_config.php',
			'app/controller/error_controller.php',
			'app/controller/home_controller.php',
			'.htaccess',
			'index.php',
		],
	);


	// check whether this installer is invoked by unit test
	private function isUnitTest() {
		// parse composer file
		$filePath = dirname($this->vendorDir).'/composer.json';
		$json = json_decode(file_get_contents($filePath), true);
		if ( $json === false ) return false;
		// extract package name
		$packageName = isset($json['name']) ? $json['name'] : '';
		// done!
		return ( $packageName == 'fuseboxy/fuseboxy-test' );
	}


	// check whether to run this installer (according to package type)
	public function supports($packageType) {
		return in_array($packageType, ['fuseboxy-core', 'fuseboxy-module']);
	}


	// proceed to copy files (according to package)
	public function copyFile($vendorDir, $packageName) {
		$baseDir = dirname($vendorDir).'/';
		$packageDir = $vendorDir.'/'.$packageName.'/';
		// obtain file list
		if ( isset(self::$file2copy['*']) ) $file2copy = self::$file2copy['*'];
		elseif ( isset(self::$file2copy[$packageName]) ) $file2copy = self::$file2copy[$packageName];
		else $file2copy = [];
		// go through each specified file
		foreach ( $file2copy as $src => $dst ) {
			if ( is_numeric($src) ) $src = $dst;
			// create directory (when necessary)
			$dir = dirname($dst);
			if ( $dir != '.' and !is_dir($baseDir.$dir) ) mkdir($baseDir.$dir, 0755, true);
			// copy file (when necessary)
			// ===> do not overwrite!
			// ===> otherwise, modified config file or customized index will be overwritten everytime composer update is run...
			if ( is_file($packageDir.$src) and !is_file($baseDir.$dst) ) copy($packageDir.$src, $baseDir.$dst);
		}
		// done!
		return true;
	}


	// perform default install-operatoin of composer
	// ===> then perform custom install-operation of fuseboxy
	public function install(InstalledRepositoryInterface $repo, PackageInterface $package) {
		parent::install($repo, $package);
		// define target and source directories
		$baseDir = dirname($this->vendorDir).'/';
		$packageDir = $this->vendorDir.'/'.$package->getName().'/';
		// further adjust package location (when necessary)
		if ( !$this->isUnitTest() ) {
			$this->copyFile($this->vendorDir, $package->getName());
			// remove copied files
			// ===> also remove each parent directory (when empty)
			// ===> so that only core files (but not config file) remain in vendor directory
			foreach ( self::$file2remove[$package->getType()] as $file ) {
				unlink($packageDir.$file);
				$dir = dirname($file);
				while ( !empty($dir) and $dir != '.' ) {
					if ( empty(glob($packageDir.$dir.'/*')) ) rmdir($packageDir.$dir);
					$dir = dirname($dir);
				}
			}
			// remove certain directories
			// ===> so that git will put fuseboxy stuff into repo (instead of considering them as submodules)
			foreach ( self::$dir2remove[$package->getType()] as $dir ) Helper::rrmdir($packageDir.$dir);
		}
		// done!
		return true;
	}


	// perform default update-operation of composer
	// ===> then perform custom update-operation of fuseboxy
	public function update(InstalledRepositoryInterface $repo, PackageInterface $initial, PackageInterface $target) {
		parent::update($repo, $initial, $target);
		// define target directory
		$packageName = $target->getName();
		$packageDir = $this->vendorDir.'/'.$packageName.'/';
		// further adjust package location (when necessary)
		if ( !$this->isUnitTest() ) {
			// remove files that already copied
			// ===> (no need to copy files again in package update)
			// ===> also remove each parent directory (when empty)
			// ===> so that only core files (but not config file) remain in vendor directory
			foreach ( self::$file2remove[$target->getType()] as $file ) {
				unlink($packageDir.$file);
				$dir = dirname($file);
				while ( !empty($dir) and $dir != '.' ) {
					if ( empty(glob($packageDir.$dir.'/*')) ) rmdir($packageDir.$dir);
					$dir = dirname($dir);
				}
			}
			// remove certain directories
			// ===> so that git will put fuseboxy stuff into repo (instead of considering them as submodules)
			foreach ( self::$dir2remove[$target->getType()] as $dir ) Helper::rrmdir($packageDir.$dir);
		}
		// done!
		return true;
	}


} // class