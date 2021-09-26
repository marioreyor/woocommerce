<?php

/*
* The class itself, please note that it is inside plugins_loaded action hook
*/

function misha_init_gateway_class() {
    class WC_Misha_Gateway extends WC_Payment_Gateway {
        /**
        * Class constructor, more about it in Step 3
        */
        public function __construct() {
//            $this->fk_functions = new
            $this->id = 'frakmenta'; // payment gateway plugin ID
            $this->icon = 'https://static.frakmenta.com/img/favicon.png'; // URL of the icon that will be displayed on checkout page near your gateway name
            $this->has_fields = true; // in case you need a custom credit card form
            $this->method_title = 'Frakmenta';
            $this->method_description = 'Financia las compras de tus clientes con frakmenta';

            $this->supports = array(
                'products'
            );

            // Method with all the options fields
            $this->init_form_fields();

            // Load the settings.
            $this->init_settings();
            $this->title = 'Paga con frakmenta';
            $this->description = $this->get_option( 'description' );
            $this->enabled = $this->get_option( 'enabled' );

            // This action hook saves the settings
            add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ));

            // We need custom JavaScript to obtain a token
            add_action( 'wp_enqueue_scripts', array( $this, 'payment_scripts' ));
            add_action( 'wp_enqueue_scripts', array( $this, 'payment_fields' ));

        }

        /**
        * Plugin options, we deal with it in Step 3 too
        */
        public function init_form_fields(){
            null;
        }

        /**
        * You will need it if you want your custom credit card form, Step 4 is about it
        */
        public function payment_fields() {

        }

        /*
        * Custom CSS and JS, in most cases required only when you decided to go with a custom credit card form
        */
        public function payment_scripts() {
            null;
        }

        /*
        * Fields validation, more in Step 5
        */
        public function validate_fields() {
            null;
        }

        /*
        * We're processing the payments here, everything about it is in Step 5
        */
        public function process_payment( $order_id ) {
            null;
        }

        /*
        * In case you need a webhook, like PayPal IPN etc
        */
        public function webhook() {
            null;
        }
    }
}