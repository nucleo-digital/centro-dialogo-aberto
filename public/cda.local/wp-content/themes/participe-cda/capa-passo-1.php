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
$category_slug = get_query_var('category_name');
$obj_category = get_category_by_slug( $category_slug );

$context['nome_projeto'] = $obj_category->name;
$context['id_projeto'] = $obj_category->term_id;
$context['button_comecar'] = get_bloginfo('home') . '/projetos/' . $category_slug . '/votar';
Timber::render('capa-passo-1.twig', $context);
