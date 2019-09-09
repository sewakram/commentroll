<?php
/**
 *@package Comment Role Capability
 */
namespace Inc\Customers;
/**
 * Eail notification of comments to your customers
 */
class Emailnotification
{
	
	function __construct()
	{
		// add_action( 'wp_enqueue_scripts', array($this, 'prs_front_end_script') );
		add_action('comment_post',array($this,'pulse_alert'), 11, 2);
		add_action('transition_comment_status',array($this, 'my_approve_comment_callback'), 10, 3);
		// do_action( 'comment_post', 'pulse_alertdo', 10, 2);
	}

	public function pulse_alert($comment_ID, $approved) {  
	global $pagenow;	
	$comment = get_comment( $comment_ID );
    $comment_author_email=$comment->comment_author_email;
	$post = get_post( $comment->comment_post_ID );
	$user = get_user_by( 'id', $post->post_author );
     // echo "<pre>";print_r($comment->notification);die();
		if($comment->notification==1){
		if($comment_ID){
				$headers = "MIME-Version: 1.0" . "\n";
				$headers .= "Content-type:text/html;charset=UTF-8" . "\n";
				$headers .= 'Cc:'.get_bloginfo( 'admin_email' ). "\r\n";
			if ( $approved == 1 ) {
				$variables = array();
				$variables['admin_user'] = $user->display_name;
				$variables['post_copy_year']=date("Y");
				$variables['post_sitename']=get_bloginfo( 'name' );
				$variables['post_siteemail']=get_bloginfo( 'admin_email' );
				$variables['post_us_link']=site_url('unsubscribe/').'?comment_id='.$comment->comment_ID;//admin_url('?page=unsubscribe-comment-notification').'&comment_id='.$comment->comment_ID;
			$htmlContent = file_get_contents(dirname(__FILE__).'/emailapproveadmin.html');
			foreach($variables as $x => $value) {
				$htmlContent=str_replace($x,$value,$htmlContent);
				}
			mail( $user->user_email, "Comment created and Approved ", $htmlContent,$headers );

		}else{
					$emailcommentpost=array();
					$emailcommentpost['post_user']=$comment->comment_author;
					$emailcommentpost['post_copy_year']=date("Y");
					$emailcommentpost['post_sitename']=get_bloginfo( 'name' );
					$emailcommentpost['post_siteemail']=get_bloginfo( 'admin_email' );
					$emailcommentpost['post_us_link']=site_url('unsubscribe/').'?comment_id='.$comment->comment_ID;//admin_url( '?page=unsubscribe-comment-notification').'&comment_id='.$comment->comment_ID;
				$commentpostContent = file_get_contents(dirname(__FILE__).'/emailcommentpost.html');
				foreach($emailcommentpost as $x => $value) {
					$commentpostContent=str_replace($x,$value,$commentpostContent);
					}
				mail( $comment_author_email, "Comment has been posted successfull", $commentpostContent,$headers);
				$parentcomment = get_comment( $comment->comment_parent);
					if($parentcomment->notification==1){
				if($comment->comment_parent!=0){
							$author = get_comment_author_email( $comment->comment_parent );
							$emailcommentreply=array();
							$emailcommentreply['replay_user']=get_comment_author($comment->comment_parent);
							$emailcommentreply['post_copy_year']=date("Y");
							$emailcommentreply['post_sitename']=get_bloginfo( 'name' );
							$emailcommentreply['post_siteemail']=get_bloginfo( 'admin_email' );
							$emailcommentreply['post_parentmsg']=$parentcomment->comment_content;
							$emailcommentreply['post_msg']=$comment->comment_content;
							$emailcommentreply['post_replay_link']=site_url('replyemail/?').'comment_id='.$comment->comment_ID.'&parent_id='.$comment->comment_parent.'&post_id='.$comment->comment_post_ID;
							$emailcommentreply['post_us_link']=site_url('unsubscribe/').'?comment_id='.$comment->comment_ID;
						$commentreplyContent = file_get_contents(dirname(__FILE__).'/emailcommentreply.html');
						foreach ($emailcommentreply as $key => $value) {
								$commentreplyContent=str_replace($key,$value,$commentreplyContent);
							}
						mail( $author, "Reply on comment", $commentreplyContent,$headers );
						// mail(get_bloginfo( 'admin_email' ), "Reply on comment", $commentreplyContent,$headers );

				}else{
							$emailparentapprove=array();
							$emailparentapprove['parent_user']=$user->display_name;
							$emailparentapprove['post_copy_year']=date("Y");
							$emailparentapprove['post_sitename']=get_bloginfo( 'name' );
							$emailparentapprove['post_siteemail']=get_bloginfo( 'admin_email' );
							$emailparentapprove['post_us_link']=site_url('unsubscribe/').'?comment_id='.$comment->comment_ID;//admin_url( '?page=unsubscribe-comment-notification').'&comment_id='.$comment->comment_ID;
						$parentapproveContent = file_get_contents(dirname(__FILE__).'/emailparentapprove.html');
						foreach ($emailparentapprove as $key => $value) {
							$parentapproveContent=str_replace($key,$value,$parentapproveContent);
							}
						mail( $user->user_email, "New comment posted", $parentapproveContent,$headers );	
				}
			}
				
			}

			}
	}
			
	}
  
	public function my_approve_comment_callback($new_status, $old_status, $comment) {
	if($old_status != $new_status) {
		// echo "hooooollaaaaaaaaaa";exit;
		$headers = "MIME-Version: 1.0" . "\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\n";
	    if($new_status == 'approved') {
	        
	        $adminapprove=array();
	        $adminapprove['appove_user']=$comment->comment_author;
	        $adminapprove['post_copy_year']=date("Y");
	        $adminapprove['post_sitename']=get_bloginfo( 'name' );
	        $adminapprove['post_siteemail']=get_bloginfo( 'admin_email' );
	        $adminapprove['post_us_link']=site_url('unsubscribe/').'?comment_id='.$comment->comment_ID;//admin_url( '?page=unsubscribe-comment-notification').'&comment_id='.$comment->comment_ID;
	        $adminapproveContent = file_get_contents(dirname(__FILE__).'/adminapprove.html');

	        foreach ($adminapprove as $key => $value) {
	        	$adminapproveContent=str_replace($key,$value,$adminapproveContent);
	        }
	        // var_export($adminapproveContent);exit;
	        mail( $comment->comment_author_email, "comment has been approve",$adminapproveContent,$headers );
	   }else if($new_status == 'unapproved') {
	        
	        $adminunapprove=array();
	        $adminunapprove['unappove_user']=$comment->comment_author;
	        $adminunapprove['post_copy_year']=date("Y");
	        $adminunapprove['post_sitename']=get_bloginfo( 'name' );
	        $adminunapprove['post_siteemail']=get_bloginfo( 'admin_email' );
	        $adminunapprove['post_us_link']=site_url('unsubscribe/').'?comment_id='.$comment->comment_ID;//admin_url( '?page=unsubscribe-comment-notification').'&comment_id='.$comment->comment_ID;
	        $adminunapproveContent = file_get_contents(dirname(__FILE__).'/adminunapprove.html');
	        foreach ($adminunapprove as $key => $value) {
	        	$adminunapproveContent=str_replace($key,$value,$adminunapproveContent);
	        }
	        mail( $comment->comment_author_email, "comment has been unapproved", $adminunapproveContent,$headers );
	    }
	}
   }
	
	
}