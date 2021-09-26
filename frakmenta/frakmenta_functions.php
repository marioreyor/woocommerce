<?php

/*
 * Add my new menu to the Admin Control Panel
 */
// Hook the 'admin_menu' action hook, run the function named 'mfp_Add_My_Admin_Link()'

// Add a new top level menu link to the ACP
add_action( 'init', 'frakmenta_default_configuration');
add_action( 'admin_post_fk_form_response', 'process_admin_form');
add_action( 'admin_menu', 'frakmenta_admin_link' );
add_action( 'admin_enqueue_scripts', 'frakmenta_styles');
add_action( 'admin_notices', 'frakmenta_update_parameters_notice');
add_shortcode('fk_admin_parameters', 'fk_admin_parameters');
add_action( 'woocommerce_before_single_product', 'frakmenta_product_option', 10 );
add_filter( 'woocommerce_payment_gateways', 'frakmenta_add_gateway_class' );
add_action( 'plugins_loaded', 'misha_init_gateway_class' );

function frakmenta_add_gateway_class( $gateways ) {
    $gateways[] = 'WC_Misha_Gateway'; // your class name is here
    return $gateways;
}

function frakmenta_admin_link()
{
    add_menu_page('Frakmenta', 'Configuración de frakmenta', 'manage_options', 'menu_config_fk', 'admin_fk_plugin');
}

function frakmenta_styles() {
    $myCssFileSrc = plugins_url( '/assets/css/frakmenta.css', __FILE__ );
    $myJssFileSrc = plugins_url( '/assets/js/admin.js', __FILE__ , array('jquery'));

    wp_enqueue_style( 'fk-admin-style', $myCssFileSrc );
    wp_enqueue_script('fk-admin', $myJssFileSrc);
}

function frakmenta_default_configuration(){
    if (empty(get_option('FRAKMENTA_EXIST_ACCOUNT'))) {
        update_option('FRAKMENTA_DELEGATION', 1);
        update_option('FRAKMENTA_EXIST_ACCOUNT', 0);
        update_option('FRAKMENTA_LOCATION_SIMULATOR', '.product_attributes');
        update_option('FRAKMENTA_PRODUCT_OPTION', 0);
    }
}

function process_admin_form(){

    if (!empty($_POST['submitButton'])){
        update_option('FRAKMENTA_EXIST_ACCOUNT', $_POST['fk_account']);
        update_option('FRAKMENTA_PRODUCT_OPTION', $_POST['fk_sim_product']);
        update_option('FRAKMENTA_LOCATION_SIMULATOR', $_POST['fk_location_simulator']);
        update_option('FRAKMENTA_MODE', $_POST['fk_account']);

        if ($_POST['fk_mode']==1){
            update_option('FRAKMENTA_URL', 'https://frakmenta.com');
            update_option('FRAKMENTA_PUBLIC_KEY', $_POST['fk_public_key']);
            update_option('FRAKMENTA_MERCHANT_ID', $_POST['fk_merchant_id']);
            update_option('FRAKMENTA_PRIVATE_KEY', $_POST['fk_private_key']);
        }
        else{
            update_option('FRAKMENTA_URL', 'https://beta2.frakmenta.com');
            update_option('FRAKMENTA_PUBLIC_KEY', 'ae8dffe2183df23633746d6cd056e74ffa7b596a0337e9dacd1e876170757ca6');
            update_option('FRAKMENTA_MERCHANT_ID', 27785);
            update_option('FRAKMENTA_PRIVATE_KEY', '423d6f3a595b9c47eb0fb47dc786ae7cc52e35f178dc83463d851867b68ab5f9');
        }
    }
    wp_redirect( admin_url( '/admin.php?page=menu_config_fk&paramerts=1'));
}

function frakmenta_update_parameters_notice() {
    global $pagenow;
    # Check current admin page.
    if ($pagenow == 'admin.php' && isset($_GET['paramerts']) && $_GET['page']=='menu_config_fk') {?>
    <div class="updated notice">
        <p>La actualización de la configuración de frakmenta se ha realizado exitosamente</p>
    </div>
    <?php }
}

function frakmenta_product_option(){
    $fkLocalCSS = plugins_url( '/assets/css/frakmenta.css', __FILE__ );
    $fkLocalJS = plugins_url( '/assets/js/front_products.js', __FILE__ , array('jquery', plugins_url( '/js/fk_framework/fk_framework_main.js')));
    $fkRemoteCSS =  get_option('FRAKMENTA_URL').'/css/widget-ecommerce.css';
    $fkRemoteJS = get_option('FRAKMENTA_URL').'/js/widgetEcommerce.js';

    wp_enqueue_style( 'fk-local-admin-style', $fkLocalCSS );
    wp_enqueue_script('fk-local-admin', $fkLocalJS);

    $frakmenta_params = array(
        "FRAKMENTA_URL" => get_option('FRAKMENTA_URL'),
        "FRAKMENTA_PUBLIC_KEY" => get_option('FRAKMENTA_PUBLIC_KEY')
    );

    wp_localize_script( 'fk-local-admin', 'frakmentaParams', $frakmenta_params );

    wp_enqueue_style( 'fk-remote-admin-style', $fkRemoteCSS );
    wp_enqueue_script('fk-remote-admin', $fkRemoteJS);

}

