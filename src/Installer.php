<?php
namespace Fuseboxy\Composer;


use Composer\Installer\LibraryInstaller;
use Composer\Package\PackageInterface;
use Composer\Repository\InstalledRepositoryInterface;


class Installer extends LibraryInstaller {


	// properties
	private static $dir2remove = array(
		'.git',
	);
	private static $dir2create = array(
		'app/config',
		'app/controller',
	);
	private static $file2copy = array(
		'app/config/fusebox_config.php',
		'app/controller/error_controller.php',
		'app/controller/home_controller.php',
		'index.php',
		'.htaccess',
		'web.config',
	);


	// handle framework core only
	public function supports($packageType) {
		return in_array($packageType, ['fuseboxy-core']);
	}


	public function install(InstalledRepositoryInterface $repo, PackageInterface $package) {
		return $this->install__fuseboxyCore($repo, $package);
	}
	// copy certain resources to app-path to get started
	private function install__fuseboxyCore(InstalledRepositoryInterface $repo, PackageInterface $package) {
		die($package->getType().':abc-xyz');
		return true;
/*
		// perform default installation
		parent::install($repo, $package);
		// create directories
		$baseDir = dirname($this->vendorDir).'/';
		foreach ( self::$dir2create as $dir ) if ( !is_dir($baseDir.$dir) ) mkdir($baseDir.$dir, 0755, true);
		// copy selected files
		$packageDir = $this->vendorDir.'/'.$package->getName().'/';
		foreach ( self::$file2copy as $file ) if ( !is_file($baseDir.$file) ) copy($packageDir.$file, $baseDir.$file);
		// only keep framework core (and remove all others)
		foreach ( self::$file2copy as $file ) unlink($packageDir.$file);
		foreach ( self::$dir2create as $dir ) rmdir($packageDir.$dir);
		foreach ( self::$dir2remove as $dir ) Helper::rrmdir($packageDir.$dir);
		// done!
		return true;
*/
	}


	// remove certain resources and only keep framework core in vendor-path
	public function update(InstalledRepositoryInterface $repo, PackageInterface $initial, PackageInterface $target) {
		// perform default operation
		parent::update($repo, $initial, $target);
		// only keep framework core (and remove all others)
		$packageDir = $this->vendorDir.'/'.$target->getName().'/';
		foreach ( self::$file2copy as $file ) unlink($packageDir.$file);
		foreach ( self::$dir2create as $dir ) rmdir($packageDir.$dir);
		foreach ( self::$dir2remove as $dir ) Helper::rrmdir($packageDir.$dir);
		// done!
		return true;
	}


} // class