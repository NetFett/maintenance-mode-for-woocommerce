<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( !class_exists( 'Nf_Maintenance_i18n' ) ) {

    class Nf_Maintenance_i18n {

    	public function load_plugin_textdomain() {

            /**
             * WordPress handles translation packs automatically for plugins hosted on WordPress.org.
             * Since version 4.6, translations are loaded from wp-content/languages/plugins/.
             * 
             * We keep this method for architectural consistency, but leave it empty to 
             * let WordPress core handle the just-in-time loading of translations.
             */

    	}

    }

}