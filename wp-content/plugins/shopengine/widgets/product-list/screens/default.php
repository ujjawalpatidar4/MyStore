<?php

use ShopEngine\Utils\Helper;

defined('ABSPATH') || exit;
// product query
$args = [
	'post_type'      => 'product',
	'posts_per_page' => isset($products_per_page) ? $products_per_page : 6,
	'order'          => isset($product_order) ? $product_order : 'DESC'
];
if( $out_of_stock_product_visibility == 'hide'|| ( $out_of_stock_product_visibility == 'default' && 'yes' == get_option('woocommerce_hide_out_of_stock_items'))){
    $args['meta_query'] [] = array(
        'key'     => '_stock_status',
        'value'   => 'outofstock',
        'compare' => '!='
	);
}
if (isset($product_orderby)) {
	switch ($product_orderby) {
	case 'price':
		$args['meta_key'] = '_price';
		$args['orderby']  = 'meta_value_num';
		break;
	case 'sales':
		$args['meta_key'] = 'total_sales';
		$args['orderby']  = 'meta_value_num';
		break;
	case 'rated':
		$args['meta_key'] = '_wc_average_rating';
		$args['orderby']  = 'meta_value_num';
		break;
	case 'sku':
		$args['meta_key'] = '_sku';
		$args['orderby']  = 'meta_value';
		break;
	case 'stock_status':
		$args['meta_key'] = '_stock_status';
		$args['orderby']  = 'meta_value';
		break;
	default:
		$args['orderby'] = $product_orderby;
	}
} else {
	$args['orderby'] = 'date';
}

$product_visibility_term_ids = wc_get_product_visibility_term_ids();

switch ($product_by) {
	case 'category':
		$args['tax_query'] = [
			[
				'taxonomy' => 'product_cat',
				'field'    => 'term_id',
				'terms'    => (isset($term_list) && !empty($term_list)) ? $term_list : []
			]
		];
		break;
	case 'tag':
		$args['tax_query'] = [
			[
				'taxonomy' => 'product_tag',
				'field'    => 'term_id',
				'terms'    => (isset($tag_lists) && !empty($tag_lists)) ? $tag_lists : []
			]
		];
		break;
	case 'rating':
		$product_visibility_terms  = wc_get_product_visibility_term_ids();
		$product_visibility_not_in = [];
		$rating_terms              = [];

		if ('yes' === get_option('woocommerce_hide_out_of_stock_items')) {
			$product_visibility_not_in[] = $product_visibility_terms['outofstock'];
		}

		for ($i = 1; $i <= 5; $i++) {

			$t_dx = 'rated-' . $i;

			if (in_array($i, $rating_list, false) && isset($product_visibility_terms[$t_dx])) {
				$rating_terms[] = $product_visibility_terms[$t_dx];
			}
		}

		if (!empty($rating_terms)) {
			$tax_query[] = [
				'taxonomy'      => 'product_visibility',
				'field'         => 'term_taxonomy_id',
				'terms'         => $rating_terms,
				'operator'      => 'IN',
				'rating_filter' => true
			];
		}

		if (!empty($product_visibility_not_in)) {
			$tax_query[] = [
				'taxonomy' => 'product_visibility',
				'field'    => 'term_taxonomy_id',
				'terms'    => $product_visibility_not_in,
				'operator' => 'NOT IN'
			];
		}

		if (!empty($tax_query)) {
			$args['tax_query'] = $tax_query;
		}
		break;
	case 'attribute':
		if (!empty($pa_attribute_list)) {

			$tx_qry = [
				'relation' => 'OR'
			];

			foreach ($pa_attribute_list as $txn) {

				$terms = get_terms($txn);
				$terms_slug = [];

				foreach($terms as $term) {
					$terms_slug[] = $term->slug;
				}

				$tx_qry[] = [
					'taxonomy' => $txn,
					'field'    => 'slug',
					'terms'    => $terms_slug,
					'operator' => 'IN'
				];
			}

			$args['tax_query'] = $tx_qry;
		}
		break;
	case 'author':
		$args['author__in'] = (isset($author_list) && !empty($author_list)) ? $author_list : [];
		break;
	case 'product':
		$args['post__in'] = (isset($product_list) && !empty($product_list)) ? $product_list : [];
		break;
	case 'featured':
		$args['tax_query'][] = [
			'taxonomy' => 'product_visibility',
			'field'    => 'term_taxonomy_id',
			'terms'    => $product_visibility_term_ids['featured']
		];
		break;
	case 'sale':
		$args['post__in'] = wc_get_product_ids_on_sale();
		break;
	case 'viewed':
		$viewed_products        = !empty($_COOKIE['woocommerce_recently_viewed']) ? (array) explode('|', sanitize_text_field(wp_unslash($_COOKIE['woocommerce_recently_viewed']))) : [];
		$viewed_products        = array_reverse(array_filter(array_map('absint', $viewed_products)));
		$query_args['post__in'] = $viewed_products;
		break;
}

$productQuery = new \WP_Query($args);

?>

<div class="shopengine-product-list">

	<?php if ($productQuery->have_posts()): ?>

        <div class="<?php echo $product_view_style === "list" ? esc_attr("product-list-view") : esc_attr("product-list-grid"); ?>">

			<?php while ($productQuery->have_posts()): $productQuery->the_post();
                $product = wc_get_product(get_the_ID());?>

					<div class="shopengine-single-product-item">

						<!-- product thumb -->
						<div class="product-thumb">

							<!-- product thumb -->
							<a title="<?php esc_attr_e('View Product Full Details','shopengine')?>" href="<?php echo esc_url(get_the_permalink()); ?>">
								<?php shopengine_content_render(woocommerce_get_product_thumbnail($product->get_id())); ?>
							</a>

							<!-- add to cart -->
							<div class="overlay-add-to-cart position-<?php echo isset($product_hover_overlay_position) ? esc_attr($product_hover_overlay_position) : 'bottom'; ?>">
								<?php woocommerce_template_loop_add_to_cart();?>
							</div>
							<!-- tag and sale badge -->
							<?php
							$product_tags   = get_the_terms(get_the_ID(), 'product_tag');
							$badge_position = !empty($badge_position) ? $badge_position : 'top-right';
							$badge_align    = !empty($badge_align) ? $badge_align : 'horizontal';
							?>
							<div class="product-tag-sale-badge position-<?php echo esc_attr($badge_position); ?> align-<?php echo esc_attr($badge_align); ?>">
								<ul>
									<!-- percentage -->
									<?php 
									if($show_off === "yes"){
										if (!empty($product->get_regular_price()) && !empty($product->get_sale_price()) && $product->get_regular_price() !== $product->get_sale_price() ): ?>
											<li class="badge no-link off">
												<?php
													$percentage = round((($product->get_regular_price() - $product->get_sale_price()) / $product->get_regular_price()) * 100);
													echo esc_html(sprintf('%1$s%2$s%3$s', '-', $percentage, '%'));
												?>
											</li>
									<?php endif;} ?>
									<!-- custom tags -->
									<?php 
									if($show_tag === "yes"){
										if($product_tags && !is_wp_error($product_tags)): 
											$product_tag = $product_tags[0];
											$bg = get_term_meta($product_tag->term_id, 'devmonsta_bajaar_tag_bg_color', true); ?>
											<li class="badge tag">
												<a title="<?php echo esc_html($product_tag->name, 'shopengine');?>" href="<?php echo esc_url(get_term_link($product_tag->term_id)); ?>">
												<?php esc_html_e($product_tag->name, 'shopengine'); ?></a>
											</li>
									<?php endif;} ?> 
									 <!-- on sale -->
									<?php
									if($show_sale === "yes"){ 
										if ($product->is_on_sale()) :?>
										<li class='badge no-link sale'><?php esc_html_e('Sale!', 'shopengine')?></li>
									<?php endif;} ?> 
									<!-- out of stock -->
									<?php
									if($show_stock_out_badge === "yes"){ 
										if(!$product->is_in_stock()):?>
											<li class="badge no-link out-of-stock">
											<?php echo esc_html__('Out of Stock', 'shopengine'); ?>
										</li>
									<?php endif;} ?> 
								</ul>
							</div>
						</div>

                    <!-- product category -->
					<?php if($product_view_style === "list"): ?>
					<div class="list-view-wrapper-contents">
					<?php endif; ?>
					<?php
                        $product_cats   = get_the_terms(get_the_ID(), 'product_cat');
                        $category_limit = (isset($category_limit) && !empty($category_limit)) ? esc_attr($category_limit) : 1;

                        if ($product_cats && !is_wp_error($product_cats)):
                            $slice_product_cats = array_slice($product_cats, 0, $category_limit);
                        if (count($slice_product_cats) > 0): ?>
								<div class='product-category'>
									<ul>
										<?php foreach ($slice_product_cats as $key => $product_cat): ?>
											<li>
												<a title="<?php esc_html_e('Product Category','shopengine')?>" href="<?php echo esc_url(get_term_link($product_cat)); ?>">
													<?php
														echo esc_html($product_cat->name);
														if ($key !== (count($slice_product_cats) - 1)) {
															echo ',';
														}
													?>
												</a>
											</li>
									<?php endforeach;?>
                                </ul>
                            </div>
						<?php endif;
                    endif;
					?>

                    <!-- product title -->
                    <h3 class='product-title'>
                        <a title="<?php esc_html_e('View Product Full Details','shopengine')?>" href="<?php the_permalink();?>">
							<?php
                                if (isset($title_character) && !empty($title_character)):
                                    echo esc_html(substr(get_the_title(), 0, $title_character));
                                else:
                                    echo esc_html(get_the_title());
                                endif;
                            ?>
                        </a>
                    </h3>

                    <!-- product rating -->
                    <div class="product-rating">
						<?php
                            if ($product->get_rating_count() > 0) {
                                woocommerce_template_loop_rating();
                            } else {
                                shopengine_content_render(sprintf('<div class="star-rating">%1$s</div>', wc_get_star_rating_html(0, 0)));
                            }

                            echo wp_kses(sprintf('<span class="rating-count">(%1$s)</span>', $product->get_review_count()), Helper::get_kses_array());
                        ?>
                    </div>

                    <!-- product price -->
                    <div class="product-price">
						<?php woocommerce_template_single_price(); ?>
                    </div>
				    <?php if($product_view_style === "list"): ?>
					</div>
					<?php endif; ?>
                </div> <!-- end item -->

			<?php endwhile;?>

        </div>

	<?php else:

            esc_html_e('No product found.', 'shopengine');

        endif;

    wp_reset_postdata();?>

</div>
