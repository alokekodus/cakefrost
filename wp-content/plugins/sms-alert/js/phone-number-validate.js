(function($){

$.fn.saIntel={
				initIntellinput:function(options){
					var default_cc = (typeof sa_country_settings !='undefined' && sa_country_settings['sa_default_countrycode'] && sa_country_settings['sa_default_countrycode']!='') ? sa_country_settings['sa_default_countrycode'] : '91';
										
					var selected_countries 			= (typeof sa_intl_warning !=  'undefined' && sa_intl_warning['whitelist_countries']) ? sa_intl_warning['whitelist_countries'] : new Array();
					
					var whitelist_countries = [];
					
					for(var c=0;c<selected_countries.length;c++)
					{
						var v = getCountryByCode(selected_countries[c]);
						
						whitelist_countries.push(v[0].iso2.toUpperCase());
					}
					
					var country= $("#billing_country").val();
					
					
					var default_opt = {
						"initialCountry": country,
						"separateDialCode": true,
						"nationalMode": true,
						"formatOnDisplay": false,
						"hiddenInput": "billing_phone",
						"utilsScript": "/utils.js?v=3.3.1",
						"onlyCountries": whitelist_countries
					};
					if(default_cc!='')
					{
						var object = $.extend({},default_opt, options);
					}
					else
					{
						var object = $.extend(default_opt, {initialCountry: "auto",geoIpLookup: function(success, failure) {
							$.get("https://ipapi.co/json/").always(function(resp) {
								var countryCode = (resp && resp.country) ? resp.country : "US";
								success(countryCode);
								
							}).fail(function() {
								console.log("ip lookup is not working.");
							});
						}},options);
					}
					
					
					return object;
				}
			};
	
	jQuery.fn.saIntellinput = $.fn.saIntel.initIntellinput; //call method addSmilesBox and assign to mbSmilesBox
}(jQuery)); 

jQuery( window ).on("load",function() {
    var $ = jQuery;
    var country= $("#billing_country").val();
	
    //var input = document.querySelector("#billing_phone, .phone-valid");
	
	var invalid_no 		= (typeof sa_intl_warning  !=  'undefined' && sa_intl_warning['invalid_no']) ? sa_intl_warning ['invalid_no'] : "Invalid number";
	var invalid_country = (typeof sa_intl_warning  !=  'undefined' && sa_intl_warning['invalid_country']) ? sa_intl_warning['invalid_country'] : "Invalid country code";
	var ppvn 			= (typeof sa_intl_warning !=  'undefined' && sa_intl_warning['ppvn']) ? sa_intl_warning['ppvn'] : "Please provide a valid Number";
	
    var errorMap = [invalid_no, invalid_country, ppvn, ppvn, invalid_no];
    $("#billing_phone").after("<p class='error sa_phone_error' style='display:none'></p>");
	$(document).find(".phone-valid").after("<span class='error sa_phone_error' style='display:none'></span>");

	var vars = {};
	var default_cc = (typeof sa_country_settings !='undefined' && sa_country_settings['sa_default_countrycode'] && sa_country_settings['sa_default_countrycode']!='') ? sa_country_settings['sa_default_countrycode'] : '91';
	var enter_here = (typeof sa_notices !=  'undefined' && sa_notices['enter_here']) ? sa_notices['enter_here'] : "Enter Number Here";
	
	jQuery("#billing_phone, .phone-valid").each(function(i,item){
		jQuery(item).attr('data-id','sa_intellinput_'+i)
			.attr("placeholder", enter_here)
			.intlTelInput("destroy");
		
		var object = jQuery(this).saIntellinput({hiddenInput:"billing_phone"});
		
		vars['sa_intellinput_'+i] = jQuery(this).intlTelInput(object);
		var itis = vars['sa_intellinput_'+i];
		
		
		
		
		if(default_cc!='')
		{
			var selected_cc = getCountryByCode(default_cc);
			var show_default_cc = selected_cc[0].iso2.toUpperCase();
			itis.intlTelInput("setCountry",show_default_cc);
		}
	});	
	
	//get all country data		
	function getCountryByCode(code) {
		return window.intlTelInputGlobals.getCountryData().filter(
		function(data){ return (data.dialCode == code) ? data.iso2 : ''; }
		);
	}

	jQuery('#billing_country').change(function(){
		var iti = vars[jQuery("#billing_phone").attr('data-id')];
		iti.intlTelInput("setCountry",$(this).val());
		onChangeCheckValidno(document.querySelector("#billing_phone"));
	});

	var reset = function(obj) {
       // jQuery(".sa_phone_error").text("");
       jQuery(obj).parents("form").find(".sa_phone_error").hide();
		
    };	

	function onChangeCheckValidno(obj)
	{
		reset(obj);
		var input 	= obj;
		//var iti 	= vars[jQuery(obj).attr('data-id')]; // 04/01/2020
		var iti 	= jQuery(obj);
		if (input.value.trim()) {
			if (iti.intlTelInput('isValidNumber')) {
				jQuery("#smsalert_otp_token_submit").attr("disabled",false);
				jQuery("#sa_bis_submit").attr("disabled",false);
				iti.parents("form").find(".sa-otp-btn-init").attr("disabled",false);
			} else{
				var errorCode = iti.intlTelInput('getValidationError');
				//input.focus();
                iti.parents(".iti--separate-dial-code").next(".sa_phone_error").text(errorMap[errorCode]);
				jQuery("#smsalert_otp_token_submit").attr("disabled",true);
				iti.parents(".iti--separate-dial-code").next(".sa_phone_error").removeAttr("style");
				iti.parents("form").find(".sa-otp-btn-init").attr("disabled",true);
				jQuery("#sa_bis_submit").attr("disabled",true);
			}
			
        }
		
	}

    jQuery(document).on("blur","#billing_phone, .phone-valid",function() {
		onChangeCheckValidno(this);
    });

	//backinstock form
	// jQuery('.sa_bis_submit, .sa-otp-btn-init, form.register input[type=submit],input[type=submit]').click(function(){
		// var ph_field = jQuery(this).parents("form").find(".phone-valid");
		// if(typeof ph_field.val()=='undefined')
		// {
			// var ph_field = jQuery(this).parents("form").find("#billing_phone");
		// }

		// if(typeof ph_field.val()!='undefined')
		// {
			// ph_field.val(ph_field.intlTelInput("getNumber").replace(/\D/g, ""));
		// }
	// });
		
	//jQuery(".phone-valid,#billing_phone").keyup(function(){
	jQuery(document).on("keyup","#billing_phone, .phone-valid",function() {
		var fullnumber =  jQuery(this).intlTelInput("getNumber"); //get number with std code
		jQuery(this).intlTelInput("setNumber",fullnumber);
		jQuery(this).next("[name=billing_phone]").val(fullnumber);
		
		if (jQuery(this).intlTelInput('isValidNumber')) {
			reset(this);
			jQuery(this).parents("form").find(".sa-otp-btn-init").attr("disabled",false);
		}
		
	});
	
	jQuery(".phone-valid,#billing_phone").trigger('keyup');

	// on keyup / change flag: reset
    jQuery("#billing_phone").change(function() {
		reset(this);
    });
});