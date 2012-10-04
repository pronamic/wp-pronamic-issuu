<?php

/**
 * Get the Issuu PDF attachments
 * 
 * @param string $post_id
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

/**
 * Check if the post has Issuu PDF attachments

 * @param string $post_id
 * @return array
 */
function pronamic_issuu_has_pdf( $post_id = null ) {
	$post_id = ( null === $post_id ) ? get_the_ID() : $post_id;
	
	$pdfs = pronamic_issuu_get_pdfs( $post_id );
	
	return !empty( $pdfs );
}

/**
 * Get the image URL from Issuu
 * 
 * @param string $document_id
 * @param int $page
 * @param string $size
 */
function pronamic_issuu_get_image_url( $document_id, $page = 1, $size = null ) {
	$size = ( null === $size ) ? '' : '_thumb_' . $size;

	$url = sprintf(
		'http://image.issuu.com/%s/jpg/page_%d%s.jpg', 
		$document_id, 
		$page,
		$size
	);
	
	return $url;
}

function pronamic_issuu_get_document_url( $username, $name ) {
	$url = sprintf(
		'http://issuu.com/%s/docs/%s',
		$username,
		$name
	);

	$url = add_query_arg( array(
		'mode'                => 'window',
		'printButtonEnabled'  => false,
		'shareButtonEnabled'  => false,
		'searchButtonEnabled' => false,
		'backgroundColor'     => '#222222'
	), $url );

	return $url;
}
