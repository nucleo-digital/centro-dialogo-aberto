<?php
/**
 * The main template file
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file
 *
 * Methods for TimberHelper can be found in the /functions sub-directory
 *
 * @package 	WordPress
 * @subpackage 	Timber
 * @since 		Timber 0.1
 */

	if (!class_exists('Timber')){
		echo 'Timber not activated. Make sure you activate the plugin in <a href="/wp-admin/plugins.php#timber">/wp-admin/plugins.php</a>';
	}
	$context = Timber::get_context();
	$context['posts'] = Timber::get_posts();
	
	$categories = get_categories();
	foreach ($categories as $k => $val) {
		$categories[$k]->tipo_projeto          = get_tax_meta($val->term_id,'cda_radio_field_id');
		if (get_tax_meta($val->term_id,'cda_radio_field_id') == 'conceito') {
			$categories[$k]->tipo_projeto_label  = 'Projeto Conceito';
		} else {
			$categories[$k]->tipo_projeto_label  = 'Projeto Piloto';
		}
		$categories[$k]->imagem_representativa = get_tax_meta($val->term_id,'cda_image_field_id');
		$categories[$k]->cor_representativa    = get_tax_meta($val->term_id,'cda_color_field_id');
	}
	
	//var_dump($categories);

	$context['categories'] = $categories;
	$templates = array('index.twig');
	if (is_home()){
		array_unshift($templates, 'home.twig');
	}
	Timber::render('index.twig', $context);


