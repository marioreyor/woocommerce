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
 * @package     Payments
 * @author      Sistemas Findirect <desarrollo-frakmenta@findirect.com>
 * @copyright   Copyright (c) Frakmenta, Findirect. (https://www.frakmenta.com)
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR
 * PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN
 * ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace Frakmenta\WooCommerce\PaymentMethods;

use Frakmenta\WooCommerce\Utils\Logger;
use Frakmenta\WooCommerce\PaymentMethods\FrakmentaEcommerce\FrakmentaPayment;
use Frakmenta\WooCommerce\Utils\FrakmentaCommonUtils;
use Frakmenta\WooCommerce\Services\OrderService;
use WC_Order;
use WP_REST_Request;
use WP_REST_Response;

/**
 * The payment methods controller.
 *
 * Defines all the functionalities needed to register the Payment Methods actions and filters
 *
 * @since   4.0.0
 */
class PaymentMethodsController {

    /**
     * Register the stylesheets related with the payment methods
     *
     * @see https://developer.wordpress.org/reference/functions/wp_enqueue_style/
     *
     * @return void
     */
    public function enqueue_styles(): void {
        if ( is_checkout() || is_product()) {
            wp_enqueue_style( 'frakmenta-commons-css', FRAKMENTA_PLUGIN_URL . '/assets/commons/css/frakmenta.css', array(), FRAKMENTA_PLUGIN_VERSION, 'all' );
            wp_enqueue_style( 'frakmenta-commons-css', FRAKMENTA_PLUGIN_URL . '/assets/commons/css/frakmenta_additionals.css', array(), FRAKMENTA_PLUGIN_VERSION, 'all' );
            wp_enqueue_style( 'frakmenta-commons-css', FRAKMENTA_PLUGIN_URL . '/assets/commons/css/frakmenta_style.css', array(), FRAKMENTA_PLUGIN_VERSION, 'all' );
            wp_enqueue_style( 'frakmenta-remote-widget-css', get_option('FRAKMENTA_URL').'/css/widget-ecommerce.css', array(), FRAKMENTA_PLUGIN_VERSION);
        }
    }

    private function get_product_price() : string {
        global $product;

        if ( ! is_a( $product, 'WC_Product' ) ) {
            $product = wc_get_product( get_the_id() );
        }

        $price = $product->is_on_sale()?$product->get_sale_price():$product->get_regular_price();
        return strval($price);
    }


    public function enqueue_scripts(): void {

        $frakmenta_params = array(
            "FRAKMENTA_URL" => get_option('FRAKMENTA_URL'),
            "FRAKMENTA_PUBLIC_KEY" => get_option('FRAKMENTA_PUBLIC_KEY'),
            "FRAKMENTA_LOGO" => FRAKMENTA_PLUGIN_URL . '/assets/commons/img/logo_frakmenta.png'
        );

        if (is_product() && !empty(get_option("FRAKMENTA_PRODUCT_OPTION"))) {

            $frakmenta_params["FRAKMENTA_PRODUCT_PRICE"] = $this->get_product_price();

            wp_register_script('frakmenta-product-local-js', FRAKMENTA_PLUGIN_URL . '/assets/products/js/frakmenta_front_products.js', array('jquery'));
            wp_localize_script('frakmenta-product-local-js', 'frakmentaParams', $frakmenta_params );
            wp_enqueue_script('frakmenta-product-local-js');

            wp_register_script('frakmenta-product-remote-js', get_option('FRAKMENTA_URL').'/js/widgetEcommerce.js', array( 'jquery' ), FRAKMENTA_PLUGIN_VERSION, false );
            wp_localize_script('frakmenta-product-remote-js', 'frakmentaParams', $frakmenta_params );
            wp_enqueue_script('frakmenta-product-remote-js');
        }

        if (is_checkout()) {
            wp_register_script('frakmenta-payment-remote-js', get_option('FRAKMENTA_URL').'/js/widgetEcommerce.js', array( 'jquery' ), FRAKMENTA_PLUGIN_VERSION, false );
            wp_localize_script('frakmenta-payment-remote-js', 'frakmentaParams', $frakmenta_params );
            wp_enqueue_script('frakmenta-payment-remote-js');

            wp_register_script('frakmenta-payment-local-js', FRAKMENTA_PLUGIN_URL . '/assets/payments/js/frakmenta_check_out.js', array('jquery'));
            wp_localize_script('frakmenta-payment-local-js', 'frakmentaParams', $frakmenta_params );
            wp_enqueue_script('frakmenta-payment-local-js');
        }

    }



    /**
     * Merge existing gateways and Frakmenta
     *
     * @param array $gateways
     * @return array
     */
    public static function set_gateway( array $gateways ): array {
        return array_merge( $gateways, ['frakmentaPayment' => FrakmentaPayment::class]);
    }

    /**
     * Filter the payment methods by the countries defined in their settings
     *
     * @param   array $payment_gateways
     * @return  array
     */
    public function filter_gateway_per_country( array $payment_gateways ): array {
        $customer_country = ( WC()->customer ) ? WC()->customer->get_billing_country() : false;
        foreach ( $payment_gateways as $gateway_id => $gateway ) {
            if ( ! empty( $gateway->countries ) && $customer_country && ! in_array( $customer_country, $gateway->countries, true ) ) {
                unset( $payment_gateways[ $gateway_id ] );
            }
        }
        return $payment_gateways;
    }

    /**
     * Filter used to get WooCommerce when go to thankyou page
     *
     * @param string $order_id The order number id received in callback thankyou page
     *
     * @return void
     */
    public function check_payment_frakmenta($order_id) : void
    {
        if (!empty($order_id)){
            $order = wc_get_order( $order_id );

            if ($order->get_payment_method()=='frakmenta'){
                $orderService = new OrderService();
                $result = $orderService->getStatusOperationFrakmenta($order);
                if ($result=='success'){
                    $transactionFrakmenta = FrakmentaCommonUtils::clean_operation_id_frakmenta($order->get_transaction_id());
                    require_once FRAKMENTA_PLUGIN_DIR_PATH . 'templates/fk_transaction_details.php';
                }
            }
        }
    }
}
