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
 * @author      Findirect <desarrollo-frakmenta@findirect.com>
 * @copyright   Copyright (c) Frakmenta, Findirect. (https://www.frakmenta.com)
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR
 * PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN
 * ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 *
 * Plugin Name:             Frakmenta
 * Plugin URI:              https://frakmenta.com/desarrolladores
 * Description:             Frakmenta Payment Plugin
 * Version:                 1.1.5
 * Author:                  Findirect
 * Author URI:              https://www.frakmenta.com
 * Copyright:               Copyright (c) Frakmenta, Findirect. (https://www.frakmenta.com)
 * License:                 GNU General Public License v3.0
 * License URI:             http://www.gnu.org/licenses/gpl-3.0.html
 * Requires at least:       5.0
 * Tested up to:            5.8.0
 * WC requires at least:    4.2.0
 * WC tested up to:         5.6.0
 * Requires PHP:            7.2
 * Text Domain:             frakmenta
 * Domain Path:             /languages
 */

if (!defined('ABSPATH')) {
    die();
}

/**
 * Plugin version
 */
define('FRAKMENTA_PLUGIN_VERSION', '1.1.5');

/**
 * Plugin URL
 * Do not include a trailing slash. Should be include it in the string with which is concatenated
 */
define('FRAKMENTA_PLUGIN_URL', plugins_url('', __FILE__));

/**
 * Plugin dir path
 * Include a trailing slash. Should not be include it in the string with which is concatenated
 */
define('FRAKMENTA_PLUGIN_DIR_PATH', plugin_dir_path(__FILE__));

/**
 * Composer's autoload file.
 */
require_once FRAKMENTA_PLUGIN_DIR_PATH . 'vendor/autoload.php';

use Frakmenta\WooCommerce\Utils\Activator;
use Frakmenta\WooCommerce\Main;

/**
 * The code that runs during plugin activation.
 * The class is documented in src/utils/Activator.php
 *
 * @param bool $network_wide
 * @see     https://developer.wordpress.org/reference/functions/register_activation_hook/
 *
 * @since   4.0.0
 */
function activate_frakmenta(bool $network_wide): void
{
    $activator = new Activator();
    $activator->activate($network_wide);
}

register_activation_hook(__FILE__, 'activate_frakmenta');

/**
 * Init plugin
 *
 * @since    4.0.0
 * @see      https://developer.wordpress.org/plugins/hooks/
 */
function init_frakmenta()
{
    if (!function_exists('is_plugin_active')) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
    if (is_plugin_active('woocommerce/woocommerce.php')) {
        $plugin = new Main();
        $plugin->init();
    }
}

init_frakmenta();


