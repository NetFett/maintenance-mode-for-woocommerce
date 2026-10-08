<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( !class_exists( 'Nf_Maintenance' ) ) {

    class Nf_Maintenance {

    	protected $loader;
    	protected $plugin_name;
    	protected $version;

    	public function __construct() {

            $this->version = NF_MAINTENANCE_VERSION;
    		$this->plugin_name = 'maintenance-mode-for-woocommerce';
    		$this->load_dependencies();
    		$this->set_locale();

            if ($this->isWoo()) {
                if ( is_admin() ){
        		    $this->define_admin_hooks();
                }
                if ( !is_admin() ){
            	    $this->define_public_hooks();
                }
            }

            if (!$this->isWoo()) {
                add_action( 'admin_init', array( $this, 'deactivate_self' ) );
                add_action( 'admin_notices', array( $this, 'nowoo_notice' ) );
            }


    	}
        function isWoo(){
            if ( class_exists( 'WooCommerce' ) ) {
                return true;
            }
            $active_plugins = apply_filters( 'nf_maintenance_active_plugins', get_option( 'active_plugins', array() ) );
            if ( is_array( $active_plugins ) && in_array( 'woocommerce/woocommerce.php', $active_plugins ) ) {
                return true;
            }
            if ( is_multisite() ) {
                $sitewide_plugins = get_site_option( 'active_sitewide_plugins', array() );
                if ( is_array( $sitewide_plugins ) && isset( $sitewide_plugins['woocommerce/woocommerce.php'] ) ) {
                    return true;
                }
            }
            return false;
        }


    	private function load_dependencies() {

    		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-nf-maintenance-loader.php';
    		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-nf-maintenance-i18n.php';


            if ( is_admin() ){
    		    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-nf-maintenance-admin.php';
            }
            if ( !is_admin() ){
    		    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-nf-maintenance-public.php';
            }

    		$this->loader = new Nf_Maintenance_Loader();

    	}

        function nowoo_notice() {
            ?>
            <div class="notice notice-error is-dismissible">
                <p><?php echo esc_html__('Maintenance mode for WooCommerce requires WooCommerce to be installed and activated!', 'maintenance-mode-for-woocommerce');?></p>
            </div>
            <?php
        }

        public function deactivate_self() {
            deactivate_plugins( plugin_basename( dirname( dirname( __FILE__ ) ) . '/maintenance-mode-for-woocommerce.php' ) );
            if ( isset( $_GET['activate'] ) ) {
                unset( $_GET['activate'] );
            }
        }


    	private function set_locale() {

    		$plugin_i18n = new Nf_Maintenance_i18n();

    		$this->loader->add_action( 'init', $plugin_i18n, 'load_plugin_textdomain' );

    	}


    	private function define_admin_hooks() {

    		$plugin_admin = new Nf_Maintenance_Admin( $this->get_plugin_name(), $this->get_version() );

    		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
    		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

    		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_settings_page' );
    		$this->loader->add_action( 'admin_init', $plugin_admin, 'add_settings' );

     		$this->loader->add_filter( 'option_page_capability_nf_maintenance_group', $this, 'nf_set_settings_permissions', 10, 1 );

     		$this->loader->add_filter( 'plugin_action_links_maintenance-mode-for-woocommerce/maintenance-mode-for-woocommerce.php', $this, 'nf_settings_link', 10, 1 );

            $this->loader->add_filter( 'plugin_row_meta', $this, 'nf_plugin_add_links', 10, 4 );

            $options = get_option( 'nf_maintenance_cfg' );
            if ( isset($options["active"]) and
                 isset($options["show_warning"]) and
                 "1" === $options["active"]  and
                 "1" === $options["show_warning"] ) {
    		    $this->loader->add_action( 'admin_bar_menu', $this, 'nf_dashboard_admin_notice', 999, 1 );
            }

    	}


        function nf_dashboard_admin_notice( $wp_admin_bar ) {

        	$url = esc_url( add_query_arg(
        		'page',
        		'maintenance-mode-for-woocommerce',
        		get_admin_url() . 'admin.php'
        	) );

            $args = array(
                'id' => 'maintenance-mode-for-woocommerce',
                'title' => '<span class="ab-icon"></span><span class="ab-label">' . __( 'Maintenance mode ON', 'maintenance-mode-for-woocommerce' ) . '</span>',
                'href' => $url,
                'meta' => array(
                    'target' => '_self',
                    'class' => 'nf-ab',
                    'title' => 'Maintenance mode ON'
                )
            );
            $wp_admin_bar->add_node($args);


        }


        public function nf_settings_link( $links ) {

        	$url = esc_url( add_query_arg(
        		'page',
        		'maintenance-mode-for-woocommerce',
        		get_admin_url() . 'admin.php'
        	) );
        	$settings_link = "<a href='$url'>" . __( 'Settings', 'maintenance-mode-for-woocommerce' ) . '</a>';

        	array_unshift(
        		$links,
        		$settings_link
        	);

        	return $links;
        }


        function nf_plugin_add_links( $links_array, $plugin_file_name, $plugin_data, $status ){

        	if(  $plugin_file_name == 'maintenance-mode-for-woocommerce/maintenance-mode-for-woocommerce.php' ) {

            	$url = esc_url( add_query_arg(
            		'page',
            		'maintenance-mode-for-woocommerce',
            		get_admin_url() . 'admin.php'
            	) );
            	$settings_link = "<a href='$url'>" . __( 'Settings', 'maintenance-mode-for-woocommerce' ) . '</a>';

        		$links_array[] = $settings_link;

        	}

        	return $links_array;
        }


        public function nf_set_settings_permissions() {

            return 'manage_woocommerce';

        }


    	private function define_public_hooks() {

    		$plugin_public = new Nf_Maintenance_Public( $this->get_plugin_name(), $this->get_version() );

    		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );

    		$this->loader->add_action( 'template_redirect', $plugin_public, 'nf_woo_redirect' );

            $options = get_option( 'nf_maintenance_cfg' );
            if ( isset($options["active"]) and
                 isset($options["show_warning"]) and
                 "1" === $options["active"]  and
                 "1" === $options["show_warning"] ) {
    		    $this->loader->add_action( 'admin_bar_menu', $this, 'nf_dashboard_admin_notice', 999, 1 );
            }

            if ( isset($options["active"]) and
                 isset($options["show_frontend_notice"]) and
                 "1" === $options["active"] and
                 "1" === $options["show_frontend_notice"] ) {
    		    $this->loader->add_action( 'wp_body_open', $plugin_public, 'render_frontend_banner', 1 );
    		    $this->loader->add_action( 'wp_footer', $plugin_public, 'render_frontend_banner', 999 );
            }

    	}


    	public function run() {
    		$this->loader->run();
    	}


    	public function get_plugin_name() {
    		return $this->plugin_name;
    	}


    	public function get_loader() {
    		return $this->loader;
    	}


    	public function get_version() {
    		return $this->version;
    	}

    }
}