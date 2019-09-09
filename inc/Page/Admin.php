<?php 

/**

 *@package Comment Role Capability

 */

namespace Inc\Page;

use Inc\Admin\BrandOwner;

/**

 * Comment Role caplbility Admin pages

 */

class Admin

{

	

	function __construct()

	{

		# code...

	}



	public function register()

	{

		add_action( 'admin_menu', array($this, 'prs_register_menu') );

	}



	public function prs_register_menu()

	{

		add_menu_page( 'Accessbility', 'Accessbility', 'manage_options', 'prs-comment-capability', array($this, 'prs_comment_capability'), 'dashicons-visibility', 110 );
		add_submenu_page( 'prs-comment-capability', 'Customers', 'Customers', 'manage_options', 'customers_details', array($this, 'manage_customer_details') );


	}



	public function prs_comment_capability()

	{

		require PLUGIN_PATH.'/inc/Template/Accessbility.php';

	}

	public function manage_customer_details()
	{
		$brandowner = new BrandOwner();

		$brandowner->prs_manage_information();
		// $brandowner->tt_render_view_page();
	}

}