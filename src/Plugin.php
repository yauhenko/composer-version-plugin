<?php

namespace Yauhenko\Composer\Plugins\Version;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\Capable;
use Composer\Plugin\PluginInterface;
use Composer\Plugin\Capability\CommandProvider as CommandProviderCapability;

class Plugin implements PluginInterface, Capable {

	public static Composer $composer;

	public function activate(Composer $composer, IOInterface $io) {
		self::$composer = $composer;
	}

	public function deactivate(Composer $composer, IOInterface $io) {
		// Nothing to do
	}

	public function uninstall(Composer $composer, IOInterface $io) {
		// Nothing to do
	}

	public function getCapabilities(): array {
		return [
			CommandProviderCapability::class => CommandProvider::class
		];
	}

}
