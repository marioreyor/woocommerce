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
 * @package     Settings
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

namespace Frakmenta\WooCommerce\Settings;

/**
 * The settings page controller.
 *
 * Defines all the functionalities needed on the settings page
 *
 * @since   4.0.0
 */
class SettingsController {

    /**
     * In plugin version < 4.0.0 the options frakmenta_testmode, and frakmenta_debugmode
     * had been stored as strings and returns yes - no.
     *
     * This function also works to returns booleans instead of strings for
     * frakmenta_second_chance and frakmenta_remove_all_settings options
     *
     * @since 4.0.0
     * @see https://developer.wordpress.org/reference/hooks/option_option/
     *
     * @return  boolean
     */
    public function filter_frakmenta_settings_as_booleans( string $value ): bool {
        if ( 'yes' === $value || '1' === $value ) {
            return true;
        }
        return false;
    }

    /**
     * This function returns int instead of strings for frakmenta_time_active
     *
     * @see https://developer.wordpress.org/reference/hooks/option_option/
     *
     * @return  integer
     */
    public function filter_frakmenta_settings_as_int( string $value ): int {
        return (int) $value;
    }

    /**
     * Register the stylesheets for the settings page.
     *
     * @see https://developer.wordpress.org/reference/functions/wp_enqueue_style/
     * @return void
     */
    public function enqueue_styles(): void {
        wp_enqueue_style( 'frakmenta-commons-css', FRAKMENTA_PLUGIN_URL . '/assets/commons/css/frakmenta.css', [], FRAKMENTA_PLUGIN_VERSION, 'all' );
    }

    /**
     * Register the JavaScript needed in the backend.
     *
     * @see https://developer.wordpress.org/reference/functions/wp_enqueue_script/
     * @see https://developer.wordpress.org/reference/functions/wp_localize_script/
     * @return void
     */
    public function enqueue_scripts():void {
        self::default_values_plugin();

        $frakmenta_vars = ["FRAKMENTA_DELEGATION" => get_option('FRAKMENTA_DELEGATION'), "FRAKMENTA_EXIST_ACCOUNT" => get_option('FRAKMENTA_EXIST_ACCOUNT'), "FRAKMENTA_URL" => get_option('FRAKMENTA_URL'), "FRAKMENTA_PUBLIC_KEY" => get_option('FRAKMENTA_PUBLIC_KEY'), "FRAKMENTA_MERCHANT_ID" => get_option('FRAKMENTA_MERCHANT_ID'), "FRAKMENTA_MODE" => get_option('FRAKMENTA_TEST_MODE'), "FRAKMENTA_PRODUCT_OPTION" => get_option('FRAKMENTA_PRODUCT_OPTION'), "LOCATION_SIMULATOR_DEFAULT" => get_option('LOCATION_SIMULATOR_DEFAULT')];

        wp_register_script( 'frakmenta-admin-js', FRAKMENTA_PLUGIN_URL . '/assets/admin/js/frakmenta-admin.js', ['jquery'], FRAKMENTA_PLUGIN_VERSION, false );

        wp_localize_script( 'frakmenta-admin-js', 'frakmenta', $frakmenta_vars );
        wp_enqueue_script( 'frakmenta-admin-js' );
    }

    /**
     * Register the common settings page in WooCommerce menu section.
     *
     * @see https://developer.wordpress.org/reference/functions/add_submenu_page/
     * @return void
     */
    public function register_common_settings_page(): void {
        $title = sprintf( __( 'Frakmenta Configuración v. %s', 'frakmenta' ), FRAKMENTA_PLUGIN_VERSION ); // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
        add_submenu_page(
            'woocommerce',
            esc_html( $title ),
            __( 'Configuración Frakmenta', 'frakmenta' ),
            'manage_woocommerce',
            'frakmenta-settings',
            $this->display_frakmenta_settings(...)
        );
    }

    /**
     * Returns active tab defined in get variable
     *
     * @return  string
     */
    private function get_tab_active(): string {
        if ( ! isset( $_GET['tab'] ) || '' === $_GET['tab'] ) {
            $tab_active = 'general';
        }
        if ( isset( $_GET['tab'] ) && '' !== $_GET['tab'] ) {
            $tab_active = $_GET['tab'];
        }
        return $tab_active;
    }

    /**
     * Register general settings in common settings page
     *
     * @return void
     */
    public function register_common_settings(): void {
        $settings_fields = new SettingsFields();
        $settings        = $settings_fields->get_settings();
        foreach ( $settings as $tab_key => $section ) {
            $this->add_settings_section( $tab_key, $section['title'] );
            foreach ( $section['fields'] as $field ) {
                $this->register_setting( $field, $tab_key );
                $this->add_settings_field( $field, $tab_key );
            }
        }
    }

    /**
     * Add settings field
     *
     * @see https://developer.wordpress.org/reference/functions/add_settings_field/
     * @param   array  $field      The field
     * @param   string $tab_key    The key of the tab
     * @return  void
     */
    private function add_settings_field( array $field, string $tab_key ): void {
        add_settings_field(
            $field['id'],
            $this->generate_label_for_settings_field( $field ),
            $this->display_field(...),
            'frakmenta-settings-' . $tab_key,
            $tab_key,
            ['field' => $field]
        );
    }

    /**
     * Return the label tag to be used in add_settings_field
     *
     * @param   array $field  The settings field array
     * @return  string
     */
    private function generate_label_for_settings_field( array $field ): string {
        if ( '' === $field['tooltip'] ) {
            return sprintf( '<label for="%s">%s</label>', $field['id'], $field['label'] );
        }
        return sprintf( '<label for="%s">%s %s</label>', $field['id'], $field['label'], wc_help_tip( $field['tooltip'] ) );
    }

    /**
     * Filter which set the settings page and adds a screen options of WooCommerce
     *
     * @see http://hookr.io/filters/woocommerce_screen_ids/
     * @return  array
     */
    public function set_wc_screen_options_in_common_settings_page( array $screen ): array {
        $screen[] = 'woocommerce_page_frakmenta-settings';
        return $screen;
    }

    /**
     * Register setting
     *
     * @see https://developer.wordpress.org/reference/functions/register_setting/
     * @return  void
     */
    private function register_setting( array $field, string $tab_key ): void {
        register_setting(
            'frakmenta-settings-' . $tab_key,
            $field['id'],
            ['type'              => $field['setting_type'], 'show_in_rest'      => false, 'sanitize_callback' => $field['callback']]
        );
    }

    /**
     * Add settings section
     *
     * @see https://developer.wordpress.org/reference/functions/add_settings_section/
     *
     * @return  void
     */
    private function add_settings_section( string $section_key, string $section_title ): void {
        add_settings_section(
            $section_key,
            $section_title,
            $this->display_intro_section(...),
            'frakmenta-settings-' . $section_key
        );
    }

    /**
     * Callback to display the title on each settings sections
     *
     * @see https://developer.wordpress.org/reference/functions/add_settings_section/
     * @return  void
     */
    public function display_intro_section( array $args ): void {
        $settings_fields = new SettingsFields();
        $settings        = $settings_fields->get_settings();
        if ( ! empty( $settings[ $args['id'] ]['intro'] ) ) {
            esc_html( printf( '<p>%s</p>', $settings[ $args['id'] ]['intro'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }
    }

    /**
     * Return the HTML view by field.
     * Is the callback function in add_settings_field
     *
     * @param   array $args
     * @return  void
     */
    public function display_field( $args ): void {
        $field                  = $args['field'];
        $settings_field_display = new SettingsFieldsDisplay( $field );
        $settings_field_display->display();
    }

    /**
     * Display the common settings page view.
     *
     * @return void
     */
    public function display_frakmenta_settings(): void {

        $tab_active   = $this->get_tab_active();
        /*$needs_update = $this->needs_update();*/
        remove_query_arg( 'needs-setup' );
        require_once FRAKMENTA_PLUGIN_DIR_PATH . 'templates/fk_admin_page.php';
    }

    /**
     * Set frakmenta default parametersf
     *
     * @return void
     */
    public function default_values_plugin(): void {
        if (empty(get_option('FRAKMENTA_EXIST_ACCOUNT'))) {
            update_option('FRAKMENTA_DELEGATION', 1);
            update_option('FRAKMENTA_EXIST_ACCOUNT', 0);
            update_option('FRAKMENTA_LOCATION_SIMULATOR', '.product_attributes');
            update_option('FRAKMENTA_PRODUCT_OPTION', 0);
        }
    }

    /**
     * Change frakmenta parameters form configuration admin
     *
     * @return void
     */
    public function process_admin_form(){
        if (!empty($_POST['submitButton'])){
            update_option('FRAKMENTA_EXIST_ACCOUNT', $_POST['fk_account']);
            update_option('FRAKMENTA_PRODUCT_OPTION', $_POST['fk_sim_product']);
            update_option('FRAKMENTA_LOCATION_SIMULATOR', $_POST['fk_location_simulator']);
            update_option('FRAKMENTA_MODE', $_POST['fk_mode']);

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
        wp_redirect( admin_url( '/admin.php?page=frakmenta-settings&paramerts=1'));
    }

    /**
     * Change frakmenta parameters form configuration admin
     *
     * @return void
     */
    public function fk_admin_parameters(){
    //function in plugin file with return value,
    //  custom fetch query
    frakmenta_default_configuration();
    return json_encode(["FRAKMENTA_DELEGATION" => get_option('FRAKMENTA_DELEGATION'), "FRAKMENTA_EXIST_ACCOUNT" => get_option('FRAKMENTA_EXIST_ACCOUNT'), "FRAKMENTA_URL" => get_option('FRAKMENTA_URL'), "FRAKMENTA_PUBLIC_KEY" => get_option('FRAKMENTA_PUBLIC_KEY'), "FRAKMENTA_MERCHANT_ID" => get_option('FRAKMENTA_MERCHANT_ID'), "FRAKMENTA_MODE" => get_option('FRAKMENTA_TEST_MODE'), "FRAKMENTA_PRODUCT_OPTION" => get_option('FRAKMENTA_PRODUCT_OPTION'), "LOCATION_SIMULATOR_DEFAULT" => get_option('LOCATION_SIMULATOR_DEFAULT')]);
}

}
