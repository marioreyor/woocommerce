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

namespace Frakmenta\WooCommerce\Exceptions;

/**
 * Class MissingDependencyException
 *
 * @package Frakmenta\WooCommerce\Exceptions
 */
class MissingDependencyException extends \Exception {

    /**
     * The missing required plugins names
     *
     * @var array
     */
    private $missing_plugin_names;

    /**
     * MissingDependencyException constructor.
     *
     * @param array $missing_plugins
     */
    public function __construct( array $missing_plugins ) {
        parent::__construct();
        $this->missing_plugin_names = $missing_plugins;
    }

    /**
     * Get the list of all missing plugins
     *
     * @return array
     */
    public function get_missing_plugin_names(): array {
        return $this->missing_plugin_names;
    }

}
