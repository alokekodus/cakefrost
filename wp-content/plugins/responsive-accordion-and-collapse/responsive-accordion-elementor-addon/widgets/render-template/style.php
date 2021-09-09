
#wpsm_accordion_<?php echo $post_id; ?> .wpsm_panel-heading{
	padding:0px !important;
}
#wpsm_accordion_<?php echo $post_id; ?> .wpsm_panel-title {
	margin:0px !important; 
	text-transform:none !important;
	line-height: 1 !important;
	
}
#wpsm_accordion_<?php echo $post_id; ?> .wpsm_panel-title a{
	text-decoration:none;
	overflow:hidden;
	display:block;
	padding:0px;	
	border-bottom:0px !important;
}

#wpsm_accordion_<?php echo $post_id; ?> .wpsm_panel-title a:focus {
	outline: 0px !important;
}

#wpsm_accordion_<?php echo $post_id; ?> .wpsm_panel-title a:hover, #wpsm_accordion_<?php echo $post_id; ?> .wpsm_panel-title a:focus {
	
}
#wpsm_accordion_<?php echo $post_id; ?> .acc-a{
	
	border-color: #ddd;
}
#wpsm_accordion_<?php echo $post_id; ?> .wpsm_panel-default > .wpsm_panel-heading{
	
	border-top-left-radius: 0px;
	border-top-right-radius: 0px;
}
#wpsm_accordion_<?php echo $post_id; ?> .wpsm_panel-default {
	<?php if($settings['accordion_margin_space'] === 'yes' ) { ?>
	border:1px solid transparent !important;
	<?php } else { ?>
	border:0px solid transparent !important;
	<?php } ?>
}
#wpsm_accordion_<?php echo $post_id; ?> {
	margin-bottom: 20px;
	overflow: hidden;
	float: none;
	width: 100%;
	display: block;
}
#wpsm_accordion_<?php echo $post_id; ?> .ac_title_class{
	display: block;
	padding-top: 12px;
	padding-bottom: 12px;
	padding-left: 15px;
	padding-right: 15px;
}
#wpsm_accordion_<?php echo $post_id; ?>  .wpsm_panel {
	overflow:hidden;
	-webkit-box-shadow: 0 0px 0px rgba(0, 0, 0, .05);
	box-shadow: 0 0px 0px rgba(0, 0, 0, .05);
	<?php if($settings['accordion_enable_radius'] === 'yes' ) { ?>
	border-radius: 4px;
	<?php }
	else {
	?>
		border-radius: 0px;
	<?php
	}
	?>
}
#wpsm_accordion_<?php echo $post_id; ?>  .wpsm_panel + .wpsm_panel {
	<?php if($settings['accordion_margin_space'] === 'yes' ) { ?>
	margin-top: 5px;
	<?php }
	else {
	?>
	margin-top: 0px;
	<?php
	}
	?>
}
#wpsm_accordion_<?php echo $post_id; ?>  .wpsm_panel-body{
	
	overflow: hidden;
	<?php if($settings['accordion_border_show']==="yes")
	{ ?>
	border-style: solid !important;
	border-width: 2px !important;
	
	<?php } 
	else {
	?>
	border: 2px solid transparent !important;
	<?php } ?>
}

#wpsm_accordion_<?php echo $post_id; ?> .ac_open_cl_icon{
	
	float:
	<?php 
	if($settings['accordion_open_close_icon']==='yes')
	{
		echo "left";
	}
	else
	{
		echo "right";
	}	 
	?> 
	!important;
	padding-top: 12px !important;
	padding-bottom: 12px !important;
	line-height: 1.0 !important;
	padding-left: 15px !important;
	padding-right: 15px !important;
	display: inline-block !important;
}

<?php 
	 switch($settings['accordion_style']){
			case "1":
			?>
			
			<?php
			break;
			case "2":
			 ?>
			 #wpsm_accordion_<?php echo $post_id; ?> .wpsm_panel-heading {
				background-image: url(<?php echo wpshopmart_accordion_directory_url.'img/style-soft.png'; ?>);
				background-position: 0 0;
				background-repeat: repeat-x;
			}
			#wpsm_accordion_<?php echo $post_id; ?> .ac_open_cl_icon{
				background-image: url(<?php echo wpshopmart_accordion_directory_url.'img/style-soft.png'; ?>);
				background-position: 0 0;
				background-repeat: repeat-x;
			}
			<?php
			break;
			case "3":
			?>
				#wpsm_accordion_<?php echo $post_id; ?> .wpsm_panel-heading {
				background-image: url(<?php echo wpshopmart_accordion_directory_url.'img/style-noise.png'; ?>);
				background-position: 0 0;
				background-repeat: repeat-x;
				}
				#wpsm_accordion_<?php echo $post_id; ?> .ac_open_cl_icon{
				background-image: url(<?php echo wpshopmart_accordion_directory_url.'img/style-noise.png'; ?>);
				background-position: 0 0;
				background-repeat: repeat-x;
				}
			<?php
			break;
		}
?>	
<?php echo $settings['accordion_custom_css']; ?>