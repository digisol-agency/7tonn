<?php
/*
* Main Page (Default Template)
*/

//get global prefix
$theId = kona_getId();

//get template header
get_header();

if (have_posts()) : while (have_posts()) : the_post();

// pagebuilder activated or not
$pb_enabled = get_post_meta($theId, '_sr_pagebuilder_active', true);
$vc_enabled = "false";
$vc_enabled = get_post_meta($theId, '_wpb_vc_js_status', true);
$elementor_enabled = false;
if( is_plugin_active( 'elementor/elementor.php' ) ) {
	if ( \Elementor\Plugin::$instance->db->is_built_with_elementor($theId) ) { $elementor_enabled = true; } 
} 

$wrapperCheck = false;
if (!$pb_enabled && !$elementor_enabled && ($vc_enabled == "false" || !$vc_enabled)) { $wrapperCheck = "wrapper-medium"; }
if( $post->post_password ) { $wrapperCheck = "wrapper"; }
?>

        <?php if ($wrapperCheck) { ?><div class="<?php echo esc_attr($wrapperCheck); ?>"><?php } ?>
		<?php the_content(); ?>
		<?php if ($wrapperCheck) { ?>
		<div class="clear"></div>
      	<?php 
			$pagesDefault = array(
				'before'           => '<div class="content-pagination"><span class="h6 widget-title title-alt">' . __( 'Pages:', 'kona' ).'</span><span class="pages">',
				'after'            => '</div>',
			);																 
			wp_link_pages($pagesDefault); 
		?>
       	</div>
       	
        <div class="spacer-big"></div> 
		<?php } ?>

<?php if (comments_open() && !post_password_required() ) { ?><div class="post-comments wrapper-medium"><?php comments_template( '', true );?></div><div class="spacer-big"></div><?php } ?>

<?php endwhile; ?>
<?php endif; ?>

<?php get_footer(); ?>