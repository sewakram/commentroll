<?php 
/**
 *@package Comment Role Capability
 */

namespace Inc\Base;

/**
 * add Enqueue Scripts in Comment role plugin
 */
class EnqueueScripts
{
	
	function __construct()
	{
		# code...
	}

	public function register($value='')
	{
		add_action( 'admin_enqueue_scripts', array($this, 'prs_admin_scripts'));
		add_action( 'wp_enqueue_scripts', array($this, 'prs_front_end_script') );
		add_action( 'wp_ajax_option_action', array($this, 'prs_option_action') );
	}


	public function prs_admin_scripts($hook)
	{	
		wp_enqueue_style( 'prs-tab-styles', PLUGIN_URL. 'inc/Template/assets/prs_tab_styles.css', '', '1.0','' );
		if ( 'toplevel_page_prs-comment-capability' != $hook ) {
		        return;
		    }
		    wp_enqueue_script( 'prs-custom-script', PLUGIN_URL. 'inc/Template/assets/prs_search_script.js', array('jquery'), '1.0', false );
		    wp_localize_script( 'prs-custom-script', 'prs_object',
            array( 'ajax_url' => admin_url( 'admin-ajax.php' ),  'nonce' => wp_create_nonce( 'jsforwp_prs_nonce' ) ) );
	}

	public function prs_front_end_script()
	{
		wp_enqueue_style( 'prs_css_register', PLUGIN_URL.'/inc/Template/assets/registration.css');
		wp_enqueue_script( 'prs-register-validation', PLUGIN_URL.'/inc/Template/assets/prs_register_valid.js', 'jquery', time(), true );
	}

	public function prs_option_action()
	{
		$options = get_option('prs_brand_mail');
		$post = $_POST['keyword'];
		if($options['brand_owner_activity'] ["$post"] == 0):
			$options['brand_owner_activity'] ["$post"] = 1;
			 if(update_option( 'prs_brand_mail', $options )):
			 	echo '<p id="response" style="color:green">Enable Successfully!</p>'; exit;
			 else:
			 	echo '<p id="response" style="color:red">Error! Something Wrong!</p>'; exit;
			 endif;
	    else:
	    	$options['brand_owner_activity'] ["$post"] = 0;
			if(update_option( 'prs_brand_mail', $options )):
			 	echo '<p id="response" style="color:green">Disable Successfully!</p>'; exit;
			 else:
			 	echo '<p id="response" style="color:red">Error! Something Wrong!</p>'; exit;
			 endif;
		endif;
		die();
	}
}
