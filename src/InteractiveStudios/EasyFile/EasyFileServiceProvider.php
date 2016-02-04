<?php namespace InteractiveStudios\EasyFile;

use Illuminate\Support\ServiceProvider;

class EasyFileServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		if (! $this->app->routesAreCached()) {
			require __DIR__.'/../../routes.php';
		}
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{

	}

}
