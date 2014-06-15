<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package UCFBands
 */
?>


        </div><!-- /#page-wrapper -->    
    

        <!--// GLOBAL SOCIAL MEDIA ICONS //-->
        <div class="nav-social">
        
            <a href="#"><img class="social-icon no-frame" data-toggle="tooltip" data-placement="top" title="UCFBands on Facebook" src="<?php echo home_url(); ?>/wp-content/themes/ucfbands/images/flat-social-media/color-square/facebook.png"></a> 
            
            <a href="#"><img class="social-icon no-frame" data-toggle="tooltip" data-placement="top" title="UCFBands on Twitter" src="<?php echo home_url(); ?>/wp-content/themes/ucfbands/images/flat-social-media/color-square/twitter.png"></a> 
            
            <a href="#"><img class="social-icon no-frame" data-toggle="tooltip" data-placement="top" title="UCFBands on YouTube" src="<?php echo home_url(); ?>/wp-content/themes/ucfbands/images/flat-social-media/color-square/youtube.png"></a> 
        
        </div><!-- /.nav-social -->



    </div><!-- /#wrapper -->



	<?php wp_footer(); ?>

	<script>
	
		<!-- ToolTips -->
		$('.social-icon').tooltip();
				
	</script>


</body>

</html>

	
	
	<?php /* ORIGINAL 
	</div><!-- #content -->

	<footer id="colophon" class="site-footer" role="contentinfo">
		<div class="site-info">
			<a href="<?php echo esc_url( __( 'http://wordpress.org/', 'ucfbands' ) ); ?>"><?php printf( __( 'Proudly powered by %s', 'ucfbands' ), 'WordPress' ); ?></a>
			<span class="sep"> | </span>
			<?php printf( __( 'Theme: %1$s by %2$s.', 'ucfbands' ), 'UCFBands', '<a href="http://JpakMedia.com/" rel="designer">JpakMedia</a>' ); ?>
		</div><!-- .site-info -->
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>

*/ ?>