<?php
/**
 *@package Comment Role Capability
 */
namespace Inc\Template;

$args = array('role' => 'editor', 'orderby' => 'user_nicename',
    'order'   => 'ASC');

echo "<style>
#keyword {
  border-box: box-sizing;
  background-image: url('".plugin_dir_url( __FILE__ )."assets/searchicon.png');
  background-position: 14px 12px;
  background-repeat: no-repeat;
  font-size: 16px;
  padding: 14px 20px 12px 45px;
  border: none;
  border-bottom: 1px solid #ddd;
}
#keyword:focus {outline: 3px solid #ddd;}

.dropdown {
  position: relative;
  display: inline-block;
}

.dropdown-content {
  position: absolute;
  background-color: #f6f6f6;
  min-width: 230px;
  overflow: auto;
  border: 1px solid #ddd;
  z-index: 1;
}

.dropdown-content a {
  color: black;
  padding: 12px 16px;
  text-decoration: none;
  display: block;
}

.dropdown a:hover {background-color: #ddd;}

.show {display: block;}
select {
        width:250px;
    }
</style>";
function display_flash_notices() {
    $notices = get_option( "my_flash_notices", array() );
    // Iterate through our notices to be displayed and print them.
    foreach ( $notices as $notice ) {
       if($notice['notice'] != '')
       {
          printf('<div class="notice notice-%1$s %2$s"><p>%3$s</p></div>',
              $notice['type'],
              $notice['dismissible'],
              $notice['notice']
          );
       }
    }
    // Now we reset our options to prevent notices being displayed forever.
    if( ! empty( $notices ) ) {
        delete_option( "my_flash_notices", array() );
    }
  }
display_flash_notices();
echo '<h2>Users Accessbility</h2>';

$userid = get_users( $args );

$select_id = 'prs_users';

$label = 'Editor Users';

$selected = 0;

$prs_user_access_nonce = wp_create_nonce( 'prs_add_user_access_form_nonce' );;

echo '<div class="dropdown"><form action="'.esc_url( admin_url('admin-post.php') ).'" method="post" id="prs_add_user_access_form"><input type="hidden" name="action" value="prs_form_response"/><input type="hidden" name="prs_user_access_nonce" value="'.$prs_user_access_nonce.'" /><select name="'. $select_id .'" id="'.$select_id.'">';

echo '<option value = "" >All '.$label.' </option>';
foreach ($userid as $user) {
    echo '<option value="', esc_html($user->ID), '"', $selected == $user->ID ? ' selected="selected"' : '', '>', esc_html($user->user_nicename).' ('.esc_html($user->user_email).') ', '</option>';
}

echo '</select>';

echo '<input type="text" id="removeuser" size="39" name="post_active" value="" readonly /></div>';

echo '<input type="submit" class="button button-primary" name="prs_user_access" value="Submit">';

echo "</form>";

echo '<div class="dropdown" style="text-align: center;">
  		<div id="myDropdown" size="50" class="dropdown-content" style="margin-left: -336px;margin-top:10px;">';

echo '<input type="text" name="keyword" id="keyword" onkeyup="fetch()"></input>';

echo '<div id="datafetch"></div>';

echo '</div>
	</div>';

