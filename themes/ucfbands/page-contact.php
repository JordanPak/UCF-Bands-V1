<?php
/*
Template Name: Contact
*/
?>

<?php get_header(); ?>


            <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

            
            <!--// FIX AFFIX //-->
            <div class="top-fixed">
            
            	
                <?php 
				
					// Get Featured Image Link // 
					$single_featured_image = wp_get_attachment_url( get_post_thumbnail_id($post->ID) );
						
				?>
            
                <!--// PAGE TITLE //-->
                <div class="section-title" style="background-image: url('<?php echo $single_featured_image ?>');">
                
                    <h1><?php the_title(); ?></h1>
                
                </div><!-- /.section-title -->
                
                                    
            </div><!-- /.top-fixed -->
            

            
            <!--// PAGE CONTENT //-->
            <div id="page-content">
                
                <br><br>
                
                <div class="row">
                
					<?php
                    
                            if( get_the_content() == '' )
                                echo '<div class="col-lg-3"><div class="block block-featured"><h2><i class="fa fa-calendar"></i> Coming Soon!</h2></div></div>';
                                
                            else
                                the_content(); 
                     
					?>


							<!--// GOOGLE MAP //-->
							<div class="col-lg-6">
								<div class="block">
									
									<iframe width="100%" height="600px%" frameborder="0" style="border:0" src="https://www.google.com/maps/embed/v1/place?q=University%20of%20Central%20Florida%20Bands%20Department%20of%20Music&key=AIzaSyBwRMGuuEA0HhosFyJCAGz6-AY1de5JKHc"></iframe>
									
								</div>
							</div>

                            
                      
                    <?php endwhile; ?>		
                                
                                
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

				</div><!-- /.row -->
                
            </div><!-- /#page-content -->
			

<?php get_footer();