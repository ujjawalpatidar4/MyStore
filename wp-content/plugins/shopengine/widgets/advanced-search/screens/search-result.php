<?php 

if(empty($_GET['nonce']) || !wp_verify_nonce( sanitize_text_field(  wp_unslash( $_GET['nonce']) ), 'wp_rest')){
    return;
}

if( !empty($_GET['s'])) {

    $product_query = array(
        'orderby'        => 'date',
        'post_status'    => 'publish',
        'post_type'      => 'product',
        's'              => sanitize_text_field( wp_unslash($_GET['s']) ),
        'posts_per_page' => 30,
    );
    
    if( !empty( $_GET['product_cat'])  ) {

        $cats = explode(',', trim(sanitize_text_field( wp_unslash($_GET['product_cat'])), ','));

        $sanitized = array_map(function ($val){

            return intval($val);
        }, 
        $cats);

	    $product_query['tax_query'] = array(
		    array(
			    'taxonomy'      => 'product_cat',
			    'field'         => 'term_id',
			    'terms'         => $sanitized,
			    'operator'      => 'IN'
		    )
	    );
    }

    $products = new WP_Query($product_query);


    global $wpdb;

    $categories = $wpdb->get_results($wpdb->prepare("SELECT terms.* from {$wpdb->prefix}terms AS terms INNER JOIN {$wpdb->prefix}term_taxonomy AS term_taxonomy
    ON terms.term_id = term_taxonomy.term_id WHERE term_taxonomy.taxonomy = 'product_cat'
    AND terms.name LIKE %s LIMIT 5", '%'. sanitize_text_field( wp_unslash($_GET['s']) ) .'%'));
}

if( $products->have_posts() || !empty($categories)): ?>

<div class="shopengine-search-product">

    <?php foreach($categories as $category): ?>


        <div class="shopengine-category-name shopengine-search-product__item">
            <div class="shopengine-search-product__item--image">
                <img src="<?php
                $term_meta = get_term_meta($category->term_id);
                if(!empty($term_meta['thumbnail_id'][0])) {
                    $attachment = wp_get_attachment_image_src($term_meta['thumbnail_id'][0]);
                    echo isset($attachment[0]) ? esc_url($attachment[0]) : esc_url(wc_placeholder_img_src());
                } else {
                    echo esc_url(wc_placeholder_img_src());
                }

                ?>" alt="<?php echo isset($term_meta['thumbnail_id'][0]) ? esc_attr(trim( strip_tags( get_post_meta( $term_meta['thumbnail_id'][0], '_wp_attachment_image_alt', true ) ) ) ) : ""; ?>">
            </div>

            <?php $category_url = get_term_link($category->slug, 'product_cat'); ?>

            <div class="shopengine-search-product__item--content">
                <h4 class="shopengine-search-product__item--title">
                    <a title="<?php echo esc_html($category->name, 'shopengine');?>" href="<?php echo esc_url($category_url) ?>"> <?php echo esc_html($category->name) ?> </a>
                </h4>
            </div>
            
            <a title="<?php esc_html_e('Show Category','shopengine')?>" class="shopengine-search-more-btn" href="<?php echo esc_url($category_url); ?>"><i class="fas fa-chevron-right"></i></a>
        </div>


    <?php endforeach;?>
    
    <?php 
        while ( $products->have_posts() ) : $products->the_post(); 
        $id         = esc_attr( get_the_ID() );
        $image_url  = get_the_post_thumbnail_url( $id );
        $attachment_id = get_post_thumbnail_id($id);
        $alt        = trim( strip_tags( get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ) );
        $product    = wc_get_product( $id );
        $price      = wc_price($product->get_price());
        $reg_price  = wc_price($product->get_regular_price());
    ?>

        <div class="shopengine-search-product__item">
            <div class="shopengine-search-product__item--image">
                <img src="<?php echo esc_url( $image_url ) ?>" alt="<?php esc_attr_e($alt,'shopengine'); ?>">
            </div>
            <div class="shopengine-search-product__item--content">
                <h4 class="shopengine-search-product__item--title">
                    <a title="<?php esc_html_e('View Product Full Details','shopengine')?>" href="<?php the_permalink() ?>"> <?php the_title() ?> </a>
                </h4>
                <div class="shopengine-product-rating">
                    <?php
                        if($product->get_rating_count() > 0){
                            woocommerce_template_loop_rating();
                        } else {
                            $rating_html  = '<div class="star-rating">';
                            $rating_html .= wc_get_star_rating_html( 0, 0 );
                            $rating_html .= '</div>';

                            echo wp_kses_post($rating_html);
                        }

                        // review count
                        $review_count = $product->get_review_count();
                        echo "<span class='rating-count'>(". esc_html( $review_count ) .")</span>";
                    ?>
                </div>
                <div class="shopengine-search-product__item--price">
                    <?php echo wp_kses_post($product->get_price_html()); ?>
                </div>
            </div>
            <a title="<?php esc_html_e('Searching More Products','shopengine')?>" class="shopengine-search-more-btn" href="<?php echo esc_url(get_the_permalink()); ?>"><i class="fas fa-chevron-right"></i></a>
        </div>

    <?php 

        endwhile; 
        wp_reset_query();
        wp_reset_postdata();
    
    ?>

    <div class="shopengine-search-product__item">
        <a title="<?php esc_html_e('Searching a Products','shopengine')?>" class="shopengine-search-more-products" href="<?php echo esc_url( get_the_permalink(get_option('woocommerce_shop_page_id')) ); ?>"><i class="fas fa-retweet"></i><?php echo esc_html__('More Products', 'shopengine'); ?></a>
    </div>

</div>

<?php

else: ?>
    <div class="shopengine-search-product">
        <div>
            <?php echo esc_html__('No result found', 'shopengine'); ?>
        </div>
    </div>
<?php

endif;
