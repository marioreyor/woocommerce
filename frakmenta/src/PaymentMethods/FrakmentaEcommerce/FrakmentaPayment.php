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
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR
 * PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN
 * ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace Frakmenta\WooCommerce\PaymentMethods\FrakmentaEcommerce;

use Frakmenta\WooCommerce\PaymentMethods\Base\BasePaymentMethod;
use Frakmenta\WooCommerce\Services\APIService;
use Frakmenta\WooCommerce\Utils\FrakmentaCommonUtils;
use Frakmenta\WooCommerce\Utils\Logger;

class FrakmentaPayment extends BasePaymentMethod
{

    /**
     * @return string
     */
    public function get_payment_method_id(): string
    {
        return 'frakmenta';
    }

    /**
     * @return string
     */
    public function get_payment_method_code(): string
    {
        return 'FRAKMENTA';
    }

    /**
     * @return string
     */
    public function get_payment_method_type(): string
    {
        return ($this->get_option('direct', 'yes') === 'yes') ? 'direct' : 'redirect';
    }

    public function get_payment_method_title_checkout($minImport = 0, $maxImport = 0): string
    {
        $title = 'Frakmenta';

        global $woocommerce;
        $totalCart = $woocommerce->cart->total;

        if (floatval($totalCart)>0){
            if (floatval($totalCart)>floatval($minImport) && floatval($totalCart)<floatval($maxImport) && floatval($totalCart)>0)
                $title = 'Paga a plazos con frakmenta';
            elseif (floatval($totalCart)<floatval($minImport) && floatval($totalCart)>0){
                $value = floatval($minImport)-floatval($totalCart);
                $title = 'Te faltan '.$value.'€ para pagar a plazos con frakmenta';
            }
        }

        return $title;
    }

    public function get_payment_method_title(): string
    {
        return 'Frakmenta';
    }

    /**
     * @return string
     */
    public function get_payment_method_description(): string
    {
        $method_description = sprintf(
        /* translators: %2$: The payment method title */
            __('Financia las compras de tus clientes con frakmenta. <br />Consigue <a href="https://frakmenta.com" target="_blank">más información</a> en frakmenta.', 'Frakmenta'),
            'https://frakmenta.com',
            'Frakmenta'
        );
        return $method_description;
    }

    /**
     * @return boolean
     */
    public function has_fields(): bool
    {
        return ($this->get_option('direct', 'yes') === 'yes') ? true : false;
    }

    /**
     * @return array
     */
    public function add_form_fields(): array
    {
        $form_fields = parent::add_form_fields();
        $form_fields['direct'] = [
            'title' => __('Transaction Type', 'frakmenta'),
            /* translators: %1$: The payment method title */
            'label' => sprintf(__('Enable direct %1$s', 'frakmenta'), 'Frakmenta'),
            'type' => 'checkbox',
            'default' => 'yes',
            'desc_tip' => __('If enabled, additional information can be entered during WooCommerce checkout. If disabled, additional information will be requested on the Frakmenta payment page.', 'Frakmenta'),
        ];
        return $form_fields;
    }

    /**
     * @return array
     */
    public function get_checkout_fields_ids(): array
    {
        return ['salutation', 'birthday'];
    }

    /**
     * @return string
     */
    public function get_payment_method_icon(): string
    {
        return 'logo.png';
    }

    public function get_payment_mechant_limits(): array
    {
        $frakmenta_parameters = FrakmentaCommonUtils::get_frakmenta_current_parameters();

        if (!empty($frakmenta_parameters['FRAKMENTA_EXIST_ACCOUNT'])) {

            $data = [
                "apikey" => $frakmenta_parameters['FRAKMENTA_PUBLIC_KEY']
            ];

            $apiFrakmenta = new APIService();
            $response = $apiFrakmenta->connection($frakmenta_parameters['FRAKMENTA_URL'] . '/api/fk/v2/limits?', $data, 'GET');

            $limits = json_decode((string) $response);

            if ($limits->status != 'ok') {
                $max_import = 1000;
                $min_import = 59;
            } else {
                $max_import = $limits->data->max_import;
                $min_import = $limits->data->min_import;
            }
        }
        else {
            $max_import = 1000;
            $min_import = 59;
        }

        return ["max_import" => $max_import, "min_import" => $min_import];
    }

}
