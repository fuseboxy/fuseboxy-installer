<?php
namespace Fuseboxy\Composer;


use Composer\Installer\LibraryInstaller;
use Composer\Package\PackageInterface;
use Composer\Repository\InstalledRepositoryInterface;


class Installer extends LibraryInstaller {


	private static $dir2remove = array(
		'fuseboxy-core'   => [],
		'fuseboxy-module' => [],
	);


	private static $file2move = array(
		'fuseboxy-core' => [
			'app/config/fusebox_config.php',
			'app/controller/error_controller.php',
			'app/controller/home_controller.php',
			'.htaccess',
			'index.php',
			'web.config',
		],
		'fuseboxy-module' => [],
	);


	public function supports($packageType) {
		return in_array($packageType, ['fuseboxy-core', 'fuseboxy-module']);
	}


	public function install(InstalledRepositoryInterface $repo, PackageInterface $package) {
		// perform default installation
		parent::install($repo, $package);
		// define target and source directories
		$baseDir = dirname($this->vendorDir).'/';
		$packageDir = $this->vendorDir.'/'.$package->getName().'/';
		// create directories
		foreach ( self::$file2move[$package->getType()] as $file ) {
			$dir = dirname($file);
			if ( $dir != '.' and !is_dir($baseDir.$dir) ) mkdir($baseDir.$dir, 0755, true);
		}
		// copy files
		// ===> do not overwrite!
		// ===> otherwise, modified config file or customized index will be overwritten everytime composer update is run
		foreach ( self::$file2move[$package->getType()] as $file ) if ( !is_file($baseDir.$file) ) copy($packageDir.$file, $baseDir.$file);
		// remove copied files
		// ===> so that only core files (but not config file) remain in vendor directory
		foreach ( self::$file2move[$package->getType()] as $file ) Helper::rrmdir($packageDir.$file);
		// remove certain directories
		// ===> so that git will put fuseboxy stuff into repo (instead of considering them as submodules)
		foreach ( self::$dir2remove[$package->getType()] as $dir ) Helper::rrmdir($packageDir.$dir);
		// done!
		return true;
	}


	public function update(InstalledRepositoryInterface $repo, PackageInterface $initial, PackageInterface $target) {
		// perform default operation
		parent::update($repo, $initial, $target);
		// define target directory
		$packageDir = $this->vendorDir.'/'.$target->getName().'/';
		// remove files that already copied in installation
		// ===> so that only core files (but not config file) remain in vendor directory
		foreach ( self::$file2move[$target->getType()] as $file ) Helper::rrmdir($packageDir.$file);
		// remove certain directories
		// ===> so that git will put fuseboxy stuff into repo (instead of considering them as submodules)
		foreach ( self::$dir2remove[$target->getType()] as $dir ) Helper::rrmdir($packageDir.$dir);
		// done!
		return true;
	}


} // class