<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   Essential_Grid
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/essential/
 * @copyright 2014 ThemePunch
 */
?>
	<h2><?php echo esc_html(get_admin_page_title()); ?> - <?php _e('Overview', EG_TEXTDOMAIN); ?></h2>
	<div id="eg-global-settings-wrap">
		<a target="_blank" class="button-secondary" href="http://themepunch.com/essential/documentation/"><?php _e('Help', EG_TEXTDOMAIN); ?></a>
		<a class="button-secondary" id="button-general-settings" href="#"><?php _e('Global Settings', EG_TEXTDOMAIN); ?></a>
	</div>
	
	<div id="eg-grid-overview-wrapper">
		<?php
		$grids = Essential_Grid::get_essential_grids();
        
		if(!empty($grids) && is_array($grids)){
            
			?>
			<table class='wp-list-table widefat fixed eg-postbox'>
				<thead>
					<tr>
						<th width="20px"><?php _e('ID', EG_TEXTDOMAIN); ?></th>
						<th width="15%"><?php _e('Name', EG_TEXTDOMAIN); ?></th>
						<th width="120px"><?php _e('Shortcode', EG_TEXTDOMAIN); ?></th>
						<th width="200px"><?php _e('Actions', EG_TEXTDOMAIN); ?> </th>
						<th width="45%"><?php _e('Settings', EG_TEXTDOMAIN); ?> </th>
					</tr>
				</thead>
				<tbody>
				<?php
				foreach($grids as $grid){
				
					$cur_grid = Essential_Grid::get_essential_grid_by_id($grid->id);
					
					?>
					<tr>
						<td><?php echo $grid->id; ?></td>
						<td><?php echo $grid->name; ?></td>
						<td>[ess_grid alias="<?php echo $grid->handle; ?>"]</td>
						<td>
							<div class="btn-wrap-overview-<?php echo $grid->id; ?>">
								<a class="button-primary revgreen" href="<?php echo Essential_Grid_Base::getViewUrl(Essential_Grid_Admin::VIEW_GRID_CREATE, 'create='.$grid->id); ?>"><i class="eg-icon-cog"></i><?php _e("Settings", EG_TEXTDOMAIN); ?></a>
								<a class="button-primary revred eg-btn-delete-grid" id="eg-delete-<?php echo $grid->id; ?>" href="javascript:void(0)"><i class="eg-icon-trash"></i></a>
								<a class="button-primary revyellow eg-btn-duplicate-grid" id="eg-duplicate-<?php echo $grid->id; ?>" href="javascript:void(0)"><i class="eg-icon-picture"></i></a>
							</div>
						</td>
						<td>
						<?php
						echo ucfirst($cur_grid['params']['layout']);
						if($cur_grid['params']['layout'] == 'even')
							echo ', '.$cur_grid['params']['x-ratio'].':'.$cur_grid['params']['y-ratio'];
						
						if(isset($cur_grid['postparams']['source-type']))
							echo ', '.ucfirst($cur_grid['postparams']['source-type']);
							
						if(isset($cur_grid['params']['layout-sizing']))
							echo ', '.ucfirst($cur_grid['params']['layout-sizing']);
						?>
						</td>
					</tr>
					<?php
				}
				?>
				</tbody>
			</table>
			<?php
		}else{
			_e('No Essential Grids found!', EG_TEXTDOMAIN);
		}
		?>
	</div>
	
	<a class='button-primary revblue' href='<?php echo $this->getViewUrl(Essential_Grid_Admin::VIEW_GRID_CREATE, 'create=true'); ?>'><?php _e('Create New Ess. Grid', EG_TEXTDOMAIN); ?></a>
	
	<?php
	require_once('elements/grid-info.php');
	?>
	
	<?php Essential_Grid_Dialogs::globalSettingsDialog(); ?>
	
	
	<script type="text/javascript">
		AdminEssentials.initOverviewGrid();
	</script>