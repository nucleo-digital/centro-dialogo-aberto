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
$get_post = get_posts($get_post_args);
if (count($post) > 0) $post = $get_post[0];

$thumb = get_query_var('thumb');
$action = get_query_var('action');

if (get_query_var('username')){
    $current_user = get_user_by('login', get_query_var('username'));
} else {
    global $current_user;
    get_currentuserinfo();
}

echo $action . 'd';

if ($action == 'like') 
    add_post_meta($post->ID, $thumb, $current_user->ID, 0);
else
    delete_post_meta($post->ID, $thumb, $current_user->ID);

?>