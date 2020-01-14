<?php
namespace Fuseboxy\Composer;


use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;


class ModuleInstallerPlugin implements PluginInterface {

	// register custom installer for fuseboxy module
	public function activate(Composer $composer, IOInterface $io) {
		$installer = new _ModuleInstaller($io, $composer);
		$composer->getInstallationManager()->addInstaller($installer);
	}

}