<?php get_header(); ?>
            
			
            <!--// PAGE CONTENT //-->
            <div id="page-content">
                
                <br> 


				<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

                
                <!--// PAGE TITLE //-->
                <h1 class="page-title"><?php the_title(); ?></h1>    
                                

				<div class="row">
				
                
                    <!--// MAIN VERBIAGE //-->
                    <div class="col-lg-8">
                                        
                        <div class="block">
                                                    
                            
                            <section class="post_content clearfix" itemprop="articleBody">
                                
								<?php 
                                            
                                    if( get_the_content() == '' )
                                        echo '<h3><i class="fa fa-calendar"></i> Coming Soon!</h3>';
                                        
                                    else
                                        the_content(); 
                                            
								?>
                        
                            </section> <!-- end article section -->
                            
                        
                            <?php comments_template('',true); ?>
                            
                  
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


                        </div><!-- /.block -->
                                            
                    </div><!-- / Main Verbiage -->   
                    
                    
                    
					<?php get_sidebar(); // sidebar 1 ?>
                                                        
                
                
                
            	</div><!-- /.row -->
                
            </div><!-- /#page-content -->




			<?php /* ORIGINAL
			<div id="content" class="clearfix row">
			
				<div id="main" class="col-sm-8 clearfix" role="main">

					<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
					
					<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">
						
						<header>
							
							<div class="page-header"><h1 class="page-title" itemprop="headline"><?php the_title(); ?></h1></div>
						
						</header> <!-- end article header -->
					
						<section class="post_content clearfix" itemprop="articleBody">
							<?php the_content(); ?>
					
						</section> <!-- end article section -->
						
						<footer>
			
							<?php the_tags('<p class="tags"><span class="tags-title">' . __("Tags","wpbootstrap") . ':</span> ', ', ', '</p>'); ?>
							
						</footer> <!-- end article footer -->
					
					</article> <!-- end article -->
					
					<?php comments_template('',true); ?>
					
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
			
				</div> <!-- end #main -->
    
				<?php get_sidebar(); // sidebar 1 ?>
    
			</div> <!-- end #content -->
			
			*/ ?>

<?php get_footer(); ?>