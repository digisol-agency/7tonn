<?php
/**
 * Single Product Meta
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/meta.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     4.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;
?>

<?php if (get_option('_sr_shopsinglesku') || get_option('_sr_shopsinglecategories') || get_option('_sr_shopsingletags')) { ?>
<div class="product_meta">

	<?php do_action( 'woocommerce_product_meta_start' ); ?>
	
	<?php if (get_option('_sr_shopsinglesku')) { ?>
	<?php if ( wc_product_sku_enabled() && ( $product->get_sku() || $product->is_type( 'variable' ) ) ) : ?>

		<span class="sku_wrapper"><?php esc_html_e( 'SKU:', 'kona' ); ?> <span class="sku"><?php echo ( $sku = $product->get_sku() ) ? $sku : esc_html__( 'N/A', 'kona' ); ?></span></span>

	<?php endif; ?>
	<?php } ?>

	<?php if (get_option('_sr_shopsinglecategories')) { 
		if (count( $product->get_category_ids() ) > 1) { $catText = esc_html__( 'Categories', 'kona' ); }
		else { $catText = esc_html__( 'Category', 'kona' ); }
	?>
	<?php echo wc_get_product_category_list( $product->get_id(), ', ', '<span class="posted_in">' .esc_html( $catText ). ': ', '</span>' ); ?>
	<?php } ?>

	<?php if (get_option('_sr_shopsingletags')) { 
		if (count( $product->get_category_ids() ) > 1) { $tagText = esc_html__( 'Tags', 'kona' ); }
		else { $tagText = esc_html__( 'Tag', 'kona' ); }
	?>
	<?php echo wc_get_product_tag_list( $product->get_id(), ', ', '<span class="tagged_as">' .esc_html( $tagText ). ': ', '</span>' ); ?>
	<?php } ?>

	<?php do_action( 'woocommerce_product_meta_end' ); ?>

</div>
<?php } ?>
