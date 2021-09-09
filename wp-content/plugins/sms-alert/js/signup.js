jQuery(function() {
	jQuery('[name=billing_phone]').on("change", function (e) {
			if(smsalert_mdet.update_otp_enable=='on')
			{
				var new_phone = jQuery('[name=billing_phone]:last-child').val();
				var old_phone = jQuery('#old_billing_phone').val();
				if(new_phone!='' && new_phone!=old_phone)
				{
					jQuery(this).parents('form').find('#sa_verify').removeClass("sa-default-btn-hide");
					jQuery('[name="save_address"]').addClass("sa-default-btn-hide");
				}
				else{
					jQuery('[name="save_address"]').removeClass("sa-default-btn-hide");
					jQuery(this).parents('form').find('#sa_verify').addClass("sa-default-btn-hide");
				}
			}
        });
		/* jQuery('.sa-default-btn-hide[name="save_address"]').each(function(index) {
			jQuery(this).removeClass('sa-default-btn-hide');
			jQuery(this).parents('form').find('#sa_verify').addClass("sa-default-btn-hide");
		}); */
		
		jQuery('input[id="reg_email"]').each(function(index) {
			//if(smsalert_mdet.mail_accept==0)
			{
				//jQuery(this).closest(".form-required").removeClass("form-required").find(".description").remove();
				jQuery(this).parent().hide();
			}
			/* else if(smsalert_mdet.mail_accept==1){
				jQuery(this).parent().children("label").html("Email");
				jQuery(this).closest(".form-required").removeClass("form-required").find(".description").remove();
			} */
			});
		var register = jQuery("#smsalert_name").closest(".register");
		register.find(".woocommerce-Button, button[name='register']").each(function()
		{
			if (jQuery(this).attr("name") == "register") {
				if (!jQuery(this).text()!=smsalert_mdet.signupwithotp) {
				   jQuery(this).val(smsalert_mdet.signupwithotp);
				   jQuery(this).find('span').text(smsalert_mdet.signupwithotp);
				}
			}
		});
});
// login js
jQuery(function($) {
    function isEmpty(el) {
        return !jQuery.trim(el)
    }
    var tokenCon;
    var akCallback = -1;
    var body = jQuery("body");
    var modcontainer = jQuery(".smsalert-modal");
    var noanim = false;
    $.fn.smsalert_login_modal = function($this) {
        show_smsalert_login_modal($this);
        return false
    };
    jQuery(document).on("click", ".smsalert-login-modal", function() {
        if (!jQuery(this).attr("attr-disclick")) {
            show_smsalert_login_modal(jQuery(this))
        }
        return false
    });
	function getUrlParams(url) {
		var params = {};
		url.substring(0).replace(/[?&]+([^=&]+)=([^&]*)/gi,
			function (str, key, value) {
				 params[key] = value;
			});
		return params;
	}

    function show_smsalert_login_modal($this) {
		//jQuery(".u-column2").css("display",'none');
		var windowWidth = jQuery(window).width();
		var params 		= getUrlParams($this.attr("href"));
		var def 		= params["default"];
		var showonly 	= params["showonly"];
		if ((def == 'register' && showonly=='') || showonly=='register') {
			jQuery(".u-column1,.signdesc").css("display",'none');
			jQuery(".u-column2").css("display",'block');
			jQuery("#slide_form").css("transform","translateX(-373px)");
		} else if ((def == 'login' && showonly=='') || showonly=='login') {
			jQuery(".u-column1").css("display",'block');
			jQuery(".u-column2,.signdesc").css("display",'none');
		} else if (showonly == 'login,register' || showonly == 'register,login') {
			if(def == 'login')
			{
				jQuery(".u-column2").css("display",'none');
				jQuery(".u-column1,.signdesc").css("display",'block');
			}
			else{
				jQuery(".backtoLoginContainer,.u-column2").css("display",'block');
				jQuery(".u-column1,.signdesc").css("display",'none');
				jQuery("#slide_form").css("transform","translateX(-373px)");
			}
		}
		modcontainer.css({
			display: "block"
		});
		return false
    }
    jQuery(document).on("click", ".smsalert-modal .backtoLogin", function() {
        jQuery(".backtoLoginContainer").css("display",'none');
		jQuery(".signdesc").css("display",'block');
		if(jQuery(".from-left #slide_form").length || jQuery(".from-right #slide_form").length || jQuery(".center #slide_form").length){
			jQuery("#slide_form").css("transform","translateX(0)");
			jQuery(".u-column1,.signdesc").show();

		}else{
			jQuery(".u-column2").css("display",'none');
			jQuery(".u-column1").css("display",'block');
			jQuery(".signupbutton").css("display",'block');

		}
	});
    jQuery(document).on("click", ".smsalert-modal .signupbutton", function() {
        jQuery(".backtoLoginContainer").css("display",'block');
		jQuery(".signdesc").css("display",'none');
		if(jQuery(".from-left #slide_form").length || jQuery(".from-right #slide_form").length || jQuery(".center #slide_form").length){
			jQuery(".u-column2").show();
			jQuery("#slide_form").css("transform","translateX(-373px)");
		}else{
			jQuery(".u-column2").css("display",'block');
			jQuery(".u-column1").css("display",'none');
		}
	});
});
jQuery(document).on("click", ".smsalert-login-modal", function(){
	var display = jQuery(this).attr('data-display');
	jQuery(".smsalert-modal.smsalertModal").addClass(display);
	if(display == 'from-right'){
		jQuery(".from-right > .modal-content").animate({right:'0',opacity:'1'}, 100);
	}
	if(display == 'from-left'){
		jQuery(".from-left > .modal-content").animate({left:'0',opacity:'1'}, 100);;
	}
});

jQuery(document).on("click",".from-right .close,.from-left .close",function(){
	jQuery(".modal-content").removeAttr("style");
});