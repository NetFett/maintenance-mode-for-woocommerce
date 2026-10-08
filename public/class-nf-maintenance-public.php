<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( !class_exists( 'Nf_Maintenance_Public' ) ) {

    class Nf_Maintenance_Public {

    	private $plugin_name;
    	private $version;
        private static $banner_rendered = false;

    	public function __construct( $plugin_name, $version ) {

    		$this->plugin_name = $plugin_name;
    		$this->version = $version;

    	}


    	public function enqueue_styles() {

    	    wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/nf-maintenance-public.css', array(), $this->version, 'all' );

    	}

        public function nf_woo_redirect() {

            $user = wp_get_current_user();
            $roles = array( 'shop_manager', 'administrator' );
            if ( array_intersect( $roles, (array) $user->roles ) ) {
                return;
            }

            $options = get_option( 'nf_maintenance_cfg' );
            if ( ! isset( $options["active"] ) || "1" !== $options["active"] ) {
                return;
            }

            if ( ! isset( $options['locked_out'] ) ) {
                if ( is_user_logged_in() ) {
                    return;
                }
            }

            if ( function_exists( 'is_woocommerce' ) && ( is_woocommerce() || is_cart() || is_checkout() ) ) {

                if ( ! empty( $options["nf_postid"] ) ) {
                    $target_id = (int) $options["nf_postid"];

                    // Polylang support
                    if ( function_exists( 'pll_the_languages' ) && function_exists( 'pll_get_post' ) ) {
                        $pll_id = pll_get_post( $target_id );
                        if ( ! empty( $pll_id ) ) {
                            $target_id = (int) $pll_id;
                        }
                    // WPML support
                    } elseif ( has_filter( 'wpml_object_id' ) ) {
                        $wpml_id = apply_filters( 'wpml_object_id', $target_id, 'page', true );
                        if ( ! empty( $wpml_id ) ) {
                            $target_id = (int) $wpml_id;
                        }
                    }

                    // Fallback to original page if translated page is not published
                    if ( 'publish' !== get_post_status( $target_id ) ) {
                        $target_id = (int) $options["nf_postid"];
                    }

                    if ( 'publish' !== get_post_status( $target_id ) ) {
                        return;
                    }

                    // Loop protection: Never redirect if already on the target maintenance page
                    if ( is_page( $target_id ) || (int) get_queried_object_id() === $target_id ) {
                        return;
                    }

                    $permalink = get_permalink( $target_id );
                    if ( $permalink ) {
                        wp_safe_redirect( $permalink );
                        exit;
                    }
                }

            }
        }

        public function render_frontend_banner() {

            if ( self::$banner_rendered ) {
                return;
            }

            $user = wp_get_current_user();
            $roles = array( 'shop_manager', 'administrator' );
            if ( ! array_intersect( $roles, (array) $user->roles ) ) {
                return;
            }

            $options = get_option( 'nf_maintenance_cfg' );
            if ( empty( $options['active'] ) || '1' !== $options['active'] ) {
                return;
            }

            if ( empty( $options['show_frontend_notice'] ) || '1' !== $options['show_frontend_notice'] ) {
                return;
            }

            self::$banner_rendered = true;

            $settings_url = esc_url( add_query_arg(
                'page',
                $this->plugin_name,
                get_admin_url() . 'admin.php'
            ) );
            ?>
            <div id="mmfw-frontend-banner" class="mmfw-frontend-banner">
                <div class="mmfw-banner-inner">
                    <div class="mmfw-banner-message">
                        <span class="mmfw-banner-icon" aria-hidden="true">&#9888;&#65039;</span>
                        <span class="mmfw-banner-text">
                            <strong><?php echo esc_html__( 'Maintenance mode ON', 'maintenance-mode-for-woocommerce' ); ?>:</strong>
                            <?php echo esc_html__( 'Customers are currently redirected to the maintenance page.', 'maintenance-mode-for-woocommerce' ); ?>
                        </span>
                    </div>
                    <a href="<?php echo $settings_url; ?>" class="mmfw-banner-btn">
                        <?php echo esc_html__( 'Settings', 'maintenance-mode-for-woocommerce' ); ?>
                    </a>
                </div>
            </div>
            <?php
        }

    }

}