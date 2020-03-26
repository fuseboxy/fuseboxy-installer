<?php
namespace Fuseboxy\Composer;


use Composer\Installer\LibraryInstaller;
use Composer\Package\PackageInterface;
use Composer\Repository\InstalledRepositoryInterface;


class Installer extends LibraryInstaller {


	// properties
	private static $dirToCreate = array(
		'app/config',
		'app/controller',
	);
	private static $fileToCopy = array(
		'app/config/fusebox_config.php',
		'app/controller/error_controller.php',
		'app/controller/home_controller.php',
		'index.php',
		'.htaccess',
		'web.config',
	);


	// only framework core needs custom handling
	public function supports($packageType) {
		return ( $packageType == 'fuseboxy-core' );
	}


	// copy selected files to app-path for first install
	public function install(InstalledRepositoryInterface $repo, PackageInterface $package) {
		// perform default installation
		parent::install($repo, $package);
		// create directories
		$baseDir = dirname($this->vendorDir).'/';
		foreach ( self::$dirToCreate as $dir ) if ( !is_dir($baseDir.$dir) ) mkdir($baseDir.$dir, 0755, true);
		// copy selected files
		$packageDir = $this->vendorDir.'/'.$package->getName().'/';
		foreach ( self::$fileToCopy as $file ) copy($packageDir.$file, $baseDir.$file);
		// only keep framework core (and remove all others)
		foreach ( self::$fileToCopy  as $file ) unlink($packageDir.$file);
		foreach ( self::$dirToCreate as $dir ) rmdir($packageDir.$dir);
		// done!
		return true;
	}


	// only keep framework core in vendor-path
	public function update(InstalledRepositoryInterface $repo, PackageInterface $initial, PackageInterface $target) {
		// perform default operation
		parent::update($repo, $initial, $target);
		// only keep framework core (and remove all others)
		$packageDir = $this->vendorDir.'/'.$package->getName().'/';
		foreach ( self::$fileToCopy  as $file ) unlink($packageDir.$file);
		foreach ( self::$dirToCreate as $dir ) rmdir($packageDir.$dir);
		// done!
		return true;
	}


} // class