<?php

/**

 *@package Comment Role Capability

 */

namespace Inc\Admin;



/**

 * Activate rewrite rules in comment role capability plugin

 */

class Activate
{

	public static function activate()
	{
		flush_rewrite_rules();
	}

}