<?php
namespace Fuseboxy\Composer;


use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;


class InstallerPlugin implements PluginInterface {

	// register custom installer for fuseboxy core
	public function activate(Composer $composer, IOInterface $io) {
		$installer = new Installer($io, $composer);
		$composer->getInstallationManager()->addInstaller($installer);
	}

}