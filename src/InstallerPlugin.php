<?php
namespace Fuseboxy\Composer;


use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;


class InstallerPlugin implements PluginInterface {

	// register custom installer
	public function activate(Composer $composer, IOInterface $io) {
		$coreInstaller = new CoreInstaller($io, $composer);
		$moduleInstaller = new ModuleInstaller($io, $composer);
		$composer->getInstallationManager()->addInstaller($coreInstaller);
		$composer->getInstallationManager()->addInstaller($moduleInstaller);
	}

}