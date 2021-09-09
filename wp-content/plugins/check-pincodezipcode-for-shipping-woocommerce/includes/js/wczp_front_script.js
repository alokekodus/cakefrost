function setCookie(cname,cvalue,exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires=" + d.toGMTString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for(var i = 0; i < ca.length; i++) {
            var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}


jQuery(document).ready(function() {

    wczp_postcode = getCookie("wczp_postcode");
    jQuery("#billing_postcode").val(wczp_postcode);

    jQuery("body").on('click', '.wczpbtn', function() {

        var postcode = jQuery('.wczpcheck').val();

        if(postcode != '') {
            jQuery('.wczp_empty').css('display', 'none');
            jQuery('.wczpc_maindiv').append('<div class="wczpc_spinner"><img src="'+ wczp_plugin_url +'/includes/images/loader-3.gif"></div>');
            jQuery('.wczpc_maindiv').addClass('wczpc_loader');
            jQuery.ajax({
                type: "post",
                dataType: 'json',
                url: wczp_ajax_postajax.ajaxurl,
                data: { 
                        action:   "WCZP_check_location",
                        postcode: postcode,
                     },
                success: function(msg) {
                    jQuery(".wczpc_spinner").remove();
                    jQuery('.wczpc_maindiv').removeClass('wczpc_loader');
                    if(msg.totalrec == 1) {
                        jQuery('.wczp_checkcode').show();
                        jQuery('.wczp_cookie_check_div').hide();
                        
                        var date = '';
                        if(msg.showdate == "on") {
                            date = "delivery date : "+msg.deliverydate;
                        }
                        jQuery('.response_pin').html(msg.avai_msg);
                        jQuery('.wczp_avaicode').html(postcode);
                    } else {
                        jQuery('.wczp_checkcode').show();
                        jQuery('.wczp_cookie_check_div').hide();
                        jQuery('.wczp_avaicode').html(postcode);
                        jQuery('.response_pin').html(wczp_not_srvcbl_txt);
                    }
                }
            });
        } else {
            jQuery('.wczp_empty').css('display', 'block');
        }

    });


    jQuery("body").on('click', '.wczpcheckbtn', function() {
        jQuery('.wczp_cookie_check_div').css('display', 'flex');
        jQuery('.wczp_checkcode').hide();
    });


    jQuery("body").on('click', '.wczpinzipsubmit', function() {
         var popup_postcode = jQuery('.wczpopuppinzip').val();
         jQuery('.wczp_err').remove();

        if(popup_postcode != '') {
            jQuery('.wczp_empty').css('display', 'none');
             jQuery('.wczpc_maindiv_popup').append('<div class="wczpc_spinner"><img src="'+ wczp_plugin_url +'/includes/images/loader-3.gif"></div>');
            jQuery('.wczpc_maindiv_popup').addClass('wczpc_loader');
            jQuery.ajax({
                type: "POST",
                url: wczp_ajax_postajax.ajaxurl,
                dataType: 'json',
                data: { 
                        action:"WCZP_popup_check_zip_code",
                        popup_postcode: popup_postcode,
                     },
                success: function(msg){
                   jQuery(".wczpc_spinner").remove();
                    jQuery('.wczpc_maindiv_popup').removeClass('wczpc_loader');
                        if(msg.totalrec == 1) {
                            jQuery('.wczp_empty').css('display', 'none');
                            jQuery('.wczp_checkcodee').show();
                            jQuery('.wczp_cookie_check_div').hide();
                             jQuery('.response_pin_popup').show();
                            jQuery('.response_pin_popup').html(msg.avai_msg);
                            jQuery('.wczp_avaicodee').html(popup_postcode);
                            setInterval(function(){ location.reload(); }, 5000);
                        } else {
                            jQuery('.wczp_empty').css('display', 'none');
                            jQuery('.wczp_checkcode').show();
                            jQuery('.wczp_cookie_check_div').hide();
                            jQuery('.response_pin_popup').show();
                            jQuery('.wczp_avaicode').html(popup_postcode);
                            jQuery('.response_pin_popup').html(wczp_not_srvcbl_txt);
                        }
                }
            });
        }else{
            jQuery('.wczp_empty').css('display', 'block');
            jQuery('.response_pin_popup').hide();
        }
    });

    jQuery("body").on('keyup', '#billing_postcode', function() {
        var pincode = jQuery(this).val();

        if(pincode != '') {
            jQuery.ajax({
                type: "POST",
                url: wczp_ajax_postajax.ajaxurl,
                dataType: 'json',
                data: { 
                        action:"WCZP_pincode_change_checkout",
                        pincode: pincode,
                     },
                success: function(response) {
                    jQuery("body").trigger("update_checkout");
                }
            });
        }

    });
});


jQuery(document).ready(function() {
    var usernamea = getCookie("usernamea");

    if (usernamea != "popusetp") {
    	setTimeout(function() {
    		jQuery("#wczpModal").show();
        	jQuery('#wczp_pincode_popup').show();
    	}, 1000);
    }

    jQuery("body").on('click', '.close', function(e){

        e.preventDefault();

        jQuery('#wczpModal').hide();
        jQuery('#wczp_pincode_popup').hide();

        setCookie("usernamea", "popusetp", 7);
    });

    jQuery('.wczpbtn').click(function() {
            jQuery('.response_pin').animate(
                { deg: 360 },
                {
                    duration: 1200,
                    step: function(now) {
                    jQuery(this).css({ transform: 'rotate(' + now + 'deg)' });
                }
            }
          );
    });

    jQuery("body").on('click', '#wczp_pincode_popup', function() {
    	jQuery('#wczpModal').hide();
        jQuery('#wczp_pincode_popup').hide();
        setCookie("usernamea", "popusetp", 7);
    });
});
jQuery(document).on('keypress','#wczpModal',function(e) {
    if(e.which == 13) {
        return false;
    }
});