<?php
global $wpdb;

if(!defined('WP_UNINSTALL_PLUGIN') or !WP_UNINSTALL_PLUGIN) exit;
    
// clenaup all data
if(get_option('chainedquiz_cleanup_db')==1)
{
	// now drop tables	
	// NYI
	    
	// clean options
	// NYI
}