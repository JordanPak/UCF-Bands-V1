<?php
/**
 * Represents the view for the metabox in post / pages
 *
 * @package   Essential_Grid
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/essential/
 * @copyright 2014 ThemePunch
 */
	
	if(!isset($post)) return false; //not called as it should be
	
	$base = new Essential_Grid_Base();
	$item_skin = new Essential_Grid_Item_Skin();
	$item_elements = new Essential_Grid_Item_Element();
	$meta = new Essential_Grid_Meta();
	
	$values = get_post_custom($post->ID);
	
	$eg_sources_html5_mp4 = isset($values['eg_sources_html5_mp4']) ? esc_attr($values['eg_sources_html5_mp4'][0]) : "";
	$eg_sources_html5_ogv = isset($values['eg_sources_html5_ogv']) ? esc_attr($values['eg_sources_html5_ogv'][0]) : "";
	$eg_sources_html5_webm = isset($values['eg_sources_html5_webm']) ? esc_attr($values['eg_sources_html5_webm'][0]) : "";
	$eg_vimeo_ratio = isset($values['eg_vimeo_ratio']) ? esc_attr($values['eg_vimeo_ratio'][0]) : "0";
	$eg_youtube_ratio = isset($values['eg_youtube_ratio']) ? esc_attr($values['eg_youtube_ratio'][0]) : "0";
	$eg_html5_ratio = isset($values['eg_html5_ratio']) ? esc_attr($values['eg_html5_ratio'][0]) : "0";
	$eg_soundcloud_ratio = isset($values['eg_soundcloud_ratio']) ? esc_attr($values['eg_soundcloud_ratio'][0]) : "0";
	$eg_sources_youtube = isset($values['eg_sources_youtube']) ? esc_attr($values['eg_sources_youtube'][0]) : "";
	$eg_sources_vimeo = isset($values['eg_sources_vimeo']) ? esc_attr($values['eg_sources_vimeo'][0]) : "";
	$eg_sources_image = isset($values['eg_sources_image']) ? esc_attr($values['eg_sources_image'][0]) : "";
	$eg_sources_iframe = isset($values['eg_sources_iframe']) ? esc_attr($values['eg_sources_iframe'][0]) : "";
	$eg_sources_soundcloud = isset($values['eg_sources_soundcloud']) ? esc_attr($values['eg_sources_soundcloud'][0]) : "";
	
	$eg_sources_image_url = '';
	if(intval($eg_sources_image) > 0){
		//get URL to Image
		$img = wp_get_attachment_image_src($eg_sources_image, 'full');
		if($img !== false){
			$eg_sources_image_url = $img[0];
		}else{
			$eg_sources_image = '';
		}
	}
	
	$eg_settings_custom_meta_skin = isset($values['eg_settings_custom_meta_skin']) ? unserialize($values['eg_settings_custom_meta_skin'][0]) : "";
	$eg_settings_custom_meta_element = isset($values['eg_settings_custom_meta_element']) ? unserialize($values['eg_settings_custom_meta_element'][0]) : "";
	$eg_settings_custom_meta_setting = isset($values['eg_settings_custom_meta_setting']) ? unserialize($values['eg_settings_custom_meta_setting'][0]) : "";
	$eg_settings_custom_meta_style = isset($values['eg_settings_custom_meta_style']) ? unserialize($values['eg_settings_custom_meta_style'][0]) : "";
	
	if(!isset($disable_advanced) || $disable_advanced == false){
		$eg_meta = array();
		
		if(!empty($eg_settings_custom_meta_skin)){
			foreach($eg_settings_custom_meta_skin as $key => $val){
				$eg_meta[$key]['skin'] = @$val;
				$eg_meta[$key]['element'] = @$eg_settings_custom_meta_element[$key];
				$eg_meta[$key]['setting'] = @$eg_settings_custom_meta_setting[$key];
				$eg_meta[$key]['style'] = @$eg_settings_custom_meta_style[$key];
			}
		}
		
		$advanced = array();
		
		$eg_skins = $item_skin->get_essential_item_skins();
		
		foreach($eg_skins as $skin){
			if(!empty($skin['layers'])){
				$advanced[$skin['id']]['name'] = $skin['name'];
				$advanced[$skin['id']]['handle'] = $skin['handle'];
				foreach($skin['layers'] as $layer){
					if(empty($layer)) continue; //some layers may be NULL...
					
					//check if special, ignore special elements
					$settings = $layer['settings'];
					if(!empty($settings) && isset($settings['special']) && $settings['special'] == 'true') continue;
					
					$advanced[$skin['id']]['layers'][] = $layer['id'];
				}
			}
		}
	
		$eg_elements = $item_elements->get_allowed_meta();
		
	}
	
	$custom_meta = $meta->get_all_meta();
	
	if(isset($disable_advanced) && $disable_advanced == true){ //only show if we are in preview mode
		?>
		<form id="eg-form-post-meta-settings">
			<input type="hidden" name="post_id" value="<?php echo $post->ID; ?>" />
		<?php
	}
	
	wp_nonce_field('eg_meta_box_nonce', 'essential_grid_meta_box_nonce');
	
	?>
	
	<style type="text/css">	
		/******************************
			-	META BOX STYLING	-
		********************************/

		#eg-meta-box .eg-mb-label 	{	min-width:80px; margin-right:20px; display:inline-block;}
		#eg-meta-box h2				{	font-size:18px;background:#f1f1f1; margin-left:-12px;margin-right:-12px; padding:5px 10px; margin-bottom:30px; line-height:29px;}
		
		#eg-meta-box .eg-remove-custom-meta-field	{	padding: 0px 12px; }
		
		#eg-meta-box .eg-custom-meta-style,
		#eg-meta-box .wp-picker-container		 	  {	line-height: 20px;vertical-align: middle; }
		
		#eg-meta-box .wp-picker-container .wp-color-result	{	margin:0px;}
		#eg-meta-box .eg-custom-meta-setting-wrap {	line-height: 45px; border-bottom:1px solid #f1f1f1; padding:10px 0px; }	
		
		#eg-meta-box .eg-cs-row			{	height:45px;}
		#eg-meta-box .eg-cs-row-min		{	min-height:45px;}		
		
		#eg-meta-box hr	{	border-top: 1px solid #f1f1f1;}	
		
		#eg-meta-box .eg-notifcation	{	background:#f1f1f1; padding:10px 15px;   font-style: italic; box-sizing:border-box;
											-moz-box-sizing:border-box; line-height:20px; margin-top:10px;
											-webkit-box-sizing:border-box; 
										}
						
		/****************************
		* Custom Button Styles
		****************************/
		
		#eg-meta-box .button-primary,
		#button_upload_plugin		{	border:none !important; text-shadow: none !important; border: none !important; outline: none !important;box-shadow: none !important;
												line-height: 26px !important; height: 27px !important; margin:2px 3px 2px 0px!important;color:#fff !important;
												background:transparent !important;
											}
		
		#eg-meta-box .button-primary.button-fixed
									{	height: auto !important;}
		
		.multiple_text_add			{	text-decoration: none !important}
		.egwithhover,
		.egwithhover:link,
		.egwithhover:visited		{	color:#27ae60; font-size:13px; text-decoration: none !important;}
		.egwithhover:hover			{	color:#2ecc71; }
		
		
		#remove_multiple_text,
		.redicon.withhover			{	color:#e74c3c !important;width: 20px;height: 10px;position: absolute;right: -15px;top: 5px;font-size: 12px;}
		#remove_multiple_text:hover,
		.redicon.withhover:hover	{	color:#c0392b !important}
		
		#eg-meta-box .revblue,
		#eg-meta-box .revblue.button-disabled,
		.revblue,
		.revblue.button-disabled				{	background:#3498db !important}
		#eg-meta-box .revblue:hover,
		.revblue:hover							{	background:#2c3e50 !important}
		
		.revbluedark,
		.revbluedark.button-disabled,
		#eg-meta-box .revbluedark,
		#eg-meta-box .revbluedark.button-disabled	{	background:#34495e !important}
		#eg-meta-box .revbluedark:hover,
		.revbluedark:hover							{	background:#2c3e50 !important}
		
		#button_upload_plugin.revgreen,
		#eg-meta-box .revgreen,
		.revgreen								{	background:#27ae60 !important}
		
		#button_upload_plugin.revgreen:hover,
		.revgreen:hover,
		.revgreen.ui-state-active,
		#eg-meta-box .revgreen:hover,
		#eg-meta-box .revgreen.ui-state-active 	{	background:#2ecc71 !important}
		
		#eg-meta-box .revred,
		#eg-meta-box .eg-remove-custom-meta-field,
		#eg-meta-box .revred.button-disabled	{	background: #e74c3c !important}
		#eg-meta-box .eg-remove-custom-meta-field:hover,		
		#eg-meta-box .revred:hover				{	background: #c0392b !important}
		
		#eg-meta-box .revyellow,
		#eg-meta-box .revyellow.button-disabled	{	background: #f1c40f !important}
		#eg-meta-box .revyellow:hover			{	background: #f39c12 !important}
		
		.revgray,
		#eg-meta-box .revgray					{	background: #95a5a6 !important}
		.revgray:hover,
		#eg-meta-box .revgray:hover					{	background: #7f8c8d !important}
		
		
		.revcarrot,
		.revcarrot.button-disabled,
		#eg-meta-box .revcarrot,
		#eg-meta-box .revcarrot.button-disabled	{	background: #e67e22 !important}
		.revcarrot:hover,
		#eg-meta-box .revcarrot:hover				{	background: #d35400 !important}
		
		
		
		#button_upload_plugin.revpurple,
		#eg-meta-box .revpurple,
		.revpurple								{	background:#9b59b6 !important}
		
		#button_upload_plugin.revpurple:hover,
		.revpurple:hover,
		.revpurple.ui-state-active,
		#eg-meta-box .revpurple:hover,
		#eg-meta-box .revpurple.ui-state-active 	{	background:#8e44ad !important}
		
		
		#eg-meta-box .iris-picker	{ 
			position: absolute;
			vertical-align: bottom;
			z-index: 100;
		}
		
		#eg_sources_image-wrapper img{
			max-width: 400px; width:auto;
			max-height: 400px;height:auto;
			
		}
		
		#eg-meta-box  .eg-custom-meta-setting-wrap:first-child	{	margin-top:0px !important; padding-top:0px !important;}
		#eg-meta-box  .eg-custom-meta-setting-wrap:last-child		{	border-bottom:none !important;}

	</style>
	<?php
	if(isset($disable_advanced) && $disable_advanced == true){ //only show if we are in preview mode
		$cobbles = '1:1';
		$raw_cobbles = isset($values['eg_cobbles']) ? json_decode($values['eg_cobbles'][0], true) : '';
		if(isset($grid_id) && isset($raw_cobbles[$grid_id]) && isset($raw_cobbles[$grid_id]['cobbles']))
			$cobbles = $raw_cobbles[$grid_id]['cobbles'];
			
		?>
		<h2 style="margin-top:-6px"><span style="margin:5px 10px 0px 10px"class="dashicons dashicons-list-view"></span><?php _e('Grid specific settings', EG_TEXTDOMAIN); ?></h2>
		<div>
			<div class="eg-cs-row" style="float:left">
				<label class="eg-mb-label"><?php _e('Cobbles Element Size', EG_TEXTDOMAIN); ?></label>
			</div>
			<select name="eg_cobbles_size" id="eg_cobbles_size">
				<option value="1:1"<?php selected($cobbles, '1:1'); ?>><?php _e('width 1, height 1', EG_TEXTDOMAIN); ?></option>
				<option value="1:2"<?php selected($cobbles, '1:2'); ?>><?php _e('width 1, height 2', EG_TEXTDOMAIN); ?></option>
				<option value="1:3"<?php selected($cobbles, '1:3'); ?>><?php _e('width 1, height 3', EG_TEXTDOMAIN); ?></option>
				<option value="2:1"<?php selected($cobbles, '2:1'); ?>><?php _e('width 2, height 1', EG_TEXTDOMAIN); ?></option>
				<option value="2:2"<?php selected($cobbles, '2:2'); ?>><?php _e('width 2, height 2', EG_TEXTDOMAIN); ?></option>
				<option value="2:3"<?php selected($cobbles, '2:3'); ?>><?php _e('width 2, height 3', EG_TEXTDOMAIN); ?></option>
				<option value="3:1"<?php selected($cobbles, '3:1'); ?>><?php _e('width 3, height 1', EG_TEXTDOMAIN); ?></option>
				<option value="3:2"<?php selected($cobbles, '3:2'); ?>><?php _e('width 3, height 2', EG_TEXTDOMAIN); ?></option>
				<option value="3:3"<?php selected($cobbles, '3:3'); ?>><?php _e('width 3, height 3', EG_TEXTDOMAIN); ?></option>
			</select>
			<div style="clear:both; height: 20px;"></div>
		</div>
		<?php
	}
	?>
	<h2 style="margin-top:-6px"><span style="margin:5px 10px 0px 10px"class="dashicons dashicons-list-view"></span><?php _e('Custom Meta', EG_TEXTDOMAIN); ?></h2>
	
	<div>
		<?php
		if(!empty($custom_meta)){
			foreach($custom_meta as $cmeta){
				//check if post already has a value set
				$val = isset($values['eg-'.$cmeta['handle']]) ? esc_attr($values['eg-'.$cmeta['handle']][0]) : @$cmeta['default'];
				?>
					<div class="eg-cs-row-min"><label class="eg-mb-label"><?php echo $cmeta['name']; ?>:</label>
					<?php
					switch($cmeta['type']){
						case 'text':
							echo '<input type="text" name="eg-'.$cmeta['handle'].'" value="'.$val.'" />';
							break;
						case 'select':
							$el = $meta->prepare_select_by_string($cmeta['select']);
							echo '<select name="eg-'.$cmeta['handle'].'">';
							if(!empty($el) && is_array($el)){
								echo '<option value="">'.__('---', EG_TEXTDOMAIN).'</option>';
								foreach($el as $ele){
									$sel = ($ele == $val) ? ' selected="selected"' : '';
									echo '<option value="'.$ele.'"'.$sel.'>'.$ele.'</option>';
								}
							}
							echo '</select>';
							break;
						case 'image':
							$var_src = '';
							if(intval($val) > 0){
								//get URL to Image
								$img = wp_get_attachment_image_src($val, 'full');
								if($img !== false){
									$var_src = $img[0];
								}else{
									$val = '';
								}
							}else{
								$val = '';
							}
							?>
							<input type="hidden" value="<?php echo $val; ?>" name="eg-<?php echo $cmeta['handle']; ?>" id="eg-<?php echo $cmeta['handle']; ?>" />
							<a class="button-primary revblue eg-cm-image-add" href="javascript:void(0);" data-setto="eg-<?php echo $cmeta['handle']; ?>"><?php _e('Choose Image', EG_TEXTDOMAIN); ?></a>
							<a class="button-primary revred eg-cm-image-clear" href="javascript:void(0);" data-setto="eg-<?php echo $cmeta['handle']; ?>"><?php _e('Remove Image', EG_TEXTDOMAIN); ?></a>
							<div>
								<img id="eg-<?php echo $cmeta['handle']; ?>-img" src="<?php echo $var_src; ?>" <?php echo ($var_src == '') ? 'style="display: none;"' : ''; ?>>
							</div>
							<?php
							break;
					}
					?>
					</div>
				<?php
			}
		}else{
			_e('No metas available yet. Add some through the Custom Meta menu of Essential Grid.', EG_TEXTDOMAIN);
			?><div style="clear:both; height:20px"></div><?php 			
		}
		?>

		<a href="<?php echo Essential_Grid_Admin::getSubViewUrl(Essential_Grid_Admin::VIEW_SUB_CUSTOM_META_AJAX); ?>" class="button-primary revblue" style="margin-top:20px !important; margin-bottom:20px !important;" target="_blank"><?php _e('Create New Meta Keys', EG_TEXTDOMAIN); ?></a>
	</div>
	
	
	<h2><span style="margin:5px 10px 0px 10px"class="dashicons dashicons-admin-media"></span><?php _e('Alternative Sources', EG_TEXTDOMAIN); ?></h2>
	<p style="margin-top:10px">
		<strong style="font-size:14px"><?php _e('HTML5 Video Source`s', EG_TEXTDOMAIN); ?></strong>
	</p>
	<p>
		<div class="eg-cs-row" style="float:left"><label class="eg-mb-label"><?php _e('MP4', EG_TEXTDOMAIN); ?></label> <input type="text" name="eg_sources_html5_mp4" id="eg_sources_html5_mp4" style="margin-right:20px" value="<?php echo $eg_sources_html5_mp4; ?>" /></div>
		<div class="eg-cs-row" style="float:left"><label class="eg-mb-label"><?php _e('OGV', EG_TEXTDOMAIN); ?></label> <input type="text" name="eg_sources_html5_ogv" id="eg_sources_html5_ogv" style="margin-right:20px" value="<?php echo $eg_sources_html5_ogv; ?>" /></div>
		<div class="eg-cs-row" style="float:left"><label class="eg-mb-label"><?php _e('WEBM', EG_TEXTDOMAIN); ?></label> <input type="text" name="eg_sources_html5_webm" id="eg_sources_html5_webm" style="margin-right:20px" value="<?php echo $eg_sources_html5_webm; ?>" /></div>
		<div class="eg-cs-row" style="float:left">		
			<label class="eg-mb-label"><?php _e('Video Ratio:', EG_TEXTDOMAIN); ?></label>
			<select id="eg-html5-ratio" name="eg_html5_ratio" class="eg-tooltip-wrap" title="<?php _e('Choose the Video Ratio', EG_TEXTDOMAIN); ?>">
				<option value="0"<?php selected($eg_html5_ratio, '0'); ?>>4:3</option>
				<option value="1"<?php selected($eg_html5_ratio, '1'); ?>>16:9</option>					
			</select>
		</div>
		
		<div style="clear:both"></div>
	</p>
		
	<p style="margin-top:10px">
		<strong style="font-size:14px"><?php _e('YouTube or Vimeo Video Source`s', EG_TEXTDOMAIN); ?></strong>
	</p>

	<p>
		<div class="eg-cs-row" style="float:left"><label class="eg-mb-label" for="eg_sources_youtube"><?php _e('YouTube ID', EG_TEXTDOMAIN); ?></label><input type="text" name="eg_sources_youtube" id="eg_sources_youtube" style="margin-right:20px"  value="<?php echo $eg_sources_youtube; ?>" /></div>		
		<div class="eg-cs-row" style="float:left">		
			<label class="eg-mb-label"><?php _e('Video Ratio:', EG_TEXTDOMAIN); ?></label>
			<select id="eg-youtube-ratio" name="eg_youtube_ratio" class="eg-tooltip-wrap" title="<?php _e('Choose the Video Ratio', EG_TEXTDOMAIN); ?>">
				<option value="0"<?php selected($eg_youtube_ratio, '0'); ?>>4:3</option>
				<option value="1"<?php selected($eg_youtube_ratio, '1'); ?>>16:9</option>					
			</select>
		</div>
		<div style="clear:both"></div>		
		<div class="eg-cs-row" style="float:left"><label  class="eg-mb-label" for="eg_sources_vimeo"><?php _e('Vimeo ID', EG_TEXTDOMAIN); ?></label><input type="text" name="eg_sources_vimeo" id="eg_sources_vimeo" style="margin-right:20px" value="<?php echo $eg_sources_vimeo; ?>" /></div>
		<div class="eg-cs-row" style="float:left">		
			<label class="eg-mb-label"><?php _e('Video Ratio:', EG_TEXTDOMAIN); ?></label>
			<select id="eg-vimeo-ratio" name="eg_vimeo_ratio" class="eg-tooltip-wrap" title="<?php _e('Choose the Video Ratio', EG_TEXTDOMAIN); ?>">
				<option value="0"<?php selected($eg_vimeo_ratio, '0'); ?>>4:3</option>
				<option value="1"<?php selected($eg_vimeo_ratio, '1'); ?>>16:9</option>					
			</select>
		</div>
		<div style="clear:both"></div>		
	</p>
	
	
	<p style="margin-top:10px">
		<strong style="font-size:14px"><?php _e('Sound Cloud', EG_TEXTDOMAIN); ?></strong>
	</p>

	<p>
		<div class="eg-cs-row" style="float:left"><label class="eg-mb-label" for="eg_sources_soundcloud"><?php _e('SoundCloud Track', EG_TEXTDOMAIN); ?></label><input type="text" name="eg_sources_soundcloud" id="eg_sources_soundcloud" style="margin-right:20px"  value="<?php echo $eg_sources_soundcloud; ?>" /></div>		
		<div class="eg-cs-row" style="float:left">		
			<label class="eg-mb-label"><?php _e('Frame Ratio:', EG_TEXTDOMAIN); ?></label>
			<select id="eg-soundcloud-ratio" name="eg_soundcloud_ratio" class="eg-tooltip-wrap" title="<?php _e('Choose the SoundCloud iFrame Ratio', EG_TEXTDOMAIN); ?>">
				<option value="0"<?php selected($eg_soundcloud_ratio, '0'); ?>>4:3</option>
				<option value="1"<?php selected($eg_soundcloud_ratio, '1'); ?>>16:9</option>					
			</select>
		</div>
		<div style="clear:both"></div>		
	</p>
		
	<p style="margin-top:10px">
		<strong style="font-size:14px"><?php _e('Image Source`s', EG_TEXTDOMAIN); ?></strong>
	</p>
	<p>
		<label  class="eg-mb-label" for="eg_sources_image"><?php _e('Alt. Image', EG_TEXTDOMAIN); ?></label>
		<input type="text" name="eg_sources_image" id="eg_sources_image" style="display: none;" value="<?php echo $eg_sources_image; ?>" />
		<a id="eg-choose-from-image-library" class="button-primary revblue" data-setto="eg_sources_image" href="javascript:void(0);"><?php _e('Choose Image', EG_TEXTDOMAIN); ?></a>
		<a id="eg-clear-from-image-library" class="button-primary eg-remove-custom-meta-field" href="javascript:void(0);"><?php _e('Remove Image', EG_TEXTDOMAIN); ?></a>		
	</p>
	<div id="eg_sources_image-wrapper">
		<img id="eg_sources_image-img" src="<?php echo $eg_sources_image_url; ?>">
	</div>
	
	<p style="margin-top:10px">
		<strong style="font-size:14px"><?php _e('iFrame HTML Markup', EG_TEXTDOMAIN); ?></strong>
	</p>
	<p>
		<textarea type="text" style="width:100%" name="eg_sources_iframe" id="eg_sources_iframe"><?php echo $eg_sources_iframe; ?></textarea>
	</p>
	
	<?php
	if(!isset($disable_advanced) || $disable_advanced == false){
	?>
	
	<h2><span style="margin:5px 10px 0px 10px"class="dashicons dashicons-admin-generic"></span><?php _e('Custom Post Based Skin Modifications', EG_TEXTDOMAIN); ?></h2>
	<div id="eg-advanced-param-wrap">
		<div id="eg-advanced-param">
			
		</div>
		<a class="button-primary revblue" href="javascript:void(0);" id="eg-add-custom-meta-field" style="margin-top:10px !important"><?php _e('Add New Custom Skin Rule', EG_TEXTDOMAIN); ?></a>
		<div class="eg-notifcation">
			<div class="dashicons dashicons-lightbulb" style="float:left;margin-right:10px;"></div>
			<div style="float:left; "><?php _e("For default Skin Settings please use the Essential Grid Skin Editor.<br> Only add Rules here to change the Skin Element Styles only for this Post !<br>Every rule defined here will overwrite the Global Skin settings explicit for this Post in the Grid where the Skin is used. ", EG_TEXTDOMAIN); ?></div>
			<div style="clear:both"></div>
		</div>
		
	</div>
	
	<?php
	}
	
	if(isset($disable_advanced) && $disable_advanced == true){ //only show if we are in preview mode
		?>
		</form>
		<?php
	}
	?>
	<script type="text/javascript">
		jQuery(function(){
			
			jQuery('#eg-choose-from-image-library').click(function(e) {
				e.preventDefault();
				AdminEssentials.upload_image_img(jQuery(this).data('setto'));
				
				return false; 
			});
			
			jQuery('#eg-clear-from-image-library').click(function(e) {
				e.preventDefault();
				jQuery('#eg_sources_image').val('');
				jQuery('#eg_sources_image-img').attr("src","");
				jQuery('#eg_sources_image-img').hide();
				return false; 
			});
			
			
			jQuery('.eg-cm-image-add').click(function(e) {
				e.preventDefault();
				AdminEssentials.upload_image_img(jQuery(this).data('setto'));
				
				return false; 
			});
			
			jQuery('.eg-cm-image-clear').click(function(e) {
				e.preventDefault();
				var setto = jQuery(this).data('setto');
				jQuery('#'+setto).val('');
				jQuery('#'+setto+'-img').attr("src","");
				jQuery('#'+setto+'-img').hide();
				return false; 
			});
			
			
			<?php
			if(!isset($disable_advanced) || $disable_advanced == false){
			?>
			
			AdminEssentials.setInitSkinsJson(<?php echo $base->jsonEncodeForClientSide($advanced); ?>);
			AdminEssentials.setInitElementsJson(<?php echo $base->jsonEncodeForClientSide($eg_meta); ?>);
			AdminEssentials.setInitStylingJson(<?php echo $base->jsonEncodeForClientSide($eg_elements); ?>);
			
			AdminEssentials.initMetaBox();
			
			<?php
			}
			?>
			if(jQuery('#eg_sources_image-img').attr('src') !== '')
				jQuery('#eg_sources_image-img').show();
			else
				jQuery('#eg_sources_image-img').hide();
				
		});
		
		
	</script>