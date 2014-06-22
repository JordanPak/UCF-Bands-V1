<?php
/*
Template Name: Full Width Page (No Titles)
*/
?>

<?php get_header(); ?>

            

            
            <!--// PAGE CONTENT //-->
            <div id="page-content">
                
				
                <?php if (have_posts()) : while (have_posts()) : the_post();

                	
                    if( get_the_content() == '' )
                        echo '<div class="col-lg-3"><div class="block block-featured"><h2><i class="fa fa-calendar"></i> Coming Soon!</h2></div></div>';
                        
                    else
                        the_content(); 
                        
                  
				endwhile; ?>		
                            
                            
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