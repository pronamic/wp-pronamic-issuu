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

/**
 * Get the Issuu PDFs
 * 
 * @param mixed $post
 * @return array
 */
function pronamic_issuu_get_pdfs( $post_id = null ) {
	$post_id = ( null === $post_id ) ? get_the_ID() : $post_id;

	return get_posts( array(
		'post_parent'    => $post_id , 
		'post_type'      => 'attachment' , 
		'post_status'    => 'inherit' , 
		'posts_per_page' => -1 , 
		'post_mime_type' => 'application/pdf' , 
		'orderby'        => 'menu_order' ,
		'order'          => 'ASC' , 
		'meta_query'     => array(
			array(
				'key'     => 'issuu_pdf_id' , 
				'value'   => '' ,
				'compare' => '!='
			)
		)
	) );
}
