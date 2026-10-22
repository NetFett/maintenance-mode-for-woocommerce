<?php

/**
 * @wordpress-plugin
 * Plugin Name:       Maintenance mode for WooCommerce
 * Description:       Put WooCommerce in maintenance mode, without affecting other parts of a website.
 * Version:           1.3.1
 * Author:            netfett
 * Author URI:        https://netfett.de
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       maintenance-mode-for-woocommerce
 * Domain Path:       /languages
 * Requires at least: 6.0
 * Requires PHP:      8.2
 * Requires Plugins:  woocommerce
 * WC requires at least: 9.0
 * WC tested up to:   10.9.4
 * Tested up to:      7.1
 * Stable tag:        1.3.1
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

const NF_MAINTENANCE_VERSION = '1.3.1';


if ( !function_exists( 'nf_maintenance_activate' ) ) {
    function nf_maintenance_activate() {
    	require_once plugin_dir_path( __FILE__ ) . 'includes/class-nf-maintenance-activator.php';
    	Nf_Maintenance_Activator::activate();
    }
}

if ( !function_exists( 'nf_maintenance_deactivate' ) ) {
    function nf_maintenance_deactivate() {
    	require_once plugin_dir_path( __FILE__ ) . 'includes/class-nf-maintenance-deactivator.php';
    	Nf_Maintenance_Deactivator::deactivate();
    }
}

add_action( 'before_woocommerce_init', function() {
        if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
                \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
        }
} );

register_activation_hook( __FILE__, 'nf_maintenance_activate' );
register_deactivation_hook( __FILE__, 'nf_maintenance_deactivate' );

// Safety check: Prevent execution and deactivate if PHP is below 8.2
if ( version_compare( PHP_VERSION, '8.2', '<' ) ) {
    add_action( 'admin_init', function() {
        deactivate_plugins( plugin_basename( __FILE__ ) );
        if ( isset( $_GET['activate'] ) ) {
            unset( $_GET['activate'] );
        }
    } );
    add_action( 'admin_notices', function() {
        ?>
        <div class="notice notice-error is-dismissible">
            <p>
                <?php
                printf(
                    /* translators: 1: Current PHP version, 2: Required PHP version */
                    esc_html__( 'Maintenance mode for WooCommerce requires PHP version %2$s or higher. Your server is running PHP version %1$s. The plugin has been deactivated.', 'maintenance-mode-for-woocommerce' ),
                    esc_html( PHP_VERSION ),
                    '8.2'
                );
                ?>
            </p>
        </div>
        <?php
    } );
    return;
}

require plugin_dir_path( __FILE__ ) . 'includes/class-nf-maintenance.php';

if ( !function_exists( 'nf_maintenance_run' ) ) {
    function nf_maintenance_run() {

    	$plugin = new Nf_Maintenance();
    	$plugin->run();

    }
}
nf_maintenance_run();

