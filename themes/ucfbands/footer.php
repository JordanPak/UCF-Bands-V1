        </div><!-- /#page-wrapper -->    
		
        
        <!--// NAV FOOTER //-->
        <div id="nav-footer">
        	

            <!--// GLOBAL SOCIAL MEDIA ICONS //-->
            <div class="nav-social">
            
                <a href="#"><img class="social-icon no-frame" data-toggle="tooltip" data-placement="top" title="UCFBands on Facebook" src="<?php echo home_url(); ?>/wp-content/themes/ucfbands/images/flat-social-media/bw-square/facebook.png"></a> 
                
                <a href="#"><img class="social-icon no-frame" data-toggle="tooltip" data-placement="top" title="UCFBands on Twitter" src="<?php echo home_url(); ?>/wp-content/themes/ucfbands/images/flat-social-media/bw-square/twitter.png"></a> 
                
                <a href="#"><img class="social-icon no-frame" data-toggle="tooltip" data-placement="top" title="UCFBands on YouTube" src="<?php echo home_url(); ?>/wp-content/themes/ucfbands/images/flat-social-media/bw-square/youtube.png"></a> 
            
            </div><!-- /.nav-social -->
			
            <br>

        	<?php
			
				// Home Page - Featured Block 1
				if ( is_active_sidebar('nav_footer') )
					get_sidebar( 'nav_footer' );
		
    		?>
                        
            <div class="credits">
            	<p><a href="<?php echo home_url(); ?>/website-credits">Website Credits</a> | &copy; UCFBands 2014</p>
            </div>

		
        </div><!-- /#nav-footer -->
        

    </div><!-- /#wrapper -->

    
    <!--[if lt IE 7 ]>
        <script src="//ajax.googleapis.com/ajax/libs/chrome-frame/1.0.3/CFInstall.min.js"></script>
        <script>window.attachEvent('onload',function(){CFInstall.check({mode:'overlay'})})</script>
    <![endif]-->
    
    <?php wp_footer(); // js scripts are inserted using this function ?>


</body>

</html>