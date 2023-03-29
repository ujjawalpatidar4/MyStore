<?php

namespace ShopEngine\Base;

defined('ABSPATH') || exit;

use ShopEngine\Widgets\Products;

abstract class Widget extends \Elementor\Widget_Base {

	abstract public function config();

	abstract protected function screen();

	public function get_help_url() {
		return 'https://wpmet.com/knowledgebase/shopengine/?ref__widget=' . $this->config()->get_name();
	}

	public function show_in_panel() {

		$territory = $this->config()->get_template_territory();

		if(empty($territory)) {
			return true;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- It's a fronted user part, not possible to verify nonce here
		$current_template_id = (int)((isset($_GET['action']) && sanitize_text_field(wp_unslash($_GET['action'])) == 'elementor' && !empty($_GET['post']) ) ? sanitize_text_field(wp_unslash($_GET['post'])) : get_the_ID());

		$current_template = Products::instance()->get_template_type_by_id($current_template_id);

		return in_array($current_template, $territory);
	}

	public function shopengine_widget_before_render() {
		//todo - remove shopengine later

		echo '<div class="shopengine shopengine-widget">';
	}

	public function shopengine_widget_after_render() {
		echo '</div>';
	}

	public function render() {
		$this->shopengine_widget_before_render();
		if( !is_null(WC()->cart) ) {
			$this->screen();
		}
		$this->shopengine_widget_after_render();
	}

	public function get_name() {
		return 'shopengine-' . $this->config()->get_name();
	}

	public function get_title() {
		return $this->config()->get_title();
	}

	public function get_icon() {
		return $this->config()->get_icon();
	}

	public function get_categories() {
		return $this->config()->get_categories();
	}

	public function get_keywords() {
		return $this->config()->get_keywords();
	}
}
