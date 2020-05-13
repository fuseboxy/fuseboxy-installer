<?php
namespace Fuseboxy\Composer;


use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;


class CoreInstallerPlugin implements PluginInterface {

	// register custom installer
	public function activate(Composer $composer, IOInterface $io) {
		$installer = new CoreInstaller($io, $composer);
		$composer->getInstallationManager()->addInstaller($installer);
	}

}