	<#
	var post_id='<?php echo get_the_ID(); ?>';
	var acc_title='<?php echo get_the_title(get_the_ID()); ?>';
	#>	
	
	<div class="wpsm_panel-group" id="wpsm_accordion_{{ post_id }}" >	
			<# 	
				var i=1;
				var j;
				if ( settings.accordion_list.length ) {
					<# _.each( settings.accordion_list, function( item,index ) {					  
					 accordion_description_Key = view.getRepeaterSettingKey( 'accordion_description', 'team_list', index );
					 view.addInlineEditingAttributes( accordion_description_Key, 'none' );				
					 switch(settings.accordion_expand_collapse){
					    case "1":
							j=1;
						break;
						case "2":
							j=i;
						break;
						case "3":
							j=0;
						break;
					 }					 
					#>	
			<!-- Inner panel Start -->
			<div class="wpsm_panel wpsm_panel-default">
				<div class="wpsm_panel-heading wpsm-heading-class" role="tab" >
				  <h4 class="wpsm_panel-title">
					<a  class="<# if(i!=1){  {{ collapsed }} } #>"  data-toggle="collapse" data-parent="<# if(settings.accordion_toggle_collapse != 'yes') { #> #wpsm_accordion_{{ post_id }} <# } #>" href="#ac_{{ post_id }}_collapse<# {{ i }} #>"  >
						<# if(settings.accordion_open_close_icon_show == 'yes' ) 
						{ #>
							<span class="ac_open_cl_icon fa fa-<# if(i==j){ {{ minus }} } else { {{ plus }} } #>"></span>
							
						<#
						} #> 
						<span class="ac_title_class">
							<# if(settings.accordion_tile_font_icon_show == 'yes' )
							{ 
								 if(item.accordion_display_icon == "yes")
								{
							#>
									<span style="margin-right:6px;" class="<# {{ JSON.stringify(item.accordion_icon) }} #>"></span>
							<#
								}
							}
							if(item.accordion_title == '' ) { {{ no title }}  } else { {{ item.accordion_title }} } #>
						</span>
					</a>
				  </h4>
				</div>
				<div id="ac_{{ post_id }}_collapse<# {{ i }} #>" class="wpsm_panel-collapse collapse <# if(i==j){ {{ in }} } #>"  >
				  <div class="wpsm_panel-body"><span {{{ view.getRenderAttributeString( accordion_description_Key ) }}}>
					<#  {{ item.accordion_description }} #></span>
				  </div>
				</div>
			</div>
			<#
				 i++;
				});
			}
			#>		
			
	</div>
	