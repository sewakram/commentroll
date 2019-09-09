<?php
/**
 *@package Comment Role Capability
 */
namespace Inc\Admin;

/**
 * Activate rewrite rules in comment role capability plugin
 */
class Deactivate
{
	
	public static function deactivate()
	{
		flush_rewrite_rules();
	}
}