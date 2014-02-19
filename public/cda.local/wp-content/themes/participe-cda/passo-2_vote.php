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

$thumb = get_query_var('thumb');

if (get_query_var('username')){
    $current_user = get_user_by('login', get_query_var('username'));
} else {
    global $current_user;
    get_currentuserinfo();
}

add_post_meta($post->ID, $thumb, $current_user->ID, 0);

echo 'true';


?>