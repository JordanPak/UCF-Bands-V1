<?php
/**
 * @package   Essential_Grid
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/essential/
 * @copyright 2014 ThemePunch
 */
 //force the js file to be included
	//wp_enqueue_script('essential-grid-item-editor-script', EG_PLUGIN_URL.'admin/assets/js/grid-editor.js', array('jquery'), Essential_Grid::VERSION );
	
?>
	<h2><?php echo esc_html(get_admin_page_title()); ?></h2>
	
	<div id="eg-grid-custom-meta-wrapper">
		<?php
		$metas = new Essential_Grid_Meta();
		$custom_metas = $metas->get_all_meta();
		
		if(!empty($custom_metas)){
			foreach($custom_metas as $meta){
				?>
				<div class="postbox eg-postbox" style="width:100%;min-width:500px">
					<h3 class="box-closed"><span style="font-weight:400"><?php _e('Handle:', EG_TEXTDOMAIN); ?></span><span>eg-<?php echo $meta['handle']; ?> </span><div class="postbox-arrow"></div></h3>
					<div class="inside" style="display:none;padding:0px !important;margin:0px !important;height:100%;position:relative;background:#ebebeb">
						<input type="hidden" name="esg-meta-handle[]" value="<?php echo $meta['handle']; ?>" />
						<div class="eg-custommeta-row">
							<div class="eg-cus-row-l"><label><?php _e('Name:', EG_TEXTDOMAIN); ?></label><input type="text" name="esg-meta-name[]" value="<?php echo @$meta['name']; ?>"></div>
							<div class="eg-cus-row-l">
								<label><?php _e('Type:', EG_TEXTDOMAIN); ?></label><select name="esg-meta-type[]" disabled="disabled">
									<option value="<?php echo $meta['type']; ?>"><?php echo ucfirst($meta['type']); ?></option>
								</select>
							</div>
							<div class="eg-cus-row-l"><label><?php _e('Default:', EG_TEXTDOMAIN); ?></label><input type="text" name="esg-meta-default[]" value="<?php echo @$meta['default']; ?>"></div>
							<div class="eg-cus-row-l">
								<?php if($meta['type'] == 'select') { ?>
								<label><?php _e('List:', EG_TEXTDOMAIN); ?></label><textarea class="eg-custommeta-textarea" name="esg-meta-select[]"><?php echo @$meta['select']; ?></textarea>
								<?php } ?>
							</div>									
						</div>
						<div class="eg-custommeta-save-wrap-settings">
							<a class="button-primary revblue eg-meta-edit" href="javascript:void(0);"><?php _e('Edit', EG_TEXTDOMAIN); ?></a>
							<a class="button-primary revred eg-meta-delete" href="javascript:void(0);"><?php _e('Remove', EG_TEXTDOMAIN); ?></a>
						</div>
					</div>
				</div>
				<?php
			}
		}
		?>
	</div>
	
	<a class="button-primary revblue" id="eg-meta-add" href="javascript:void(0);"><?php _e('Add New Meta', EG_TEXTDOMAIN); ?></a>
	
	<?php Essential_Grid_Dialogs::custom_meta_dialog(); ?>
	
	<script type="text/javascript">
		jQuery(function(){
			AdminEssentials.initCustomMeta();
		});
	</script>