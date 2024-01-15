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

use WC_Log_Handler_File;

/**
 * Class Logger
 *
 * @see https://tools.ietf.org/html/rfc5424#page-11
 *
 * @package Frakmenta\WooCommerce\Utils
 * @since    4.4.2
 */
class Logger {

    /**
     * Log method for emergency level
     * System is unusable.
     * Example:
     */
    public static function log_emergency( string $message ) {
        $logger = wc_get_logger();
        $logger->log( 'emergency', $message, ['source' => 'frakmenta'] );
    }

    /**
     * Log method for alert level
     * Action must be taken immediately.
     * Example: Entire website down, database unavailable, etc.
     */
    public static function log_alert( string $message ) {
        $logger = wc_get_logger();
        $logger->log( 'alert', $message, ['source' => 'frakmenta'] );
    }

    /**
     * Log method for critical level
     * Critical conditions.
     * Example: Unexpected exceptions.
     */
    public static function log_critical( string $message ) {
        $logger = wc_get_logger();
        $logger->log( 'critical', $message, ['source' => 'frakmenta'] );
    }

    /**
     * Log method for error level
     * Error conditions
     * Example: Set to shipped or invoiced an order, which is on initialized status
     */
    public static function log_error( string $message ) {
        $logger = wc_get_logger();
        $logger->log( 'error', $message, ['source' => 'frakmenta'] );
    }

    /**
     * Log method for warning level
     * Exceptional occurrences that are not errors because do not lead to a complete failure of the application.
     * Example: Entire website down, database unavailable, etc.
     */
    public static function log_warning( string $message ) {
        $logger = wc_get_logger();
        $logger->log( 'warning', $message, ['source' => 'frakmenta'] );
    }

    /**
     * Log method for notice level
     * Normal but significant events.
     * Example: A notification has been processed using a payment method different than the one registered when the order was created.
     */
    public static function log_notice( string $message ) {
        $logger = wc_get_logger();
        $logger->log( 'notice', $message, ['source' => 'frakmenta'] );
    }

    /**
     * Log method for info level
     * Interesting events.
     * Example: The payment link of a transaction
     */
    public static function log_info( string $message ) {
        if ( get_option( 'Frakmenta_debugmode', false ) ) {
            $logger = wc_get_logger();
            $logger->log( 'info', $message, ['source' => 'frakmenta'] );
        }
    }

    /**
     * Log method for debug level
     * Detailed debug information: Denotes specific and detailed information of every action.
     * Example: The trace of every action registered in the system.
     */
    public static function log_debug( string $message ) {
        if ( get_option( 'Frakmenta_debugmode', false ) ) {
            $logger = wc_get_logger();
            $logger->log( 'debug', $message, ['source' => 'frakmenta'] );
        }
    }

    /**
     * Return an array of logs filenames
     *
     * @return array
     */
    private function get_logs(): array {
        $logs = WC_Log_Handler_File::get_log_files();
        return $logs;
    }

    /**
     * Return an array of logs that belongs to Frakmenta
     *
     * @return array
     */
    public function get_Frakmenta_logs(): array {
        $logs = $this->get_logs();
        foreach ( $logs as $key => $log ) {
            if ( !str_contains( $log, 'frakmenta' ) ) {
                unset( $logs[ $key ] );
            }
        }
        return $logs;
    }

}
