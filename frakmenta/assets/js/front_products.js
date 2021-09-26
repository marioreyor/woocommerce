let price, pricePlain, fkApiKey, fkApiUrl, fkEcommerceUrl;
let maxImport = 1000;
let minImport = 59;
let cant=1;

jQuery(document).ready(function () {
    iniciar_simulador_fk();
});

jQuery(document).on("click",".btn-touchspin", function () {
    cant = jQuery('#quantity_wanted').val()=='undefined'?1:jQuery('#quantity_wanted').val();
    pricePlain = getProductPriceByMeta();

    price = parseInt(pricePlain*100);
    jQuery('#fk-widget-installments').attr('data-product_price', price);
    /* simulator();*/
});

function product_simulator_pay(){
    cant = document.getElementsByName('quantity')=='undefined'?1:document.getElementsByName('quantity')[0].attributes[7].nodeValue;
    pricePlain = getProductPriceByMeta();
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
                    show_advertising_fk();
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
    return Number.parseFloat((tags[0].outerText.replace('€','')).replace(',','.')).toFixed(2);
}

function show_simulator(){
    jQuery("<div class='col-img-payment' style='margin-bottom:1em'><div class='fk-installments' id='fk-widget-installments' data-product_price='"+price+"'></div></div>").insertAfter('.cart');
}

function show_advertising_fk(){
    priceRest = Math.round((minImport - priceAbsolute) * 100) / 100;
    jQuery("<div id='frakmenta-advertising' style='margin-bottom:1em;margin-left:10px;margin-right:10px'>Si añades <strong>"+priceRest+"€</strong> puedes pagar a plazos con <img style='margin-top: -10px;width:80px' src='"+logoFrakmenta+"'></div>").insertAfter(fkLocationSimulador);
}