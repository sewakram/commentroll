//alert('Hey i am working');
function fetch(){
    jQuery.ajax({
        url: prs_object.ajax_url,
        type: 'post',
        dataType: 'html',
        data: { action: 'data_fetch', keyword: jQuery('#keyword').val(), _ajax_nonce: prs_object.nonce },
        success: function(data) {
            jQuery('#datafetch').html( data );
        }
    });

}

jQuery(document).on('click', 'a.closebutton', function() {
         if(jQuery("input#removeuser").val() != '')
         {
           jQuery("input#removeuser").val(jQuery("input#removeuser").val() + "," + jQuery(this).attr('rel'));
           jQuery('.closebutton').remove();
         }
         else
         {
           jQuery("input#removeuser").val(jQuery("input#removeuser").val() + "" + jQuery(this).attr('rel'));
           jQuery('.closebutton').remove();
         }
           return false;
});

