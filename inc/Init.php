<?php 

/**

 *@package Comment Role Capability

 */



namespace Inc;



/**

 * Initialize class Details of review rating

 */

final class Init

{

	

	private static function get_services()

	{

		return [

			Page\Admin::class,

			Base\SettingLinks::class,

			Base\EnqueueScripts::class,

			Admin\Accessbility::class,

			Base\Shortcodes::class,

		];

	}



	public static function register_service()

	{

		foreach (self::get_services() as $class) {

			$service = self::instantiate($class);

			if(method_exists($service, 'register'))

			{

				$service->register();

			}

		}

	}



	private static function instantiate($class)

	{

		$service = new $class();



		return $service;

	}

}