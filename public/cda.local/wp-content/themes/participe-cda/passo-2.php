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
$context['category_slug'] = get_query_var('category_name');
$obj_category = get_category_by_slug( $context['category_slug'] );
$context['step_avaliacao'] = 'step_beforey_selected';
$context['step_proposta'] = 'step_selected';

$query = array(
    'category'  => $obj_category->term_id,
    'post_type' => 'proposta',
    'orderby'   => 'post_date',
    'order'     => 'DESC',
    'numberposts' => 9999
);
$context['posts'] = Timber::get_posts($query);

$context['nome_projeto'] = $obj_category->name;
$context['id_projeto'] = $obj_category->term_id;
$context['slug_projeto'] = $obj_category->slug;
$context['cor_projeto'] = get_tax_meta($obj_category->term_id,'cda_color_field_id');
$context['username'] = get_query_var('username');

if ($post_slug) {
    $get_post_args = array(
      'name' => $post_slug,
      'post_type' => 'proposta',
      'post_status' => 'publish',
      'numberposts' => 1
    );
    $get_post = get_posts($get_post_args);
    if (count($post) > 0) $post = $get_post[0];
}

$post_meta = get_post_meta($post->ID);

// add meta values

foreach ($post_meta as $key => $value) {
    $post->$key = $value[0];
}

$context['post'] = $post;

// IMAGE GALLERY
$context['post']->mgop_media_value = unserialize($context['post']->mgop_media_value);

foreach ($context['post']->mgop_media_value as $key => $value) {

  $i=0;

  foreach ($value as $v) {
    $img = wp_prepare_attachment_for_js( $v );
    $context['post']->mgop_media_value[$key][$i] = $img;
    $i++;
  }

}

// USER
if (get_query_var('username')){
    $current_user = get_user_by('login', get_query_var('username'));

    $context['profile'] = $current_user;
    $context['compartilhar_imagem'] = get_stylesheet_directory_uri() . '/assets/images/avaliacao_share_facebook_geral.jpg';
} else {
    global $current_user;
    get_currentuserinfo();

    $context['profile'] = $current_user;
    $context['compartilhar_link'] = get_bloginfo('home') . '/projetos/' . $category_slug . '/'. $current_user->user_login;
}

$context['comments'] = get_comments(array('order'=>'desc', 'post_id' => $post->ID, 'status' => 'approve'));
$context['user_comments'] = get_comments(array('order'=>'desc', 'user_id' => $current_user->ID, 'status' => 'hold', 'post_id' => $post->ID));

// VOTES
$context['up'] = get_post_meta( $context['post']->ID, 'up' );
$context['down'] = get_post_meta( $context['post']->ID, 'down' );

if (in_array($current_user->ID, $context['up']))
  $context['user_vote'] = 'up';
else if (in_array($current_user->ID, $context['down']))
  $context['user_vote'] = 'down';

// ICONE
for($i=0; $i<count($context['posts']); $i++) {
  $icon = wp_get_attachment_url( $context['posts'][$i]->_thumbnail_id, array(16,16) );
  $context['posts'][$i]->icon = $icon;
}

Timber::render('passo-2.twig', $context);
