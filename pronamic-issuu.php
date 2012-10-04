<?php
/*
Plugin Name: Pronamic Issuu
Plugin URI: http://pronamic.eu/wp-plugins/issuu/
Description: Easily integrate Issuu documents into WordPress.
 
Version: 1.0
Requires at least: 3.0

Author: Pronamic
Author URI: http://pronamic.eu/

Text Domain: pronamic_issuu
Domain Path: /languages/

License: GPL

GitHub URI: https://github.com/pronamic/wp-pronamic-issuu
*/

class Pronamic_Issuu_Plugin {
	/**
	 * The plugin file
	 * 
	 * @var string
	 */
	public static $file;

	//////////////////////////////////////////////////

	/**
	 * Bootstrap
	 */
	public static function bootstrap( $file ) {
		self::$file = $file;

		add_action( 'init',           array( __CLASS__, 'init' ) );
		add_action( 'admin_init',     array( __CLASS__, 'admin_init' ) );
		add_action( 'admin_menu',     array( __CLASS__, 'admin_menu' ) );
	}

	//////////////////////////////////////////////////

	/**
	 * Initialize
	 */
	public static function init() {
		// Text domain
		$rel_path = dirname( plugin_basename( self::$file ) ) . '/languages/';
	
		load_plugin_textdomain( 'pronamic_issuu', false, $rel_path );

		// Require
		require_once dirname( self::$file ) . '/includes/taxonomy.php';
		require_once dirname( self::$file ) . '/includes/gravityforms.php';
		require_once dirname( self::$file ) . '/includes/template.php';
	
		// Post types
		$slug = get_option( 'pronamic_issuu_doc' );
		$slug = empty( $slug ) ? _x( 'documents', 'slug', 'pronamic_issuu' ) : $slug;
	
		register_post_type( 'pronamic_issuu_doc', array(
			'labels'             => array(
				'name'               => _x( 'Documents', 'post type general name', 'pronamic_issuu' ), 
				'singular_name'      => _x( 'Document', 'post type singular name', 'pronamic_issuu' ), 
				'add_new'            => _x( 'Add New', 'pronamic_issuu_doc', 'pronamic_issuu' ), 
				'add_new_item'       => __( 'Add New Document', 'pronamic_issuu' ), 
				'edit_item'          => __( 'Edit Document', 'pronamic_issuu' ), 
				'new_item'           => __( 'New Document', 'pronamic_issuu' ), 
				'view_item'          => __( 'View Document', 'pronamic_issuu' ), 
				'search_items'       => __( 'Search Document', 'pronamic_issuu' ), 
				'not_found'          => __( 'No documents found', 'pronamic_issuu' ), 
				'not_found_in_trash' => __( 'No documents found in Trash', 'pronamic_issuu' ),  
				'parent_item_colon'  => __( 'Parent Document:', 'pronamic_issuu' ), 
				'menu_name'          => __( 'Documents', 'pronamic_issuu' )
			) , 
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'capability_type'    => 'post',
			'has_archive'        => true,
			'rewrite'            => array( 'slug' => $slug ), 
			'menu_icon'          => plugins_url( 'includes/images/issuu.png', self::$file ), 
			'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'custom-fields' ) 
		));
	}

	/**
	 * Admin initialize
	 */
	public static function admin_init() {
		add_settings_section(
			'pronamic_issuu_general', // id
			__( 'General', 'pronamic_issuu' ), // title
			array( __CLASS__, 'settings_section' ), // callback
			'pronamic_issuu' // page
		);

		// Un we can't add the permalink options to permalink settings page
		// @see http://core.trac.wordpress.org/ticket/9296
		add_settings_section(
			'pronamic_issuu_permalinks', // id
			__( 'Permalinks', 'pronamic_issuu' ), // title
			array( __CLASS__, 'settings_section' ), // callback
			'pronamic_issuu' // page
		);

		// Fields
		add_settings_field( 
			'pronamic_issuu_username', // id
			__( 'Username', 'pronamic_issuu' ), // title
			array( __CLASS__, 'input_text' ),  // callback
			'pronamic_issuu', // page
			'pronamic_issuu_general', // section 
			array(  // args 
				'class' => 'regular-text',
				'label_for' => 'pronamic_issuu_username' 
			) 
		);

		add_settings_field( 
			'pronamic_issuu_api_key', // id
			__( 'API key', 'pronamic_issuu' ), // title
			array( __CLASS__, 'input_text' ),  // callback
			'pronamic_issuu', // page
			'pronamic_issuu_general', // section 
			array(  // args 
				'class' => 'regular-text',
				'label_for' => 'pronamic_issuu_api_key' 
			) 
		);

		add_settings_field( 
			'pronamic_issuu_api_secret', // id
			__( 'API secret', 'pronamic_issuu' ), // title
			array( __CLASS__, 'input_text' ),  // callback
			'pronamic_issuu', // page
			'pronamic_issuu_general', // section 
			array(  // args 
				'class' => 'regular-text',
				'label_for' => 'pronamic_issuu_api_secret' 
			) 
		);

		add_settings_field( 
			'pronamic_issuu_doc_base', // id
			__( 'Document base', 'pronamic_issuu' ), // title
			array( __CLASS__, 'input_text' ),  // callback
			'pronamic_issuu', // page
			'pronamic_issuu_permalinks', // section 
			array(  // args 
				'class' => 'regular-text code',
				'label_for' => 'pronamic_issuu_doc_base' 
			) 
		);
	
		register_setting( 'pronamic_issuu', 'pronamic_issuu_username' );
		register_setting( 'pronamic_issuu', 'pronamic_issuu_api_key' );
		register_setting( 'pronamic_issuu', 'pronamic_issuu_api_secret' );
		register_setting( 'pronamic_issuu', 'pronamic_issuu_doc_base' );
	}
	
	/**
	 * Admin menu
	 */
	public static function admin_menu() {
		add_submenu_page( 
			'edit.php?post_type=pronamic_issuu_doc' , // parent_slug
			__( 'Issuu Settings', 'pronamic_issuu' ) , // page_title
			__( 'Settings', 'pronamic_issuu' ), // menu_title
			'read' , // capability
			'pronamic_issuu_settings' , // menu_slug
			array( __CLASS__, 'page_settings' ) // function 
		);
	}
	
	/**
	 * Page settings
	 */
	public static function page_settings() {
		include dirname( self::$file) . '/admin/settings.php';
	}

	/**
	 * Settings section
	 */
	public static function settings_section() {
		
	}

	/**
	 * Input tekst
	 * 
	 * @param array $args
	 */
	public static function input_text( $args ) {
		printf(
			'<input name="%s" id="%s" type="text" value="%s" class="%s" />', 
			esc_attr( $args['label_for'] ),
			esc_attr( $args['label_for'] ),
			esc_attr( get_option( $args['label_for'] ) ),
			$args['class']
		);
	}
}

Pronamic_Issuu_Plugin::bootstrap( __FILE__ );
