<?php 
/**
 *@package Comment Role Capability
 */

namespace Inc\Base;

/**
 * add Shrotcode in Comment role plugin
 */

class Shortcodes
{
	
	function __construct()
	{
		
	}

	public function register()
	{
		add_shortcode( 'reply-comment',  array($this, 'comment_reply_activity'));
	}

	public function mytheme_enqueue_comment_reply()
	{
		wp_enqueue_script( 'comment-reply' ); 
	}
	
	public function comment_reply_activity()
	{
		// Hook into wp_enqueue_scripts
		add_action( 'comment_form_before', array($this, 'mytheme_enqueue_comment_reply' ));


		
		if(isset($_GET['post_id']) && isset($_GET['parent_id']) && isset($_GET['comment_id']))
		{
			wp_list_comments(  $args = array(),  $comments = $_GET['comment_id'] );

			comment_form(array(), $_GET['post_id']);
			echo '<script>
				setTimeout(function(){ jQuery(".comment-reply-link").trigger("click"); }, 1000);
				jQuery(".gpur-add-user-ratings-wrapper").remove(),jQuery(".gpur-comment-form-title").remove(),jQuery(".comment-form-attachment").remove(),jQuery(".comentlink").remove(),jQuery("#starrate").val("0");
			</script>';
		}
		
	}
}