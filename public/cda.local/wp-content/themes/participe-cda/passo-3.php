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
$aba = get_query_var('aba');
$context['category_slug'] = get_query_var('category_name');
$obj_category = get_category_by_slug( $context['category_slug'] );
$context['step_proposta'] = 'step_before_selected';
$context['step_sugestao'] = 'step_selected';
$context['show_step_2'] = true;

$context['nome_projeto'] = $obj_category->name;
$context['id_projeto'] = $obj_category->term_id;
$context['slug_projeto'] = $obj_category->slug;
$context['cor_projeto'] = get_tax_meta($obj_category->term_id,'cda_color_field_id');

$context['spots'] = get_option( 'passo-3-points_'  . $context['id_projeto']);

$img_map = get_tax_meta($obj_category->term_id,'cda_image_2_field_id');
$context['img_map'] = new TimberImage($img_map);

$pts = get_tax_meta($obj_category->term_id,'cda_text_field_id_2');
$pts = split (",", $pts);

foreach ($pts as $i=>$id) {
  $pts[$i] = new TimberImage($id);
}

$context['pts'] = $pts;
$context['tab_user_title'] = 'Minha sugestão';
$context['tab_user_url'] = 'minha_sugestao';

if (!$aba || $aba == 'proposta') {

  $context['tab'] = 'proposta';
  $context['tab_proposta'] = 'selected';

  $query = array(
      'category'  => $obj_category->term_id,
      'post_type' => 'sugestao',
      'orderby'   => 'post_date',
      'order'     => 'ASC',
      'numberposts' => 1
  );

  $context['post'] = Timber::get_posts($query);
  $context['post'] = $context['post'][0];

} else if ($aba == 'minha_sugestao') {

    if (!is_user_logged_in()) {
        wp_redirect( get_bloginfo('url') . '/entrar/?redirect_to='.get_bloginfo('url').'/projetos/'.$context['slug_projeto'].'/sugestao/minha_sugestao/' ); exit;
    }

  global $current_user;
  get_currentuserinfo();

  $context['tab'] = 'minha_sugestao';
  $context['tab_user'] = 'selected';
  $context['user_login'] = $current_user->user_login;

  $query = array(
      'category'  => $obj_category->term_id,
      'post_type' => 'sugestao',
      'order'     => 'ASC',
      'author'   => $current_user->ID,
      'numberposts' => 1
  );  

  $context['post'] = Timber::get_posts($query);

  if (!count($context['post'])) {

    $query = array(
        'category'  => $obj_category->term_id,
        'post_type' => 'sugestao',
        'orderby'   => 'post_date',
        'order'     => 'ASC',
        'numberposts' => 1
    );

    $context['post'] = Timber::get_posts($query);

  }

  $context['post'] = $context['post'][0];

} else if ($aba == 'geral') {

  $context['tab'] = 'geral';
  $context['tab_geral'] = 'selected';

  $query = array(
      'category'  => $obj_category->term_id,
      'post_type' => 'sugestao',
      'order'     => 'ASC',
      'numberposts' => 99999999999
  );  

  $context['posts'] = Timber::get_posts($query);

  // print_r($context['posts']);

} else {

  $current_user = get_user_by('login', $aba);

  $context['tab'] = 'usermap';
  $context['tab_user'] = 'selected';
  $context['tab_user_title'] = 'Mapa de ' . $current_user->display_name;
  $context['tab_user_url'] = $aba;
  $context['tab_user_login'] = $current_user->display_name;

  $query = array(
      'category'  => $obj_category->term_id,
      'post_type' => 'sugestao',
      'order'     => 'ASC',
      'author'   => $current_user->ID,
      'numberposts' => 1
  );  

  $context['post'] = Timber::get_posts($query);
  $context['post'] = $context['post'][0];

}

Timber::render('passo-3.twig', $context);
