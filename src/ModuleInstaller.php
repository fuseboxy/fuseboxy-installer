<?php
namespace Fuseboxy\Composer;


use Composer\Installer\LibraryInstaller;
use Composer\Package\PackageInterface;
use Composer\Repository\InstalledRepositoryInterface;


class ModuleInstaller extends LibraryInstaller {


	// properties
	private static $dir2remove = array('.git');


	// handle module only
	public function supports($packageType) {
		return ( $packageType == 'fuseboxy-module' );
	}


	// remove git directory after installation
	public function install(InstalledRepositoryInterface $repo, PackageInterface $package) {
		// perform default installation
		parent::install($repo, $package);
		// remove directory (and contents)
		foreach ( self::$dir2remove as $dir ) Helper::rrmdir($this->vendorDir.'/'.$package->getName().'/'.$dir);
		// done!
		return true;
	}


	// remove git directory after update
	public function update(InstalledRepositoryInterface $repo, PackageInterface $initial, PackageInterface $target) {
		// perform default operation
		parent::update($repo, $initial, $target);
		// remove directory (and contents)
		foreach ( self::$dir2remove as $dir ) Helper::rrmdir($this->vendorDir.'/'.$package->getName().'/'.$dir);
		// done!
		return true;
	}


} // class