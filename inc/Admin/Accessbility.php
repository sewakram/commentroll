<?php
/**
 *@package Comment Role Capability
 */

namespace Inc\Admin;

/**
 * Declare class for Editor User Access Particular post comments only
 */
class Accessbility
{

	public function __construct(){

	}

	public function register($value='')
	{
		add_action( 'admin_post_prs_form_response', array($this, 'get_form_response') );
	    add_action( 'admin_notices', array( $this, 'add_flash_notice' ) );
	}

	public function get_form_response()
	{
			if($_POST['prs_users'] != '')
			{
				if( isset( $_POST['prs_user_access_nonce'] ) && wp_verify_nonce( $_POST['prs_user_access_nonce'], 'prs_add_user_access_form_nonce') ) {
					global $wpdb;
					$table = $wpdb->prefix."comment_user_access";
					$select = $wpdb->get_var("select count(*) from ".$table." where users=".$_POST['prs_users']);
					if($select)
					{
						$wpdb->update($table,
							array( 
								'post_access' => $_POST['post_active'],
								'time' => date('Y-m-d H:i:s')
							),
							array(
								'users' => absint($_POST['prs_users'])
							)
						);
						$userinfo = get_userdata( absint($_POST['prs_users']) );
						$msg = '<b style="color:green">Successfully! update</b> User <b>'.$userinfo->user_nicename.'</b> accessbility';
					}
					else
					{
						$wpdb->insert($table,
							array( 
								'users' => absint($_POST['prs_users']),
								'post_access' => $_POST['post_active'],
								'time' => date('Y-m-d H:i:s')
							)
						);
						$userinfo = get_userdata( absint($_POST['prs_users']) );
						$msg = '<b style="color:green">Successfully! insert</b> User <b>'.$userinfo->user_nicename.'</b> accessbility';
					}
					
					do_action( 'admin_notices', __($msg), "info", false );

					// add_flash_notice( __("My notice message, this is a warning and is dismissible"), "warning", true );
					// add_flash_notice( __("My notice message, this is an info, but, it is not dismissible"), "info", false );
					// add_flash_notice( __("My notice message, this is an error, but, it is not dismissible"), "error", false );
                    print('<script>window.location.href="admin.php?page=prs-comment-capability"</script>');
					exit;
				}
				else {
					wp_die( __( 'Invalid nonce specified', PLUGIN_NAME ), __( 'Error', PLUGIN_NAME ), array(
								'response' 	=> 403,
								'back_link' => 'admin.php?page=' . PLUGIN_NAME,
						) );
				}
			}
			else
			{
				$msg = '<b style="color:red">Error!</b> Please Select Editor Users';
					do_action( 'admin_notices', __($msg), "error", false );
					print('<script>window.location.href="admin.php?page=prs-comment-capability"</script>');
					exit;
			}
				
	}

	public function add_flash_notice( $notice = "", $type = "warning", $dismissible = true ) {
	    // Here we return the notices saved on our option, if there are not notices, then an empty array is returned
	    $notices = get_option( "my_flash_notices", array() );
	 
	    $dismissible_text = ( $dismissible ) ? "is-dismissible" : "";
	 
	    // We add our new notice.
	    array_push( $notices, array( 
	            "notice" => $notice, 
	            "type" => $type, 
	            "dismissible" => $dismissible_text
	        ) );
	 
	    // Then we update the option with our notices array
	    update_option("my_flash_notices", $notices );
    }

	public function prs_adminmanage()
	{

		
	
	} 

}