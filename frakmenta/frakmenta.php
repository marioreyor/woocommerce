<?php # -*- coding: utf-8 -*-
// phpcs:disable

/**
 * Plugin Name: Frakmenta ecommerce
 * Description: Pago con frakmenta de compras en comercios
 * Author: Sistemas findirect
 * Author URI: jose_baez@findirect.com
 * Version: 1.0.0
 * WC requires at least: 3.6.4
 * WC tested up to: 4.1
 * License: GPLv2+
 * Text Domain: woo-frakmenta
 * Domain Path: /languages/
 */

require_once plugin_dir_path(__FILE__) . 'includes/frakmenta_functions.php';
require_once plugin_dir_path(__FILE__) . 'includes/fk_payment.php';

function admin_fk_plugin() {
    include('views/admin/fk_admin_page.php');
}

function fk_admin_parameters(){
    //function in plugin file with return value,
    //  custom fetch query
    frakmenta_default_configuration();
    return json_encode(array(
        "FRAKMENTA_DELEGATION" => get_option('FRAKMENTA_DELEGATION'),
        "FRAKMENTA_EXIST_ACCOUNT" => get_option('FRAKMENTA_EXIST_ACCOUNT'),
        "FRAKMENTA_URL" => get_option('FRAKMENTA_URL'),
        "FRAKMENTA_PUBLIC_KEY" => get_option('FRAKMENTA_PUBLIC_KEY'),
        "FRAKMENTA_MERCHANT_ID" => get_option('FRAKMENTA_MERCHANT_ID'),
        "FRAKMENTA_MODE" => get_option('FRAKMENTA_TEST_MODE'),
        "FRAKMENTA_PRODUCT_OPTION" => get_option('FRAKMENTA_PRODUCT_OPTION'),
        "LOCATION_SIMULATOR_DEFAULT" => get_option('LOCATION_SIMULATOR_DEFAULT')
    ));
}
//
//
//namespace WCFrakmenta;
//
//use Closure;
//
//$bootstrap = Closure::bind(
//    function () {
//
//        /**
//         * @return bool
//         */
//        function autoload()
//        {
//            $autoloader = __DIR__ . '/vendor/autoload.php';
//            if (file_exists($autoloader)) {
//                /** @noinspection PhpIncludeInspection */
//                require $autoloader;
//
//                require_once __DIR__ . '/src/inc/functions.php';
//            }
//
//            return class_exists(Frakmenta::class);
//        }
//
//        if (!autoload()) {
//            return;
//        }
//
//        $bootstrapper = new Bootstrapper(resolve(), __FILE__);
//
//        add_action('plugins_loaded', [$bootstrapper, 'bootstrap'], 0);
//        add_action('init', function () {
//            load_plugin_textdomain('woo-frakmenta');
//        });
//    },
//    null
//);
//
//$bootstrap();
