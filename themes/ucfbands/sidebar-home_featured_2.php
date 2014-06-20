
                <!--// HALF WIDTH COL //-->
                <div class="col-lg-6">
                
                    <div class="block block-featured">
                            
                        <div id="" role="complementary">
                        
                            <?php if ( is_active_sidebar( 'home_featured_2' ) ) : ?>
        
                                <?php dynamic_sidebar( 'home_featured_2' ); ?>
        
                            <?php else : ?>
        
                                <!-- This content shows up if there are no widgets defined in the backend. -->
                                
                                <div class="alert alert-message">
                                
                                    <p><?php _e("Please activate some Widgets","wpbootstrap"); ?>.</p>
                                
                                </div>
        
                            <?php endif; ?>
        
                        </div>
                
                    </div><!-- /.block -->
                
                </div><!-- / Right Column -->         

