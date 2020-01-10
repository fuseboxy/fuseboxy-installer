<?php
namespace Fuseboxy\Composer;


use Composer\Installer\InstallerInterface;
use Composer\Package\PackageInterface;
use Composer\Repository\InstalledRepositoryInterface;


class ModuleInstaller implements InstallerInterface {

	public function supports($packageType) {

	}

	public function isInstalled(InstalledRepositoryInterface $repo, PackageInterface $package) {

	}

	public function install(InstalledRepositoryInterface $repo, PackageInterface $package) {

	}

	public function update(InstalledRepositoryInterface $repo, PackageInterface $initial, PackageInterface $target) {

	}

	public function uninstall(InstalledRepositoryInterface $repo, PackageInterface $package) {

	}

	public function getInstallPath(PackageInterface $package) {

	}

}