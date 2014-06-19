<?php
/**
 * @package   Essential_Grid
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/essential/
 * @copyright 2014 ThemePunch
 */
 
class Essential_Grid_Dialogs {
	
	/**
	 * Insert Global Settings Dialog
	 * @since    1.0.0
	 */
	public static function globalSettingsDialog(){
		$curPermission = Essential_Grid_Admin::getPluginPermissionValue();
		$output_protection = get_option('tp_eg_output_protection', 'none');
		$tooltips = get_option('tp_eg_tooltips', 'true');
		$wait_for_fonts = get_option('tp_eg_wait_for_fonts', 'true');
		?>
		<div id="global-settings-dialog-wrap" class="essential-dialog-wrap" title="<?php _e('Global Settings', EG_TEXTDOMAIN); ?>"  style="display: none;">
			<p>
				<?php echo _e('View Plugin Permissions', EG_TEXTDOMAIN); ?>:
				<select name="plugin_permissions">
					<option <?php echo ($curPermission == Essential_Grid_Admin::ROLE_ADMIN) ?  'selected="selected" ' : '';?>value="admin"><?php _e('Admin', EG_TEXTDOMAIN); ?></option>
					<option <?php echo ($curPermission == Essential_Grid_Admin::ROLE_EDITOR) ? 'selected="selected" ' : '';?>value="editor"><?php _e('Editor, Admin', EG_TEXTDOMAIN); ?></option>
					<option <?php echo ($curPermission == Essential_Grid_Admin::ROLE_AUTHOR) ? 'selected="selected" ' : '';?>value="author"><?php _e('Author, Editor, Admin', EG_TEXTDOMAIN); ?></option>
				</select>
			</p>
			<p>
				<?php echo _e('Advanced Tooltips', EG_TEXTDOMAIN); ?>:
				<select name="plugin_tooltips">
					<option <?php echo ($tooltips == 'true') ?  'selected="selected" ' : '';?>value="true"><?php _e('On', EG_TEXTDOMAIN); ?></option>
					<option <?php echo ($tooltips == 'false') ? 'selected="selected" ' : '';?>value="false"><?php _e('Off', EG_TEXTDOMAIN); ?></option>
				</select>
			</p>
			<p>
				<?php echo _e('Wait for Fonts', EG_TEXTDOMAIN); ?>:
				<select name="wait_for_fonts">
					<option <?php echo ($wait_for_fonts == 'true') ?  'selected="selected" ' : '';?>value="true"><?php _e('On', EG_TEXTDOMAIN); ?></option>
					<option <?php echo ($wait_for_fonts == 'false') ? 'selected="selected" ' : '';?>value="false"><?php _e('Off', EG_TEXTDOMAIN); ?></option>
				</select>
			</p>
			<p>
				<?php echo _e('Output Filter Protection', EG_TEXTDOMAIN); ?>:
				<select name="output_protection">
					<option <?php echo ($output_protection == 'none') ?  'selected="selected" ' : '';?>value="none"><?php _e('None', EG_TEXTDOMAIN); ?></option>
					<option <?php echo ($output_protection == 'compress') ? 'selected="selected" ' : '';?>value="compress"><?php _e('By Compressing Output', EG_TEXTDOMAIN); ?></option>
					<option <?php echo ($output_protection == 'echo') ? 'selected="selected" ' : '';?>value="echo"><?php _e('By Echo Output', EG_TEXTDOMAIN); ?></option>
				</select>
			</p>
		</div>
		<?php
	}
	
	
	/**
	 * Insert Pages Dialog
	 * @since    1.0.0
	 */
	public static function pages_select_dialog(){
		$pages = get_pages(array('sort_column' => 'post_name'));
		?>
		<div id="pages-select-dialog-wrap" class="essential-dialog-wrap" title="<?php _e('Choose Pages', EG_TEXTDOMAIN); ?>"  style="display: none;">
			<?php echo _e('Choose Pages', EG_TEXTDOMAIN); ?>:
			<table>
				<tr>
					<td colspan="2"><input type="checkbox" id="check-uncheck-pages"><?php echo _e('Select All', EG_TEXTDOMAIN); ?></td>
				</tr>
				<?php
				foreach($pages as $page){
					?>
					<tr><td><input type="checkbox" value="<?php echo str_replace('"', '', $page->post_title).' (ID: '.$page->ID.')'; ?>" name="selected-pages"></td><td><?php echo str_replace('"', '', $page->post_title).' (ID: '.$page->ID.')'; ?></td></tr>
					<?php
				}
				?>
			</table>
		</div>
		<?php
	}
	
	
	/**
	 * Insert global CSS Dialog
	 * @since    1.0.0
	 */
	public static function global_css_edit_dialog(){
		$global_css = Essential_Grid_Global_Css::get_global_css_styles();
		?>
		<div id="global-css-edit-dialog-wrap" class="essential-dialog-wrap" title="<?php _e('Global Custom CSS', EG_TEXTDOMAIN); ?>"  style="display: none;">
			<textarea id="eg-global-css-editor"><?php echo $global_css; ?></textarea>
		</div>
		<?php
	}
	
	
	/**
	 * Insert navigation skin CSS Dialog
	 * @since    1.0.0
	 */
	public static function navigation_skin_css_edit_dialog(){
		?>
		<div id="navigation-skin-css-edit-dialog-wrap" class="essential-dialog-wrap" title="<?php _e('Navigation Skin CSS', EG_TEXTDOMAIN); ?>"  style="display: none;">
			<textarea id="eg-navigation-skin-css-editor"></textarea>
		</div>
		<?php
	}
    
	
	/**
	 * Fontello Icons
	 * @since    1.0.0
	 */
	public static function fontello_icons_dialog(){
		?>
		<div id="eg-fontello-icons-dialog-wrap" style="width:602px; height:405px; margin-left:15px;margin-top:15px;overflow:visible;display:none">
			<div id="dialog-eg-fakeicon-in"></div>
			<div id="dialog-eg-fakeicon-out"></div>				
			<div class="eg-icon-chooser eg-icon-soundcloud"></div>
			<div class="eg-icon-chooser eg-icon-music"></div>
			<div class="eg-icon-chooser eg-icon-color-adjust"></div>
			<div class="eg-icon-chooser eg-icon-mail"></div>
			<div class="eg-icon-chooser eg-icon-mail-alt"></div>
			<div class="eg-icon-chooser eg-icon-heart"></div>
			<div class="eg-icon-chooser eg-icon-heart-empty"></div>
			<div class="eg-icon-chooser eg-icon-star"></div>
			<div class="eg-icon-chooser eg-icon-star-empty"></div>
			<div class="eg-icon-chooser eg-icon-user"></div>
			<div class="eg-icon-chooser eg-icon-male"></div>
			<div class="eg-icon-chooser eg-icon-female"></div>
			<div class="eg-icon-chooser eg-icon-video"></div>
			<div class="eg-icon-chooser eg-icon-videocam"></div>
			<div class="eg-icon-chooser eg-icon-picture-1"></div>
			<div class="eg-icon-chooser eg-icon-camera"></div>
			<div class="eg-icon-chooser eg-icon-camera-alt"></div>
			<div class="eg-icon-chooser eg-icon-th-large"></div>
			<div class="eg-icon-chooser eg-icon-th"></div>
			<div class="eg-icon-chooser eg-icon-ok"></div>
			<div class="eg-icon-chooser eg-icon-ok-circled2"></div>
			<div class="eg-icon-chooser eg-icon-ok-squared"></div>
			<div class="eg-icon-chooser eg-icon-cancel"></div>
			<div class="eg-icon-chooser eg-icon-plus"></div>
			<div class="eg-icon-chooser eg-icon-plus-circled"></div>
			<div class="eg-icon-chooser eg-icon-plus-squared"></div>
			<div class="eg-icon-chooser eg-icon-minus"></div>
			<div class="eg-icon-chooser eg-icon-minus-circled"></div>
			<div class="eg-icon-chooser eg-icon-minus-squared"></div>
			<div class="eg-icon-chooser eg-icon-minus-squared-alt"></div>
			<div class="eg-icon-chooser eg-icon-info-circled"></div>
			<div class="eg-icon-chooser eg-icon-info"></div>
			<div class="eg-icon-chooser eg-icon-home"></div>
			<div class="eg-icon-chooser eg-icon-link"></div>
			<div class="eg-icon-chooser eg-icon-unlink"></div>
			<div class="eg-icon-chooser eg-icon-link-ext"></div>
			<div class="eg-icon-chooser eg-icon-lock"></div>
			<div class="eg-icon-chooser eg-icon-lock-open"></div>
			<div class="eg-icon-chooser eg-icon-eye"></div>
			<div class="eg-icon-chooser eg-icon-eye-off"></div>
			<div class="eg-icon-chooser eg-icon-tag"></div>
			<div class="eg-icon-chooser eg-icon-thumbs-up"></div>
			<div class="eg-icon-chooser eg-icon-thumbs-up-alt"></div>
			<div class="eg-icon-chooser eg-icon-download"></div>
			<div class="eg-icon-chooser eg-icon-upload"></div>
			<div class="eg-icon-chooser eg-icon-reply"></div>
			<div class="eg-icon-chooser eg-icon-forward"></div>
			<div class="eg-icon-chooser eg-icon-export-1"></div>
			<div class="eg-icon-chooser eg-icon-print"></div>
			<div class="eg-icon-chooser eg-icon-gamepad"></div>
			<div class="eg-icon-chooser eg-icon-trash"></div>
			<div class="eg-icon-chooser eg-icon-doc-text"></div>
			<div class="eg-icon-chooser eg-icon-doc-inv"></div>
			<div class="eg-icon-chooser eg-icon-folder-1"></div>
			<div class="eg-icon-chooser eg-icon-folder-open"></div>
			<div class="eg-icon-chooser eg-icon-folder-open-empty"></div>
			<div class="eg-icon-chooser eg-icon-rss"></div>
			<div class="eg-icon-chooser eg-icon-rss-squared"></div>
			<div class="eg-icon-chooser eg-icon-phone"></div>
			<div class="eg-icon-chooser eg-icon-menu"></div>
			<div class="eg-icon-chooser eg-icon-cog-alt"></div>
			<div class="eg-icon-chooser eg-icon-wrench"></div>
			<div class="eg-icon-chooser eg-icon-basket-1"></div>
			<div class="eg-icon-chooser eg-icon-calendar"></div>
			<div class="eg-icon-chooser eg-icon-calendar-empty"></div>
			<div class="eg-icon-chooser eg-icon-lightbulb"></div>
			<div class="eg-icon-chooser eg-icon-resize-full-alt"></div>
			<div class="eg-icon-chooser eg-icon-move"></div>
			<div class="eg-icon-chooser eg-icon-down-dir"></div>
			<div class="eg-icon-chooser eg-icon-up-dir"></div>
			<div class="eg-icon-chooser eg-icon-left-dir"></div>
			<div class="eg-icon-chooser eg-icon-right-dir"></div>
			<div class="eg-icon-chooser eg-icon-down-open"></div>
			<div class="eg-icon-chooser eg-icon-left-open"></div>
			<div class="eg-icon-chooser eg-icon-right-open"></div>
			<div class="eg-icon-chooser eg-icon-angle-left"></div>
			<div class="eg-icon-chooser eg-icon-angle-right"></div>
			<div class="eg-icon-chooser eg-icon-angle-double-left"></div>
			<div class="eg-icon-chooser eg-icon-angle-double-right"></div>
			<div class="eg-icon-chooser eg-icon-left-big"></div>
			<div class="eg-icon-chooser eg-icon-right-big"></div>
			<div class="eg-icon-chooser eg-icon-up-hand"></div>
			<div class="eg-icon-chooser eg-icon-ccw-1"></div>
			<div class="eg-icon-chooser eg-icon-shuffle-1"></div>
			<div class="eg-icon-chooser eg-icon-play"></div>
			<div class="eg-icon-chooser eg-icon-play-circled"></div>
			<div class="eg-icon-chooser eg-icon-stop"></div>
			<div class="eg-icon-chooser eg-icon-pause"></div>
			<div class="eg-icon-chooser eg-icon-fast-fw"></div>
			<div class="eg-icon-chooser eg-icon-desktop"></div>
			<div class="eg-icon-chooser eg-icon-laptop"></div>
			<div class="eg-icon-chooser eg-icon-tablet"></div>
			<div class="eg-icon-chooser eg-icon-mobile"></div>
			<div class="eg-icon-chooser eg-icon-flight"></div>
			<div class="eg-icon-chooser eg-icon-font"></div>
			<div class="eg-icon-chooser eg-icon-bold"></div>
			<div class="eg-icon-chooser eg-icon-italic"></div>
			<div class="eg-icon-chooser eg-icon-text-height"></div>
			<div class="eg-icon-chooser eg-icon-text-width"></div>
			<div class="eg-icon-chooser eg-icon-align-left"></div>
			<div class="eg-icon-chooser eg-icon-align-center"></div>
			<div class="eg-icon-chooser eg-icon-align-right"></div>
			<div class="eg-icon-chooser eg-icon-search"></div>
			<div class="eg-icon-chooser eg-icon-indent-left"></div>
			<div class="eg-icon-chooser eg-icon-indent-right"></div>
			<div class="eg-icon-chooser eg-icon-ajust"></div>
			<div class="eg-icon-chooser eg-icon-tint"></div>
			<div class="eg-icon-chooser eg-icon-chart-bar"></div>
			<div class="eg-icon-chooser eg-icon-magic"></div>
			<div class="eg-icon-chooser eg-icon-sort"></div>
			<div class="eg-icon-chooser eg-icon-sort-alt-up"></div>
			<div class="eg-icon-chooser eg-icon-sort-alt-down"></div>
			<div class="eg-icon-chooser eg-icon-sort-name-up"></div>
			<div class="eg-icon-chooser eg-icon-sort-name-down"></div>
			<div class="eg-icon-chooser eg-icon-coffee"></div>
			<div class="eg-icon-chooser eg-icon-food"></div>
			<div class="eg-icon-chooser eg-icon-medkit"></div>
			<div class="eg-icon-chooser eg-icon-puzzle"></div>
			<div class="eg-icon-chooser eg-icon-apple"></div>
			<div class="eg-icon-chooser eg-icon-facebook"></div>
			<div class="eg-icon-chooser eg-icon-gplus"></div>
			<div class="eg-icon-chooser eg-icon-tumblr"></div>
			<div class="eg-icon-chooser eg-icon-twitter-squared"></div>
			<div class="eg-icon-chooser eg-icon-twitter"></div>
			<div class="eg-icon-chooser eg-icon-vimeo-squared"></div>
			<div class="eg-icon-chooser eg-icon-youtube"></div>
			<div class="eg-icon-chooser eg-icon-youtube-squared"></div>
			<div class="eg-icon-chooser eg-icon-picture"></div>
			<div class="eg-icon-chooser eg-icon-check"></div>
			<div class="eg-icon-chooser eg-icon-back"></div>
			<div class="eg-icon-chooser eg-icon-thumbs-up-1"></div>
			<div class="eg-icon-chooser eg-icon-thumbs-down"></div>
			<div class="eg-icon-chooser eg-icon-download-1"></div>
			<div class="eg-icon-chooser eg-icon-upload-1"></div>
			<div class="eg-icon-chooser eg-icon-reply-1"></div>
			<div class="eg-icon-chooser eg-icon-forward-1"></div>
			<div class="eg-icon-chooser eg-icon-export"></div>
			<div class="eg-icon-chooser eg-icon-folder"></div>
			<div class="eg-icon-chooser eg-icon-rss-1"></div>
			<div class="eg-icon-chooser eg-icon-cog"></div>
			<div class="eg-icon-chooser eg-icon-tools"></div>
			<div class="eg-icon-chooser eg-icon-basket"></div>
			<div class="eg-icon-chooser eg-icon-login"></div>
			<div class="eg-icon-chooser eg-icon-logout"></div>
			<div class="eg-icon-chooser eg-icon-resize-full"></div>
			<div class="eg-icon-chooser eg-icon-popup"></div>
			<div class="eg-icon-chooser eg-icon-arrow-combo"></div>
			<div class="eg-icon-chooser eg-icon-left-open-1"></div>
			<div class="eg-icon-chooser eg-icon-right-open-1"></div>
			<div class="eg-icon-chooser eg-icon-left-open-mini"></div>
			<div class="eg-icon-chooser eg-icon-right-open-mini"></div>
			<div class="eg-icon-chooser eg-icon-left-open-big"></div>
			<div class="eg-icon-chooser eg-icon-right-open-big"></div>
			<div class="eg-icon-chooser eg-icon-left"></div>
			<div class="eg-icon-chooser eg-icon-right"></div>
			<div class="eg-icon-chooser eg-icon-ccw"></div>
			<div class="eg-icon-chooser eg-icon-cw"></div>
			<div class="eg-icon-chooser eg-icon-arrows-ccw"></div>
			<div class="eg-icon-chooser eg-icon-level-down"></div>
			<div class="eg-icon-chooser eg-icon-level-up"></div>
			<div class="eg-icon-chooser eg-icon-shuffle"></div>
			<div class="eg-icon-chooser eg-icon-palette"></div>
			<div class="eg-icon-chooser eg-icon-list-add"></div>
			<div class="eg-icon-chooser eg-icon-back-in-time"></div>
			<div class="eg-icon-chooser eg-icon-monitor"></div>
			<div class="eg-icon-chooser eg-icon-paper-plane"></div>
			<div class="eg-icon-chooser eg-icon-brush"></div>
			<div class="eg-icon-chooser eg-icon-droplet"></div>
			<div class="eg-icon-chooser eg-icon-clipboard"></div>
			<div class="eg-icon-chooser eg-icon-megaphone"></div>
			<div class="eg-icon-chooser eg-icon-key"></div>
			<div class="eg-icon-chooser eg-icon-github"></div>
			<div class="eg-icon-chooser eg-icon-github-circled"></div>
			<div class="eg-icon-chooser eg-icon-flickr"></div>
			<div class="eg-icon-chooser eg-icon-flickr-circled"></div>
			<div class="eg-icon-chooser eg-icon-vimeo"></div>
			<div class="eg-icon-chooser eg-icon-vimeo-circled"></div>
			<div class="eg-icon-chooser eg-icon-twitter-1"></div>
			<div class="eg-icon-chooser eg-icon-twitter-circled"></div>
			<div class="eg-icon-chooser eg-icon-facebook-1"></div>
			<div class="eg-icon-chooser eg-icon-facebook-circled"></div>
			<div class="eg-icon-chooser eg-icon-facebook-squared"></div>
			<div class="eg-icon-chooser eg-icon-gplus-1"></div>
			<div class="eg-icon-chooser eg-icon-gplus-circled"></div>
			<div class="eg-icon-chooser eg-icon-pinterest"></div>
			<div class="eg-icon-chooser eg-icon-pinterest-circled"></div>
			<div class="eg-icon-chooser eg-icon-tumblr-1"></div>
			<div class="eg-icon-chooser eg-icon-tumblr-circled"></div>
			<div class="eg-icon-chooser eg-icon-linkedin"></div>
			<div class="eg-icon-chooser eg-icon-linkedin-circled"></div>
			<div class="eg-icon-chooser eg-icon-dribbble"></div>
			<div class="eg-icon-chooser eg-icon-dribbble-circled"></div>
			<div class="eg-icon-chooser eg-icon-picasa"></div>
			<div class="eg-icon-chooser eg-icon-ok-1"></div>
			<div class="eg-icon-chooser eg-icon-doc"></div>
			<div class="eg-icon-chooser eg-icon-left-open-outline"></div>
			<div class="eg-icon-chooser eg-icon-left-open-2"></div>
			<div class="eg-icon-chooser eg-icon-right-open-outline"></div>
			<div class="eg-icon-chooser eg-icon-right-open-2"></div>
			<div class="eg-icon-chooser eg-icon-equalizer"></div>
			<div class="eg-icon-chooser eg-icon-layers-alt"></div>
			<div class="eg-icon-chooser eg-icon-pencil-1"></div>
			<div class="eg-icon-chooser eg-icon-align-justify"></div>
		</div>
        <?php
	}
	
	
	/**
	 * Insert custom meta Dialog
	 * @since    1.0.0
	 */
	public static function custom_meta_dialog(){
		?>
		<div id="custom-meta-dialog-wrap" class="essential-dialog-wrap" title="<?php _e('Custom Meta', EG_TEXTDOMAIN); ?>"  style="display: none; padding:20px !important;">
			
			<div class="eg-cus-row-l"><label><?php _e('Handle:', EG_TEXTDOMAIN); ?></label><span style="margin-left:-20px;margin-right:2px;"><strong>eg-</strong></span><input type="text" name="eg-custom-meta-handle" value="" /></div>
			<div class="eg-cus-row-l"><label><?php _e('Name:', EG_TEXTDOMAIN); ?></label><input type="text" name="eg-custom-meta-name" value="" /></div>
			<div class="eg-cus-row-l"><label><?php _e('Default:', EG_TEXTDOMAIN); ?></label><input type="text" name="eg-custom-meta-default" value="" /></div>
			<div class="eg-cus-row-l"><label><?php _e('Type:'); ?></label><select name="eg-custom-meta-type"><option value="text"><?php _e('Text'); ?></option><option value="select"><?php _e('Select'); ?></option><option value="image"><?php _e('Image', EG_TEXTDOMAIN); ?></option></select></div>
			<div id="eg-custom-meta-select-wrap" style="display: none;">
				<?php _e('Comma Seperated List of Elements:', EG_TEXTDOMAIN); ?>
				<textarea name="eg-custom-meta-select" style="width: 100%;height: 70px;"></textarea>
			</div>
			
		</div>
		<?php
	}
	
	
	/**
	 * Insert Widget Areas Dialog
	 * @since    1.0.0
	 */
	public static function widget_areas_dialog(){
		?>
		<div id="widget-areas-dialog-wrap" class="essential-dialog-wrap" title="<?php _e('New Widget Area', EG_TEXTDOMAIN); ?>"  style="display: none; padding:20px !important;">
			
			<div class="eg-cus-row-l"><label><?php _e('Handle:', EG_TEXTDOMAIN); ?></label><span style="margin-right:2px;"><strong>eg-</strong></span><input type="text" name="eg-widget-area-handle" value="" /></div>
			<div class="eg-cus-row-l"><label><?php _e('Name:', EG_TEXTDOMAIN); ?></label><input type="text" name="eg-widget-area-name" style="margin-left:29px;" value="" /></div>
			
		</div>
		<?php
	}
	
	
	/**
	 * Insert font Dialog
	 * @since    1.0.0
	 */
	public static function fonts_dialog(){
		?>
		<div id="font-dialog-wrap" class="essential-dialog-wrap" title="<?php _e('Add Font', EG_TEXTDOMAIN); ?>"  style="display: none; padding:20px !important;">
			
			<div class="tp-googlefont-cus-row-l"><label><?php _e('Handle:', EG_TEXTDOMAIN); ?></label><span style="margin-left:-20px;margin-right:2px;"><strong>tp-</strong></span><input type="text" name="eg-font-handle" value="" /></div>
			<div style="margin-top:0px; padding-left:100px; margin-bottom:20px;">
				<i style="font-size:12px;color:#777; line-height:20px;"><?php _e('Unique WordPress handle (Internal use only)', EG_TEXTDOMAIN); ?></i>
			</div>
			<div class="tp-googlefont-cus-row-l"><label><?php _e('Parameter:', EG_TEXTDOMAIN); ?></label><input type="text" name="eg-font-url" value="" /></div>
			<div style="padding-left:100px;">
				<i style="font-size:12px;color:#777; line-height:20px;"><?php _e('Copy the Google Font Family from <a href="http://www.google.com/fonts" target="_blank">http://www.google.com/fonts</a><br/>i.e.:<strong>Open+Sans:400,600,700</strong>', EG_TEXTDOMAIN); ?></i>
			</div>
			
		</div>
		
		
		<?php
	}
	
	
	/**
	 * Meta Dialog
	 * @since    1.0.0
	 */
	public static function meta_dialog(){
		$m = new Essential_Grid_Meta();
		$item_ele = new Essential_Grid_Item_Element();
		
		$post_items = $item_ele->getPostElementsArray();
		$metas = $m->get_all_meta();
		?>
		<div id="meta-dialog-wrap" class="essential-dialog-wrap" title="<?php _e('Meta', EG_TEXTDOMAIN); ?>"  style="display: none; padding:20px !important;">
			<table>
				<tr class="eg-table-title"><td><?php _e('Meta Handle', EG_TEXTDOMAIN); ?></td><td><?php _e('Description', EG_TEXTDOMAIN); ?>
			<?php
			if(!empty($post_items)){
				foreach($post_items as $phandle => $pitem){
					echo '<tr><td>%'.$phandle.'%</td><td>'.$pitem['name'].'</td></tr>';
				}
			}
			
			if(!empty($metas)){
				foreach($metas as $meta){
					echo '<tr><td>%eg-'.$meta['handle'].'%</td><td>'.$meta['name'].'</td></tr>';
				}
			}
			
			if(Essential_Grid_Woocommerce::is_woo_exists()){
				$metas = Essential_Grid_Woocommerce::get_meta_array();
				
				foreach($metas as $meta => $name){
					echo '<tr><td>%'.$meta.'%</td><td>'.$name.'</td></tr>';
				}
				
			}
			
			?>
			</table>
		</div>
		<?php
	}
	
	
	/**
	 * Post Meta Dialog
	 * @since    1.0.0
	 */
	public static function post_meta_dialog(){
		?>
		<div id="post-meta-dialog-wrap" class="essential-dialog-wrap" title="<?php _e('Post Meta Editor', EG_TEXTDOMAIN); ?>"  style="display: none; padding:20px !important;">
			<div id="eg-meta-box">
			
			</div>
		</div>
		<?php
	}
	
	
	/**
	 * Custom Element Image Dialog
	 * @since    1.0.1
	 */
	public static function custom_element_image_dialog(){
		?>
		<div id="custom-element-image-dialog-wrap" class="essential-dialog-wrap" title="<?php _e('Please Choose', EG_TEXTDOMAIN); ?>"  style="display: none; padding:20px !important;">
			<?php
			_e('Please choose the art you wish to add the Image(s): Single Image or Bulk Images ?', EG_TEXTDOMAIN);
			?>
		</div>
		<?php
	}
	
	
	/**
	 * Edit Custom Element Dialog
	 * @since    1.0.0
	 */
	public static function edit_custom_element_dialog(){
		$meta = new Essential_Grid_Meta();
		$item_elements = new Essential_Grid_Item_Element();
		
		?>
		<div id="edit-custom-element-dialog-wrap" class="essential-dialog-wrap" title="<?php _e('Element Settings', EG_TEXTDOMAIN); ?>"  style="display: none; padding:15px 0px;">
			<form id="edit-custom-element-form">
				<input type="hidden" name="custom-type" value="" />
				<div class="eg-elset-title esg-item-skin-media-title">
					<?php _e('Media:', EG_TEXTDOMAIN); ?>
				</div>
				<div id="esg-item-skin-elements-media">
					<div class="eg-elset-row esg-item-skin-elements" id="esg-item-skin-elements-media-sound">
						<div class="eg-elset-label"  for="custom-soundcloud"><?php _e('SoundCloud Track', EG_TEXTDOMAIN); ?></div><input name="custom-soundcloud" type="input" value="" />
					</div>
					<div class="eg-elset-row esg-item-skin-elements" id="esg-item-skin-elements-media-youtube">
						<div class="eg-elset-label"  for="custom-soundcloud"><?php _e('YouTube ID', EG_TEXTDOMAIN); ?></div><input name="custom-youtube" type="input" value="" />
					</div>
					<div class="eg-elset-row esg-item-skin-elements" id="esg-item-skin-elements-media-vimeo">
						<div class="eg-elset-label"  for="custom-soundcloud"><?php _e('Vimeo ID', EG_TEXTDOMAIN); ?></div><input name="custom-vimeo" type="input" value="" />
					</div>
					<div class="esg-item-skin-elements" id="esg-item-skin-elements-media-html5">
						<div class="eg-elset-row"><div class="eg-elset-label"  for="custom-html5-mp4"><?php _e('MP4', EG_TEXTDOMAIN); ?></div><input name="custom-html5-mp4" type="input" value="" /></div>
						<div class="eg-elset-row"><div class="eg-elset-label"  for="custom-html5-ogv"><?php _e('OGV', EG_TEXTDOMAIN); ?></div><input name="custom-html5-ogv" type="input" value="" /></div>
						<div class="eg-elset-row"><div class="eg-elset-label"  for="custom-html5-webm"><?php _e('WEBM', EG_TEXTDOMAIN); ?></div><input name="custom-html5-webm" type="input" value="" /></div>
					</div>
					<div class="eg-elset-row esg-item-skin-elements" id="esg-item-skin-elements-media-image">
						<div class="eg-elset-label" for="custom-image"><?php _e('Image', EG_TEXTDOMAIN); ?></div>
						<input type="hidden" value="" id="esg-custom-image" name="custom-image">
						<a id="eg-custom-choose-from-image-library" class="button-primary revblue" href="javascript:void(0);" data-setto="esg-custom-image"><?php _e('Choose Image', EG_TEXTDOMAIN); ?></a>
						<a id="eg-custom-clear-from-image-library" class="button-primary revred eg-custom-remove-custom-meta-field" href="javascript:void(0);"><?php _e('Remove Image', EG_TEXTDOMAIN); ?></a>
						
						<div id="custom-image-wrapper" style="width:100%;">
							<img id="esg-custom-image-img" src="" style="max-width:200px; display: none;margin:20px 0px 0px 250px;">
						</div>
					</div>
					<div class="eg-elset-row esg-item-skin-elements" id="esg-item-skin-elements-media-ratio">
						<div class="eg-elset-label"  for="custom-ratio"><?php _e('Video Ratio', EG_TEXTDOMAIN); ?></div>
						<select name="custom-ratio">
							<option value="0"><?php _e('4:3', EG_TEXTDOMAIN); ?></option>
							<option value="1"><?php _e('16:9', EG_TEXTDOMAIN); ?></option>
						</select>
					</div>
				</div>
				<div id="">
					
					<?php
					$custom_meta = $meta->get_all_meta();
					if(!empty($custom_meta)){
						echo '<div class="eg-elset-title">';				
						_e('Custom Meta:', EG_TEXTDOMAIN);
						echo '</div>';
					
						foreach($custom_meta as $cmeta){
							?>
							<div class="eg-elset-row"><div class="eg-elset-label"  class="eg-mb-label"><?php echo $cmeta['name']; ?>:</div>
								<?php
								switch($cmeta['type']){
									case 'text':
										echo '<input type="text" name="eg-'.$cmeta['handle'].'" value="" />';
										break;
									case 'select':
										$el = $meta->prepare_select_by_string($cmeta['select']);
										echo '<select name="eg-'.$cmeta['handle'].'">';
										if(!empty($el) && is_array($el)){
											echo '<option value="">'.__('---', EG_TEXTDOMAIN).'</option>';
											foreach($el as $ele){
												
												echo '<option value="'.$ele.'">'.$ele.'</option>';
											}
										}
										echo '</select>';
										break;
									case 'image':
										$var_src = '';
										?>
										<input type="hidden" value="" name="eg-<?php echo $cmeta['handle']; ?>" id="eg-<?php echo $cmeta['handle']; ?>" />
										<a class="button-primary revblue eg-image-add" href="javascript:void(0);" data-setto="eg-<?php echo $cmeta['handle']; ?>"><?php _e('Choose Image', EG_TEXTDOMAIN); ?></a>
										<a class="button-primary revred eg-image-clear" href="javascript:void(0);" data-setto="eg-<?php echo $cmeta['handle']; ?>"><?php _e('Remove Image', EG_TEXTDOMAIN); ?></a>
										<div>
											<img id="eg-<?php echo $cmeta['handle']; ?>-img" src="<?php echo $var_src; ?>" <?php echo ($var_src == '') ? 'style="max-width:200px; display: none;margin:20px 0px 0px 250px;"' : ''; ?>>
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
					
					$elements = $item_elements->getElementsForDropdown();
					$p_lang = array('post' => __('Post', EG_TEXTDOMAIN), 'woocommerce' => __('WooCommerce', EG_TEXTDOMAIN));
					
					foreach($elements as $type => $element){
						?>
						<div class="eg-elset-title">
							<?php echo $p_lang[$type]; ?>
						</div>
						<?php
						foreach($element as $handle => $name){
							echo '<div class="eg-elset-row"><div class="eg-elset-label"  for="'.$handle.'">'.$name['name'].':</div><input name="'.$handle.'" value="" /></div>';
						}
					}
					echo '<div class="eg-elset-title">';	
					_e('Link To:', EG_TEXTDOMAIN);
					echo '</div>';
					
					echo '<div class="eg-elset-row"><div class="eg-elset-label"  for="post-link">'.__('Post Link', EG_TEXTDOMAIN).':</div><input name="post-link" value="" /></div>';
					
					echo '<div class="eg-elset-title">';	
					_e('Other:', EG_TEXTDOMAIN);
					echo '</div>';
					
					echo '<div class="eg-elset-row"><div class="eg-elset-label"  for="custom-filter">'.__('Filter (comma seperated)', EG_TEXTDOMAIN).':</div><input name="custom-filter" value="" /></div>';
					?>
					<div class="eg-elset-row">
						<div class="eg-elset-label" for="cobbles">
							<?php _e('Cobbles Element Size:', EG_TEXTDOMAIN); ?>
						</div>
						<select name="cobbles-size">
							<option value="1:1"><?php _e('width 1, height 1', EG_TEXTDOMAIN); ?></option>
							<option value="1:2"><?php _e('width 1, height 2', EG_TEXTDOMAIN); ?></option>
							<option value="1:3"><?php _e('width 1, height 3', EG_TEXTDOMAIN); ?></option>
							<option value="2:1"><?php _e('width 2, height 1', EG_TEXTDOMAIN); ?></option>
							<option value="2:2"><?php _e('width 2, height 2', EG_TEXTDOMAIN); ?></option>
							<option value="2:3"><?php _e('width 2, height 3', EG_TEXTDOMAIN); ?></option>
							<option value="3:1"><?php _e('width 3, height 1', EG_TEXTDOMAIN); ?></option>
							<option value="3:2"><?php _e('width 3, height 2', EG_TEXTDOMAIN); ?></option>
							<option value="3:3"><?php _e('width 3, height 3', EG_TEXTDOMAIN); ?></option>
						</select>
					</div>
				</div>
			</form>
			<script type="text/javascript">
				jQuery('.eg-image-add').click(function(e) {
					e.preventDefault();
					AdminEssentials.upload_image_img(jQuery(this).data('setto'));
					
					return false; 
				});
				
				jQuery('.eg-image-clear').click(function(e) {
					e.preventDefault();
					var setto = jQuery(this).data('setto');
					jQuery('#'+setto).val('');
					jQuery('#'+setto+'-img').attr("src","");
					jQuery('#'+setto+'-img').hide();
					return false; 
				});
				
				jQuery('#eg-custom-choose-from-image-library').click(function(e) {
					e.preventDefault();
					AdminEssentials.upload_image_img(jQuery(this).data('setto'));
					
					return false; 
				});
				
				jQuery('#eg-custom-clear-from-image-library').click(function(e) {
					e.preventDefault();
					jQuery('#esg-custom-image-src').val('');
					jQuery('#custom-image-img').attr("src","");
					jQuery('#custom-image-img').hide();
					return false; 
				});
			</script>
		</div>
		<?php
	}
	
}
?>