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
$obj_category = get_category_by_slug( $context['category_slug'] );

$context['nome_projeto'] = $obj_category->name;
$context['id_projeto'] = $obj_category->term_id;
$context['slug_projeto'] = $obj_category->slug;
$context['cor_projeto'] = get_tax_meta($obj_category->term_id,'cda_color_field_id');

$context['username'] = get_query_var('username');

$img = new TimberImage(get_tax_meta($obj_category->term_id,'cda_image_2_field_id')['id']);

$context['mapa_projeto'] = $img;

$context['points'] = get_option( 'passo-3-points_'  . $context['id_projeto']);

Timber::render('passo-3-edit_points.twig', $context);
