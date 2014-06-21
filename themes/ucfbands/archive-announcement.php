<?php get_header(); ?>

            
            <!--// FIX AFFIX //-->
            <div class="top-fixed">
            
            	            
                <!--// PAGE TITLE //-->
                <div class="section-title" style="background-image: url('<?php echo home_url(); ?>/wp-content/themes/ucfbands/images/section-headers/page-title-sample-bg2.jpg');">
                
                    <h1>Announcements</h1>
                
                </div><!-- /.section-title -->
                
                                    
            </div><!-- /.top-fixed -->

            
            <!--// PAGE CONTENT //-->
            <div id="page-content">
				
				<?php echo do_shortcode('[ess_grid alias="announcements"]'); ?>

            </div><!-- /#page-content -->
			

<?php get_footer(); ?>