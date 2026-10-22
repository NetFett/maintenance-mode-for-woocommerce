<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( !class_exists( 'Nf_Maintenance_Activator' ) ) {

    class Nf_Maintenance_Activator {

    	public static function activate() {
            // Check PHP version requirement (PHP 8.2+)
            if ( version_compare( PHP_VERSION, '8.2', '<' ) ) {
                deactivate_plugins( plugin_basename( dirname( dirname( __FILE__ ) ) . '/maintenance-mode-for-woocommerce.php' ) );
                wp_die(
                    sprintf(
                        /* translators: 1: Current PHP version, 2: Required PHP version */
                        esc_html__( 'Maintenance mode for WooCommerce requires PHP version %2$s or higher. Your server is running PHP version %1$s.', 'maintenance-mode-for-woocommerce' ),
                        esc_html( PHP_VERSION ),
                        '8.2'
                    ),
                    esc_html__( 'Plugin Activation Error', 'maintenance-mode-for-woocommerce' ),
                    array( 'back_link' => true )
                );
            }

            // Check if WooCommerce is installed and active
            $is_woo = false;
            if ( class_exists( 'WooCommerce' ) ) {
                $is_woo = true;
            } else {
                $active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins', array() ) );
                if ( is_array( $active_plugins ) && in_array( 'woocommerce/woocommerce.php', $active_plugins, true ) ) {
                    $is_woo = true;
                } elseif ( is_multisite() ) {
                    $sitewide = get_site_option( 'active_sitewide_plugins', array() );
                    if ( is_array( $sitewide ) && isset( $sitewide['woocommerce/woocommerce.php'] ) ) {
                        $is_woo = true;
                    }
                }
            }

            if ( ! $is_woo ) {
                deactivate_plugins( plugin_basename( dirname( dirname( __FILE__ ) ) . '/maintenance-mode-for-woocommerce.php' ) );
                wp_die(
                    esc_html__( 'Maintenance mode for WooCommerce requires WooCommerce to be installed and activated!', 'maintenance-mode-for-woocommerce' ),
                    esc_html__( 'Plugin Activation Error', 'maintenance-mode-for-woocommerce' ),
                    array( 'back_link' => true )
                );
            }

    	   	add_option( 'nf_maintenance_cfg', [ 'active'=>0 , 'nf_postid' => 0, 'show_warning' => 0 ] );

    	}

    }

}