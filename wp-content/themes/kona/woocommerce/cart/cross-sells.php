<?php
/**
 * Cross-sells
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cross-sells.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 4.4.0
 */

defined( 'ABSPATH' ) || exit;

if ( $cross_sells ) : 

$columns = 3;
$columnsMobile = 2;
$spaced = "spaced";
$titleSize = 'h5';
if (get_option('_sr_shopsinglerelatedcol')) { $columns = intval(get_option('_sr_shopsinglerelatedcol')); }
if (get_option('_sr_shopgridcolmobile')) { $columnsMobile = intval(get_option('_sr_shopgridcolmobile')); }
if (get_option('_sr_shopgridspaced')) { $spaced = get_option('_sr_shopgridspaced'); }

$gridClass = 'isotope-grid fitrows mobile-col-'.$columnsMobile.' style-column-'.$columns.' isotope-'.$spaced;

?>

	<div class="cross-sells crosssells products clearfix">
		
		<?php
		$heading = apply_filters( 'woocommerce_product_cross_sells_products_heading', __( 'You may be interested in&hellip;', 'kona' ) );

		if ( $heading ) : ?>
		<div class="section-title">
			<<?php echo esc_attr($titleSize); ?>><strong><?php echo esc_html( $heading ); ?></strong></<?php echo esc_attr($titleSize); ?>>
		</div>
		<?php endif; ?>

		<div class="<?php echo esc_attr($gridClass); ?> shop-container">

			<?php foreach ( $cross_sells as $cross_sell ) : ?>

				<?php
					$post_object = get_post( $cross_sell->get_id() );

					setup_postdata( $GLOBALS['post'] =& $post_object );

					wc_get_template_part( 'content', 'product' ); ?>

			<?php endforeach; ?>

		</div>

	</div>

<?php endif;

wp_reset_postdata();			

