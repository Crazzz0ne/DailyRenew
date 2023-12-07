<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * A command that handles IDE helper generation of helper file and PHPStorm metadata against platform environment.
 * This command performs these actions in development environment, else it does nothing, but does not crash the project.
 * @name IdeHelperHandler
 * @author Lyubomir Gardev
 * @verion 1.0
 */
class IdeHelperHandler extends Command
{

	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'ide-helper:handle';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Handles ide-helper generation through composer.';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$ide_helper_provider = '\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider';
		if (in_array(app()->environment(), ['local', 'development']) && class_exists($ide_helper_provider)) {
			$this->call('ide-helper:generate');
			$this->call('ide-helper:meta');
		}
	}
}