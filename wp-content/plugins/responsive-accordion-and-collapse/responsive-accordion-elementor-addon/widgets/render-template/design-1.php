	<?php $post_id = get_the_ID(); ?>
	<style>
		<?php require('style.php'); ?>	
	</style>	
	
	<div class="wpsm_panel-group" id="wpsm_accordion_<?php echo $post_id; ?>" >	
			<?php 	
				$i=1;
				if ( $settings['accordion_list'] ) {
					 foreach (  $settings['accordion_list'] as $index => $item ) { 
					 $accordion_description_setting_key = $this->get_repeater_setting_key( 'accordion_description', 'accordion_list', $index );
					$this->add_inline_editing_attributes( $accordion_description_setting_key,'none' );
					 
					 $i;					
					 switch($settings['accordion_expand_collapse']){
					    case "1":
							$j=1;
						break;
						case "2":
							$j=$i;
						break;
						case "3":
							$j=0;
						break;
					 }					 
					?>	
			<!-- Inner panel Start -->
			<div class="wpsm_panel wpsm_panel-default">
				<div class="wpsm_panel-heading wpsm-heading-class" role="tab" >
				  <h4 class="wpsm_panel-title">
					<a  class="<?php if($i!=1){ echo "collapsed"; } ?>"  data-toggle="collapse" data-parent="<?php if($settings['accordion_toggle_collapse']!="yes") { ?>#wpsm_accordion_<?php echo $post_id; ?> <?php } ?>" href="#ac_<?php echo $post_id; ?>_collapse<?php echo $i; ?>"  >
						<?php if($settings['accordion_open_close_icon_show'] === 'yes' ) 
						{ ?>
							<span class="ac_open_cl_icon fa fa-<?php if($i==$j){ echo "minus"; } else { echo "plus"; } ?>"></span>
							
						<?php
						} ?> 
						<span class="ac_title_class">
							<?php if($settings['accordion_tile_font_icon_show'] === 'yes' )
							{ 
								 if($item['accordion_display_icon'] === "yes")
								{
							?>
									<span style="margin-right:6px;" class="<?php echo implode(" ",$item['accordion_icon']); ?>"></span>
							<?php
								}
							}
							if($item['accordion_title'] == '' ) { echo "no title";  } else { echo esc_attr($item['accordion_title']); } ?>
						</span>
					</a>
				  </h4>
				</div>
				<div id="ac_<?php echo $post_id; ?>_collapse<?php echo $i; ?>" class="wpsm_panel-collapse collapse <?php if($i==$j){ echo "in"; } ?>"  >
				  <div class="wpsm_panel-body">
					<span <?php echo $this->get_render_attribute_string( $accordion_description_setting_key ); ?>><?php  echo do_shortcode($item['accordion_description']); ?></span>
				  </div>
				</div>
			</div>
			<?php
				 $i++;
				}
			}
			?>		
			
	</div>
	