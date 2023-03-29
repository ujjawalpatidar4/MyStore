<?php

namespace ShopEngine\Modules\Comparison;

use ShopEngine\Base\Api;
use ShopEngine\Utils\Helper;

class Route extends Api {

	public function config() {

		$this->prefix = 'comparison';
		$this->param  = "";
	}


	public function get_comparison_table() {
		$data       = $this->request->get_params();
		$product_id = !empty($data['pid']) ? $data['pid'] : "";

		if( $product_id ) Comparison_Cookie::add_product_id($product_id);
		$product_ids = Comparison_Cookie::get_product_ids($product_id);

		Comparison_Helper::get_html($product_ids);
		exit();
	}

	public function post_remove() {

		$data = $this->request->get_params();
		$pid  = $data['pid'];

		Comparison_Cookie::remove_product_id($pid);

		$product_ids = Comparison_Cookie::get_product_ids();
		if (($key = array_search($pid, $product_ids)) !== false) {
			unset($product_ids[$key]);
		}
		$share_url = home_url( '?comparison=yes&product_ids=' . implode( ',', $product_ids ) );

		wp_send_json([
			"status" => "Success",
			"share_url" => $product_ids ? $share_url : home_url( '?comparison=yes' ),
		]);
	}



	public function get_comparison_bar_data() {

		$product_ids =  Comparison_Cookie::get_product_ids();

		$content = '';
		if ( empty( $product_ids ) ) {
			$content .=  '<h1 class="shopengine-no-comparison-product-for-bar">' . esc_html__( 'No product is added for comparison, please add some product to compare',
					'shopengine' ) . '</h1>';
			return $content;
		}

		$title = esc_html__("Remove Product For Comparison", "shopengine");

		$args = [
			'post_type' => 'product',
			'post__in'  => $product_ids
		];

		$productQuery = new \WP_Query($args);

		if ($productQuery->have_posts()) {
			while ($productQuery->have_posts()) {
				$productQuery->the_post();
				$product = wc_get_product(get_the_ID());
				$content .= '<div class="comparison-for-bottom-bar-item">
				<a title="' . $title . '" class="shopengine-comparison-bar-action badge-comparison" data-pid="'.esc_attr( get_the_ID() ).'"><i class="eicon-close"></i></a>
				'.$product->get_image('woocommerce_thumbnail').'</div>';
			}
		}

		return $content;
	}



	public function get_attributes() {
	    global $wpdb;

        $attributes = $wpdb->get_results("SELECT  * FROM {$wpdb->prefix}woocommerce_attribute_taxonomies");

        $formatted_attributes = [];
        foreach ($attributes as $attribute){
            $formatted_attributes[$attribute->attribute_name] = $attribute->attribute_label;
        }

        wp_send_json([
	        "status" => "success",
	        "result" => $formatted_attributes,
	        "message" =>  "Available attributes"
        ] );
	}


	public function get_custom_meta() {

        wp_send_json([
	        "status" => "success",
	        "result" => Comparison_Helper::get_products_meta_keys(),
	        "message" =>  "Products Meta keys"
        ] );
	}



}
