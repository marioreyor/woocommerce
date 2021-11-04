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

namespace Frakmenta\WooCommerce\Utils;

class FrakmentaCommonUtils
{
    public static function get_current_date(){
        $defaultTimeZone='UTC';
        return date('Y-m-d');

    }

    public static function get_frakmenta_current_parameters(){
        return array(
            "FRAKMENTA_DELEGATION" => get_option('FRAKMENTA_DELEGATION'),
            "FRAKMENTA_EXIST_ACCOUNT" => get_option('FRAKMENTA_EXIST_ACCOUNT'),
            "FRAKMENTA_URL" => get_option('FRAKMENTA_URL'),
            "FRAKMENTA_PUBLIC_KEY" => get_option('FRAKMENTA_PUBLIC_KEY'),
            "FRAKMENTA_MERCHANT_ID" => get_option('FRAKMENTA_MERCHANT_ID'),
            "FRAKMENTA_MODE" => get_option('FRAKMENTA_MODE'),
            "FRAKMENTA_PRODUCT_OPTION" => get_option('FRAKMENTA_PRODUCT_OPTION'),
            "LOCATION_SIMULATOR_DEFAULT" => get_option('LOCATION_SIMULATOR_DEFAULT'),
            "FRAKMENTA_PRIVATE_KEY" => get_option('FRAKMENTA_PRIVATE_KEY')
        );
    }

    public static function clean_operation_id_frakmenta($id){
        return substr($id, 10);
    }
}