<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( !class_exists( 'Nf_Maintenance_Activator' ) ) {

    class Nf_Maintenance_Activator {

    	public static function activate() {

    	   	add_option( 'nf_maintenance_cfg', [ 'active'=>0 , 'nf_postid' => 0, 'show_warning' => 0 ] );

    	}

    }

}