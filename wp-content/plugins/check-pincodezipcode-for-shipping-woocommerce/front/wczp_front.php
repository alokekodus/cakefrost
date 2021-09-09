<?php

if (!defined('ABSPATH'))
    exit;

if (!class_exists('WCZP_front')) {

    class WCZP_front {

        protected static $instance;
        function WCZP_before_add_to_cart_btn() {

            $box_backgrond_clr = get_option('wczp_box_bg_clr', '#e0e0e0');
            $wczp_check_ibox_bg_clr = get_option( 'wczp_check_ibox_bg_clr', '#101f2b' );
            $changetext = get_option('wczp_chnge_btn_txt', 'Change');
            $backgrond_clr = get_option('wczp_bg_clr', '#205f7d');
            $text_clr = get_option('wczp_txt_clr', '#ffffff');
             $cash_on_delivary_text= get_option('wczp_cash_on_delivery_txt', 'Cash On Delivery');
            $text = get_option('wczp_btn_txt', 'Check');
            $not_serviceable_text = get_option('wczp_not_serviceable_txt', 'Oops! We are not currently servicing your area.');

            ?>
            <div class="wczpc_maindiv" style="background-color: <?php echo $box_backgrond_clr; ?>;">
                <h3>Check Availability At <span class="wczp_avaicode"><?php if(isset($_COOKIE['wczp_postcode']) && $_COOKIE['wczp_postcode'] != "no") { echo $_COOKIE['wczp_postcode']; } ?></span></h3>
                <div class="wczp_checkcode" style="display: <?php if(isset($_COOKIE['wczp_postcode']) && $_COOKIE['wczp_postcode'] != "no") { echo "block"; } else { echo "none"; } ?>;">
                    <div class="response_pin">
                        <?php 
                            if(isset($_COOKIE['wczp_postcode'])) {
                                global $wpdb;
                                $tablename=$wpdb->prefix.'wczp_postcode';
                                $cntSQL = "SELECT * FROM {$tablename} where wczp_pincode='".sanitize_text_field($_COOKIE['wczp_postcode'])."'";
                                $record = $wpdb->get_results($cntSQL, OBJECT);
                                $totalrec = count($record);
                                if ($totalrec == 1) {

                                    $deltxt = "";
                                    $date = $record[0]->wczp_ddate;
                                    $cod = $record[0]->wczp_cod;
                                    $string = "+".$date." days";

                                    $deliverydate = Date('jS M', strtotime($string));
                                    $dayofweek = date('D', strtotime($string));
                                    $deliverydate = $dayofweek.', '.$deliverydate;

                                    $showdate = get_option('wczp_del_shw', 'on');
                                     $wczp_cash_dilivery_shw = get_option('wczp_cash_dilivery_shw', 'on');
                                    $cash_on_delivary_text= get_option('wczp_cash_on_delivery_txt', 'Cash On Delivery');
                                    $del_avail_icon = WCZP_PLUGIN_DIR.'/includes/images/true.png';

                                    if($cod == 1) {
                                        $cod_avail = 'Available';
                                        $cod_avail_icon = WCZP_PLUGIN_DIR.'/includes/images/true.png';
                                    } else {
                                        $cod_avail = 'Not Available';
                                        $cod_avail_icon = WCZP_PLUGIN_DIR.'/includes/images/false.png';
                                    }

                                    $deltxt = "<div class='wczp_avacod'><p>".$cash_on_delivary_text."</p><span>".$cod_avail."</span></div>";
                                    
                                    if($showdate == "on") {

                                        echo '<div class="wczp_avaitxt"><span class="wczp_tficon"><img src="'.$del_avail_icon.'"></span><span class="wczp_delicons"><svg version="1.0" xmlns="http://www.w3.org/2000/svg"
                                             width="512.000000pt" height="512.000000pt" viewBox="0 0 512.000000 512.000000"
                                             preserveAspectRatio="xMidYMid meet">

                                            <g transform="translate(0.000000,512.000000) scale(0.100000,-0.100000)"
                                            fill="#000000" stroke="none">
                                            <path d="M558 4059 c-68 -35 -78 -71 -78 -279 l0 -180 -105 0 c-86 0 -113 -4
                                            -145 -20 -45 -23 -80 -80 -80 -130 0 -50 35 -107 80 -130 37 -19 58 -20 449
                                            -20 345 0 417 -2 449 -15 96 -39 101 -180 9 -249 -28 -21 -40 -21 -530 -26
                                            -456 -5 -505 -7 -534 -23 -38 -20 -73 -82 -73 -127 0 -50 35 -107 80 -130 38
                                            -19 58 -20 747 -20 l708 0 39 -23 c100 -57 100 -197 0 -254 l-39 -23 -708 0
                                            c-689 0 -709 -1 -747 -20 -45 -23 -80 -80 -80 -130 0 -50 35 -107 80 -130 35
                                            -18 59 -20 220 -20 l180 0 0 -240 c0 -352 0 -352 278 -360 l173 -5 22 -65 c30
                                            -90 69 -152 137 -220 224 -224 586 -224 810 0 68 68 107 130 137 220 l22 65
                                            761 0 761 0 22 -65 c30 -90 69 -152 137 -220 224 -224 586 -224 810 0 68 68
                                            107 130 137 220 l22 65 153 5 c176 6 205 16 238 80 19 38 20 58 20 538 0 542
                                            -2 562 -57 665 -36 64 -119 139 -197 176 -125 60 -133 61 -795 61 l-603 0 -40
                                            22 c-79 45 -78 36 -78 563 l0 465 -1377 0 c-1352 -1 -1379 -1 -1415 -21z
                                            m1052 -2184 c92 -46 160 -153 160 -250 0 -97 -68 -204 -159 -250 -121 -61
                                            -296 1 -364 129 -31 58 -31 184 0 242 30 56 106 121 162 139 57 18 155 14 201
                                            -10z m2650 0 c92 -46 160 -153 160 -250 0 -97 -68 -204 -159 -250 -121 -61
                                            -296 1 -364 129 -31 58 -31 184 0 242 30 56 106 121 162 139 57 18 155 14 201
                                            -10z"/>
                                            <path d="M3650 3690 l0 -360 410 0 c226 0 410 3 410 6 0 13 -125 210 -184 289
                                            -118 159 -233 260 -382 334 -63 31 -227 91 -250 91 -2 0 -4 -162 -4 -360z"/>
                                            </g>
                                            </svg></span>';
                                        echo '<div class="wczp_avaddate"><p>Delivery Date</p><span>'.$deliverydate.'</span></div></div>';
                                    }
                                    if($wczp_cash_dilivery_shw == "on") {   
                                        echo '<div class="wczp_dlvrytxt"><span class="wczp_tficon"><img src="'.$cod_avail_icon.'"></span><span class="wczp_delicons"><svg version="1.0" xmlns="http://www.w3.org/2000/svg"
                                         width="512.000000pt" height="512.000000pt" viewBox="0 0 512.000000 512.000000"
                                         preserveAspectRatio="xMidYMid meet">

                                        <g transform="translate(0.000000,512.000000) scale(0.100000,-0.100000)"
                                        fill="#000000" stroke="none">
                                        <path d="M969 5092 c-39 -20 -62 -41 -84 -77 l-30 -48 -3 -244 -4 -243 -136 0
                                        c-168 0 -249 -16 -361 -72 -166 -82 -299 -253 -336 -433 -13 -60 -15 -316 -15
                                        -1735 0 -1859 -4 -1738 72 -1890 73 -145 203 -257 370 -318 l73 -27 2012 -3
                                        c2250 -3 2088 -8 2242 70 149 75 272 221 324 383 l22 70 0 1715 0 1715 -22 70
                                        c-52 163 -175 309 -323 383 -111 55 -192 72 -362 72 l-136 0 -4 243 -3 244
                                        -30 48 c-22 36 -45 57 -84 77 -51 27 -59 28 -202 28 -163 0 -200 -9 -252 -58
                                        -64 -60 -67 -77 -67 -342 l0 -240 -1070 0 -1070 0 0 235 c0 256 -4 281 -58
                                        339 -52 56 -91 66 -258 66 -147 0 -153 -1 -205 -28z m3719 -3215 l-3 -1314
                                        -27 -39 c-15 -21 -44 -50 -65 -64 l-37 -25 -1996 0 -1996 0 -37 25 c-21 14
                                        -50 43 -65 64 l-27 39 -3 1314 -2 1313 2130 0 2130 0 -2 -1313z"/>
                                        <path d="M2332 2939 c-112 -56 -200 -105 -195 -109 4 -4 265 -139 578 -300
                                        l570 -292 230 114 229 115 -569 282 c-323 161 -585 284 -605 287 -28 3 -73
                                        -16 -238 -97z"/>
                                        <path d="M1643 2597 l-262 -131 590 -293 591 -292 250 125 251 126 -119 62
                                        c-65 34 -281 145 -479 245 -198 101 -405 207 -460 236 l-100 53 -262 -131z"/>
                                        <path d="M1280 1719 c0 -486 2 -585 14 -608 12 -23 123 -84 589 -327 l574
                                        -300 6 610 c4 336 3 613 -1 617 -4 4 -271 138 -594 297 l-588 290 0 -579z"/>
                                        <path d="M3600 2180 l-235 -118 -5 -232 -5 -232 -28 -24 c-40 -34 -90 -32
                                        -128 5 l-29 29 0 176 c0 97 -2 176 -5 176 -3 0 -120 -56 -259 -124 l-254 -123
                                        5 -609 c2 -335 5 -611 6 -615 2 -4 187 91 950 490 153 80 201 110 213 132 12
                                        24 14 115 12 607 l-3 580 -235 -118z"/>
                                        </g>
                                        </svg></span>'.$deltxt.'</div>';
                                    }
                                } else {
                                    echo $not_serviceable_text;
                                }
                            } 
                        ?>
                    </div>
                    <input type="button" name="wczpbtn" class="wczpcheckbtn" value="<?php echo $changetext; ?>" style="background-color: <?php echo $backgrond_clr; ?>;color: <?php echo $text_clr; ?>;">
                </div>
                <div class="wczp_cookie_check_div" style="display: <?php if(isset($_COOKIE['wczp_postcode'])  && $_COOKIE['wczp_postcode'] != "no") { echo "none"; } else { echo "flex"; } ?>;background-color: <?php echo $wczp_check_ibox_bg_clr; ?>;">

                    <svg version="1.0" xmlns="http://www.w3.org/2000/svg" width="512.000000pt" height="512.000000pt" viewBox="0 0 512.000000 512.000000" preserveAspectRatio="xMidYMid meet">
                        <g transform="translate(0.000000,512.000000) scale(0.100000,-0.100000)"
                        fill="<?php echo $backgrond_clr; ?>" stroke="none">
                            <path d="M2430 5114 c-229 -26 -360 -54 -508 -109 -392 -147 -720 -416 -943
                            -775 -210 -339 -307 -778 -259 -1170 78 -631 528 -1501 1274 -2465 176 -228
                            403 -501 455 -548 69 -62 153 -62 222 0 52 47 279 320 455 548 671 867 1102
                            1655 1238 2267 35 157 46 259 46 423 -1 480 -193 939 -540 1285 -286 287 -623
                            460 -1020 525 -94 15 -352 27 -420 19z m247 -925 c373 -50 682 -318 783 -679
                            27 -95 37 -286 21 -390 -47 -296 -241 -556 -516 -690 -162 -79 -328 -109 -497
                            -90 -132 15 -199 34 -313 90 -275 134 -469 394 -516 690 -16 104 -6 295 21
                            390 125 446 566 741 1017 679z"/>
                        </g>
                    </svg>

                    <input type="text" name="wczpcheck" class="wczpcheck" value="<?php if(isset($_COOKIE['wczp_postcode']) && sanitize_text_field($_COOKIE['wczp_postcode']) != "no") { echo sanitize_text_field($_COOKIE['wczp_postcode']); } ?>" style="background-color: <?php echo $wczp_check_ibox_bg_clr; ?>;">
                    <input type="button" name="wczpbtn" class="wczpbtn" value="<?php echo $text; ?>" style="background-color: <?php echo $backgrond_clr; ?>;color: <?php echo $text_clr; ?>;">
                </div>
                <span class="wczp_empty">Pincode field should not be empty!</span>
            </div>
            <?php    
        }


        function WCZP_check_location() {
            global $wpdb;
            $pincode = sanitize_text_field($_REQUEST['postcode']);
            $tablename=$wpdb->prefix.'wczp_postcode';
            $cntSQL = "SELECT * FROM {$tablename} where wczp_pincode='".$pincode."'";
            $record = $wpdb->get_results($cntSQL, OBJECT);
            $date = $record[0]->wczp_ddate;
            $cod = $record[0]->wczp_cod;
            $codtxt = "";
            $string = "+".$date." days";
            
            $deliverydate = Date('jS M', strtotime($string));
            $dayofweek = date('D', strtotime($string));
            $deliverydate = $dayofweek.', '.$deliverydate;

            $totalrec = count($record);
            $showdate = get_option('wczp_del_shw', 'on');

            $del_avail_icon = WCZP_PLUGIN_DIR.'/includes/images/true.png';
            $cash_on_delivary_text= get_option('wczp_cash_on_delivery_txt', 'Cash On Delivery');
            $wczp_cash_dilivery_shw = get_option('wczp_cash_dilivery_shw', 'on');
            if($cod == 1) {
                $cod_avail = 'Available';
                $cod_avail_icon = WCZP_PLUGIN_DIR.'/includes/images/true.png';
            } else {
                $cod_avail = 'Not Available';
                $cod_avail_icon = WCZP_PLUGIN_DIR.'/includes/images/false.png';
            }
            
          $codtxt = "<p>".$cash_on_delivary_text."</p><span>".$cod_avail."</span>";

            $data = array();
            $data = array(
                'pincode'      => $pincode,
                'deliverydate' => $deliverydate,
                'totalrec'     => $totalrec,
                'showdate'     => $showdate
            );

            $avai_msg = '';
                        
            $expiry = strtotime('+7 day');
            setcookie('wczp_postcode', $pincode, $expiry , COOKIEPATH, COOKIE_DOMAIN);
            if($totalrec == 1) {

                if($showdate == "on") {
                    $avai_msg .= '<div class="wczp_avaitxt"><span class="wczp_tficon"><img src="'.$del_avail_icon.'"></span><span class="wczp_delicons"><svg version="1.0" xmlns="http://www.w3.org/2000/svg"
                         width="512.000000pt" height="512.000000pt" viewBox="0 0 512.000000 512.000000"
                         preserveAspectRatio="xMidYMid meet">

                        <g transform="translate(0.000000,512.000000) scale(0.100000,-0.100000)"
                        fill="#000000" stroke="none">
                        <path d="M558 4059 c-68 -35 -78 -71 -78 -279 l0 -180 -105 0 c-86 0 -113 -4
                        -145 -20 -45 -23 -80 -80 -80 -130 0 -50 35 -107 80 -130 37 -19 58 -20 449
                        -20 345 0 417 -2 449 -15 96 -39 101 -180 9 -249 -28 -21 -40 -21 -530 -26
                        -456 -5 -505 -7 -534 -23 -38 -20 -73 -82 -73 -127 0 -50 35 -107 80 -130 38
                        -19 58 -20 747 -20 l708 0 39 -23 c100 -57 100 -197 0 -254 l-39 -23 -708 0
                        c-689 0 -709 -1 -747 -20 -45 -23 -80 -80 -80 -130 0 -50 35 -107 80 -130 35
                        -18 59 -20 220 -20 l180 0 0 -240 c0 -352 0 -352 278 -360 l173 -5 22 -65 c30
                        -90 69 -152 137 -220 224 -224 586 -224 810 0 68 68 107 130 137 220 l22 65
                        761 0 761 0 22 -65 c30 -90 69 -152 137 -220 224 -224 586 -224 810 0 68 68
                        107 130 137 220 l22 65 153 5 c176 6 205 16 238 80 19 38 20 58 20 538 0 542
                        -2 562 -57 665 -36 64 -119 139 -197 176 -125 60 -133 61 -795 61 l-603 0 -40
                        22 c-79 45 -78 36 -78 563 l0 465 -1377 0 c-1352 -1 -1379 -1 -1415 -21z
                        m1052 -2184 c92 -46 160 -153 160 -250 0 -97 -68 -204 -159 -250 -121 -61
                        -296 1 -364 129 -31 58 -31 184 0 242 30 56 106 121 162 139 57 18 155 14 201
                        -10z m2650 0 c92 -46 160 -153 160 -250 0 -97 -68 -204 -159 -250 -121 -61
                        -296 1 -364 129 -31 58 -31 184 0 242 30 56 106 121 162 139 57 18 155 14 201
                        -10z"/>
                        <path d="M3650 3690 l0 -360 410 0 c226 0 410 3 410 6 0 13 -125 210 -184 289
                        -118 159 -233 260 -382 334 -63 31 -227 91 -250 91 -2 0 -4 -162 -4 -360z"/>
                        </g>
                        </svg></span>';
                    $avai_msg .= '<div class="wczp_avaddate"><p>Delivery Date</p><span>'.$deliverydate.'</span></div></div>';
                }

                if($wczp_cash_dilivery_shw == "on") {

                    $avai_msg .= '<div class="wczp_dlvrytxt"><span class="wczp_tficon"><img src="'.$cod_avail_icon.'"></span><span class="wczp_delicons"><svg version="1.0" xmlns="http://www.w3.org/2000/svg"
                     width="512.000000pt" height="512.000000pt" viewBox="0 0 512.000000 512.000000"
                     preserveAspectRatio="xMidYMid meet">

                    <g transform="translate(0.000000,512.000000) scale(0.100000,-0.100000)"
                    fill="#000000" stroke="none">
                    <path d="M969 5092 c-39 -20 -62 -41 -84 -77 l-30 -48 -3 -244 -4 -243 -136 0
                    c-168 0 -249 -16 -361 -72 -166 -82 -299 -253 -336 -433 -13 -60 -15 -316 -15
                    -1735 0 -1859 -4 -1738 72 -1890 73 -145 203 -257 370 -318 l73 -27 2012 -3
                    c2250 -3 2088 -8 2242 70 149 75 272 221 324 383 l22 70 0 1715 0 1715 -22 70
                    c-52 163 -175 309 -323 383 -111 55 -192 72 -362 72 l-136 0 -4 243 -3 244
                    -30 48 c-22 36 -45 57 -84 77 -51 27 -59 28 -202 28 -163 0 -200 -9 -252 -58
                    -64 -60 -67 -77 -67 -342 l0 -240 -1070 0 -1070 0 0 235 c0 256 -4 281 -58
                    339 -52 56 -91 66 -258 66 -147 0 -153 -1 -205 -28z m3719 -3215 l-3 -1314
                    -27 -39 c-15 -21 -44 -50 -65 -64 l-37 -25 -1996 0 -1996 0 -37 25 c-21 14
                    -50 43 -65 64 l-27 39 -3 1314 -2 1313 2130 0 2130 0 -2 -1313z"/>
                    <path d="M2332 2939 c-112 -56 -200 -105 -195 -109 4 -4 265 -139 578 -300
                    l570 -292 230 114 229 115 -569 282 c-323 161 -585 284 -605 287 -28 3 -73
                    -16 -238 -97z"/>
                    <path d="M1643 2597 l-262 -131 590 -293 591 -292 250 125 251 126 -119 62
                    c-65 34 -281 145 -479 245 -198 101 -405 207 -460 236 l-100 53 -262 -131z"/>
                    <path d="M1280 1719 c0 -486 2 -585 14 -608 12 -23 123 -84 589 -327 l574
                    -300 6 610 c4 336 3 613 -1 617 -4 4 -271 138 -594 297 l-588 290 0 -579z"/>
                    <path d="M3600 2180 l-235 -118 -5 -232 -5 -232 -28 -24 c-40 -34 -90 -32
                    -128 5 l-29 29 0 176 c0 97 -2 176 -5 176 -3 0 -120 -56 -259 -124 l-254 -123
                    5 -609 c2 -335 5 -611 6 -615 2 -4 187 91 950 490 153 80 201 110 213 132 12
                    24 14 115 12 607 l-3 580 -235 -118z"/>
                    </g>
                    </svg></span><div class="wczp_avacod">'.$codtxt.'</div>';
                }

            }

            $data['avai_msg'] = $avai_msg;

            echo json_encode( $data );
            exit();
        }


        function WCZP_popup_div_footer() {
            
            $wczp_excld_pages_pop_shw = get_option( 'wczp_excld_pages_pop_shw' );
            $wczp_excld_pages_array = array();

            if( $wczp_excld_pages_pop_shw != '' ) {
                $wczp_excld_pages_pop_shw = str_replace( ' ', '', $wczp_excld_pages_pop_shw );
                $wczp_excld_pages_array = explode( ",", $wczp_excld_pages_pop_shw );
            }

            $current_page_id = get_the_ID();

            $wczp_force_pp_for_pcode = get_option( 'wczp_force_pp_for_pcode', 'off' );

            $wczp_cpin_pp_bg_clr = get_option( 'wczp_cpin_pp_bg_clr', '#8BC34A' );
            $wczp_cpin_pp_txt_clr = get_option( 'wczp_cpin_pp_txt_clr', '#ffffff' );
            $wczp_cpin_pp_sbtn_bg_clr = get_option( 'wczp_cpin_pp_sbtn_bg_clr', '#3f51b5' );
            $wczp_cpin_pp_sbtn_txt_clr = get_option( 'wczp_cpin_pp_sbtn_txt_clr', '#ffffff' );


            $location_icon = '<svg version="1.0" xmlns="http://www.w3.org/2000/svg"
                                 width="512.000000pt" height="512.000000pt" viewBox="0 0 512.000000 512.000000"
                                 preserveAspectRatio="xMidYMid meet">

                                <g transform="translate(0.000000,512.000000) scale(0.100000,-0.100000)"
                                fill="'.$wczp_cpin_pp_txt_clr.'" stroke="none">
                                <path d="M2385 5109 c-559 -58 -1049 -402 -1308 -915 -43 -86 -111 -283 -131
                                -384 -74 -364 -26 -736 135 -1064 24 -49 341 -553 704 -1120 490 -765 669
                                -1037 694 -1053 46 -31 116 -31 162 0 24 16 206 291 691 1047 362 564 680
                                1070 707 1125 188 383 221 827 90 1228 -84 257 -215 471 -405 661 -355 355
                                -840 527 -1339 475z m360 -912 c269 -75 478 -288 546 -557 8 -31 14 -105 14
                                -170 0 -123 -12 -188 -53 -285 -93 -219 -290 -387 -522 -446 -71 -18 -269 -18
                                -340 0 -232 59 -429 227 -522 446 -41 97 -53 162 -53 285 0 123 12 188 53 285
                                93 220 305 400 522 444 30 6 64 13 75 15 37 9 229 -3 280 -17z"/>
                                <path d="M1244 1635 c-728 -203 -1062 -553 -886 -928 32 -70 124 -174 212
                                -242 291 -223 840 -384 1525 -447 183 -17 747 -17 930 0 685 63 1234 224 1525
                                447 88 68 180 172 212 242 177 378 -171 736 -906 933 -98 26 -121 29 -131 18
                                -7 -7 -175 -269 -375 -583 -200 -314 -381 -592 -402 -620 -194 -251 -592 -250
                                -779 2 -20 26 -204 310 -409 631 -204 320 -377 582 -384 581 -6 0 -66 -16
                                -132 -34z"/>
                                </g>
                                </svg>';


            if( get_option('wczp_all_pages_pop_shw', 'on' ) == "on") {
                if ( !in_array( $current_page_id, $wczp_excld_pages_array ) ) {
                ?>
                    <div id="wczpModal" class="wczp-modal">
                        <?php
                        if($wczp_force_pp_for_pcode == 'off') {
                        ?>
                        <div id="wczp_pincode_popup" class="wczp_pincode_popup_class"></div>
                        <?php
                        } else {
                        ?>
                        <div id="wczp_pincode_popup_no_close" class="wczp_pincode_popup_class"></div>
                        <?php
                        }
                        ?>
                        <div class="modal-content" style="background-color:<?php echo $wczp_cpin_pp_bg_clr; ?>">
                            <?php
                            if($wczp_force_pp_for_pcode == 'off') {
                            ?>
                            <span class="close" style="color: <?php echo $wczp_cpin_pp_txt_clr; ?>">&times;</span>
                            <?php
                            }
                            ?>
                            <div class="modalinner">
                                <div class="popup_oc_main" style="color:<?php echo $wczp_cpin_pp_txt_clr; ?>; background-color:<?php echo $wczp_cpin_pp_bg_clr; ?>">
                                    <?php echo $location_icon; ?>
                                    <h4 class="wczp_popup_header">Check your location availability info</h4>
                                    <div class="modal-body">
                                    <form action="" method="post">
                                        <div class="wczp_popup_check_div">
                                            <input type="text" name="wczpopuppinzip" class="wczpopuppinzip" placeholder="Enter Pincode" value="">
                                            <input type="button" name="wczpinzipsubmit" class="wczpinzipsubmit" value="Submit" style="background-color:<?php echo $wczp_cpin_pp_sbtn_bg_clr; ?>; color: <?php echo $wczp_cpin_pp_sbtn_txt_clr; ?>">
                                        </div>
                                    </form>
                                    </div>
                                </div>
                                 <div class="wczpc_maindiv_popup" style="background-color:#ddd;">
                               <!--  <h3>Check Availability At <span class="wczp_avaicode"><?php if(isset($_COOKIE['wczp_postcodee']) && $_COOKIE['wczp_postcodee'] != "no") { echo $_COOKIE['wczp_postcodee']; } ?></span></h3> -->
                                <div class="response_pin_popup">
                                    <?php    
                                        if(isset($_COOKIE['wczp_postcodee'])) {
                                            global $wpdb;
                                            $tablename=$wpdb->prefix.'wczp_postcode';
                                            $cntSQL = "SELECT * FROM {$tablename} where wczp_pincode='".sanitize_text_field($_COOKIE['wczp_postcodee'])."'";
                                            $record = $wpdb->get_results($cntSQL, OBJECT);
                                            $totalrec = count($record);
                                           
                                            if ($totalrec == 1) {

                                                $deltxt = "";
                                                $date = $record[0]->wczp_ddate;
                                                $cod = $record[0]->wczp_cod;
                                                $string = "+".$date." days";

                                                $deliverydate = Date('jS M', strtotime($string));
                                                $dayofweek = date('D', strtotime($string));
                                                $deliverydate = $dayofweek.', '.$deliverydate;

                                                $showdate = get_option('wczp_del_shw', 'on');
                                                $wczp_cash_dilivery_shw = get_option('wczp_cash_dilivery_shw', 'on');
                                                
                                                $del_avail_icon = WCZP_PLUGIN_DIR.'/includes/images/true.png';

                                                if($cod == 1) {
                                                    $cod_avail = 'Available';
                                                    $cod_avail_icon = WCZP_PLUGIN_DIR.'/includes/images/true.png';

                                                } else {
                                                    $cod_avail = 'Not Available';
                                                    $cod_avail_icon = WCZP_PLUGIN_DIR.'/includes/images/false.png';
                                                }

                                                $deltxt = "<div class='wczp_avacod'><p>".$cash_on_delivary_text."</p><span>".$cod_avail."</span></div>";
                                                
                                                echo 'We are available currently servicing your area.';
                                            } else {
                                                echo $not_serviceable_text;
                                            }
                                        } 
                                    ?>
                                </div>
                                <span class="wczp_empty">Pincode field should not be empty!</span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php 
                }
            }

        }
        

        function WCZP_popup_check_zip_code() {

            global $wpdb;
            $popup_postcode = sanitize_text_field($_REQUEST['popup_postcode']);
            $tablename=$wpdb->prefix.'wczp_postcode';
            $cntSQL = "SELECT * FROM {$tablename} where wczp_pincode='".$popup_postcode."'";
            $record = $wpdb->get_results($cntSQL, OBJECT);
            $totalrec = count($record);
            $data = array();
            $data = array(
                'popup_pincode' => $popup_postcode,
                'totalrec'     => $totalrec   
            );

            $avai_msg = '';
                        
            $expiry = strtotime('+7 day');
            setcookie('wczp_postcodee', $popup_postcode, $expiry , COOKIEPATH, COOKIE_DOMAIN);
            setcookie('wczp_postcode', $popup_postcode, $expiry , COOKIEPATH, COOKIE_DOMAIN);
            setcookie('usernamea', 'popusetp', $expiry , COOKIEPATH, COOKIE_DOMAIN);
            if($totalrec == 1) {
                $avai_msg = 'We are available currently servicing your area.';
            }

            $data['avai_msg'] = $avai_msg;

            echo json_encode( $data );
            exit();
               
        }


        function WCZP_inactive_order_button_html( $button ) {

            if(get_option('wczp_checkpincode') == "on") {

                if(isset($_COOKIE['wczp_postcode'])) {

                    global $wpdb;
                    $tablename=$wpdb->prefix.'wczp_postcode';
                    $cntSQL = "SELECT * FROM {$tablename} where wczp_pincode='".sanitize_text_field($_COOKIE['wczp_postcode'])."'";
                    $record = $wpdb->get_results($cntSQL, OBJECT);
                    $totalrec = count($record);

                    if ($totalrec == 1) {

                         return $button;

                    } else {

                        $button_text = apply_filters( 'woocommerce_order_button_text', __( 'Choose valid zipcode in product page then place order', 'woocommerce' ) );
                        $button = '<a class="button" id="wczp_porepl">' . $button_text . '</a>';
                         return $button;
                    }

                }
            
            } else {
                return $button;
            }
           
        }


        function WCZP_pincode_change_checkout() {
            if(isset($_REQUEST['pincode']) && $_REQUEST['pincode'] != '') {
                $pincode = sanitize_text_field($_REQUEST['pincode']);
                $expiry = strtotime('+7 day');
                setcookie('wczp_postcode', $pincode, $expiry , COOKIEPATH, COOKIE_DOMAIN);
                
                global $wpdb;
                $tablename=$wpdb->prefix.'wczp_postcode';
                $cntSQL = "SELECT * FROM {$tablename} where wczp_pincode='".$pincode."'";
                $record = $wpdb->get_results($cntSQL, OBJECT);
                $totalrec = count($record);
                
                if ($totalrec == 1) {
                    echo 'true';
                } else {
                    echo 'false';
                }

                exit;
            }
        }


        function WCZP_check_pincode_shortcode($atts, $content = null) {

            ob_start();

            $this->WCZP_before_add_to_cart_btn();

            $content = ob_get_clean();

            return $content;
        }

        function WCZP_woo_add_cart_fee() {
         
            global $woocommerce;
            if( get_option('wczp_enable_shipping_cost', 'on') == 'on' ){
                if(isset($_COOKIE['wczp_postcode'])) {
                    global $wpdb;
                    $tablename=$wpdb->prefix.'wczp_postcode';
                    $cntSQL = "SELECT * FROM {$tablename} where wczp_pincode='".sanitize_text_field($_COOKIE['wczp_postcode'])."'";
                    $record = $wpdb->get_results($cntSQL, OBJECT);
                    //print_r( $record[0]);
                    if($record[0]->wczp_ship_amount != 0 && !empty($record[0]->wczp_ship_amount)){
                        $woocommerce->cart->add_fee( __('Shipping Amount', 'woocommerce'), $record[0]->wczp_ship_amount);
                    }
                    
                }
            }
        }
        

        function init() {

            if(get_option('wczp_enable_checkpcode', 'on') == 'on') {
                add_filter('woocommerce_order_button_html', array( $this, 'WCZP_inactive_order_button_html' ));
                add_action( 'wp_footer', array( $this, 'WCZP_popup_div_footer' ));

                if(get_option('wczp_checkpcode_pos', 'after_atc') == 'after_atc') {
                    add_action( 'woocommerce_after_add_to_cart_button', array($this,'WCZP_before_add_to_cart_btn'));
                } elseif (get_option('wczp_checkpcode_pos', 'after_atc') == 'before_atc') {
                    add_action( 'woocommerce_before_add_to_cart_button', array($this,'WCZP_before_add_to_cart_btn'));
                }

                add_shortcode( 'wczp_check_pincode', array($this, 'WCZP_check_pincode_shortcode' ));
                add_action( 'woocommerce_cart_calculate_fees', array($this,'WCZP_woo_add_cart_fee'));
                add_action( 'WCZP_popup_form_zipcode', array($this,'WCZP_popup_form_zipcode'));
                add_action( 'wp_ajax_WCZP_check_location', array($this,'WCZP_check_location' ));
                add_action( 'wp_ajax_nopriv_WCZP_check_location', array($this,'WCZP_check_location' ));
                add_action( 'wp_ajax_WCZP_popup_check_zip_code', array($this,'WCZP_popup_check_zip_code' ));
                add_action( 'wp_ajax_nopriv_WCZP_popup_check_zip_code', array($this,'WCZP_popup_check_zip_code' ));
                add_action( 'wp_ajax_WCZP_pincode_change_checkout', array($this,'WCZP_pincode_change_checkout' ));
                add_action( 'wp_ajax_nopriv_WCZP_pincode_change_checkout', array($this,'WCZP_pincode_change_checkout' ));
            }

        }
       
        public static function instance() {

            if (!isset(self::$instance)) {
                self::$instance = new self();
                self::$instance->init();
            }
            return self::$instance;
        }
    }
    WCZP_front::instance();
}