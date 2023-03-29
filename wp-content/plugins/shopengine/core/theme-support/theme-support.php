<?php

namespace ShopEngine\Core\Theme_Support;

use ShopEngine\Core\Builders\Action;
use ShopEngine\Core\Register\Module_List;
use ShopEngine\Modules\Comparison\Comparison_Cookie;
use ShopEngine\Modules\Wishlist\Wishlist;

defined('ABSPATH') || exit;

class Theme_Support {

    public static function get_module_list() {
        return Module_List::instance()->get_list();
    }

    public static function get_wishlist_product_ids() {

        if (is_user_logged_in()) {

            return get_user_meta(get_current_user_id(), Wishlist::UMK_WISHLIST, true);
        }

        if (empty($_COOKIE[Wishlist::COOKIE_KEY])) {
            return false;
        }

        return explode(',', sanitize_text_field(wp_unslash($_COOKIE[Wishlist::COOKIE_KEY])));
    }

    public static function get_comparison_product_ids() {
        return Comparison_Cookie::get_product_ids();
    }

    public static function template_list() {

        return [
            'shop',
            'archive',
            'single',
            'cart',
            'checkout',
            'order',
            'my_account_login',
            'my_account',
            'account_orders',
            'account_downloads',
            'account_orders_view',
            'account_edit_account',
            'account_edit_address',
            'quick_checkout',
            'quick_view'
        ];
    }
}
