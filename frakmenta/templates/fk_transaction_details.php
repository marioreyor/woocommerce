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
?>
<section class="woocommerce-order-details">
    <h2 class="woocommerce-order-details__title">Transacción frakmenta</h2>
    <table class="woocommerce-table woocommerce-table--order-details shop_table order_details">
        <tbody>
        <tr>
            <th class="woocommerce-table__product-name product-name">Número de operación frakmenta</th>
            <td class="woocommerce-table__product-table product-total"><?=$transactionFrakmenta?></td>
        </tr>
        </tbody>
    </table>
</section>