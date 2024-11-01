<?php
/**
Plugin Name: Widget Manager Light
Plugin URI: http://otwthemes.com/?utm_source=wp.org&utm_medium=admin&utm_content=site&utm_campaign=wml
Description:  Get control over widgets visibility. You can now customize each page with specific widgets that are relative to the content on that page. No coding required.
Author: OTWthemes
Version: 1.18
Author URI: https://codecanyon.net/user/otwthemes/portfolio?ref=OTWthemes
*/
$wp_wml_int_items = array(
	'page'              => array( array(), esc_html__( 'Pages' ), esc_html__( 'All pages', 'otw_wml' ) ),
	'post'              => array( array(), esc_html__( 'Posts', 'otw_wml' ), esc_html__( 'All posts', 'otw_wml' ) ),
	'postsincategory'   => array( array(), esc_html__( 'All posts from category', 'otw_wml' ), esc_html__( 'All categories', 'otw_wml' ) ),
	'postsintag'        => array( array(), esc_html__( 'All posts from tag', 'otw_wml' ), esc_html__( 'All tags', 'otw_wml' ) ),
	'category'          => array( array(), esc_html__( 'Categories', 'otw_wml' ), esc_html__( 'All categories', 'otw_wml' ) ),
	'posttag'           => array( array(), esc_html__( 'Tags', 'otw_wml' ), esc_html__( 'All tags', 'otw_wml' ) ),
	'author_archive'    => array( array(), esc_html__( 'Author archives', 'otw_wml' ), esc_html__( 'All author archives', 'otw_wml' ) ),
	'templatehierarchy' => array( array(), esc_html__( 'Template Hierarchy', 'otw_wml'), esc_html__( 'All templates', 'otw_wml' ) ),
	'pagetemplate'      => array( array(), esc_html__( 'Page Templates', 'otw_wml' ), esc_html__( 'All page templates', 'otw_wml' ) ),
	'archive'           => array( array(), esc_html__( 'Archives', 'otw_wml' ), esc_html__( 'All archives', 'otw_wml' ) ),
	'userroles'         => array( array(), esc_html__( 'User roles/Logged in as', 'otw_wml' ), esc_html__( 'All roles', 'otw_wml' ) )
);
/**
 * Loaded plugin 
 */
function otw_wml_plugin_loaded(){
	
	global $otw_plugin_options, $otw_wml_plugin_url, $wp_wml_int_items, $otw_wml_factory_component, $otw_wml_factory_object, $otw_wml_plugin_id;
	
	//load text domain
	load_plugin_textdomain('otw_wml',false,dirname(plugin_basename(__FILE__)) . '/languages/');
	
	$otw_wml_plugin_id = '255856f4328a7deda224ad6e5147a2e2';
	
	$otw_wml_factory_component = false;
	$otw_wml_factory_object = false;
	
	//load core component functions
	@include_once( 'include/otw_components/otw_functions/otw_functions.php' );
	
	if( !function_exists( 'otw_register_component' ) ){
		wp_die( 'Please include otw components' );
	}
	
	//register factory component
	otw_register_component( 'otw_factory', dirname( __FILE__ ).'/include/otw_components/otw_factory/', $otw_wml_plugin_url.'include/otw_components/otw_factory/' );
	
}

global $otw_plugin_options;

$otw_plugin_options = get_option( 'otw_plugin_options' );

$otw_wml_plugin_url = plugin_dir_url( __FILE__);

require_once( plugin_dir_path( __FILE__ ).'/include/otw_functions.php' );


/** plugin options
  *
  */
function otw_wml_options(){
	require_once( 'include/otw_sidebar_options.php' );
}

/**
 * factory messages
 */
function otw_wml_factory_message( $params ){
	
	global $otw_wml_plugin_id;
	
	if( isset( $params['plugin'] ) && $otw_wml_plugin_id == $params['plugin'] ){
		
		//filter out some messages if need it
	}
	if( isset( $params['message'] ) )
	{
		return $params['message'];
	}
	return $params;
}

function otw_wml_sidebars_widget_dialog(){
	require_once( 'include/otw_widget_dialog.php' );
}

function otw_wml_ajax_widget_dialog(){
	require_once( 'include/otw_widget_dialog.php' );
	die;
}
/** admin menu actions
  * add the top level menu and register the submenus.
  */ 
function otw_wml_admin_actions(){
	
	global $otw_wml_plugin_url;
	
	add_menu_page('Widget Manager', 'Widget Manager', 'manage_options', 'otw-wml', 'otw_wml_options', $otw_wml_plugin_url.'images/otw-sbm-icon.png' );
	add_submenu_page( 'otw-wml', 'Options', 'Options', 'manage_options', 'otw-wml', 'otw_wml_options' );
	add_submenu_page( __FILE__, esc_html__('Set up widget appearance', 'otw_wml'), esc_html__('Set up widget appearance', 'otw_wml'), 'manage_options', 'otw-wml-widget-dialog', 'otw_wml_sidebars_widget_dialog' );
}

function otw_wml_items_by_type(){
	require_once( 'include/otw_wml_items_by_type.php' );
	die;
}

/** include needed javascript scripts based on current page
  *  @param string
  */
function enqueue_wml_scripts( $requested_page ){

	global $otw_wml_plugin_url;
	
	switch( $requested_page ){
	
		case 'widgets.php':
				global $otw_plugin_options;
				
				if( isset( $otw_plugin_options['activate_appearence'] ) && $otw_plugin_options['activate_appearence'] ){
					wp_enqueue_script("otw_widgets", $otw_wml_plugin_url.'js/otw_widgets.js' , array( 'jquery', 'jquery-ui-dialog', 'thickbox' ), '3.0' );
					wp_enqueue_script("otw_widget_appearence", $otw_wml_plugin_url.'js/otw_widgets_appearence.js' , array( 'jquery' ), '1.2' );
					wp_enqueue_style (  'wp-jquery-ui-dialog' );
				}
			break;
		case 'admin_page_otw-wml-widget-dialog':
				wp_enqueue_script("otw_widget_appearence", $otw_wml_plugin_url.'js/otw_widgets_appearence.js' , array( 'jquery' ), '3.0' );
			break;
	}
}

/**
 * include needed styles
 */
function enqueue_wml_styles( $requested_page ){
	global $otw_wml_plugin_url;
	wp_enqueue_style( 'otw_wml_sidebar', $otw_wml_plugin_url .'css/otw_sbm_admin.css', array( 'thickbox' ), '1.1' );
}
/**
 * Loaded plugin
 */
add_action( 'plugins_loaded', 'otw_wml_plugin_loaded' );
/**
 * register admin menu 
 */
add_action('admin_menu', 'otw_wml_admin_actions');
add_action('admin_notices', 'otw_wml_admin_notice');
add_filter('sidebars_widgets', 'otw_sidebars_widgets');
add_filter('otwfcr_notice', 'otw_wml_factory_message' );
/**
 * include plugin js and css.
 */
add_action('admin_enqueue_scripts', 'enqueue_wml_scripts');
add_action('admin_print_styles', 'enqueue_wml_styles' );

//register some admin actions
if( is_admin() ){
	add_action( 'wp_ajax_otw_wml_widget_dialog', 'otw_wml_ajax_widget_dialog' );
	add_action( 'wp_ajax_otw_wml_items_by_type', 'otw_wml_items_by_type' );
	add_action( 'enqueue_block_editor_assets', 'otw_wml_add_block_assets' );
}
/** 
 *call init plugin function
 */
add_action('init', 'otw_wml_plugin_init', 102 );
?>