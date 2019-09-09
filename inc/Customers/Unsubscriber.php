<?php
/**
 *@package Comment Role Capability
 */
namespace Inc\Customers;
/**
 * Register Customers free account & connect with your customers
 */
class Unsubscriber
{
	
	function __construct()
	{
		add_action( 'wp_enqueue_scripts', array($this, 'prs_front_end_script') );
		
		add_shortcode( 'unsubscribe_form', array($this, 'prs_unsubscribe_shortcode') );
		
	}

	public function prs_front_end_script()
	{
		wp_enqueue_style( 'prs_css_register', PLUGIN_URL.'/inc/Template/assets/registration.css');
		wp_enqueue_script( 'prs-register-validation', PLUGIN_URL.'/inc/Template/assets/prs_register_valid.js', 'jquery', time(), true );
	}

	public function prs_unsubscribe_shortcode() {
        if (isset($_POST['insert'])) {
            // echo $_GET['comment_id'];
        global $wpdb;
        
			$table = $wpdb->prefix."comments";
			$result=$wpdb->update( $table, array( 'notification' => 0,'reason' => $_POST['reason'] ), array( 'comment_ID' => $_GET['comment_id'] ) );
			if ($result) {

				echo "<h3 id='mydiv'>Thanks! You have unsubscribed successfully</h3>";
				?><script>
					setTimeout(function() {
					$('#mydiv').fadeOut('fast');
					}, 3000); 
				</script>
			<?php }else{
				echo "<h3 id='mydiv'>Whoops! Something went wrong</h3>";
			}
      
	 }
    ?>
    <!-- <link type="text/css" href="<?php //echo WP_PLUGIN_URL; ?>/product/style-admin.css" rel="stylesheet" /> -->

    <div class="wrap">
       
        <?php if (isset($message)): ?><div class="updated"><p><?php echo $message; ?></p></div><?php endif; ?>
        <form method="post" enctype="multipart/form-data" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
            <!-- <p>Three capital letters for the ID</p> -->
            <table class='wp-list-table widefat fixed'>
                
                
                
                <tr>
                    <th class="ss-th-width">Reason to unsubscribe</th>
                    <td><textarea type="text" name="reason" required="required"  class="ss-field-width" /></textarea></td>
                </tr>
                
            </table>
            <input type='submit' name="insert" value='Save' class='button'>
        </form>
    </div>
    
    <?php

    }

    
}