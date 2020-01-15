<?php
namespace Fuseboxy\Composer;


use Composer\Installer\LibraryInstaller;
use Composer\Package\PackageInterface;
use Composer\Repository\InstalledRepositoryInterface;



class Installer extends LibraryInstaller {

	public function supports($packageType) {
		return ( strpos($packageType, 'fuseboxy-') === 0 );
	}

	public function getInstallPath(PackageInterface $package) {
		return 'app/';
	}

}