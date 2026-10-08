<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

check_admin_referer();

delete_option( 'nf_maintenance_cfg' );
