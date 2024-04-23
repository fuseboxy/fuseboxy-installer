<?php
namespace Fuseboxy\Composer;
use Composer\Installer\LibraryInstaller;
use Composer\Package\PackageInterface;
use Composer\Repository\InstalledRepositoryInterface;


// custom installer for fuseboxy
class Installer extends LibraryInstaller {


	// after install (but not update)
	// ===> copy following files from vendor directory to app directory
	private static $file2copy = array(
		'fuseboxy/fuseboxy-core' => [
			'app/config/fusebox_config.php',
			'app/controller/error_controller.php',
			'app/controller/home_controller.php',
			'.htaccess',
			'_env.php',
			'index.php',
		],
		'fuseboxy/fuseboxy-auth' => [
			'app/view/auth/layout.settings.php-default' => 'app/view/auth/layout.settings.php',
		],
		'fuseboxy/fuseboxy-layout' => [
			'app/view/global/layout.settings.php-default' => 'app/view/global/layout.settings.php',
		],
	);


	// after install or update
	// ===> remove following files from vendor directory
	private static $file2remove = array(
		'fuseboxy/fuseboxy-core' => [
			'app/config/fusebox_config.php',
			'app/controller/error_controller.php',
			'app/controller/home_controller.php',
			'.htaccess',
			'_env.php',
			'index.php',
		],
	);


	// check package type
	// ===> determine whether to install the package by this installer
	public function supports($packageType) {
		return in_array($packageType, ['fuseboxy-core', 'fuseboxy-module']);
	}


	// proceed custom operation to copy files
	public function customCopyFile($packageName) {
		$baseDir = dirname($this->vendorDir).'/';
		$packageDir = $this->vendorDir.'/'.$packageName.'/';
		// obtain file list
		$file2copy = self::$file2copy['*'] ?? self::$file2copy[$packageName] ?? [];
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
	}


	// proceed custom operation to remove files
	public function customRemoveFile($packageName) {
		$baseDir = dirname($this->vendorDir).'/';
		$packageDir = $this->vendorDir.'/'.$packageName.'/';
		// obtain file list
		$file2remove = self::$file2remove['*'] ?? self::$file2remove[$packageName] ?? [];
		// go through each specified file
		foreach ( $file2remove as $file ) {
			// remove specified file
			if ( is_file($packageDir.$file) ) unlink($packageDir.$file);
			// also remove each parent directory (when empty)
			// ===> so that only core files (but not config file) remain in vendor directory
			$dir = dirname($file);
			while ( !empty($dir) and $dir != '.' ) {
				if ( empty(glob($packageDir.$dir.'/*')) and is_dir($packageDir.$dir) ) rmdir($packageDir.$dir);
				$dir = dirname($dir);
			}
		}
	}


	// perform default install-operation of composer
	// ===> then perform custom install-operation of fuseboxy
	public function install(InstalledRepositoryInterface $repo, PackageInterface $package) {
		$result = parent::install($repo, $package);
		// further adjust package location
		$this->customCopyFile($package->getName());
		$this->customRemoveFile($package->getName());
		// done!
		return $result;
	}


	// perform default update-operation of composer
	// ===> then perform custom update-operation of fuseboxy
	public function update(InstalledRepositoryInterface $repo, PackageInterface $initial, PackageInterface $target) {
		$result = parent::update($repo, $initial, $target);
		// further adjust package location
		// ===> no need to copy file when package update
		// ===> to avoid overwriting modified settings file
		$this->customRemoveFile($target->getName());
		// done!
		return $result;
	}


} // class