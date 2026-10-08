<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( !class_exists( 'Nf_Maintenance_Admin' ) ) {

    class Nf_Maintenance_Admin {

    	private $plugin_name;
    	private $version;

    	public function __construct( $plugin_name, $version ) {

    		$this->plugin_name = $plugin_name;
    		$this->version = $version;

    	}


    	public function enqueue_styles() {

    	    wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/nf-maintenance-admin.css', array(), $this->version, 'all' );

    	}


    	public function enqueue_scripts() {

            // no script at the moment...

    		// wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/nf-maintenance-admin.js', array( 'jquery' ), $this->version, false );

    	}


    	public function add_settings_page(){

    		add_submenu_page(
                'woocommerce',
                'Maintenance mode for WooCommerce',
                __('Maintenance mode','maintenance-mode-for-woocommerce'),
                'manage_woocommerce',
                $this->plugin_name,
                array( $this, 'display_settings_page' ) );

    	}


        public function check_settings_before_save( $options ) {

            if ( !isset($options["active"]) ) {
                $options["active"] = 0;
            }

            if ( !isset($options["nf_postid"]) ) {
                $options["nf_postid"] = 0;
            }

            if ( !isset($options["show_warning"]) ) {
                $options["show_warning"] = 0;
            }

            if ( !isset($options["show_frontend_notice"]) ) {
                $options["show_frontend_notice"] = 0;
            }

            // User can not maintenance activate, when no page is selected.
            if ( $options["active"] != 0 AND $options["nf_postid"] == 0 ) {

                $options["active"] = 0;

                add_settings_error(
                    'nf-maintenance-save',
                    esc_attr( 'settings_updated' ),
                    __('The maintenance mode cannot be activated, if no page is selected!','maintenance-mode-for-woocommerce'),
                    'error'
                );

                return $options;
            }

            add_settings_error( 'nf-maintenance-save', 'settings_updated', __( 'Settings saved.', 'maintenance-mode-for-woocommerce'  ), 'success' );

            return $options;
        }


    	public function display_settings_page(){
    		?>
    		<div class="wrap">
    			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
    			<?php settings_errors('nf-maintenance-save'); ?>
    			<form action="options.php" method="post">
    				<?php
    				settings_fields( 'nf_maintenance_group' );
    				do_settings_sections( 'nf_maintenance_group' );
    				submit_button();
    				?>
    			</form>
    		</div>
    		<?php
    	}


    	public function add_settings(){

    		add_settings_section(
    			'nf_maintenance_section',
    			'',
    			array( $this, 'render_settings_section' ),
    			'nf_maintenance_group'
    		);

    		add_settings_field(
    			'active',
    			__('Activate Maintenance mode', 'maintenance-mode-for-woocommerce'),
    			array( $this, 'render_switch_input' ),
    			'nf_maintenance_group',
    			'nf_maintenance_section',
    			array(
    				'option'    => 'active',
                    'desc'      => __('If switch is red, maintenance mode for Woocommerce is active.', 'maintenance-mode-for-woocommerce')
    			)
    		);

    		add_settings_field(
    			'nf_postid',
    			__('Page', 'maintenance-mode-for-woocommerce'),
    			array( $this, 'render_listbox_posts' ),
    			'nf_maintenance_group',
    			'nf_maintenance_section'
     		);

    		add_settings_field(
    			'show_warning',
    			__('Admin Bar note', 'maintenance-mode-for-woocommerce'),
    			array( $this, 'render_checkbox_input' ),
    			'nf_maintenance_group',
    			'nf_maintenance_section',
    			array(
    				'option'    => 'show_warning',
                    'desc'      => __('Show message on Admin Bar, when maintenance mode is activated.','maintenance-mode-for-woocommerce')
    			)
    		);

    		add_settings_field(
    			'show_frontend_notice',
    			__('Frontend banner (footer)', 'maintenance-mode-for-woocommerce'),
    			array( $this, 'render_checkbox_input' ),
    			'nf_maintenance_group',
    			'nf_maintenance_section',
    			array(
    				'option'    => 'show_frontend_notice',
                    'desc'      => __('Show notification banner at the bottom of the page (footer) in the frontend for administrators and shop managers when maintenance mode is active.','maintenance-mode-for-woocommerce')
    			)
    		);

    		add_settings_field(
    			'locked_out',
    			__('Should all users be blocked?', 'maintenance-mode-for-woocommerce'),
    			array( $this, 'render_checkbox_input' ),
    			'nf_maintenance_group',
    			'nf_maintenance_section',
    			array(
    				'option'    => 'locked_out',
                    'desc'      => __('If checked, then logged-in users AND non-logged-in users are blocked. ','maintenance-mode-for-woocommerce').
                                   __('If unchecked, only users who are not logged in are blocked. ','maintenance-mode-for-woocommerce').
                                   __('In general, users with administrator rights are not blocked. ','maintenance-mode-for-woocommerce'),
    			)
    		);

    		register_setting(
    			'nf_maintenance_group',
    			'nf_maintenance_cfg',
                [ 'sanitize_callback'=>[$this, 'check_settings_before_save'],]
    		);

    	}


    	public function render_settings_section() {
            ?>
            <hr>
            <?php
       	}


    	public function render_checkbox_input( $args ) {
    		$options = get_option( 'nf_maintenance_cfg', 0 );
    		$value = ( isset( $options[ $args['option'] ] )? $options[ $args['option'] ] : '0' );
            ?>
            <label for="nf_maintenance_cfg[<?php echo esc_attr($args['option']);?>]"></label>
            <input type="checkbox" id="nf_maintenance_cfg[<?php echo esc_attr($args['option']);?>]" name="nf_maintenance_cfg[<?php echo esc_attr($args['option']);?>]" value="1" <?php checked(1, $value);?>>
            <?php
            if (isset($args['desc'])){
            ?>
                <p class="description"><?php echo esc_attr($args['desc']);?></p>
            <?php
            }
    	}


    	public function render_switch_input( $args ) {
    		$options = get_option( 'nf_maintenance_cfg', 0 );
    		$value = ( isset( $options[ $args['option'] ] )? $options[ $args['option'] ] : '0' );
            ?>
            <label class="mmfw-form-switch">
                <input type="checkbox" id="nf_maintenance_cfg[<?php echo esc_attr($args['option']);?>]" name="nf_maintenance_cfg[<?php echo esc_attr($args['option']);?>]" value="1" <?php checked(1, $value);?>>
                <i></i>
            </label>
            <?php
		    if (isset($args['desc'])){
			    ?>
                <p class="description"><?php echo esc_attr($args['desc']);?></p>
			    <?php
		    }
    	}


        public function render_listbox_posts(  ) {

            $lang = '';
            if ( function_exists( 'pll_the_languages' ) ) {
                $lang = pll_default_language( 'slug' );
            }

            $is_wpml = has_filter( 'wpml_object_id' ) || defined( 'ICL_SITEPRESS_VERSION' );

            $options = get_option( 'nf_maintenance_cfg', [ 'nf_postid' => 0 ]);
            ?>
            <label for="posts"></label>
            <select id="posts" name='nf_maintenance_cfg[nf_postid]'>
            <option value='0' <?php selected( $options['nf_postid'], 0 ); ?>><?php echo esc_attr__('Select page','maintenance-mode-for-woocommerce');?></option>

            <?php
            $defaults = array(
                'numberposts' => -1,
                'category' => 0,
                'orderby' => 'post_title',
                'order' => 'ASC',
                'post_type' => 'page',
                'lang' => $lang
            );

            if ( $is_wpml ) {
                $defaults['suppress_filters'] = false;
            }

            $posts = get_posts($defaults);

            $sid[] = wc_get_page_id( 'shop' );
            $sid[] = wc_get_page_id( 'myaccount' );
            $sid[] = wc_get_page_id( 'cart' );
            $sid[] = wc_get_page_id( 'checkout' );
            $sid[] = wc_get_page_id( 'view_order' );

            foreach( $posts as $p ){
                if ( in_array($p->ID, $sid) ) { continue;}
            ?>
                <option value='<?php echo esc_attr($p->ID);?>' <?php selected( $options['nf_postid'], $p->ID ); ?>><?php echo esc_attr($p->post_title);?></option>
            <?php }?>
            </select>
            <p class="description"><?php echo esc_attr__('Select the page to be displayed instead of the shop pages. Feel free, to create a new page with your own content.','maintenance-mode-for-woocommerce');?></p>
            <?php
                if ( $lang !== '' ) { ?>
                    <hr><p class="description"><?php echo esc_attr__('Polylang Plugin is active.','maintenance-mode-for-woocommerce');?></p>
                    <p class="description"><?php echo esc_attr__('The page is displayed in the current language.','maintenance-mode-for-woocommerce');?></p>
                <?php } elseif ( $is_wpml ) { ?>
                    <hr><p class="description"><?php echo esc_attr__('WPML Plugin is active.','maintenance-mode-for-woocommerce');?></p>
                    <p class="description"><?php echo esc_attr__('The page is displayed in the current language.','maintenance-mode-for-woocommerce');?></p>
                <?php }

            ?>
        <?php
        }
    }
}