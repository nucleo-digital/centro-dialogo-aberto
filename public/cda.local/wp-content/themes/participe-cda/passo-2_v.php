<?php
/**
 * The Template for displaying all single posts
 *
 * Methods for TimberHelper can be found in the /functions sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */

$context = Timber::get_context();
$post_slug = get_query_var('post_name');

$get_post_args = array(
  'name' => $post_slug,
  'post_type' => 'proposta',
  'post_status' => 'publish',
  'numberposts' => 1
);
$post = get_posts($get_post_args)[0];


$up = get_post_meta( $post->ID, 'up' );
$down = get_post_meta( $post->ID, 'down' );

print_r($up);

// if ($action == 'like') 
//     add_post_meta($post->ID, $thumb, $current_user->ID, 0);
// else
//     delete_post_meta($post->ID, $thumb, $current_user->ID);

?>