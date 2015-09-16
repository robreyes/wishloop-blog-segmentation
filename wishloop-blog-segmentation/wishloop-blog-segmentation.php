<?php
/**

 * Plugin Name: Wishloop Blog Segmentation
 * Description: Wishloop Blog Segmentation Plugin 1.0
 * Version: 1.0
 * Author: Robin Reyes


 */ 

if ( ! defined( 'WPINC' ) ) {
	die("ikaw lang");
}

define( 'WBS_PATH', WP_PLUGIN_URL . '/' . str_replace( basename( __FILE__ ), '', plugin_basename( __FILE__ ) ) );

require_once( plugin_dir_path( __FILE__ ) . 'admin/admin-wishloop-segmentation.php' );

/* wp_enqueue_script('rme-scripts', WBS_PATH . 'admin/js/wbs-js.js',array(), '1.0.0', true );
wp_enqueue_style('rme-style', WBS_PATH . 'admin/css/style.css') */;
$my_class = new WBS_Segmentation; 	
add_action( 'init', array( 'WBS_Segmentation', 'lead_magnet_title' ));
add_action( 'add_meta_boxes', array( $my_class, 'add_meta_box' ) );
add_action( 'add_meta_boxes', array( $my_class, 'save_custom_meta' ) );
register_activation_hook( __FILE__, array( 'WBS_Segmentation', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'WBS_Segmentation', 'deactivate' ) );




?>