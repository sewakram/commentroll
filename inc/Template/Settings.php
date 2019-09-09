<?php
/**
 *@package Comment Role Capability
 */
namespace Inc\Template;
ob_start();
$options = get_option('prs_brand_mail');
// echo "<pre>";
// print_r($options);
?>
<div class="tabs">
	<ul class="tab-links">
		<li class="active"><a href="#tab1">Settings</a></li>
		<li><a href="#tab2">Mail</a></li>
	</ul>

	<div class="tab-content">

		<div id="tab1" class="tab active">
			<p>Settings</p>
		</div>
		<?php $options = get_option('prs_brand_mail'); ?>
		<div id="tab2" class="tab">
			<h2>Brand Owner Mail</h2>
				<p>
					<b class="prs-left-label"> Account Welcome Email &nbsp;</b>
					<label class="switch">
						  <input type="checkbox" onclick="prs_mail(this.value)" value="prs-welcome-mail" <?php if($options['brand_owner_activity']['prs-welcome-mail']){ echo 'checked'; } ?>>
						  <span class="slider round"> </span>
					</label>
					<a id="prs-welcome-mail" href="<?php echo '?page=mail_template&form=prs-welcome-mail'; ?>"><span class="dashicons dashicons-admin-generic"></span></a>
				</p>

				<p>
					<b class="prs-left-label"> Account Activation Email </b>
					<label class="switch">
						  <input type="checkbox" onclick="prs_mail(this.value)" value="prs-account-activate-mail" <?php if($options['brand_owner_activity']['prs-account-activate-mail']){ echo 'checked'; } ?>>
						  <span class="slider round"> </span>
					</label>
					<a id="prs-account-activate-mail" href="<?php echo '?page=mail_template&form=prs-account-activate-mail'; ?>"><span class="dashicons dashicons-admin-generic"></span></a>
				</p>

				<p>
					<b class="prs-left-label"> Account Approved Email &nbsp;</b>
					<label class="switch">
						  <input type="checkbox" onclick="prs_mail(this.value)" value="prs-account-approved-mail" <?php if($options['brand_owner_activity']['prs-account-approved-mail']){ echo 'checked'; } ?>>
						  <span class="slider round"> </span>
					</label>
					<a id="prs-account-approved-mail" href="<?php echo '?page=mail_template&form=prs-account-approved-mail'; ?>"><span class="dashicons dashicons-admin-generic"></span></a>
				</p>

				<p>
					<b class="prs-left-label"> Account Rejected Email &nbsp;&nbsp;</b>
					<label class="switch">
						  <input type="checkbox" onclick="prs_mail(this.value)" value="prs-account-rejected-mail" <?php if($options['brand_owner_activity']['prs-account-rejected-mail']){ echo 'checked'; } ?>>
						  <span class="slider round"> </span>
					</label>
					<a id="prs-account-rejected-mail" href="<?php echo '?page=mail_template&form=prs-account-rejected-mail'; ?>"><span class="dashicons dashicons-admin-generic"></span></a>
				</p>
		</div>

	</div>
</div>
<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery('.tabs .tab-links a').on('click', function(e) {
			var currentAttrValue = jQuery(this).attr('href');

			// Show/Hide Tabs
			//jQuery('.tabs ' + currentAttrValue).show().siblings().hide();
			jQuery('.tabs ' + currentAttrValue).fadeIn(400).siblings().hide();

			// Change/remove current tab to active
			jQuery(this).parent('li').addClass('active').siblings().removeClass('active');

			e.preventDefault();
		});
	});

	function prs_mail(mail)
	{
	    jQuery.ajax({
	        url: '<?php echo admin_url( 'admin-ajax.php' ) ?>',
	        type: 'post',
	        dataType: 'html',
	        data: { action: 'option_action', keyword: mail },
	        success: function(data) {
	        	jQuery('#response').remove();
	            jQuery('#'+mail).after( data );
	        }
	    });

	}
</script>
<?php ob_flush(); ?>