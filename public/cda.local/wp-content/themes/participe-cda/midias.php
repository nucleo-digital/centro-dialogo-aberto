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

// Config
$context = Timber::get_context();




$args = array(
    'post_type' => 'attachment',
    'numberposts' => -1,
    'post_status' => null,
    'post_parent' => null, // any parent
    ); 
$attachments = get_posts($args);
if ($attachments) {
    foreach ($attachments as $post) {
    	echo $post->ID;
        echo " - ";
    	echo $post->post_name;
        echo " - ";
    	echo $post->post_excerpt;
        echo "<br><hr><br>";
    	// print_r($post);
    	// echo "string";
        // setup_postdata($post);
        // the_attachment_link($post->ID, false);
        // echo "<br>";
        // the_title();
        // echo " - ";
        // the_excerpt();
    }
}