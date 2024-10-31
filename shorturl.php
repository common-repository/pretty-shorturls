<?php
/*
Plugin Name: Pretty ShortURLs
Plugin URI: http://kanedo.net/projekte/
Description: Generates pretty shorturls (without GET Params)
Author: kanedo
Version: 1.1.1
Author URI: http://blog.kanedo.net/
*/

define('KANEDO_SHORT_CAMPAIGN', false);

function kanedo_shorturl_redirect($query_vars) {
	if (array_key_exists('name', $query_vars)
		&& preg_match('/^(\-|~)(?<code>[0-9a-z]+)$/', $query_vars['name'], $preg_matches)) {
		$id = base_convert($preg_matches['code'], 36, 10);
		$url = get_permalink($id);
		if ($url) {
			if(KANEDO_SHORT_CAMPAIGN){
				$url .= KANEDO_SHORT_CAMPAIGN;
			}
			$query_vars['p'] = $id;
		}
	}
	return $query_vars;
}


function kanedo_shortlink($id = 0, $context = 'post', $allow_slugs = true){
	global $wp_query;
	$post_id = 0;
	if ( 'query' == $context && is_singular() ) {
		$post_id = $wp_query->get_queried_object_id();
		$post = get_post( $post_id );
	} elseif ( 'post' == $context ) {
		$post = get_post( $id );
		if ( ! empty( $post->ID ) )
			$post_id = $post->ID;
	}

	$shortlink = '';

	// Return p= link for all public post types.
	if ( ! empty( $post_id ) ) {
		$post_type = get_post_type_object( $post->post_type );
		if ( $post_type->public )
			$shortlink = home_url('-' . base_convert($post_id, 10, 36));
	}
	return $shortlink;
	
}

add_action('init', 'kanedo_do_shorturl_redirect');
add_filter('pre_get_shortlink', 'kanedo_shortlink');
add_filter('request', 'shorturl_redirect');


?>
