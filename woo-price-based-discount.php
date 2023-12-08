<?php 

// Hook before calculate fees
add_action('woocommerce_cart_calculate_fees', 'discount_on_max_cart');

/**
 * Add custom fee if more than 80$
 * @param WC_Cart $cart
 */
function discount_on_max_cart(WC_Cart $cart) {
    $subtotal = $cart->get_subtotal();

    // Define the minimum subtotal for the discount
    $minimum_subtotal = 80;

    // Calculate the remaining amount for the discount
    $remaining_amount = $minimum_subtotal - $subtotal;

    // Check if the subtotal is less than the minimum for the discount
    if ($subtotal >= $minimum_subtotal) {
        // Exclude products with a changed price from regular in the cart
        $non_discounted_items = array_filter($cart->get_cart(), function ($cart_item) {
            $product = wc_get_product($cart_item['product_id']);
            return !($product && $product->is_on_sale()) && $product->get_regular_price() === $product->get_price();
        });

        // Calculate the amount to reduce
        $discount = array_reduce($non_discounted_items, function ($carry, $cart_item) {
            return $carry + $cart_item['line_total'];
        }, 0) * 0.2;

        // Apply discount only if there are non-discounted items in the cart
        if ($discount > 0) {
            $cart->add_fee('20% Discount on spend 80$', -$discount);
        }
    }
}

?>
