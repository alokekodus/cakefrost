(function(a){a(function(){function i(){a('#coderockz_woo_delivery_time_field option').each(function(){var b=a(this).attr('disabled');typeof b!==typeof undefined&&b!==!1&&a(this).attr('disabled',!1);var c=a(this).text();var d=c.indexOf(w);d!==-1&&a(this).text(c.substr(0,d));});}function j(c,d,b){c==d&&a('#coderockz_woo_delivery_time_field option').each(function(){if(a(this).val()!=''){var c=a(this).val().split(' - ');_times_one=c[0].split(':'),_times_two=c[1].split(':'),c=_times_one[0]*60+parseInt(_times_one[1])+' - '+(_times_two[0]*60+parseInt(_times_two[1])),c=c.split(' - '),c[0]<=b&&c[1]<=b&&a(this).attr('disabled',!0);}});}function k(d,e){var b={};if(d.length>0){for(var c=0;d.length>c;c++)b[d[c]]=(b[d[c]]||0)+1;for(var f in b){if(!b.hasOwnProperty(f))continue;typeof e!==typeof undefined&&e!==!1&&b[f]>=e&&e!=0&&a('#coderockz_woo_delivery_time_field option').each(function(){a(this).val()==f&&(a(this).attr('disabled',!0),a(this).text(a(this).text()+w));});}}}function l(c,d,e,b){c==d&&e&&a('#coderockz_woo_delivery_time_field option').each(function(){if(a(this).val()!=''){var c=a(this).val().split(' - ');_times_one=c[0].split(':'),_times_two=c[1].split(':'),c=_times_one[0]*60+parseInt(_times_one[1])+' - '+(_times_two[0]*60+parseInt(_times_two[1])),c=c.split(' - '),c[0]<=b&&c[1]>b&&a(this).attr('disabled',!0);}});}function m(b){(typeof a('#coderockz_woo_delivery_date_datepicker').val()==typeof undefined&&a('#coderockz_woo_delivery_date_datepicker').val()==0||a('#coderockz_woo_delivery_date_datepicker').val()=='')&&a('#coderockz_woo_delivery_time_field option').each(function(){if(a(this).val()!=''){var c=a(this).val().split(' - ');_times_one=c[0].split(':'),_times_two=c[1].split(':'),c=_times_one[0]*60+parseInt(_times_one[1])+' - '+(_times_two[0]*60+parseInt(_times_two[1])),c=c.split(' - '),c[0]<=b&&c[1]<=b&&a(this).attr('disabled',!0);}});}function n(b){b&&a('#coderockz_woo_delivery_time_field option').each(function(){a(this).is('[selected=selected]')&&a(this).val()==''&&(emptyOption=a(this));var b=a(this).attr('value');return!a(this).is('[disabled=disabled]')&&a(this).val()!=''&&typeof b!==typeof undefined&&b!==!1?(a('#coderockz_woo_delivery_time_field option:selected').attr('selected',!1),a(this).attr('selected',!0),a('#coderockz_woo_delivery_time_field').val(a(this).val()),typeof emptyOption!==typeof undefined&&emptyOption!==!1&&(emptyOption.attr('selected',!1),emptyOption.attr('disabled',!0)),!1):void 0;});}function o(){a('#coderockz_woo_delivery_pickup_time_field option').each(function(){var b=a(this).attr('disabled');typeof b!==typeof undefined&&b!==!1&&a(this).attr('disabled',!1);var c=a(this).text();var d=c.indexOf(x);d!==-1&&a(this).text(c.substr(0,d));});}function p(c,d,b){c==d&&a('#coderockz_woo_delivery_pickup_time_field option').each(function(){if(a(this).val()!=''){var c=a(this).val().split(' - ');_pickupTimes_one=c[0].split(':'),_pickupTimes_two=c[1].split(':'),c=_pickupTimes_one[0]*60+parseInt(_pickupTimes_one[1])+' - '+(_pickupTimes_two[0]*60+parseInt(_pickupTimes_two[1])),c=c.split(' - '),c[0]<=b&&c[1]<=b&&a(this).attr('disabled',!0);}});}function q(d,e){var b={};if(d.length>0){for(var c=0;d.length>c;c++)b[d[c]]=(b[d[c]]||0)+1;for(var f in b){if(!b.hasOwnProperty(f))continue;typeof e!==typeof undefined&&e!==!1&&b[f]>=e&&e!=0&&a('#coderockz_woo_delivery_pickup_time_field option').each(function(){a(this).val()==f&&(a(this).attr('disabled',!0),a(this).text(a(this).text()+x));});}}}function r(c,d,e,b){c==d&&e&&a('#coderockz_woo_delivery_pickup_time_field option').each(function(){if(a(this).val()!=''){var c=a(this).val().split(' - ');_times_one=c[0].split(':'),_times_two=c[1].split(':'),c=_times_one[0]*60+parseInt(_times_one[1])+' - '+(_times_two[0]*60+parseInt(_times_two[1])),c=c.split(' - '),c[0]<=b&&c[1]>b&&a(this).attr('disabled',!0);}});}function s(b,c,d){(typeof a('#coderockz_woo_delivery_pickup_date_datepicker').val()==typeof undefined&&a('#coderockz_woo_delivery_pickup_date_datepicker').val()==0||a('#coderockz_woo_delivery_pickup_date_datepicker').val()=='')&&a('#coderockz_woo_delivery_pickup_time_field option').each(function(){if(a(this).val()!=''){var d=a(this).val().split(' - ');_pickupTimes_one=d[0].split(':'),_pickupTimes_two=d[1].split(':'),d=_pickupTimes_one[0]*60+parseInt(_pickupTimes_one[1])+' - '+(_pickupTimes_two[0]*60+parseInt(_pickupTimes_two[1])),d=d.split(' - '),(d[0]<=b+c&&d[1]<=b+c||d[0]<=b+c&&disable_timeslot_with_processing_time=='1')&&a(this).attr('disabled',!0);}});}function t(b){b&&a('#coderockz_woo_delivery_pickup_time_field option').each(function(){a(this).is('[selected=selected]')&&a(this).val()==''&&(emptyPickupOption=a(this));var b=a(this).attr('value');if(!a(this).is('[disabled=disabled]')&&a(this).val()!=''&&typeof b!==typeof undefined&&b!==!1)return a('#coderockz_woo_delivery_pickup_time_field option:selected').attr('selected',!1),a(this).attr('selected',!0),a('#coderockz_woo_delivery_pickup_time_field').val(a(this).val()),typeof emptyPickupOption!==typeof undefined&&emptyPickupOption!==!1&&(emptyPickupOption.attr('selected',!1),emptyPickupOption.attr('disabled',!0)),!1;else a('#coderockz_woo_delivery_pickup_time_field').val('');});}function u(){var b=[];var r=a('#coderockz_woo_delivery_date_datepicker').data('selectable_dates');all_disable_week_days=c;for(var f=0;f<r;f++){var s=new Date();var t=s.setDate(s.getDate()+f);var d=new Date(t);var u='0'+(Number(d.getMonth())+1);var v='0'+d.getDate();var w=d.getDay().toString();var h=d.getFullYear()+'-'+u.substr(-2)+'-'+v.substr(-2);all_disable_week_days.length!=7?a.inArray(h,B)===-1&&a.inArray(h,g)===-1&&a.inArray(w,all_disable_week_days)===-1?b.push(h):r+=1:b.push('0000-00-00');}if(A){var x=b[0].substr(0,4);if(b[0].substr(5,1)=='0')var o=b[0].substr(6,1)-1;else var o=b[0].substr(5,2)-1;if(b[0].substr(8,1)=='0')var p=b[0].substr(9,1);else var p=b[0].substr(8,2);var q=new Date(x,o,p);}else var q='0000-00-00';a('#coderockz_woo_delivery_date_datepicker').length?a('#coderockz_woo_delivery_date_datepicker').flatpickr({enable:b,minDate:today_date,dateFormat:y,defaultDate:q,locale:{firstDayOfWeek:z},onChange:function(g,h,o){a('.coderockz-woo-delivery-loading-image').fadeIn(),a('#coderockz_woo_delivery_time_field').val('');var b=new Date(g);var d='0'+(b.getMonth()+1);var f='0'+b.getDate();var c=b.getFullYear()+'-'+d.substr(-2)+'-'+f.substr(-2);a.ajax({url:coderockz_woo_delivery_ajax_obj.coderockz_woo_delivery_ajax_url,type:'POST',data:{_ajax_nonce:coderockz_woo_delivery_ajax_obj.nonce,action:'coderockz_woo_delivery_get_orders',date:c},success:function(g){data=JSON.parse(g.data);var b=data.current_time;var d=data.delivery_times;var f=data.max_order_per_slot;i(),j(c,today_date,b),k(d,f),l(c,today_date,data.disabled_current_time_slot,b),m(b),n(e),a('#coderockz_woo_delivery_time_field').select2({dropdownParent:a('#coderockz_woo_delivery_delivery_time_section'),allowClear:!0}),a('.coderockz-woo-delivery-loading-image').fadeOut();}});},onReady:function(h,o,d){a('.coderockz-woo-delivery-loading-image').fadeIn();var c=new Date(d.selectedDates[0]);var f='0'+(c.getMonth()+1);var g='0'+c.getDate();if(d.selectedDates.length>0)var b=c.getFullYear()+'-'+f.substr(-2)+'-'+g.substr(-2);else var b=today_date;a.ajax({url:coderockz_woo_delivery_ajax_obj.coderockz_woo_delivery_ajax_url,type:'POST',data:{_ajax_nonce:coderockz_woo_delivery_ajax_obj.nonce,action:'coderockz_woo_delivery_get_orders',date:b},success:function(g){data=JSON.parse(g.data);var c=data.current_time;var d=data.delivery_times;var f=data.max_order_per_slot;i(),j(b,today_date,c),k(d,f),l(b,today_date,data.disabled_current_time_slot,c),m(c),n(e),a('#coderockz_woo_delivery_time_field').select2({dropdownParent:a('#coderockz_woo_delivery_delivery_time_section'),allowClear:!0}),a('.coderockz-woo-delivery-loading-image').fadeOut();}});}}):a('#coderockz_woo_delivery_time_field').length?(a('.coderockz-woo-delivery-loading-image').fadeIn(),a.ajax({url:coderockz_woo_delivery_ajax_obj.coderockz_woo_delivery_ajax_url,type:'POST',data:{_ajax_nonce:coderockz_woo_delivery_ajax_obj.nonce,action:'coderockz_woo_delivery_get_orders',onlyDeliveryTime:!0,date:today_date},success:function(f){data=JSON.parse(f.data);var b=data.current_time;var c=data.delivery_times;var d=data.max_order_per_slot;i(),j(today_date,today_date,b),k(c,d),l(today_date,today_date,data.disabled_current_time_slot,b),m(b),n(e),a('#coderockz_woo_delivery_time_field').select2({dropdownParent:a('#coderockz_woo_delivery_delivery_time_section'),allowClear:!0}),a('.coderockz-woo-delivery-loading-image').fadeOut();}})):a('.coderockz-woo-delivery-loading-image').fadeOut();}function v(){var b=[];var l=a('#coderockz_woo_delivery_pickup_date_datepicker').data('pickup_selectable_dates');all_pickup_disable_week_days=d;for(var e=0;e<l;e++){var m=new Date();var n=m.setDate(m.getDate()+e);var c=new Date(n);var u='0'+(Number(c.getMonth())+1);var v='0'+c.getDate();var w=c.getDay().toString();var g=c.getFullYear()+'-'+u.substr(-2)+'-'+v.substr(-2);all_pickup_disable_week_days.length!=7?a.inArray(g,F)===-1&&a.inArray(g,h)===-1&&a.inArray(w,all_pickup_disable_week_days)===-1?b.push(g):l+=1:b.push('0000-00-00');}if(E){var x=b[0].substr(0,4);if(b[0].substr(5,1)=='0')var i=b[0].substr(6,1)-1;else var i=b[0].substr(5,2)-1;if(b[0].substr(8,1)=='0')var j=b[0].substr(9,1);else var j=b[0].substr(8,2);var k=new Date(x,i,j);}else var k='0000-00-00';a('#coderockz_woo_delivery_pickup_date_datepicker').length?a('#coderockz_woo_delivery_pickup_date_datepicker').flatpickr({enable:b,minDate:today_date,dateFormat:C,defaultDate:k,locale:{firstDayOfWeek:D},onChange:function(g,h,i){a('.coderockz-woo-delivery-loading-image').fadeIn(),a('#coderockz_woo_delivery_pickup_time_field').val('');var b=new Date(g);var d='0'+(b.getMonth()+1);var e='0'+b.getDate();var c=b.getFullYear()+'-'+d.substr(-2)+'-'+e.substr(-2);a.ajax({url:coderockz_woo_delivery_ajax_obj.coderockz_woo_delivery_ajax_url,type:'POST',data:{_ajax_nonce:coderockz_woo_delivery_ajax_obj.nonce,action:'coderockz_woo_delivery_get_orders_pickup',date:c},success:function(g){data=JSON.parse(g.data);var b=data.current_time;var d=data.pickup_delivery_times;var e=data.pickup_max_order_per_slot;o(),p(c,today_date,b),q(d,e),r(c,today_date,data.pickup_disabled_current_time_slot,b),s(b),t(f),a('#coderockz_woo_delivery_pickup_time_field').select2({dropdownParent:a('#coderockz_woo_delivery_pickup_time_section'),allowClear:!0}),a('.coderockz-woo-delivery-loading-image').fadeOut();}});},onReady:function(h,i,d){a('.coderockz-woo-delivery-loading-image').fadeIn();var c=new Date(d.selectedDates[0]);var e='0'+(c.getMonth()+1);var g='0'+c.getDate();if(d.selectedDates.length>0)var b=c.getFullYear()+'-'+e.substr(-2)+'-'+g.substr(-2);else var b=today_date;a.ajax({url:coderockz_woo_delivery_ajax_obj.coderockz_woo_delivery_ajax_url,type:'POST',data:{_ajax_nonce:coderockz_woo_delivery_ajax_obj.nonce,action:'coderockz_woo_delivery_get_orders_pickup',date:b},success:function(g){data=JSON.parse(g.data);var c=data.current_time;var d=data.pickup_delivery_times;var e=data.pickup_max_order_per_slot;o(),p(b,today_date,c),q(d,e),r(b,today_date,data.pickup_disabled_current_time_slot,c),s(c),t(f),a('#coderockz_woo_delivery_pickup_time_field').select2({dropdownParent:a('#coderockz_woo_delivery_pickup_time_section'),allowClear:!0}),a('.coderockz-woo-delivery-loading-image').fadeOut();}});}}):a('#coderockz_woo_delivery_pickup_time_field').length?(a('.coderockz-woo-delivery-loading-image').fadeIn(),a.ajax({url:coderockz_woo_delivery_ajax_obj.coderockz_woo_delivery_ajax_url,type:'POST',data:{_ajax_nonce:coderockz_woo_delivery_ajax_obj.nonce,action:'coderockz_woo_delivery_get_orders_pickup',onlyPickupTime:!0,date:today_date},success:function(e){data=JSON.parse(e.data);var b=data.current_time;var c=data.pickup_delivery_times;var d=data.pickup_max_order_per_slot;o(),p(today_date,today_date,b),q(c,d),r(today_date,today_date,data.pickup_disabled_current_time_slot,b),s(b),t(f),a('#coderockz_woo_delivery_pickup_time_field').select2({dropdownParent:a('#coderockz_woo_delivery_pickup_time_section'),allowClear:!0}),a('.coderockz-woo-delivery-loading-image').fadeOut();}})):a('.coderockz-woo-delivery-loading-image').fadeOut();}a('#coderockz_woo_delivery_delivery_selection_box').wrap('<form autocomplete="off" class="coderockz_woo_delivery_chrome_off_autocomplete"></form>'),a('#coderockz_woo_delivery_delivery_selection_box').val(''),a('#coderockz_woo_delivery_date_datepicker').val(''),a('#coderockz_woo_delivery_time_field').val(''),a('#coderockz_woo_delivery_pickup_date_datepicker').val(''),a('#coderockz_woo_delivery_pickup_time_field').val('');var b='';if(b+='<div class="coderockz-woo-delivery-loading-image">',b+='<div class="coderockz-woo-delivery-loading-gif">',b+='<img src="'+a('#coderockz_woo_delivery_setting_wrapper').data('plugin-url')+'public/images/loading.gif" alt="" />',b+='</div>',b+='</div>',a('#coderockz_woo_delivery_setting_wrapper').append(b),today_date=a('#coderockz_woo_delivery_setting_wrapper').data('today_date'),a('#coderockz_woo_delivery_delivery_selection_box').select2({dropdownParent:a('#coderockz_woo_delivery_delivery_selection_box_field'),dropdownCssClass:'coderockz-delivery-selection-no-search'}),a('#coderockz_woo_delivery_time_field').select2({dropdownParent:a('#coderockz_woo_delivery_delivery_time_section'),allowClear:!0}),a('#coderockz_woo_delivery_pickup_time_field').select2({dropdownParent:a('#coderockz_woo_delivery_pickup_time_section'),allowClear:!0}),typeof a('#coderockz_woo_delivery_date_datepicker').data('disable_week_days')!==typeof undefined&&a('#coderockz_woo_delivery_date_datepicker').data('disable_week_days')!==!1)var c=a('#coderockz_woo_delivery_date_datepicker').data('disable_week_days');else var c=[];var y=a('#coderockz_woo_delivery_date_datepicker').data('date_format');var z=a('#coderockz_woo_delivery_date_datepicker').data('week_starts_from');var A=a('#coderockz_woo_delivery_date_datepicker').data('default_date');var B=a('#coderockz_woo_delivery_date_datepicker').data('disable_dates');if(typeof a('#coderockz_woo_delivery_pickup_date_datepicker').data('pickup_disable_week_days')!==typeof undefined&&a('#coderockz_woo_delivery_pickup_date_datepicker').data('pickup_disable_week_days')!==!1)var d=a('#coderockz_woo_delivery_pickup_date_datepicker').data('pickup_disable_week_days');else var d=[];var C=a('#coderockz_woo_delivery_pickup_date_datepicker').data('pickup_date_format');var D=a('#coderockz_woo_delivery_pickup_date_datepicker').data('pickup_week_starts_from');var E=a('#coderockz_woo_delivery_pickup_date_datepicker').data('pickup_default_date');var F=a('#coderockz_woo_delivery_pickup_date_datepicker').data('pickup_disable_dates');var e=a('#coderockz_woo_delivery_time_field').data('default_time');var f=a('#coderockz_woo_delivery_pickup_time_field').data('default_time');var w=a('#coderockz_woo_delivery_time_field').data('order_limit_notice');var x=a('#coderockz_woo_delivery_pickup_time_field').data('pickup_limit_notice');var g=[];var h=[];a('#coderockz_woo_delivery_delivery_selection_box').length?(a('#coderockz_woo_delivery_delivery_selection_field').css('display','block'),a(document).on('change','#coderockz_woo_delivery_delivery_selection_box',function(b){b.preventDefault(),a(this).parent().is('form')&&a(this).unwrap(),a('.coderockz-woo-delivery-loading-image').fadeIn(),deliveryOptionSelection=a(this).val(),deliveryOptionSelection=='delivery'?(a('#coderockz_woo_delivery_pickup_date_section').hide(),a('#coderockz_woo_delivery_pickup_time_section').hide(),a('#coderockz_woo_delivery_delivery_date_section').show(),a('#coderockz_woo_delivery_delivery_time_section').show()):deliveryOptionSelection=='pickup'&&(a('#coderockz_woo_delivery_delivery_date_section').hide(),a('#coderockz_woo_delivery_delivery_time_section').hide(),a('#coderockz_woo_delivery_pickup_date_section').show(),a('#coderockz_woo_delivery_pickup_time_section').show()),a.when(a.ajax({url:coderockz_woo_delivery_ajax_obj.coderockz_woo_delivery_ajax_url,type:'POST',data:{_ajax_nonce:coderockz_woo_delivery_ajax_obj.nonce,action:'coderockz_woo_delivery_option_delivery_time_pickup',deliveryOption:a(this).val()},success:function(b){a('body').trigger('update_checkout'),data=JSON.parse(b.data),g=data.disable_delivery_date_passed_time,h=data.disable_pickup_date_passed_time;}})).then(function(a){deliveryOptionSelection=='delivery'?u():deliveryOptionSelection=='pickup'&&v();});})):a.when(a.ajax({url:coderockz_woo_delivery_ajax_obj.coderockz_woo_delivery_ajax_url,type:'POST',data:{_ajax_nonce:coderockz_woo_delivery_ajax_obj.nonce,action:'coderockz_woo_delivery_disable_max_delivery_pickup_date'},success:function(a){data=JSON.parse(a.data),g=data.disable_delivery_date_passed_time,h=data.disable_pickup_date_passed_time;}})).then(function(b){a('body').trigger('update_checkout'),a('#coderockz_woo_delivery_delivery_date_section').css('display','block'),a('#coderockz_woo_delivery_delivery_time_section').css('display','block'),a('#coderockz_woo_delivery_pickup_date_section').css('display','block'),a('#coderockz_woo_delivery_pickup_time_section').css('display','block'),(a('#coderockz_woo_delivery_date_datepicker').length||a('#coderockz_woo_delivery_time_field').length)&&a('#coderockz_woo_delivery_pickup_date_datepicker').length==0&&a('#coderockz_woo_delivery_pickup_time_field').length==0?u():(a('#coderockz_woo_delivery_pickup_date_datepicker').length||a('#coderockz_woo_delivery_pickup_time_field').length)&&a('#coderockz_woo_delivery_date_datepicker').length==0&&a('#coderockz_woo_delivery_time_field').length==0?v():(u(),v());});});}(jQuery));