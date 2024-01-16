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

namespace Frakmenta\WooCommerce\Services;

use WC_Order;
use Frakmenta\WooCommerce\Utils\Logger;
use Frakmenta\WooCommerce\Utils\FrakmentaCommonUtils;
use Frakmenta\WooCommerce\Services\APIService;
/**
 * Class OrderService
 *
 * @package Frakmenta\WooCommerce\Services
 */
class OrderService {


    /**
     * @var CustomerService
     */
    private $customer_service;

    /**
     * @var ShoppingCartService
     */
    private $shopping_cart_service;

    /**
     * @var connectApi
     */
    private $connectApi;

    /**
     * OrderService constructor.
     */
    public function __construct() {
        $this->connectApi = new APIService();
    }

    /**
     * @param string               $gateway_code
     * @param string               $type
     * @param GatewayInfoInterface $gateway_info
     * @return OrderRequest
     */
    public function create_order_frakmenta(WC_Order $order): string {

        $cart_products = [];

        foreach( $order->get_items() as $item_id => $item ) {

            $link = 'https://frakmenta.com/no-link';
            if ($item->get_type() == 'line_item') {
                $productDetail = wc_get_product($item->get_product_id());
                $link = $productDetail->get_permalink();
            }

            $cart_products[] = [
                'id' => (string)$item->get_product_id(),
                'name' => substr((string)$item->get_name(), 0, 900),
                'quantity' => $item->get_quantity(),
                'price' => number_format((float)$item->get_total(), 2, '.', ''),
                'tax_rate' => number_format((float)$item->get_total_tax(), 2, '.', ''),
                'description' => $item->get_name(),
                'url' => $link,
                'image_url' => $link
            ];
        }

        $customer = [
            'identification' => [
                'nif' => " ",
                'legal_first_name' => empty($order->get_billing_first_name()) ? ' ' : $order->get_billing_first_name(),
                'legal_last_name' => empty($order->get_billing_last_name()) ? ' ' : $order->get_billing_last_name(),
                'date_of_birth' => FrakmentaCommonUtils::get_current_date(),
                'mobile_phone_number' => empty($order->get_billing_phone()) ? ' ' : $order->get_billing_phone(),
                'email' => $order->get_billing_email()
            ],
            'address' => [
                'line_1' => $order->get_billing_address_1(),
                'line_2' => empty($order->get_billing_address_2()) ? " " : $order->get_billing_address_2(),
                'phone' => empty($order->get_billing_phone()) ? " " : $order->get_billing_phone(),
                'city' => $order->get_billing_city(),
                'state' => $order->get_billing_state(),
                'county' => ',',
                'country_code' => $order->get_billing_country(),
                'postcode' => $order->get_billing_postcode(),
            ],
            "store_details" => [
                "customer_date_joined" => FrakmentaCommonUtils::get_current_date(),
                "customer_last_login" => FrakmentaCommonUtils::get_current_date(),
            ],
            "financial" => [
                "salary" => 0,
                "currency" => "EUR",
                "employment_status" => "N/A",
                "contract_type" => "N/A"
            ],
            'other_data' => [
                ['name' => 'Tienda', 'type' => 'STRING', 'value' => get_bloginfo( 'name' )],
                ['name' => 'Ecommerce', 'type' => 'STRING', 'value' => 'WOOCOMMERCE'],
                ['name' => 'Version', 'type' => 'STRING', 'value' => FRAKMENTA_PLUGIN_VERSION],
                ['name' => 'Enviroment', 'type' => 'STRING', 'value' => get_option('FRAKMENTA_MODE')==0?'TEST':'PRODUCTION']
            ]
        ];

        $orderShop = [
            'id' => strval($order->get_id()),
            'products' => $cart_products
        ];

        $frakmenta_parameters = FrakmentaCommonUtils::get_frakmenta_current_parameters();

        $order_received_url = wc_get_endpoint_url( 'order-received', $order->get_id(), wc_get_checkout_url() );
        $order_received_url = add_query_arg( 'key', $order->get_order_key(), $order_received_url );

        $order_checkout_url = wc_get_checkout_url();

        $success_url = $order_received_url;
        $notification_url = 'https://frakmenta.com';

        $invoice_id = hash("sha256", ((string)($frakmenta_parameters['FRAKMENTA_MERCHANT_ID'] . '-' . $order->get_id() . '-' . date('YmdHis'))));
        $product_price = (float)(number_format((float)$order->get_total(), 2, '.', '') * 100);

        $flowConfig = [
            'success_url' => $success_url,
            'notification_url' => $notification_url,
            'ko_url' => $order_checkout_url,
        ];
        $data = [
            'merchant_id' => $frakmenta_parameters['FRAKMENTA_MERCHANT_ID'],
            'invoice_id' => $invoice_id,
            'product_price' => $product_price,
            'currency_code' => 'EUR',
            'delegation' => '1',
            'type' => 'e-commerce',
            'customer' => $customer,
            'order' => $orderShop,
            'flow_config' => $flowConfig,
            'other_data' => [['name' => 'N/A', 'type' => 'STRING', 'value' => 'N/A']]
        ];

        $signature = hash("sha256",
            $data["merchant_id"].'|'.
            $data["delegation"].'|'.
            $data["type"].'|'.
            $data["invoice_id"].'|'.
            $data["product_price"].'|'.
            $data["currency_code"].'|'.
            $frakmenta_parameters['FRAKMENTA_PRIVATE_KEY'],
            FALSE);

        $data['signature'] = $signature;

        $data = json_encode($data);

        $response = $this->connectApi->connection($frakmenta_parameters['FRAKMENTA_URL']."/api/fk/v2/operations", $data, 'POST');

        $response_frakmenta = json_decode((string) $response);

        if (strtoupper($response_frakmenta->status)!='OK'){
            Logger::log_error( 'Error en la operacion de frakmenta:' . json_encode($data));
        }
        $order->set_transaction_id('frakmenta-'.$response_frakmenta->data->operation_id);
        $order->save();

        return $frakmenta_parameters['FRAKMENTA_URL'].'/op/ecommerce/'.$response_frakmenta->data->token_url.'/load';
    }

    public function getStatusOperationFrakmenta(WC_Order $order) : string {

        $frakmenta_parameters = FrakmentaCommonUtils::get_frakmenta_current_parameters();
        $idOperation = FrakmentaCommonUtils::clean_operation_id_frakmenta($order->get_transaction_id());

        $signature = hash("sha256",
        $frakmenta_parameters['FRAKMENTA_MERCHANT_ID'].'|'.
        '1|'.
        'e-commerce'.'|'.
        $idOperation.'|'.
        $frakmenta_parameters['FRAKMENTA_PRIVATE_KEY'],
        FALSE);

        $data = json_encode([
            "merchant_id" => $frakmenta_parameters['FRAKMENTA_MERCHANT_ID'],
            "delegation" => 1,
            "type" => "e-commerce",
            "operation_id" => $idOperation,
            "signature" => $signature
        ], JSON_THROW_ON_ERROR);

        $response = $this->connectApi->connection($frakmenta_parameters['FRAKMENTA_URL'].'/api/fk/v2/operations/status', $data, 'POST');

        $response_frakmenta = json_decode((string) $response);

        if ($response_frakmenta->messages[0]=='La operación ha sido aceptada'){
            if ($order->get_status()=='pending'){
                $order->set_status('processing', 'Orden pagada por frakmenta');
                $order->reduce_order_stock();
                $order->save();
            }
            return 'success';
        }
        else
            return 'error';
    }
}
