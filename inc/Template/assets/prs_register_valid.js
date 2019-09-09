jQuery( document ).ready(function( $ ) {

  $( 'body' ).on('submit','#customer-form',function(e){

  		e.preventDefault();
  		var customerform = $(this),
  			action = customerform.attr('action'),
  			formData = customerform.serializeArray(),
  			firstname = $('input[name=firstname]').val(),
  			lastname = $('input[name=lastname]').val(),
  			business = $('input[name=business]').val(),
  			email = $('input[name=email]').val(),  
  			area_code = $('input[name=area_code]').val(),
  			phone_no = $('input[name=phone_no]').val(),
  			brand_name = $('input[name=brand_name]').val(),
  			trademark = $('input[name=trademark]').val(),
  			p_category = $( "#p_category" ).val() || [],
  			p_name = $( "#p_name" ).val() || [],
  			street_address = $('input[name=street_address]').val(),
  			street_address1 = $('input[name=street_address1]').val(),
  			city = $('input[name=city]').val(),
  			state = $('input[name=state]').val(),
  			post_code = $('input[name=post_code]').val();
			
  			var prafocus = [];
  			var letters = /^[A-Za-z]+$/;
  			var hasError = '';
  			var pravalidtwo = ['firstname','lastname','business'];
  			var vartwo = [firstname,lastname,business];
  			for(var i = 0; i < pravalidtwo.length; i++)
  			{
  				$( '#prs_'+pravalidtwo[i] ).remove();
	  			$('input[name='+pravalidtwo[i]+']').removeClass('prs_valid');
	  			if(vartwo[i] == '')
	  			{
	  				$( '#prs_'+pravalidtwo[i] ).remove();
				    $('input[name='+pravalidtwo[i]+']').after('<span id="prs_'+pravalidtwo[i]+'" style="color: red;font-size:12px;">Please enter your '+pravalidtwo[i]+'</span>');
				    $('input[name='+pravalidtwo[i]+']').addClass('prs_valid');
				     hasError = 'yes';
				    prafocus.push('"'+pravalidtwo[i]+'"');
	  			}else if(!vartwo[i].match(letters) && (pravalidtwo[i] === 'firstname' || pravalidtwo[i] === 'lastname'))
	  			{
	  				$( '#prs_'+pravalidtwo[i] ).remove();
				    $('input[name='+pravalidtwo[i]+']').after('<span id="prs_'+pravalidtwo[i]+'" style="color: red;font-size:12px;">Please enter characters only</span>');
				    $('input[name='+pravalidtwo[i]+']').addClass('prs_valid');
				     hasError = 'yes';
				    prafocus.push('"'+pravalidtwo[i]+'"');
	  			}else if(vartwo[i].length < 3 && (pravalidtwo[i] === 'firstname' || pravalidtwo[i] === 'lastname'))
	  			{
	  				$( '#prs_'+pravalidtwo[i] ).remove();
				    $('input[name='+pravalidtwo[i]+']').after('<span id="prs_'+pravalidtwo[i]+'" style="color: red;font-size:12px;">Enter 3 char long name</span>');
				    $('input[name='+pravalidtwo[i]+']').addClass('prs_valid');
				     hasError = 'yes';
				    prafocus.push('"'+pravalidtwo[i]+'"');
	  			}
  			}

  			 $('input[name=email]').removeClass( 'prs_valid' );
			 $('#emailvalid').remove();
			if ( email == '' ) {
				 $('#emailvalid').remove();
			    $('input[name=email]').after('<span id="emailvalid" style="color: red;font-size:12px;">Please enter your email address</span>');
			    $('input[name=email]').addClass('prs_valid');
			     hasError = 'yes';
			    prafocus.push('email');
			}

		    function checkEmail(mail){
		    	$('#emailvalid').remove();
			    var filter = /^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/;
			    if (!filter.test(mail)) {
				    $('#emailvalid').remove();
				    $('input[name=email]').after('<span id="emailvalid" style="color: red;font-size:12px;">The E-mail Address Entered is not Valid </span>');
				    $('input[name=email]').addClass('prs_valid');
				    hasError = 'yes';
				    prafocus.push('email');
				}
			}

			if(email != '')
			{
		 		checkEmail(email);
		 	}

		 	var checkprano = /^[0-9]+$/;
		 	var praphone = ['area_code', 'phone_no'];
		 	var praphonevar = [area_code, phone_no];
		 	for(var j = 0; j < praphone.length; j++)
		 	{
		 		$( '#prs_'+praphone[j] ).remove();
	  			$('input[name='+praphone[j]+']').removeClass('prs_valid');
		 		if(praphonevar[j] == '')
	  			{
	  				$( '#prs_'+praphone[j] ).remove();
	  				$('input[name='+praphone[j]+']').after('<span id="prs_'+praphone[j]+'" style="color: red;font-size:12px;">Enter '+praphone[j].replace('_',' ')+'</span>');
	  				$('input[name='+praphone[j]+']').addClass('prs_valid');
	  				hasError = 'yes';
	  				prafocus.push('"'+praphone[j]+'"');
	  			}
	  			else if(!praphonevar[j].match(checkprano))
	  			{
	  				$( '#prs_'+praphone[j] ).remove();
	  				$('input[name='+praphone[j]+']').after('<span id="prs_'+praphone[j]+'" style="color: red;font-size:12px;">Enter Number only</span>');
	  				$('input[name='+praphone[j]+']').addClass('prs_valid');
	  				hasError = 'yes';
	  				prafocus.push('"'+praphone[j]+'"');
	  			}
	  			else if(praphonevar[j].length < 6 && praphone[j] == 'phone_no')
	  			{
	  				$( '#prs_'+praphone[j] ).remove();
	  				$('input[name='+praphone[j]+']').after('<span id="prs_'+praphone[j]+'" style="color: red;font-size:12px;">Enter minimum 6 digit long</span>');
	  				$('input[name='+praphone[j]+']').addClass('prs_valid');
	  				hasError = 'yes';
	  				prafocus.push('"'+praphone[j]+'"');
	  			}
		 	}

		 	$('input[name=brand_name]').removeClass( 'prs_valid' );
			 $('#brand_namevalid').remove();
			if ( brand_name == '' ) {
				 $('#brand_namevalid').remove();
			    $('input[name=brand_name]').after('<span id="brand_namevalid" style="color: red;font-size:12px;">Please enter Brand Name</span>');
			    $('input[name=brand_name]').addClass('prs_valid');
			     hasError = 'yes';
			    prafocus.push('brand_name');
			}

			$('input[name=trademark]').removeClass( 'prs_valid' );
			 $('#trademarkvalid').remove();
			if ( trademark == '' ) {
				 $('#trademarkvalid').remove();
			    $('input[name=trademark]').after('<span id="trademarkvalid" style="color: red;font-size:12px;">Please enter trademark number</span>');
			    $('input[name=trademark]').addClass('prs_valid');
			     hasError = 'yes';
			    prafocus.push('trademark');
			}

			var pramaintxt = ['p_category', 'p_name'];
			var pramainvar = [p_category, p_name];
			for(var k = 0; k < pramaintxt.length; k++)
			{
				$('#pra_'+pramaintxt[k]).remove();
				$('#'+pramaintxt[k]).removeClass('prs_valid');
				if(pramainvar[k] == '')
				{
					$('#pra_'+pramaintxt[k]).remove();
				   $('#'+pramaintxt[k]).after('<span id="pra_'+pramaintxt[k]+'" style="color: red;font-size:12px;">Please select '+pramaintxt[k].replace('p_','product ')+'</span>');
				    $('#'+pramaintxt[k]).addClass('prs_valid');
				     hasError = 'yes';
				    prafocus.push('"'+pramaintxt[k]+'"');
				}
			}
			
			var praaddtxt = ['street_address','city','state'];
			var praaddvar = [street_address,city,state];
			for(var p = 0; p < praaddtxt.length; p++)
			{
				$('input[name='+praaddtxt[p]+']').removeClass( 'prs_valid' );
				$('#pra_'+praaddtxt[p]).remove();
				if(praaddvar[p] == '')
				{
					$('#pra_'+praaddtxt[p]).remove();
					$('input[name='+praaddtxt[p]+']').removeClass( 'prs_valid' );
				    $('input[name='+praaddtxt[p]+']').after('<span id="pra_'+praaddtxt[p]+'" style="color: red;font-size:12px;">Please enter '+praaddtxt[p].replace('street_','')+'</span>');
				    $('input[name='+praaddtxt[p]+']').addClass('prs_valid');
				     hasError = 'yes';
				    prafocus.push('"'+praaddtxt[p]+'"');
				}
			}

			$('input[name=post_code]').removeClass('prs_valid');
			$( '#prs_postcode' ).remove();
			if(post_code == '')
			{
				$( '#prs_postcode' ).remove();
  				$('input[name=post_code]').after('<span id="prs_postcode" style="color: red;font-size:12px;">Enter Post code/Zip code</span>');
  				$('input[name=post_code]').addClass('prs_valid');
  				hasError = 'yes';
  				prafocus.push('post_code');
			}
			else if(!post_code.match(checkprano))
  			{
  				$( '#prs_postcode' ).remove();
  				$('input[name=post_code]').after('<span id="prs_postcode" style="color: red;font-size:12px;">Enter Number only</span>');
  				$('input[name=post_code]').addClass('prs_valid');
  				hasError = 'yes';
  				prafocus.push('post_code');
  			}

  			if(prafocus[0])
  			{
  				if(prafocus[0].replace('_','') == '"pcategory"' || prafocus[0].replace('_','') == '"pname"')
	  			{
	  				$('select[name='+prafocus[0]+']').focus();
	  			}
	  			else if(prafocus[0])
	  			{
	  				$('input[name='+prafocus[0]+']').focus();
	  			}
  			}
  			
  			//alert(hasError);
			if ( hasError === 'yes' ) {
				return false;								
			}
			document.getElementById("customer-form").submit(); 
			return true;
  	})
});