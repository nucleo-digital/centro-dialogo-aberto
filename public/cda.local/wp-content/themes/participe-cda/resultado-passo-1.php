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
$context['posts'] = Timber::get_posts();
$category_slug = get_query_var('category_name');
$obj_category = get_category_by_slug( $category_slug );

$context['nome_projeto'] = $obj_category->name;
Timber::render('resultado-passo-1.twig', $context);
