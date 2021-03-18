<?php

namespace Yauhenko\Composer\Plugins\Version;

use Composer\Plugin\Capability\CommandProvider as CommandProviderCapability;

class CommandProvider implements CommandProviderCapability {

	public function getCommands(): array {
		return [
			new Command()
		];
	}

}
