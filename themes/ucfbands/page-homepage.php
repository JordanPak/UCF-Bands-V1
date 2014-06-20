<?php
/*
Template Name: Home Page
*/
?>

<?php get_header(); ?>


            <!--// FIX AFFIX //-->
            <div class="top-fixed">
            
				 <?php masterslider(1); ?>               
                                    
            </div><!-- /.top-fixed -->
            
			<br><br>
            
            <!--// PAGE CONTENT //-->
            <div id="page-content">
                
				
                
                <?php 
					
					// If either Featured Block is active, may a row for it/them
					if ( is_active_sidebar('home_featured_1') || is_active_sidebar('home_featured_2') )
					{
					
						echo '<div class="row">';
						
							// Home Page - Featured Block 1
							if ( is_active_sidebar('home_featured_1') )
								get_sidebar( 'home_featured_1' );
						
							
							// Home Page - Featured Block 2
							if ( is_active_sidebar('home_featured_2') )
								get_sidebar( 'home_featured_2' );
								
								
						echo '</div>';
                
				
					} // End if a featured block is active
                	
					
					// Start Row
					echo '<div class="row">';
					
					
						// Home Page - Opening Verbiage Block (1)
						if (have_posts()) : while (have_posts()) : the_post(); ?>
	
							
							<!--// RIGHT COLUMN //-->
							<div class="col-lg-4">
							
								<div class="block">
									
									<h2>UCF Bands</h2>
									
									<?php the_content(); ?>
									
								</div><!-- /.block -->
								
							</div><!-- /.col -->
							
						
						<?php endwhile; 
						
					
						// Home Page - Announcements
						get_sidebar( 'home_announcements' );
						
						
						// Home Page - Upcoming Events
						get_sidebar( 'home_events' ); 
					
					
					// End row
					echo '</div>';
					
				?>		
                            
                            
				<?php else : ?>
                
                    <article id="post-not-found">
                        
                        <header>
                            <h1><?php _e("Not Found", "wpbootstrap"); ?></h1>
                        </header>
                        
                        <section class="post_content">
                            <p><?php _e("Sorry, but the requested resource was not found on this site.", "wpbootstrap"); ?></p>
                        </section>
                        
                        <footer>
                        </footer>
                    </article>
                
                <?php endif; ?>


                
            </div><!-- /#page-content -->
			

<?php get_footer();