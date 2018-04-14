<?php
namespace App\Providers;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Support\ServiceProvider;

class ResponseServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot(ResponseFactory $factory) {
		$factory->macro('api', function ($data = ['data' => []], $httpCode = 200) use ($factory) {

			/*
				            if (! isset($data['success'])) {
				                if (((string) $httpCode)[0] == 2) {
				                    $data['success'] = 1;
				                } else {
				                    $data['success'] = 0;
				                }
				            }
				            if (! isset($data['message'])) {
				                $data['message'] = '';
			*/
			return $factory->json($data, $httpCode);
		});
	}

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register() {
		//
	}
}
