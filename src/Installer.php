<?php
namespace Fuseboxy\Composer;
use Composer\Installer\LibraryInstaller;
use Composer\Package\PackageInterface;
use Composer\Repository\InstalledRepositoryInterface;


class Installer extends LibraryInstaller {


	private static $dir2remove = array(
		'fuseboxy-core'   => ['.git'],
		'fuseboxy-module' => ['.git'],
	);
	private static $file2copy = array(
		'fuseboxy-core' => [
			'app/config/fusebox_config.php' => 'app/config/fuseboxy_config.xxx',
			'app/controller/error_controller.php',
			'app/controller/home_controller.php',
			'.htaccess',
			'index.php',
		],
	);
	private static $file2remove = array(
		'fuseboxy-core' => [
			'app/config/fusebox_config.php',
			'app/controller/error_controller.php',
			'app/controller/home_controller.php',
			'.htaccess',
			'index.php',
		],
	);


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


	public function supports($packageType) {
		return in_array($packageType, ['fuseboxy-core', 'fuseboxy-module']);
	}


	public function install(InstalledRepositoryInterface $repo, PackageInterface $package) {
		// perform default installation
		parent::install($repo, $package);
		// define target and source directories
		$baseDir = dirname($this->vendorDir).'/';
		$packageDir = $this->vendorDir.'/'.$package->getName().'/';
		// further adjust package location (when necessary)
		if ( !$this->isUnitTest() ) {
			// create directories
			foreach ( self::$file2copy[$package->getType()] as $src => $dst ) {
				$dir = dirname($dst);
				if ( $dir != '.' and !is_dir($baseDir.$dir) ) mkdir($baseDir.$dir, 0755, true);
			}
			// copy files
			// ===> do not overwrite!
			// ===> otherwise, modified config file or customized index will be overwritten everytime composer update is run
			foreach ( self::$file2copy[$package->getType()] as $src => $dst ) {
				if ( is_numeric($src) ) $src = $dst;
				if ( is_file($packageDir.$src) and !is_file($baseDir.$dst) ) copy($packageDir.$src, $baseDir.$dst);
			}
			// remove copied files
			// ===> so that only core files (but not config file) remain in vendor directory
			foreach ( self::$file2remove[$package->getType()] as $file ) Helper::rrmdir($packageDir.$file);
			// remove certain directories
			// ===> so that git will put fuseboxy stuff into repo (instead of considering them as submodules)
			foreach ( self::$dir2remove[$package->getType()] as $dir ) Helper::rrmdir($packageDir.$dir);
		}
		// done!
		return true;
	}


	public function update(InstalledRepositoryInterface $repo, PackageInterface $initial, PackageInterface $target) {
		// perform default operation
		parent::update($repo, $initial, $target);
		// define target directory
		$packageDir = $this->vendorDir.'/'.$target->getName().'/';
		// further adjust package location (when necessary)
		if ( !$this->isUnitTest() ) {
			// remove files that already copied
			// ===> (no need to copy files again in package update)
			// ===> so that only core files (but not config file) remain in vendor directory
			foreach ( self::$file2remove[$target->getType()] as $file ) Helper::rrmdir($packageDir.$file);
			// remove certain directories
			// ===> so that git will put fuseboxy stuff into repo (instead of considering them as submodules)
			foreach ( self::$dir2remove[$target->getType()] as $dir ) Helper::rrmdir($packageDir.$dir);
		}
		// done!
		return true;
	}


} // class