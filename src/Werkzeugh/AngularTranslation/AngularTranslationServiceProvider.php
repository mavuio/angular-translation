<?php namespace Werkzeugh\AngularTranslation;

use Illuminate\Support\ServiceProvider;

class AngularTranslationServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
       // $this->app->booting(function()
       // {
	   $this->app->register('Waavi\Translation\TranslationServiceProvider');
       // });

	}

	public function boot()
	{


     \Route::controller('/public_endpoints/angular_translation', 'Werkzeugh\AngularTranslation\AngularTranslationController');

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
