<?php

namespace Fiorello\AffinityIntegration;

use Illuminate\Support\ServiceProvider;

class AffinityIntegrationServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('fiorello/affinity-integration');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{

		$this->app['affinity'] = $this->app->share(function() {
			return new Affinity;
		});
		/*
		* Register the service provider for the dependency.
		*/

		$this->app->booting(function()
		{
			$loader = \Illuminate\Foundation\AliasLoader::getInstance();
			$loader->alias('Affinity', 'Fiorello\AffinityIntegration\Facades\Affinity');
			$loader->alias('SoapClient', 'SoapClient::class');
		});

	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}
