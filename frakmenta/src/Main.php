<?php declare(strict_types=1);

/**
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the Frakmenta plugin
 * to newer versions in the future. If you wish to customize the plugin for your
 * needs please document your changes and make backups before you update.
 *
 * @category    Frakmenta
 * @package     Connect
 * @author      Sistemas Findirect <desarrollo-frakmenta@findirect.com>
 * @copyright   Copyright (c) Frakmenta, Findirect. (https://www.frakmenta.com)
 *
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR
 * PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN
 * ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace Frakmenta\WooCommerce;

use Frakmenta\WooCommerce\PaymentMethods\PaymentMethodsController;
use Frakmenta\WooCommerce\Settings\SettingsController;
use Frakmenta\WooCommerce\Utils\Loader;

/**
 * This class is the core of the plugin.
 *
 * Is used to define internationalization, settings hooks, and
 * public face site hooks.
 *
 * @since      4.0.0
 */
class Main {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @var Loader Maintains and registers all hooks for the plugin.
     */
    private $loader;

    /**
     * Define the core functionality of the plugin.
     *
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public face of the site.
     */
    public function __construct() {
        $this->loader = new Loader();
        $this->define_settings_hooks();
        $this->define_payment_methods_hooks();
    }

    /**
     * Register all of the hooks related to the common settings
     * of the plugin.
     *
     * @return void
     */
    private function define_settings_hooks(): void {
        // Settings controller
        $plugin_settings = new SettingsController();

        if ( is_admin() ) {
            // Enqueue styles in controller settings page
            $this->loader->add_action( 'admin_enqueue_scripts', $plugin_settings, 'enqueue_styles', 1 );
            // Enqueue scripts in controller settings page
            $this->loader->add_action( 'admin_enqueue_scripts', $plugin_settings, 'enqueue_scripts' );
            // Add menu page for common settings page
            $this->loader->add_action( 'admin_menu', $plugin_settings, 'register_common_settings_page', 60 );
            // Add the new settings page the WooCommerce screen options
            $this->loader->add_filter( 'woocommerce_screen_ids', $plugin_settings, 'set_wc_screen_options_in_common_settings_page' );
            // Process change of configuration
            $this->loader->add_action( 'admin_post_fk_form_response', $plugin_settings, 'process_admin_form');
            // Get default configuration frakmenta parameters
            $this->loader->add_action('init', $plugin_settings, 'default_values_plugin', 1);
        }
    }


    /**
     * Register all of the hooks related to the payment methods
     * of the plugin.
     *
     * @return  void
     */
    private function define_payment_methods_hooks(): void {
        // Payment controller
        $payment_methods = new PaymentMethodsController();
        // Enqueue styles in payment methods
        $this->loader->add_action( 'wp_enqueue_scripts', $payment_methods, 'enqueue_styles' );
        // Enqueue scripts in payment methods
        $this->loader->add_action( 'wp_enqueue_scripts', $payment_methods, 'enqueue_scripts' );
        // Register frakmenta payment gateways in WooCommerce.
        $this->loader->add_filter( 'woocommerce_payment_gateways', $payment_methods, 'set_gateway' );
        // Filter per country
        $this->loader->add_filter( 'woocommerce_available_payment_gateways', $payment_methods, 'filter_gateway_per_country', 11 );
        // CheckPayment frakmenta
        $this->loader->add_action( 'woocommerce_thankyou', $payment_methods, 'check_payment_frakmenta', 10, 1);
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @return  void
     */
    public function init() {
        $this->loader->init();
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @return Loader Orchestrates the hooks of the plugin.
     */
    public function get_loader(): Loader {
        return $this->loader;
    }
}
