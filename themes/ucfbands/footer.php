        </div><!-- /#page-wrapper -->    
		
        
        <!--// NAV FOOTER //-->
        <div id="nav-footer">
        	
        	<?php
			
				// Home Page - Featured Block 1
				if ( is_active_sidebar('nav_footer') )
					get_sidebar( 'nav_footer' );
		
    		?>
            
    
            <!--// GLOBAL SOCIAL MEDIA ICONS //-->
            <div class="nav-social">
            
                <a href="#"><img class="social-icon no-frame" data-toggle="tooltip" data-placement="top" title="UCFBands on Facebook" src="<?php echo home_url(); ?>/wp-content/themes/ucfbands/images/flat-social-media/color-square/facebook.png"></a> 
                
                <a href="#"><img class="social-icon no-frame" data-toggle="tooltip" data-placement="top" title="UCFBands on Twitter" src="<?php echo home_url(); ?>/wp-content/themes/ucfbands/images/flat-social-media/color-square/twitter.png"></a> 
                
                <a href="#"><img class="social-icon no-frame" data-toggle="tooltip" data-placement="top" title="UCFBands on YouTube" src="<?php echo home_url(); ?>/wp-content/themes/ucfbands/images/flat-social-media/color-square/youtube.png"></a> 
            
            </div><!-- /.nav-social -->

		
        </div><!-- /#nav-footer -->
        

    </div><!-- /#wrapper -->

    
    <!--[if lt IE 7 ]>
        <script src="//ajax.googleapis.com/ajax/libs/chrome-frame/1.0.3/CFInstall.min.js"></script>
        <script>window.attachEvent('onload',function(){CFInstall.check({mode:'overlay'})})</script>
    <![endif]-->
    
    <?php wp_footer(); // js scripts are inserted using this function ?>


</body>

</html>


			<?php /*  ORIGINAL

			<footer role="contentinfo">
			
				<div id="inner-footer" class="clearfix">
		          <hr />
		          <div id="widget-footer" class="clearfix row">
		            <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('footer1') ) : ?>
		            <?php endif; ?>
		            <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('footer2') ) : ?>
		            <?php endif; ?>
		            <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('footer3') ) : ?>
		            <?php endif; ?>
		          </div>
					
					<nav class="clearfix">
						<?php wp_bootstrap_footer_links(); // Adjust using Menus in Wordpress Admin ?>
					</nav>
					
					<p class="pull-right"><a href="http://320press.com" id="credit320" title="By the dudes of 320press">320press</a></p>
			
					<p class="attribution">&copy; <?php bloginfo('name'); ?></p>
				
				</div> <!-- end #inner-footer -->
				
			</footer> <!-- end footer -->
		
		</div> <!-- end #container -->
				
		<!--[if lt IE 7 ]>
  			<script src="//ajax.googleapis.com/ajax/libs/chrome-frame/1.0.3/CFInstall.min.js"></script>
  			<script>window.attachEvent('onload',function(){CFInstall.check({mode:'overlay'})})</script>
		<![endif]-->
		
		<?php wp_footer(); // js scripts are inserted using this function ?>

	</body>

</html>

 */ ?>