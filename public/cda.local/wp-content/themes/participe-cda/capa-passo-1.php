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
$context['cor_projeto'] = get_tax_meta($obj_category->term_id,'cda_color_field_id');

$images_id_field  = get_tax_meta($context['id_projeto'],'cda_text_field_id');
$images_id = explode(',', $images_id_field);
$galeria_imagens = array();

$i = 0;
$matrix = 0;
$galeria_imagem = array();
foreach ($images_id as $image_id) {
	if ($i % 3 === 0) {
		$matrix++;
	}
    $galeria_imagem["galeria_imagens_".$matrix][] = new TimberImage($image_id);
    $i++;
}
$context['galeria_imagem'] = $galeria_imagem;
$context['button_comecar'] = get_bloginfo('home') . '/projetos/' . $category_slug . '/votar';
Timber::render('capa-passo-1.twig', $context);
