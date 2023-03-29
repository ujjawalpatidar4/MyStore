<?php
/**
 * This template will overwrite the WooCommerce file: woocommerce/templates/checkout/form-shipping.php.
 */

defined('ABSPATH') || exit;

if(is_checkout() || get_post_type() == \ShopEngine\Core\Template_Cpt::TYPE) {

    $checkout = WC()->checkout();

	if(!empty($checkout->checkout_fields)) { ?>


        <div class="shopengine-checkout-form-additional">

            <div class="woocommerce-additional-fields">
				<?php do_action('woocommerce_before_order_notes', $checkout); ?>

				<?php if(apply_filters('woocommerce_enable_order_notes_field', 'yes' === get_option('woocommerce_enable_order_comments', 'yes'))) : ?>

					<?php if(!empty(WC()->cart) && (!WC()->cart->needs_shipping() || wc_ship_to_billing_address_only())) : ?>

                        <h3><?php esc_html_e('Additional information', 'shopengine'); ?></h3>

					<?php endif; ?>

                    <div class="woocommerce-additional-fields__field-wrapper">
						<?php foreach($checkout->get_checkout_fields('order') as $key => $field) : ?>
							<?php woocommerce_form_field($key, $field, $checkout->get_value($key)); ?>
						<?php endforeach; ?>
                    </div>

				<?php endif; ?>

				<?php do_action('woocommerce_after_order_notes', $checkout); ?>
            </div>

        </div>

		<?php
	}
}
