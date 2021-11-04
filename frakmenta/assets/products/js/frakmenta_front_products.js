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

let price, pricePlain, fkApiKey, fkApiUrl, fkEcommerceUrl;
let maxImport = 1000;
let minImport = 59;
let cant=1;

jQuery(document).ready(function () {
    iniciar_simulador_fk();
});

jQuery(document).on("click",".input-text.qty.text", function () {
    cant = jQuery('.input-text.qty.text').val()=='undefined'?1:jQuery('.input-text.qty.text').val();
    priceAbsolute = parseFloat(getProductPriceByMeta());
    pricePlain = priceAbsolute.toFixed(2);
    priceWithQuantity = pricePlain*cant;
    price = parseInt(pricePlain*100)*cant;
    jQuery('#fk-widget-installments').attr('data-product_price', price);
    jQuery('.frakmenta-advertising').remove();

    simulator();

    if (priceWithQuantity<minImport)
        show_advertising_fk();
    else if (priceWithQuantity>maxImport)
        show_exced_maxAmount_fk();
});

function product_simulator_pay(){
    if (productPriceFK==0)
        priceAbsolute = parseFloat(getProductPriceByMeta());
    else
        priceAbsolute = parseFloat(productPriceFK);

    cant = document.getElementsByName('quantity')=='undefined'?1:document.getElementsByName('quantity')[0].attributes[7].nodeValue;
    pricePlain = priceAbsolute.toFixed(2);
    price = parseInt(pricePlain*100);
    if (price>0){
        get_commerce_limits();
    }
}


function iniciar_simulador_fk(){
    get_scripts_params();
    product_simulator_pay();
}

function get_scripts_params(){
    fkApiKey = frakmentaParams.FRAKMENTA_PUBLIC_KEY;
    fkApiUrl = frakmentaParams.FRAKMENTA_URL;
    fkEcommerceUrl = frakmentaParams.FRAKMENTA_URL;
    logoFrakmenta = frakmentaParams.FRAKMENTA_LOGO;
    productPriceFK = frakmentaParams.FRAKMENTA_PRODUCT_PRICE;
}

function get_commerce_limits(){
    merchantLimits = jQuery.getJSON(fkApiUrl + "/api/fk/v2/limits?apikey="+fkApiKey, function() {
          })
            .done(function(data,status,xhr) {
                maxImport = data.data.max_import;
                minImport = data.data.min_import;

                if (pricePlain<maxImport && pricePlain>minImport) {
                    show_simulator();
                    simulator();
                } else {
                    console.log(pricePlain, 'no cubre');
                    if (pricePlain<minImport)
                        show_advertising_fk();
                    else
                        show_exced_maxAmount_fk();
                }
            })
            .fail(function(data) {
                if (pricePlain<maxImport && pricePlain>minImport) {
                    show_simulator();
                    simulator();
                }
            });
}

function getProductPriceByMeta(){
    let tags = jQuery('p.price').children('.woocommerce-Price-amount.amount').children('bdi');
    if (tags.length==0) {
        tags = jQuery('.woocommerce-Price-amount').children('bdi');
        price = Number.parseFloat((tags[tags.length-1].outerText.replace('€','')).replace(',','.')).toFixed(2);
    } else {
        price = Number.parseFloat((tags[0].outerText.replace('€','')).replace(',','.')).toFixed(2);
    }
    return price;
}

function show_simulator(){
    jQuery("<div class='col-img-payment' style='margin-bottom:1em'><div class='fk-installments' id='fk-widget-installments' data-product_price='"+price+"'></div></div>").insertAfter('.cart');
}

function show_advertising_fk(){
    priceRest = Math.round((minImport - priceAbsolute) * 100) / 100;
    jQuery("<div class='frakmenta-advertising'>Si añades <strong>"+priceRest+"€</strong> puedes pagar a plazos con <img class='logo-advertising' src='"+logoFrakmenta+"'></div>").insertAfter(".col-img-payment");
}

function show_exced_maxAmount_fk(){
    jQuery("<div class='frakmenta-advertising'>Financia con <img class='logo-advertising' src='"+logoFrakmenta+"'> hasta 1.000€</div>").insertAfter(".col-img-payment");
}