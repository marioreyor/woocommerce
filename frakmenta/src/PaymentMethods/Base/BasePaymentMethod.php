<?php declare( strict_types=1 );
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
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR
 * PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN
 * ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
namespace Frakmenta\WooCommerce\PaymentMethods\Base;

use Exception;
use Frakmenta\WooCommerce\Services\OrderService;
use Frakmenta\WooCommerce\Utils\Logger;
use WC_Countries;
use WC_Gateway_COD;
use WC_Payment_Gateway;
use WP_Error;


abstract class BasePaymentMethod extends WC_Payment_Gateway implements PaymentMethodInterface {

    /**
     * What type of transaction, should be 'direct' or 'redirect'
     *
     * @var string
     */
    protected $type;

    /**
     * An array with the keys of the required custom fields
     *
     * @var array
     */
    protected $checkout_fields_ids;

    /**
     * The minimun amount for the payment method
     *
     * @var string
     */
    public $min_amount;

    /**
     * A custom initialized order status for this payment method
     *
     * @var string
     */
    public $initial_order_status;

    /**
     * Frakmenta limits merchant
     *
     * @var string
     */
    public $frakmentaMerchantLimits;


    /**
     * Construct for Core class.
     */
    public function __construct() {
        $this->frakmentaMerchantLimits = $this->get_payment_mechant_limits();
        $this->max_amount          = $this->frakmentaMerchantLimits['max_import'];
        $this->min_amount          = $this->frakmentaMerchantLimits['min_import'];
        $this->supports            = ['products'];
        $this->id                  = $this->get_payment_method_id();
        $this->type                = $this->get_payment_method_type();
        $this->method_title        = $this->get_payment_method_title();
        if (is_checkout()) {
            $this->method_title    = $this->get_payment_method_title_checkout(
                (float)$this->frakmentaMerchantLimits['min_import'],
                (float)$this->frakmentaMerchantLimits['max_import']
            );
        }

        $this->method_description  = $this->get_payment_method_description();
        $this->gateway_code        = $this->get_payment_method_code();
        $this->has_fields          = $this->has_fields();
        $this->checkout_fields_ids = $this->get_checkout_fields_ids();
        $this->icon                = $this->get_logo();
        $this->form_fields         = $this->add_form_fields();
        error_log("BasePaymentMethod Constructor" );
        $this->init_form_fields();
        $this->init_settings();

        $this->enabled              = $this->get_option( 'enabled', 'no' );
        $this->title                = $this->get_payment_method_title();
        if (is_checkout()) {
            $this->title            = $this->get_payment_method_title_checkout(
                (float)$this->frakmentaMerchantLimits['min_import'],
                (float)$this->frakmentaMerchantLimits['max_import']
            );
        }

        $this->description          = $this->get_option( 'description' );
        $this->countries            = $this->get_option( 'countries' );
        $this->initial_order_status = $this->get_option( 'initial_order_status', false );
        $this->errors               = [];

        add_action(
            'woocommerce_update_options_payment_gateways_' . $this->id,
            [$this, 'process_admin_options']
        );

        add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, [$this, 'display_errors']);
    }

    /**
     * Return the full path of the (locale) logo
     *
     * @return string
     */
    private function get_logo(): string {
        $language = substr( (string) get_locale(), 0, 2 );

        $icon = $this->get_payment_method_icon();

        $icon_locale = substr_replace( $icon, "-$language", - 4, - 4 );
        if ( file_exists( FRAKMENTA_PLUGIN_URL . 'assets/commons/img/' . $icon_locale ) ) {
            $icon = $icon_locale;
        }

        return esc_url( FRAKMENTA_PLUGIN_URL . '/assets/commons/img/' . $icon );
    }

    /**
     * Return an array of allowed countries defined in WooCommerce Settings.
     *
     * @return array
     */
    private function get_countries(): array {
        $countries = new WC_Countries();
        return $countries->get_allowed_countries();
    }

    /**
     * Return if payment methods requires custom checkout fields
     *
     * @return boolean
     */
    public function has_fields(): bool {
        return false;
    }

    /**
     * Return the custom checkout fields id`s
     *
     * @return array
     */
    public function get_checkout_fields_ids(): array {
        return [];
    }

    /**
     * Define the form option - settings fields.
     *
     * @return  array
     */
    public function add_form_fields(): array {


        return [
            'enabled'              => [
                'title'   => __( 'Enable/Disable', 'Frakmenta' ),
                'label'   => 'Enable ' . $this->get_method_title() . ' Gateway',
                'type'    => 'checkbox',
                'default' => 'no',
            ],
            'title'                => [
                'title'    => __( 'Title', 'Frakmenta' ),
                'type'     => 'text',
                'desc_tip' => __( 'This controls the title which the user sees during checkout.', 'frakmenta' ),
                'default'  => $this->get_method_title(),
            ],
            'initial_order_status' => [
                'title'    => __( 'Initial Order Status', 'Frakmenta' ),
                'type'     => 'select',
                'options'  => $this->get_order_statuses(),
                'desc_tip' => __( 'Initial order status for this payment method.', 'Frakmenta' ),
                'default'  => 'wc-default',
            ],
            'countries'            => [
                'title'       => __( 'Pais', 'Frakmenta' ),
                'type'        => 'multiselect',
                'description' => __( 'If you select one or more countries, this payment method will be shown in the checkout page, if the payment address`s country of the customer match with the selected values. Leave blank for no restrictions.', 'frakmenta' ),
                'desc_tip'    => __( 'For most operating system and configurations, you must hold Ctrl or Cmd in your keyboard, while you click in the options to select more than one value.', 'frakmenta' ),
                'options'     => $this->get_countries(),
                'default'     => $this->get_option( 'countries', [] ),
            ]
        ];    }

    /**
     * Process the payment and return the result.
     *
     * @param integer $order_id Order ID.
     *
     * @return  array|mixed|void
     */
    public function process_payment( $order_id ) {

        $order_service       = new OrderService();
        $order         = wc_get_order( $order_id );
        $paymentUrl = $order_service->create_order_frakmenta($order);

        return ['result'   => 'success', 'redirect' => esc_url_raw($paymentUrl)];
    }

    /**
     * Prints checkout custom fields
     *
     * @return  mixed
     */
    public function payment_fields() {
        global $woocommerce;
        $realTotalCart = $woocommerce->cart->total;
        $totalCart = $realTotalCart * 100;
        error_log("payment_fields");
        if ($realTotalCart >= $this->min_amount && $realTotalCart <= $this->max_amount)
            echo "<input type='hidden' name='validate_payfrakmenta' value='1' required readonly>";
        else
            echo "<input type='hidden' name='validate_payfrakmenta' required readonly>";

        echo "<div class='col-img-payment' style='margin-bottom:1em'><div class='fk-installments' id='fk-widget-installments' data-product_price='$totalCart'></div></div>";
    }

    /**
     * Validate_fields
     *
     * @return  boolean
     */
    public function validate_fields(): bool {
        if ( isset( $_POST['validate_payfrakmenta' ] ) && '' === $_POST['validate_payfrakmenta' ] ) {
            wc_add_notice( __( 'Lo sentimos tú transacción no cumple con los requisitos para ser financiada por frakmenta', 'frakmenta' ), 'error' );
        }

        if ( wc_get_notices( 'error' ) ) {
            return false;
        }

        return true;
    }

    /**
     * Returns the WooCommerce registered order statuses
     *
     * @see     http://hookr.io/functions/wc_get_order_statuses/
     *
     * @return  array
     */
    private function get_order_statuses(): array {
        $order_statuses               = wc_get_order_statuses();
        $order_statuses['wc-default'] = __( 'Default value set in common settings', 'frakmenta' );
        return $order_statuses;
    }
}
