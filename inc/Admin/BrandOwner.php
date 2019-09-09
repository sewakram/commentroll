<?php

/**

 *@package Comment Role Capability

 */

namespace Inc\Admin;

/**

 * Comment Role caplbility Admin pages

 */

use \WP_List_Table;



class BrandOwner extends WP_List_Table

{

    /**

        * [REQUIRED] You must declare constructor and give some basic params

        */

    function __construct()

    {

        global $status, $page;



        parent::__construct(array(

            'singular' => 'Brand Owner',

            'plural' => 'Brand Owners',

        ));



    }



    /**

        * [REQUIRED] this is a default column renderer

        *

        * @param $item - row (key, value array)

        * @param $column_name - string (key)

        * @return HTML

        */

    function column_default($item, $column_name)

    {

        return $item[$column_name];

    }



    /**

        * [OPTIONAL] this is example, how to render specific column

        *

        * method name must be like this: "column_[column_name]"

        *

        * @param $item - row (key, value array)

        * @return HTML

        */

    function column_email($item)

    {

        return '<em>' . $item['email'] . '</em>';

    }



    /**

        * [OPTIONAL] this is example, how to render column with actions,

        * when you hover row "Edit | Delete" links showed

        *

        * @param $item - row (key, value array)

        * @return HTML

        */

    function column_firstname($item)

    {

        // links going to /admin.php?page=[your_plugin_page][&other_params]

        // notice how we used $_REQUEST['page'], so action will be done on curren page

        // also notice how we use $this->_args['singular'] so in this example it will

        // be something like &person=2
         global $wpdb;

           $results = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}customer_registration WHERE id ='".$item['id']."'",OBJECT);
           
           // $test= ($results->admin_approve==1)? '<a href="?page='.$_REQUEST['page'].'&action=unverify&user_id='.$item['id'].'"': '<a href="?page='.$_REQUEST['page'].'&action=verify&user_id='.$item['id'].'"';
           // echo 'url1:'.'<a href="?page='.$_REQUEST['page'].'&action=unverify&user_id='.$item['id'].'"<br>'; 
           // echo 'url2:'.'<a href="?page='.$_REQUEST['page'].'&action=verify&user_id='.$item['id'].'"<br>'; 
           if($results->admin_approve==1)
           {
                $anchor = '<a href="?page=%s&action=%s&user_id=%s">Unverify</a>';
                $text_anchor = 'unverify';
                $edituser = '<a href="'.admin_url( 'user-edit.php?user_id='.$item['id'].'&wp_http_referer=/wp-admin/users.php' ).'">%s</a>';
           }
           else
           {
                $anchor = '<a href="?page=%s&action=%s&user_id=%s">Verify</a>';
                $text_anchor = 'verify';
                $edituser = '<a href="?page=prs-brandowner-form&id=%s">%s</a>';
           }

        $actions = array(

            'edit' => sprintf($edituser, $item['id'], __('Edit', 'prs')),

            'delete' => sprintf('<a href="?page=%s&action=delete&id=%s">%s</a>', $_REQUEST['page'], $item['id'], __('Delete', 'prs')),

            'view'    => sprintf('<a href="?page=%s&action=%s&id=%s">View</a>',$_REQUEST['page'],'view',$item['id']),

            $text_anchor  => sprintf($anchor, $_REQUEST['page'], $text_anchor, $item['id'])

        );
       

           $status=($results->admin_approve==1)? '<b style="color:green">Verified</b>':'<b style="color:red">Pending</b>';

        $full = $item['firstname'].' '.$item['lastname'].'</br>'.$status;

        return sprintf('%s %s',

            $full,

            $this->row_actions($actions)

        );

    }



    /**

        * [REQUIRED] this is how checkbox column renders

        *

        * @param $item - row (key, value array)

        * @return HTML

        */

    function column_cb($item)

    {

        return sprintf(

            '<input type="checkbox" name="id[]" value="%s" />',

            $item['id']

        );

    }



    /**

        * [REQUIRED] This method return columns to display in table

        * you can skip columns that you do not want to show

        * like content, or description

        *

        * @return array

        */

    function get_columns()

    {

        $columns = array(

            'cb' => '<input type="checkbox" />', //Render a checkbox instead of text

            'firstname' => __('Name', 'prs'),

            'email' => __('E-Mail', 'prs'),

            'phone_no' => __('PhoneNo.', 'prs'),

            'business' => __('Business', 'prs'),

            'brand_name' => __('Brand Name', 'prs'),

            'trademark' => __('Trademark', 'prs'),

            'address' => __('Address.', 'prs'),

        );

        return $columns;

    }



    /**

        * [OPTIONAL] This method return columns that may be used to sort table

        * all strings in array - is column names

        * notice that true on name column means that its default sort

        *

        * @return array

        */

    function get_sortable_columns()

    {

        $sortable_columns = array(

            'firstname' => array('firstname', true),

            'email' => array('email', false),

            'phone_no' => array('phone_no', false),

            'business' => array('business', false),

            'brand_name' => array('brand_name', false),

            'trademark' => array('trademark', false),

            'address' => array('address.', false),

        );

        return $sortable_columns;

    }



    /**

        * [OPTIONAL] Return array of bult actions if has any

        *

        * @return array

        */

    function get_bulk_actions()

    {

        $actions = array(

            'delete' => 'Delete',

            'verify' => 'Verify'

        );

        return $actions;

    }



    /**

        * [OPTIONAL] This method processes bulk actions

        * it can be outside of class

        * it can not use wp_redirect coz there is output already

        * in this example we are processing delete action

        * message about successful deletion will be shown on page in next part

        */

    function process_bulk_action()

    {
        //print_r($_REQUEST);die();
        global $wpdb;

        $table_name = $wpdb->prefix . 'customer_registration'; // do not forget about tables prefix


        if ('delete' === $this->current_action()) {

            $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();

            if (is_array($ids)) $ids = implode(',', $ids);



            if (!empty($ids)) {

               $datadelete =  $wpdb->get_results("select email,admin_approve from $table_name where id In($ids)");
               
               foreach ($datadelete as $data) {
                        $userid = get_user_by( 'email', $data->email );
                        if($userid->ID)
                        {
                            wp_delete_user($userid->ID);
                        }
               }
               $wpdb->query("DELETE FROM $table_name WHERE id IN($ids)");

            }

        }



        if ('verify' === $this->current_action()) {

            $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();

            if (is_array($ids)) $ids = implode(',', $ids);



            if (!empty($ids)) {

                // echo $ids;exit;

               // $wpdb->query("DELETE FROM $table_name WHERE id IN($ids)");

            }

        }

    }



    /**

        * [REQUIRED] This is the most important method

        *

        * It will get rows from database and prepare them to be showed in table

        */

    function prepare_items()

    {

        global $wpdb;

        $table_name = $wpdb->prefix . 'customer_registration';



        $per_page = 5; 



        $columns = $this->get_columns();

        $hidden = array();

        $sortable = $this->get_sortable_columns();



        $this->_column_headers = array($columns, $hidden, $sortable);

        echo $this->current_action;

        // [OPTIONAL] process bulk action if any

        $this->process_bulk_action();



        $search = ( isset( $_REQUEST['s'] ) ) ? $_REQUEST['s'] : false;

        $do_search = ( $search ) ? $wpdb->prepare("where email LIKE '%%%s%%'", $search ) : '';

        $do_search .= ( $search ) ? $wpdb->prepare("OR firstname LIKE '%%%s%%'", $search ) : '';

        $do_search .= ( $search ) ? $wpdb->prepare("OR lastname LIKE '%%%s%%'", $search ) : '';

        $do_search .= ( $search ) ? $wpdb->prepare("OR concat(firstname,' ',lastname) LIKE '%%%s%%'", $search ) : '';

        $do_search .= ( $search ) ? $wpdb->prepare("OR phone_no LIKE '%%%s%%'", $search ) : '';

        $do_search .= ( $search ) ? $wpdb->prepare("OR brand_name LIKE '%%%s%%'", $search ) : ''; 

        $do_search .= ( $search ) ? $wpdb->prepare("OR trademark LIKE '%%%s%%'", $search ) : '';

        $do_search .= ( $search ) ? $wpdb->prepare("OR address LIKE '%%%s%%'", $search ) : '';

       

        // will be used in pagination settings

        if($search)

            $total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_name $do_search");

        else

            $total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_name");



        // prepare query params, as usual current page, order by and order direction

        $paged = isset($_REQUEST['paged']) ? ($per_page * max(0, intval($_REQUEST['paged']) - 1)) : 0;

        $orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'id';

        $order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'asc';



        // [REQUIRED] define $items array

        // notice that last argument is ARRAY_A, so we will retrieve array

        if($search)

            $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name $do_search ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged), ARRAY_A);

        else

            $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged), ARRAY_A);

            //echo $wpdb->last_query;die();

        // [REQUIRED] configure pagination

        $this->set_pagination_args(array(

            'total_items' => $total_items, // total items defined above

            'per_page' => $per_page, // per page constant defined at top of method

            'total_pages' => ceil($total_items / $per_page) // calculate pages count

        ));

    }



        public function prs_manage_information()

        {

            global $wpdb;

            $this->prepare_items();

            $message = '';

            if ('delete' === $this->current_action()) {

                $message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('Successfully Delete: %d', 'prs'), count($_REQUEST['id'])) . '</p></div>';

            }else if ($this->current_action()==='view' && isset($_GET['id']) && $_GET['page']=='customers_details' ) {

                // echo "Details page";

                $this->tt_render_view_page($_GET['id']);
                exit;

            }else if ($this->current_action()==='verify' && isset($_GET['user_id']) && $_GET['page']=='customers_details' ) {

                // echo "Details page";

                $this->admin_verify_customer($_GET['user_id']);

            }
            
          



            

            ?>

            <div class="wrap">



                <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>

                <h2>
                    <?php _e('Brand Owner', 'prs');?>
                    <a class="add-new-h2"
                                        href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=prs-brandowner-form');?>"><?php _e('Add new', 'prs')?>
                    </a>
                </h2>
                <?php echo $message; ?>

                <form id="persons-table" method="GET">

                    <?php 

                       $this->search_box( __( 'Search' ), 'search-box-id' );

                    ?>

                    <input type="hidden" name="page" value="<?= esc_attr($_REQUEST['page']) ?>"/>

                    <?php $this->display() ?>

                </form>



            </div>

            <?php

            

        }



        // ///////////////////////

            function tt_render_view_page($id){

            global $wpdb;

            // echo $id;exit;



            $results = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}customer_registration WHERE id ='".$id."'",OBJECT);

            // echo "<pre>"; print_r($results);exit;

            ?>

            <div class="wrap">



            <div id="icon-users" class="icon32"><br/></div>

            <h2>Brand Owner Details</h2>

            <table class='wp-list-table widefat fixed'>

                

                <tr>

                    <th >Name</th>

                    <td><span><?php echo $results->firstname." ".$results->lastname?></span></td>

                </tr>

                <tr>

                    <th class="ss-th-width">Email</th>

                    <td><span><?php echo $results->email?></span></td>

                </tr>

                <tr>

                    <th class="ss-th-width">Business</th>

                    <td><span><?php echo $results->business?></span></td>

                </tr>

                <tr>

                    <th class="ss-th-width">Phone Number</th>

                    <td><span><?php echo $results->phone_no?></span></td>

                </tr>

                <tr>

                    <th class="ss-th-width">Brand name</th>

                    <td><span><?php echo $results->brand_name?></span></td>

                </tr>

                <tr>

                    <th class="ss-th-width">Trademark</th>

                    <td><span><?php echo $results->trademark?></span></td>

                </tr>

                <tr>

                    <th class="ss-th-width">Address</th>

                    <td><span><?php echo $results->address.",".$results->city.",".$results->state."-".$results->post_code?></span></td>

                </tr>

                <tr>

                    <th class="ss-th-width">Status</th>

                    <td><span><?php echo ($results->status==1)? "Active":"Inactive" ?></span></td>

                </tr> 

                <tr>

                    <th class="ss-th-width">Verify Status</th>

                    <td><span><?php echo ($results->admin_approve==1)? "Verified":"Pending" ?></span></td>

                </tr>            

                

            </table><br>

             <a class="button action" href="<?php echo admin_url('admin.php?page=customers_details')?>">Back</a>



            </div>

        

            <?php

            }



             function admin_unverify_customer($user_id){


                global $wpdb;

                // echo $user_id;exit;

                $table = $wpdb->prefix."customer_registration";



                $singledata = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}customer_registration WHERE id ='".$user_id."'",OBJECT);



                $updatedata=$wpdb->update( $table, array( 'admin_approve' => 0), array( 'id' => $user_id ) );

                // var_dump($updatedata);exit;

                if($updatedata){
                    echo ("<script type='text/javascript'>

                            alert('Unverified successfully');

                             window.location='".admin_url('admin.php?page=customers_details')."';

                            </script>"); 

                }

                else{

                

                echo ("<script type='text/javascript'>

                alert('Sorry! already unverified');

                window.location='".admin_url('admin.php?page=customers_details')."';

                </script>");

                

                }

            
             }

             function admin_verify_customer($user_id){

                global $wpdb;

                // echo $user_id;exit;

                $table = $wpdb->prefix."customer_registration";



                $singledata = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}customer_registration WHERE id ='".$user_id."'",OBJECT);



                $updatedata=$wpdb->update( $table, array( 'admin_approve' => 1), array( 'id' => $user_id ) );

                // var_dump($updatedata);exit;

                if($updatedata){

                    $chk=$wpdb->get_row("SELECT * FROM {$wpdb->prefix}users WHERE user_email ='".$singledata->email."'",OBJECT);



                    if(!$chk){

                            $insertdata=$wpdb->insert("{$wpdb->prefix}users",array( 'user_login' => $singledata->firstname,'user_nicename' => $singledata->firstname.' '.$singledata->lastname,'user_email' => $singledata->email,'user_registered' => $singledata->time,'display_name' => $singledata->firstname.' '.$singledata->lastname,'user_status' => 1),array('%s','%s','%s','%s','%s'));

                            $singlelast = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}users WHERE id ='".$wpdb->insert_id."'",OBJECT);

                          // var_export($singlelast->ID);exit;

                            $wpdb->insert("{$wpdb->prefix}usermeta",array( 'user_id' => $singlelast->ID,'meta_key' => 'nickname','meta_value' => $singledata->firstname),array('%d','%s','%s'));

                            $wpdb->insert("{$wpdb->prefix}usermeta",array( 'user_id' => $singlelast->ID,'meta_key' => 'first_name','meta_value' => $singledata->firstname),array('%d','%s','%s'));

                            $wpdb->insert("{$wpdb->prefix}usermeta",array( 'user_id' => $singlelast->ID,'meta_key' => 'last_name','meta_value' => $singledata->lastname),array('%d','%s','%s'));

                            if($insertdata){

                            

                            echo ("<script type='text/javascript'>

                            alert('Verified successfully');

                             window.location='".admin_url('admin.php?page=customers_details')."';

                            </script>");

                            

                            }

                    }

                    

                    

                  // echo "<pre>";print_r($insertdata);exit;  

                }

                else{

                

                echo ("<script type='text/javascript'>

                alert('Sorry! already verified');

                window.location='".admin_url('admin.php?page=customers_details')."';

                </script>");

                

                }

            }

        ///////////////////////////

}