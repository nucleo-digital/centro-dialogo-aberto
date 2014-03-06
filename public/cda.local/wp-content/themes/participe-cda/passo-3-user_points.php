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
$context['category_slug'] = get_query_var('category_name');
$points = get_query_var('points');
$obj_category = get_category_by_slug( $context['category_slug'] );

$context['nome_projeto'] = $obj_category->name;
$context['id_projeto'] = $obj_category->term_id;
$context['slug_projeto'] = $obj_category->slug;
$context['cor_projeto'] = get_tax_meta($obj_category->term_id,'cda_color_field_id');

global $current_user;
get_currentuserinfo();

$query = array(
  'category'  => $obj_category->term_id,
  'post_type' => 'sugestao',
  'order'     => 'ASC',
  'author'   => $current_user->ID,
  'numberposts' => 1
);

$post = get_posts($query);


if (count($post)) {

	$post = $post[0];

	$query = array(
	  'ID'  => $post->ID,
	  'post_content' => $points,
	);

	print_r(wp_update_post($query));

} else {

	$query = array(
		'post_content' => $points,
		'post_name' => 'sugestao-de-' . $current_user->user_login,
		'post_title' => 'Sugestão de ' . $current_user->display_name,
		'post_status' => 'publish',
		'post_type' => 'sugestao',
		'post_author'   => $current_user->ID,
		'post_category'  => array($obj_category->term_id),
	);

	print_r(wp_insert_post($query));

}

print_r('<br>');
print_r($points);

