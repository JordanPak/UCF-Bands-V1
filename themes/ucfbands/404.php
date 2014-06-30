<?php get_header(); ?>
			
            <!--// PAGE CONTENT //-->
            <div id="page-content">
                
                <br><br>
                 
				<div class="row">
				
                    <!--// MAIN VERBIAGE //-->
                    <div class="col-lg-8">
                                        
                        <div class="block">
                                                    
                                                            
								<h1><?php _e("Page Not Found","wpbootstrap"); ?><small> 404</small></h1>
								<p><?php _e("Sorry, it looks like what you're looking for isn't here! :(","wpbootstrap"); ?></p>
                        		
                                <br>
                                
                           		<p><a class="btn btn-lg btn-gold" href="<?php echo home_url(); ?>"><i class="fa fa-home"></i> UCF Bands Home</a></p>
                                
                        </div><!-- /.block -->
                                            
                    </div><!-- / Main Verbiage -->   
                    
                    
                    
					<?php get_sidebar(); // sidebar 1 ?>
                                                        
                
                
                
            	</div><!-- /.row -->
                
            </div><!-- /#page-content -->

<?php get_footer(); ?>


<?php /* ORIGINAL 
<?php get_header(); ?>
			
			<div id="content" class="clearfix row">
			
				<div id="main" class="col-sm-12 clearfix" role="main">

					<article id="post-not-found" class="clearfix">
						
						<header>

							<div class="hero-unit">
							
								<h1><?php _e("Epic 404 - Article Not Found","wpbootstrap"); ?></h1>
								<p><?php _e("This is embarassing. We can't find what you were looking for.","wpbootstrap"); ?></p>
															
							</div>
													
						</header> <!-- end article header -->
					
						<section class="post_content">
							
							<p><?php _e("Whatever you were looking for was not found, but maybe try looking again or search using the form below.","wpbootstrap"); ?></p>

							<div class="row">
								<div class="col col-lg-12">
									<?php get_search_form(); ?>
								</div>
							</div>
					
						</section> <!-- end article section -->
						
						<footer>
							
						</footer> <!-- end article footer -->
					
					</article> <!-- end article -->
			
				</div> <!-- end #main -->
    
			</div> <!-- end #content -->

<?php get_footer(); ?>
*/ ?>