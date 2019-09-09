<?php
/**
 *@package Comment Role Capability
 */
/*
 Plugin Name: Comment Accessbility
 Plugin URI: https://github.com/pravinshrikhande
 Description: This plugin editor role user accessbility. The particular editor can access only on comment list in your websites. The user can show only particular post comments accessbility - Add, Edit, Delete, Reply, Approve, Spam, Pending on comment. This user accessbility assign by admin section.
 Version: 1.0
 Author: Pravin Shrikhande
 Author URI: https://github.com/pravinshrikhande
 Licence: git GPL - pra,prs,ekka (Pravin Shrikhande)
 Licence URI: https://github.com/pravinshrikhande
 Text Domain: prs
 */
defined('ABSPATH') or die('Hey You Are Silly User');
define('PLUGIN_PATH', plugin_dir_path( __FILE__ ));
define('PLUGIN_URL', plugin_dir_url( __FILE__ ));
define('PLUGIN_NAME', dirname(__FILE__));
if(file_exists(dirname( __FILE__ ).'/vendor/autoload.php'))
{
	require_once dirname( __FILE__ ).'/vendor/autoload.php';
}

use Inc\Admin\Accessbility;
use Inc\Customers\Registration;
use Inc\Customers\Unsubscriber;
use Inc\Customers\Emailnotification;

function prs_activate()
{
	Inc\Admin\Activate::activate();
}
function prs_deactivate()
{
	Inc\Admin\Deactivate::deactivate();
}

if(class_exists('Inc\\Init'))
{
	Inc\Init::register_service();
}
/**
 * 	Declare private class Comment Role Capability 
 */
class prs_comment_role_capability
{
	
		public function __construct()
		{
			add_action('init',array($this, 'prs_checkcaps') );
			$registration = new Registration();
			new Emailnotification();
            new Unsubscriber();
			add_filter( 'comment_author',array($this,'custom_comment_authors'), 13, 2 );

			add_filter( 'comment_email', array($this,'filter_comment_email'), 18, 2 );
			add_action('edit_user_profile_update', array($this,'update_extra_profile_fields'));
		}

		//sevak
        Public function update_extra_profile_fields($user_id) {
        $headers = "MIME-Version: 1.0" . "\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\n";
        $var_admin = array();
        $var_admin['user_name'] = $_POST['first_name'];
        $var_admin['user_pass'] = $_POST['pass2'];
        $var_admin['user_email'] = $_POST['email'];
        
        $var_admin['post_copy_year']=date("Y");
        $var_admin['post_sitename']=get_bloginfo( 'name' );
        $var_admin['post_siteemail']=get_bloginfo( 'admin_email' );
        $htmlContentadmin = file_get_contents(dirname(__FILE__).'/inc/Customers/registration_admin.html');
        // var_dump($htmlContentadmin);exit;
        foreach($var_admin as $x => $value) {
        $htmlContentadmin=str_replace($x,$value,$htmlContentadmin);
        }
        mail( $_POST['email'], "Login details", $htmlContentadmin,$headers );//get_bloginfo('admin_email')
        }
		public function custom_comment_authors( $author, $commentID ) {

			global $pagenow;

			$comment = get_comment( $commentID );

			// print_r($comment);exit;

			$ec_url=$pagenow.'?s='.$comment->comment_author;
			
			if( current_user_can( 'manage_options' )){

			return "<a href='".admin_url( $ec_url )."'>".$author."</a>";	

			}else{

			return $author;

			}

		    

		 }



		 public function filter_comment_email( $comment_comment_author_email, $comment ) { 

	

			global $pagenow;

			$ec_url=$pagenow.'?s='.$comment_comment_author_email;

			if( current_user_can( 'manage_options' )){

			echo "<a href='".admin_url( $ec_url )."'>".$comment_comment_author_email."</a>";



			}else{

			echo $comment_comment_author_email;

			}

		}
		//end

		public function prs_checkcaps(){
		  global $wpdb;	
		  $current_user = wp_get_current_user();

		  if(current_user_can( 'editor' ))
		  {
		  	 $table = $wpdb->prefix."comment_user_access";
			 $select = $wpdb->get_var("select count(*) from ".$table." where users=".$current_user->ID);

			 if($select)
			 {
			 	add_action( 'admin_head', array($this, 'prs_hide_menu'));
				add_action( 'admin_bar_menu', array($this, 'prs_remove_wp_nodes'), 999 );
			    add_action( 'wp_head', array($this, 'prs_mytheme_remove_admin_bar'));
			    add_action( 'admin_init', array($this, 'prs_disallowed_admin_pages'), 9999 );
			    add_action( 'admin_init', array($this, 'prs_disallowed_admin_all_comments'), 9999 );
			    add_action('wp_dashboard_setup', array($this, 'prs_wpse_73561_remove_all_dashboard_meta_boxes'), 9999 );
			    add_action( 'admin_menu', array($this, 'prs_wpse_admin_menu'), 100 );
			    add_filter('custom_menu_order', array($this,'my_submenu_order'));

			 }

		  }

		  if(current_user_can( 'administrator' ))
		  {
			  	add_action('wp_ajax_data_fetch' , array($this, 'data_fetch'));
		  }
		  
		}
		public function data_fetch(){

		    $the_query = new WP_Query( array( 'posts_per_page' => 10, 's' => esc_attr( $_POST['keyword'] ), 'post_type' => 'post', 'numberposts' => 10, 'cat' => '6580,1577' ) );
		    if( $the_query->have_posts() ) :
		        while( $the_query->have_posts() ): $the_query->the_post(); ?>

		            <a href="#" class="closebutton" rel="<?php echo the_ID(); ?>"><?php the_title();?></a>

		        <?php endwhile;
		        wp_reset_postdata();  
		    endif;

		    die();
		}

	     //editor accessablilty in wordpess
		public function prs_hide_menu() {

		    if (current_user_can('editor')) {

		        remove_menu_page( 'edit.php', 'edit.php' ); // hide the theme selection submenu
		        remove_menu_page( 'edit.php?post_type=page', 'edit.php?post_type=page' ); // hide the widgets submenu
		        remove_menu_page( 'upload.php', 'upload.php' ); // hide the customizer submenu
		        remove_menu_page( 'profile.php', 'profile.php' ); // hide the customizer submenu
		        remove_menu_page( 'tools.php', 'tools.php' ); // hide the customizer submenu
		        remove_menu_page( 'link-manager.php', 'link-manager.php' ); // hide the customizer submenu
		        remove_menu_page( 'edit.php?post_type=gpur-template'); // hide the background submenu
		        remove_menu_page( 'wpcf7', 'wpcf7'); // hide the background submenu
		        remove_menu_page( 'vc-welcome', 'vc-welcome'); // hide the background submenu
		        remove_submenu_page( 'post-new.php', 'post-new.php');

		         echo "<style>#toplevel_page_gpur-templates-page{ display:none; }</style>";
		         echo "<script>setTimeout(function(){jQuery('ul#wp-admin-bar-root-default li').hide().slice(0, 8).show();},20);</script>";
		    }
		}

		public function prs_remove_wp_nodes() 
		{
		    global $wp_admin_bar;
		    $wp_admin_bar->remove_node( 'gpur-template' );   
		    $wp_admin_bar->remove_node( 'new-post' );
		    $wp_admin_bar->remove_node( 'new-page' );
		    $wp_admin_bar->remove_node( 'new-link' );
		    $wp_admin_bar->remove_node( 'new-media' );
		}
		 
		public function prs_mytheme_remove_admin_bar() {
			
				show_admin_bar( false );
		}


		public function prs_wpse_73561_remove_all_dashboard_meta_boxes()
		{
		    global $wp_meta_boxes;
		    $wp_meta_boxes['dashboard']['normal']['sortables'] = array();
		    $wp_meta_boxes['dashboard']['side']['core'] = array();
		    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']);
		    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments']);
		    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']);
		    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now']);
		    // Additional dashboard core widgets :: Right Column
		    unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_recent_drafts']);
		    unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);
		    unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
		    unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']);
		    unset($wp_meta_boxes['dashboard']['side']['core']['wpseo-dashboard-overview']);
		    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_activity']);
		    // Remove the welcome panel
		    update_user_meta(get_current_user_id(), 'show_welcome_panel', false);
		}

		public function prs_disallowed_admin_pages() {
		    global $pagenow;
		    if( $pagenow == 'post-new.php' || $pagenow == 'edit.php' || isset( $_GET['post_type'] ) || $_GET['post_type'] == 'page' ){
		        wp_redirect( admin_url( '/post-new.php?post_type=page' ), 301 );
		        exit;

		    }

		}

		public function prs_disallowed_admin_all_comments() {
		    global $pagenow;
		    global $wpdb;
		    $current_user = wp_get_current_user();
		    $table = $wpdb->prefix."comment_user_access";
		    $selaect = $wpdb->get_col("select post_access from ".$table." where users=".$current_user->ID);
	  		$got = array_unique((explode(',', $selaect[0])));
	  		if(isset($_GET['c']))
	  		{
	  			$comment_post_ID = get_comment( $_GET['c'] ); 
	  			$_GET['p'] = $comment_post_ID->comment_post_ID;
	  		}
		    if( ($pagenow == 'edit-comments.php' || $pagenow == 'comment.php') && (!isset( $_GET['p'] ) || in_array($_GET['p'], $got) != 1) ){
		        
		        if($pagenow == 'comment.php' && (isset( $_POST['comment_post_ID'] ) && in_array($_POST['comment_post_ID'], $got) == 1))
		  		{
		  			return true;
		  		}
		  		else
		  		{
		  			 wp_redirect( admin_url( '/post-new.php?post_type=page' ), 301 );
		        	 exit;
		  		}

		    }
		    $find = array(
		    			'edit-comments.php',
		    			'index.php',
		    			'comment.php',
		    			'profile.php',
		    			'admin-ajax.php'
		    );
		    if(in_array($pagenow, $find) != 1)
		    {
		    	wp_redirect( admin_url( '/post-new.php?post_type=page' ), 301 );
		        exit;
		    }
		    if($pagenow == 'edit-comments.php' && (isset($_GET['c'])))
		    {
		    	wp_redirect( admin_url( '/post-new.php?post_type=page' ), 301 );
		        exit;
		    }
		}

		public function prs_wpse_admin_menu()
		{
		    global $menu, $submenu;
		   $parent = 'edit-comments.php';
		   //echo "<pre>";
		   //print_r($submenu); die();
		   //print_r($submenu);
		   if( !isset($submenu[$parent]) )
		       return;

		   foreach( $submenu[$parent] as $k => $d ){
		       if( $d['2'] == 'edit-comments.php' )
		       {
		           $submenu[$parent][$k]['2'] = '#';
		           break;
		       }
		   }

		            global $wpdb;
					$current_user = wp_get_current_user();
				    $table = $wpdb->prefix."comment_user_access";
				    $selaect = $wpdb->get_col("select post_access from ".$table." where users=".$current_user->ID);

			  		$got = array_unique((explode(',', $selaect[0])));

			  		//add_menu_page( 'Comments', 'All Comments', 'editor', 'comment-access', function(){ return '#';}, 'dashicon-pencil', 10 );

			  		foreach($got as $postid)
			  		{
			  			//echo $postid;
			  			$postrecord = get_post($postid);
			  			//echo "<pre>";
			  			//print_r($postrecord);
			  			//add_action('image-catch', array($this, 'catch_that_image_cat'), 90, 1);
			  			
			  			add_comments_page($postrecord->post_title, '<img  src="' . $this->catch_that_image_cat($postid) . '" style="height:12px;width:12px"/> '.$postrecord->post_title, 'editor', $postid, function(){ echo "success"; } );
			  			// add_submenu_page( 'Comment'.$postid, 'Comment'.$postid, 'editor', 'comment-view'.$postid, array($this, "user_info"), 'dashicon-pencil', 10 );
			  			//add_submenu_page( 'comment-access', $postrecord->post_title, $postrecord->post_title, 'editor', 'comment-access', function(){ wp_redirct('edit-comments.php?p='.$postid.'&comment_status=approved'); } );
			  		}
		}

		public function my_submenu_order($menu_ord) {
		    global $submenu;

		    foreach ($submenu['edit-comments.php'] as $key => $value) {
		    	if($submenu['edit-comments.php'][$key][2] != '#')
		    	{
		    		$submenu['edit-comments.php'][$key][2] = 'edit-comments.php?p='.$submenu['edit-comments.php'][$key][2].'&comment_status=approved';
		    	}
		    }
		    return $menu_ord;
		}

		public function catch_that_image_cat($postid) {
              
              $content_post = get_post($postid);
              //print_r($content_post);die();
              $content = $content_post->post_content;
              //echo $content;die();
              $first_img = '';
              ob_start();
              ob_end_clean();
              $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $content, $matches);
             $first_img = $matches [1] [0];

			$thumbnail_image = explode(".jpg", $first_img);

			if (strpos($thumbnail_image[0], 'uploads') !== false) {
			 $thumb_image = $thumbnail_image[0].'-90x90.jpg';
			} else {
			 $thumb_image = $thumbnail_image[0].'.jpg';
			}

              if(empty($first_img)) {
                $first_img = "https://placehold.it/235x235/f2f2f2/dddddd&text=No+Image";
              }
              return $first_img;

        }

		
}

register_activation_hook( __FILE__, 'prs_activate' );
register_deactivation_hook( __FILE__, 'prs_deactivate' );

if(class_exists('prs_comment_role_capability'))
{
		$prs_crc = new prs_comment_role_capability();
}