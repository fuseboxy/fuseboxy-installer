<?php
namespace Fuseboxy\Composer;
use Composer\Installer\LibraryInstaller;
use Composer\Package\PackageInterface;
use Composer\Repository\InstalledRepositoryInterface;



// custom installer for fuseboxy
class Installer extends LibraryInstaller {


	// list of directories to remove after install or update
	// ===> so that git will track fuseboxy stuff (instead of considering them as submodules)
	private static $dir2remove = array( '*' => ['.git'] );


	// list of files to copy after install (only)
	private static $file2copy = array(
		'fuseboxy/fuseboxy-auth' => [
			'app/view/auth/layout.settings.php-default' => 'app/view/auth/layout.settings.php',
		],
		'fuseboxy/fuseboxy-core' => [
			'app/config/fusebox_config.php',
			'app/controller/error_controller.php',
			'app/controller/home_controller.php',
			'.htaccess',
			'index.php',
			'_env.php',
			'_env.php' => '_env.php.EMPTY',
			'_env.php' => '_env.php.UAT',
			'_env.php' => '_env.php.PRD',
		],
		'fuseboxy/fuseboxy-layout' => [
			'app/view/global/layout.settings.php-default' => 'app/view/global/layout.settings.php',
		],
	);


	// list of files to remove after install or update
	private static $file2remove = array(
		'fuseboxy/fuseboxy-core' => [
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


	// proceed custom operation to copy files
	public function customCopyFile($packageName) {
		$baseDir = dirname($this->vendorDir).'/';
		$packageDir = $this->vendorDir.'/'.$packageName.'/';
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


	// proceed custom operation to remove files
	public function customRemoveFile($packageName) {
		$baseDir = dirname($this->vendorDir).'/';
		$packageDir = $this->vendorDir.'/'.$packageName.'/';
		// obtain file list
		if ( isset(self::$file2remove['*']) ) $file2remove = self::$file2remove['*'];
		elseif ( isset(self::$file2remove[$packageName]) ) $file2remove = self::$file2remove[$packageName];
		else $file2remove = [];
		// go through each specified file
		foreach ( $file2remove as $file ) {
			// remove specified file
			unlink($packageDir.$file);
			// also remove each parent directory (when empty)
			// ===> so that only core files (but not config file) remain in vendor directory
			$dir = dirname($file);
			while ( !empty($dir) and $dir != '.' ) {
				if ( empty(glob($packageDir.$dir.'/*')) ) rmdir($packageDir.$dir);
				$dir = dirname($dir);
			}
		}
		// done!
		return true;
	}


	// proceed custom operation to remove directory (and content)
	public function customRemoveDir($packageName) {
		$baseDir = dirname($this->vendorDir).'/';
		$packageDir = $this->vendorDir.'/'.$packageName.'/';
		// obtain directory list
		if ( isset(self::$dir2remove['*']) ) $dir2remove = self::$dir2remove['*'];
		elseif ( isset(self::$dir2remove[$packageName]) ) $dir2remove = self::$dir2remove[$packageName];
		else $dir2remove = [];
		// remove certain directories
		foreach ( $dir2remove as $dir ) Helper::rrmdir($packageDir.$dir);
		// done!
		return true;
	}


	// perform default install-operatoin of composer
	// ===> then perform custom install-operation of fuseboxy
	public function install(InstalledRepositoryInterface $repo, PackageInterface $package) {
		parent::install($repo, $package);
		// further adjust package location
		if ( !$this->isUnitTest() ) {
			$this->customCopyFile($package->getName());
			$this->customRemoveFile($package->getName());
			$this->customRemoveDir($package->getName());
		}
		// done!
		return true;
	}


	// perform default update-operation of composer
	// ===> then perform custom update-operation of fuseboxy
	public function update(InstalledRepositoryInterface $repo, PackageInterface $initial, PackageInterface $target) {
		parent::update($repo, $initial, $target);
		// further adjust package location
		if ( !$this->isUnitTest() ) {
			// (no need to copy file agains when package update)
			$this->customRemoveFile($target->getName());
			$this->customRemoveDir($target->getName());
		}
		// done!
		return true;
	}


} // class