<?php

namespace Yauhenko\Composer\Plugins\Version;

use Composer\Command\BaseCommand;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Command extends BaseCommand {

	protected function configure() {
		$this->setName('version');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int {
		$io = new SymfonyStyle($input, $output);

		$baseDir = realpath(Plugin::$composer->getConfig()->get('vendor-dir') . '/..');

		if(!$baseDir || !is_dir($baseDir)) {
			$io->error('Failed to detect project base dir');
			return 1;
		}

		$file = $baseDir . '/composer.json';

		if(!file_exists($file) || !is_file($file)) {
			$io->error('Failed to detect composer.json location');
			return 1;
		}

		$package = json_decode(file_get_contents($file), true);

		if(!isset($package['version']) || !$package['version']) {
			$package['version'] = '0.0.0';
		}

		$version = explode('.', $package['version']);

		if(isset($version[2])) {
			$version[2]++;
			$package['version'] = implode('.', $version);
		} else {
			$io->error('Patch version could not be parsed');
			return 1;
		}

		chdir($baseDir);

		if(!str_contains(exec('git --version'), 'git version')) {
			$io->error('Git is not installed');
			return 1;
		}

		exec('git status -s', $lines, $code);

		if($code) {
			$io->error('Failed to fetch git status. Code: ' . $code);
			return 1;
		}

		if($lines) {
			$io->error(['You have uncommitted changes:', ...$lines]);
			return 1;
		}

		if(!@file_put_contents($file, str_replace("    ", "\t", json_encode($package, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)))) {
			$io->error('Failed to write composer.json');
			return 1;
		}

		exec('git add ' . $file);
		exec('git commit -m ' . escapeshellarg("version updated to {$package['version']}"));
		exec('git tag v' . escapeshellarg("v{$package['version']}"));

		$io->success('Version updated: ' . $package['version']);

		return 0;
	}

}
