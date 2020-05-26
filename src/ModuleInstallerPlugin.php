<?php
namespace Fuseboxy\Composer;


use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;


class ModuleInstallerPlugin implements PluginInterface {

	// register custom installer
	public function activate(Composer $composer, IOInterface $io) {
		$installer = new xModuleInstaller($io, $composer);
		$composer->getInstallationManager()->addInstaller($installer);
	}

}