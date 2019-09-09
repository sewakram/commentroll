<?php
/**
 *@package Comment Role Capability
 */
namespace Inc\Customers;
/**
 * Register Customers free account & connect with your customers
 */
class Registration
{
	const NONCE_VALUE = 'prs_new_customers_register';
    const NONCE_FIELD = 'register_nonce';
    protected $errors = array();
    protected $data = array();
	
	function __construct()
	{
		add_action( 'init', array($this, 'prs_ajax_productname') );
		add_shortcode( 'customers_form', array($this, 'prs_register_shortcode') );
		add_action( 'template_redirect',  array( $this, 'prs_handle_form' ) );
		add_shortcode( 'verify_registation', array($this, 'prs_verify_registration_shortcode') );
		add_action('wp_enqueue_scripts',array($this,'add_js_scripts'));
	}
	function add_js_scripts(){
		// 	<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/css/select2.min.css" rel="stylesheet" />
		// <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/js/select2.min.js"></script>
	wp_enqueue_style('select2css','https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/css/select2.min.css',false);
	wp_enqueue_script('select2js', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/js/select2.min.js', array('jquery'), false);

	}
	public function prs_verify_registration_shortcode() {
        if (isset($_GET['user_id'])) {
            // echo $_GET['comment_id'];
        global $wpdb;
        
			$table = $wpdb->prefix."customer_registration";
			$result=$wpdb->update( $table, array( 'status' => 1 ), array( 'id' => $_GET['user_id'] ) );
			if ($result) {

				echo "<h3 id='mydiv'>Thanks! You have verified successfully</h3>";
				?><script>
					setTimeout(function() {
					$('#mydiv').fadeOut('fast');
					}, 10000);
					 
					window.location.replace("http://"+window.location.hostname);
				</script>
			<?php }else{
				echo "<h3 id='mydiv'>Sorry! Something went wrong</h3>";
				?>
				<script>
					setTimeout(function() {
					
					$('#mydiv').fadeOut('slow');
					}, 10000); 
					window.location.replace("http://"+window.location.hostname);
				</script>
				<?php
			}
      
	 }
    ?>
    
    <div class="wrap">
       
        <?php if (isset($message)): ?><div class="updated"><p><?php echo $message; ?></p></div><?php endif; ?>
        
    </div>
    
    <?php

    }

	public function prs_register_shortcode() {
        if ( is_user_logged_in() )
            return sprintf( '<p>Please <a href="%s">Logout</a> for new registration.</p>', esc_url( wp_logout() ) );
        elseif ( $this->isFormSuccess() )
            return '<div class="prs_notify prs_notify-green"><span class="prs_symbol prs_icon-tick"></span> Nice one, registration Successfully.</div>';
   //      	<div class="notify"><span class="symbol icon-info"></span> A kind of a notice box !</div> 
			// <div class="notify notify-red"><span class="symbol icon-error"></span> Error message</div> 
			// <div class="notify notify-green"><span class="symbol icon-tick"></span> A positive/success/completion message</div> 
			// <div class="notify notify-yellow"><span class="symbol icon-excl"></span> A warning message</div>
        else
            return $this->register_form();
    }

    public function isFormSuccess()
    {
        
    	return filter_input( INPUT_GET, 'success' ) === 'true';
    }

    public function prs_ajax_productname()
    {
  		 add_action( 'wp_ajax_prs_getpname', array($this, 'prs_getpname') );
		 add_action( 'wp_ajax_nopriv_prs_getpname', array($this, 'prs_getpname') );
    }

    public function prs_handle_form()
    { 
    	if ( ! $this->prs_isFormSubmitted() )
    		return false;

    	$data = filter_input_array( INPUT_POST, array(
    					'firstname' => FILTER_DEFAULT,
					    'lastname' => FILTER_DEFAULT,
					    'business' => FILTER_DEFAULT,
					    'email' => FILTER_DEFAULT,
					    'area_code' => FILTER_DEFAULT,
					    'phone_no' => FILTER_DEFAULT,
					    'brand_name' => FILTER_DEFAULT,
					    'trademark' => FILTER_DEFAULT,
					    'street_address' => FILTER_DEFAULT,
					    'street_address1' => FILTER_DEFAULT,
					    'city' => FILTER_DEFAULT,
					    'state' => FILTER_DEFAULT,
					    'post_code' => FILTER_DEFAULT,
					    'register_nonce' => FILTER_DEFAULT,
					    '_wp_http_referer' => FILTER_DEFAULT
    			)
    	);

    	$data = wp_unslash( $data );
        $data = array_map( 'trim', $data );
       	$data['p_category'] = implode(',', $_POST['p_category']);
      	$data['p_name'] = implode(',', $_POST['p_name']);

      	$data['firstname'] = sanitize_text_field( $data['firstname'] );
      	$data['lastname'] = sanitize_text_field( $data['lastname'] );
      	$data['business'] = sanitize_text_field( $data['business'] );
      	$data['email'] = sanitize_email( $data['email'] );
      	$data['area_code'] = sanitize_text_field( $data['area_code'] );
      	$data['phone_no'] = sanitize_text_field( $data['phone_no'] );
      	$data['brand_name'] = sanitize_text_field( $data['brand_name'] );
      	$data['trademark'] = sanitize_text_field( $data['trademark'] );
      	$data['street_address'] = sanitize_text_field( $data['street_address'] );
      	$data['street_address1'] = sanitize_text_field( $data['street_address1'] );
      	$data['city'] = sanitize_text_field( $data['city'] );
      	$data['state'] = sanitize_text_field( $data['state'] );
      	$data['post_code'] = sanitize_text_field( $data['post_code'] );
      
      	$this->data = $data;
      	global $wpdb;

      	if ( ! $this->isNonceValid() )
            $this->errors[] = 'Security check failed, please try again.';

        if ( ! $data['firstname'] )
            $this->errors['firstname'] = 'Please enter a firstname.';

        if ( ! $data['lastname'] )
            $this->errors['lastname'] = 'Please enter a lastname.';

        if ( ! $data['business'] )
            $this->errors['business'] = 'Please enter a business.';

        if ( ! $_POST['email'] )
            $this->errors['email'] = 'Please enter a email.';
        else
        	if (!is_email($data['email']) )
            $this->errors['email'] = 'Please enter a valid email address.';

        if ( ! $data['area_code'] )
            $this->errors['area_code'] = 'Please enter a area code.';

        if ( ! $data['phone_no'] )
            $this->errors['phone_no'] = 'Please enter a phone no.';

        if ( ! $data['brand_name'] )
            $this->errors['brand_name'] = 'Please enter a brand name.';

        if ( ! $data['trademark'] )
            $this->errors['trademark'] = 'Please enter a trademark.';

        if ( ! $data['street_address'] )
            $this->errors['street_address'] = 'Please enter a address.';

        if(empty($_POST['p_category']))
        	$this->errors['p_category'] = 'Please select a category.';

        if(empty($_POST['p_name']))
        	$this->errors['p_name'] = 'Please select a product name.';

        if(empty($data['street_address']))
        	$this->errors['street_address'] = 'Please enter a address.';

        if(empty($data['city']))
        	$this->errors['city'] = 'Please enter a city.';

        if(empty($data['state']))
        	$this->errors['state'] = 'Please enter a state.';

        if(empty($data['post_code']))
        	$this->errors['post_code'] = 'Please enter a Post code.';

        $email_exists = $wpdb->get_results('select * from '.$wpdb->prefix.'customer_registration where email= "'.$_POST['email'].'"');
        if(email_exists( $_POST['email'] ))
        	$this->errors['email'] = 'Email allready exists database!';
        else if($email_exists)
        	$this->errors['email'] = 'Email allready exists database!';

        if(!$this->errors)
        {
        	$args = array(
	    		'firstname' => $data['firstname'],
	    		'lastname' => $data['lastname'],
			    'business' => $data['business'],
			    'email' => $data['email'],
			    'phone_no' => $data['area_code']."-".$data['phone_no'],
			    'brand_name' => $data['brand_name'],
			    'trademark' => $data['trademark'],
			    'p_category' => $data['p_category'],
			    'p_name' => $data['p_name'],
			    'address' => $data['street_address']." ".$data['street_address1'],
			    'city' => $data['city'],
			    'state' => $data['state'],
			    'post_code' => $data['post_code'],
			    'time' => date('Y-m-d H:i:s')

	    	);
	    	$cust_insert = $wpdb->insert($wpdb->prefix.'customer_registration',$args);
	    	if($cust_insert)
	    	{
	    		if(!empty($_POST)){
    		$headers = "MIME-Version: 1.0" . "\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\n";
		$var_user = array();
		$var_user['register_user'] = $_POST['firstname'];
		$var_user['post_copy_year']=date("Y");
		$var_user['post_sitename']=get_bloginfo( 'name' );
		$var_user['post_siteemail']=get_bloginfo( 'admin_email' );
		$var_user['user_verify_link']=site_url('verifyregistration/').'?user_id='.$wpdb->insert_id;
		//$variables['post_us_link']=site_url('unsubscribe/').'?comment_id='.$comment->comment_ID;//admin_url('?page=unsubscribe-comment-notification').'&comment_id='.$comment->comment_ID;

		$htmlContent = file_get_contents(dirname(__FILE__).'/registration.html');
		foreach($var_user as $x => $value) {
		$htmlContent=str_replace($x,$value,$htmlContent);
		}
		// var_export($htmlContent);exit();
		mail( $_POST['email'], "Registration successfully ", $htmlContent,$headers );
		// $var_admin = array();
		// $var_admin['user_name'] = $_POST['firstname']." ".$_POST['lastname'];
		// $var_admin['user_business'] = $_POST['business'];
		// $var_admin['user_email'] = $_POST['email'];
		// $var_admin['user_phone_no'] = $_POST['area_code']." ".$_POST['phone_no'];
		// $var_admin['user_brand_name'] = $_POST['brand_name'];
		// $var_admin['user_address'] = $_POST['street_address']." ".$_POST['street_address1']." ".$_POST['city']."-".$_POST['post_code'];
		// $var_admin['post_copy_year']=date("Y");
		// $var_admin['post_sitename']=get_bloginfo( 'name' );
		// $var_admin['post_siteemail']=get_bloginfo( 'admin_email' );
		// $htmlContentadmin = file_get_contents(dirname(__FILE__).'/registration_admin.html');
		// foreach($var_admin as $x => $value) {
		// $htmlContentadmin=str_replace($x,$value,$htmlContentadmin);
		// }
		// mail( get_bloginfo('admin_email'), "New registration", $htmlContentadmin,$headers );//get_bloginfo('admin_email')
    	}
	    		wp_redirect( add_query_arg( 'success', 'true' ) );
                exit;
	    	}
	    	else
	    	{
	    		$this->errors['error'] = 'Whoops, please try again.';
	    	}
        }
        // if ( ! $data['lastname'] )
        //     $this->errors[] = 'Please enter a lastname.';
     
    	// print_r($errors);die();
    	// global $wpdb;
    	
    }

    public function prs_getpname()
    {
 		$args = implode(',', $_POST['p_category']);
 		$option = '';
 		for ($i=0; $i < count($_POST['p_category']); $i++) { 
 			$prs_query = get_posts( array( 'category' => $_POST['p_category'][$i], "numberposts" => -1 ) );
 			$category = get_category( $_POST['p_category'][$i] );
 			// $option .= '<option value="" disabled="disabled" style="text-align:center;font-weight:bold;color:black">'.str_replace("-"," ",$category->slug).'</option>';
 			$option .= '<optgroup label="'.str_replace("-"," ",$category->slug).'">';
 			foreach ($prs_query as $prs_fetch) {
 				$option .= '<option value="'.$prs_fetch->ID.'">'.$prs_fetch->post_title.'</option>';
 			}
 			$option .='</optgroup>';
 			// $option .=$optiongs.$option.$optionge;
 		}
 		if($option != '')
 		{
 			echo json_encode( array( 'status' => 'success', 'p_name' => $option) );
 		}
    	else
    	{
    		echo json_encode( array( 'status' => 'unsuccess', 'p_name' => 'bad') );
    	}
    	die();
    }
    public function prs_isFormSubmitted()
    {
    	return isset( $_POST['register_nonce'] );
    }
	public function register_form()
	{
		ob_start();
		if($this->errors['error'])
		{
			echo '<div class="prs_notify prs_notify-red"><span class="prs_symbol prs_icon-error"></span> '.$this->errors['error'].'</div>';
		}
		?>

		<form action="" method="post" id="customer-form">
			<div class="replyform">
				<h2><?php echo esc_html__( 'My Account', 'prs' ); ?></h2>
				<div class="row">
					
					<div class="form-label">
					<label><?php echo esc_html__( 'Name', 'prs' ); ?></label></div>	
				
					<div class="form-input">
					 <div class="input-row"> <input type="text" name="firstname" id="firstname" value="<?php if ( isset( $this->data['firstname'] ) )
			                    echo esc_attr( $this->data['firstname'] );
			          ?>"/>
					  <p class="small"><?php echo esc_html__( 'First Name', 'prs' ); ?></p><span class="prs_server_valid"><?=$this->errors['firstname']?></span></div>
					<div class="input-row"> <input type="text" name="lastname" id="lastname" value="<?php if ( isset( $this->data['lastname'] ) )
			                    echo esc_attr( $this->data['lastname'] );
			          ?>"/>
					  <p class="small"><?php echo esc_html__( 'Last Name', 'prs' ); ?></p><span class="prs_server_valid"><?=$this->errors['lastname']?></span></div>
					</div>
					<div class="clear"></div>
				</div>
				
				<div class="row">
					<div class="form-label">
					<label><?php echo esc_html__( 'Business', 'prs' ); ?></label></div>	
				
					<div class="form-input">
					 <div class="input-row-full"> 
					 <input type="text" name="business" id="business" value="<?php if ( isset( $this->data['business'] ) )
			                    echo esc_attr( $this->data['business'] );
			          ?>" /><span class="prs_server_valid"><?=$this->errors['business']?></span>
					 
					</div>
					<div class="clear"></div>
					</div>
				<div class="clear"></div>
				</div>
				
					<div class="row">
					<div class="form-label">
					<label><?php echo esc_html__( 'Email (Should be brand domain specific)', 'prs' ); ?></label></div>	
				
					<div class="form-input">
					 <div class="input-row-full"> 
					 <input type="text" name="email" value="<?php if ( isset( $this->data['email'] ) )
			                    echo esc_attr( $this->data['email'] );
			          ?>"/>
					 <p class="small"><?php echo esc_html__( 'example@example.com', 'prs' ); ?></p><span class="prs_server_valid"><?=$this->errors['email']?></span></div>
					</div>
					<div class="clear"></div>
					</div>
					
					<div class="row">
					<div class="form-label">
					<label><?php echo esc_html__( 'Phone Number', 'prs' ); ?></label></div>	
				
					<div class="form-input">
						<div class="input-row areacode"> <input type="tel" name="area_code" value="<?php if ( isset( $this->data['area_code'] ) )
			                    echo esc_attr( $this->data['area_code'] );
			          ?>"> &nbsp;&nbsp;- 
						<p class="small"><?php echo esc_html__( 'Area Code', 'prs' ); ?></p><span class="prs_server_valid"><?=$this->errors['area_code']?></span></div>
							<div class="input-row"> <input type="tel" name="phone_no" value="<?php if ( isset( $this->data['phone_no'] ) )
			                    echo esc_attr( $this->data['phone_no'] );
			          ?>">
						<p class="small"><?php echo esc_html__( 'Phone Number', 'prs' ); ?></p><span class="prs_server_valid"><?=$this->errors['phone_no']?></span></div>
					</div>
					
					<div class="clear"></div>
					</div>
					
					<div class="row">
					<div class="form-label">
					<label><?php echo esc_html__( 'Brand Name', 'prs' ); ?></label></div>	
				
						<div class="form-input">
						 <div class="input-row-full"> 
						 <input type="text" name="brand_name" value="<?php if ( isset( $this->data['brand_name'] ) )
			                    echo esc_attr( $this->data['brand_name'] );
			          ?>"><span class="prs_server_valid"><?=$this->errors['brand_name']?></span>
						</div>
						<div class="clear"></div>
						</div>
					<div class="clear"></div>
					</div>
							<div class="row">
					<div class="form-label">
					<label><?php echo esc_html__( 'Trademark Number', 'prs' ); ?></label></div>	
				
						<div class="form-input">
						 <div class="input-row-full"> 
						 <input type="text" name="trademark" value="<?php if ( isset( $this->data['trademark'] ) )
			                    echo esc_attr( $this->data['trademark'] );
			          ?>"><span class="prs_server_valid"><?=$this->errors['trademark']?></span>
						 
						</div>
						<div class="clear"></div>
						</div>
					<div class="clear"></div>
					</div>
					
					<div class="row">
					<div class="form-label">
						
					<label><?php echo esc_html__( 'Product category', 'prs' ); ?></label></div>	
						
						<div class="form-input">
						 <div class="input-row-full"> 
						 <select class="inpselect" id="p_category" name="p_category[]" onchange="releted_post()" multiple>
						 	<option value="" id="prs_p_category" disabled="disabled"><?php echo esc_html__( 'Select Category', 'prs' ); ?></option>
								<?php 
								    $categories = get_categories();
								    // echo "<pre>";print_r($categories);exit;
							        foreach ($categories as $category) {
							            $option .= '<option value="'.$category->term_id.'">';
							            $option .= str_replace("-"," ",$category->slug);
							            $option .= ' ('.$category->category_count.')';
							            $option .= '</option>';
							        }
							        echo $option;
								?>
						 </select>
						 <span class="prs_server_valid"><?=$this->errors['p_category']?></span>
						</div>
						<div class="clear"></div>
						</div>
					<div class="clear"></div>
					</div>
					
					<div class="row">
					<div class="form-label">
					<label><?php echo esc_html__( 'Product Name', 'prs' ); ?></label></div>	
							
						<div class="form-input">
						 <div class="input-row-full"><span id="loadprsp_name"></span>
							<select class="inpselect" name="p_name[]" id="p_name" multiple>
							<option value="" id="prs_p_name"><?php echo esc_html__( 'Select Product Name', 'prs' ); ?></option>
						 </select>
						  <span class="prs_server_valid"><?=$this->errors['p_name']?></span>
						</div>
						<div class="clear"></div>
						</div>
					<div class="clear"></div>
					</div>
					
						<div class="row">
					<div class="form-label">
					<label><?php echo esc_html__( 'Address', 'prs' ); ?></label></div>	
				
						<div class="form-input">
						 <div class="input-row-full"> 
						 <input type="text" name="street_address" value="<?php if ( isset( $this->data['street_address'] ) )
			                    echo esc_attr( $this->data['street_address'] );
			          ?>">
						 <p class="small"><?php echo esc_html__( 'Street Address', 'prs' ); ?></p><span class="prs_server_valid"><?=$this->errors['street_address']?></span></div>
						 
						 <div class="input-row-full"> 
						  <input type="text" name="street_address1" value="<?php if ( isset( $this->data['street_address'] ) )
			                    echo esc_attr( $this->data['street_address1'] );
			          ?>">
						 <p class="small"><?php echo esc_html__( 'Street Address Line 2', 'prs' ); ?></p></div>
						 
						 <div class="input-row"> <input type="text" name="city" value="<?php if ( isset( $this->data['city'] ) )
			                    echo esc_attr( $this->data['city'] );
			          ?>">
					  <p class="small"><?php echo esc_html__( 'City', 'prs' ); ?></p><span class="prs_server_valid"><?=$this->errors['city']?></span></div>
					  <div class="input-row"> <input type="text" name="state" value="<?php if ( isset( $this->data['state'] ) )
			                    echo esc_attr( $this->data['state'] );
			          ?>">
					  <p class="small"><?php echo esc_html__( 'State', 'prs' ); ?></p><span class="prs_server_valid"><?=$this->errors['state']?></span></div>
					  <div class="input-row"> <input type="text" name="post_code" value="<?php if ( isset( $this->data['post_code'] ) )
			                    echo esc_attr( $this->data['post_code'] );
			          ?>">
					  <p class="small"><?php echo esc_html__( 'Postal / Zip Code', 'prs' ); ?></p><span class="prs_server_valid"><?=$this->errors['post_code']?></span></div>
						</div>
						<div class="clear"></div>
						</div>
					<div class="clear"></div>
					<?php wp_nonce_field( self::NONCE_VALUE , self::NONCE_FIELD ) ?>
					<div class="row rowwe">
						<input type="submit" name="submitForm" value="Submit" class="submit-btn">
					</div>
					</div>
					<script type="text/javascript">
				        	var lights = document.getElementsByClassName("prs_valid");
							while (lights.length)
							    lights[0].className = lights[0].className.replace(/\bprs_valid\b/g, "");
				    </script>
				    <script type="text/javascript">
						<?php foreach ( $this->errors as $key => $value) :
					         if($key == 'p_category' || $key == 'p_name')
					         {
					    ?>
					        	jQuery('select[name="<?=$key.'[]'?>"]').addClass('prs_valid');
					    <?php } else{ ?>
					       		jQuery('input[name="<?=$key?>"]').addClass('prs_valid');
					        
					    <?php } endforeach ?>
				    </script>
		</form>
		<script type="text/javascript">
			jQuery(function(jQuery){
			jQuery('.js-example-basic-multiple').select2();
			jQuery('#p_name').select2();
			});
            </script>
		<?php $cat = wp_create_nonce( 'secure_post_category' );?>
		<script type="text/javascript">
			
			function releted_post()
			{
				p_category = jQuery( "#p_category" ).val() || [];
				if(p_category != '')
				{
					jQuery.ajax({
		    			url: '<?php echo admin_url('admin-ajax.php'); ?>',
		    			async: true,
		    			data:{ 
		    				'action': 'prs_getpname',
		    				'p_category': p_category,
		    				'p_set': '<?=$cat?>',
		    			 },
		    			type: 'post',
		                dataType: 'json',
		                beforeSend : function () {
		                    jQuery('#loadprsp_name').html('<img id="loading-image" src="<?=PLUGIN_URL.'inc/Template/assets/images/prs_loader.gif'?>"/>');

		                },
		    			success: function (data){
		    				var respons = JSON.stringify(data);
		    				var dataparse = JSON.parse(respons);
		                    jQuery('#p_name').html(dataparse.p_name);
		                    jQuery('#loadprsp_name').remove();
		                    return false;
		    			}
		    		});
				}
			}
		</script>
		<?php
		return ob_get_clean();
	}

	/**
     * Is the nonce field valid?
     *
     * @return bool
     */
    public function isNonceValid() {
        return isset( $_POST[ self::NONCE_FIELD ] ) && wp_verify_nonce( $_POST[ self::NONCE_FIELD ], self::NONCE_VALUE );
    }
}