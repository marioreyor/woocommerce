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
function get_scripts_params_checkout_frakmenta(){
    fkApiKey = frakmentaParams.FRAKMENTA_PUBLIC_KEY;
    fkApiUrl = frakmentaParams.FRAKMENTA_URL;
    fkEcommerceUrl = frakmentaParams.FRAKMENTA_URL;
    logoFrakmenta = frakmentaParams.FRAKMENTA_LOGO;
}

function iniciar_simulador_checkout_frakmenta(){
    get_scripts_params_checkout_frakmenta();
}

jQuery(document).ready(function () {
    iniciar_simulador_checkout_frakmenta();
});

jQuery(function(){
    jQuery( 'body' )
        .on( 'updated_checkout', function() {
            usingGateway();
            jQuery('input[name="payment_method"]').change(function(){
                usingGateway();
            });
        });
});


function usingGateway(){
    if(jQuery('form[name="checkout"] input[name="payment_method"]:checked').val() == 'frakmenta'){
        simulator();
    }
}