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
$query = array(
    'orderby' => 'meta_value_num',
    'order' => 'asc',
    'posts_per_page' => 12,
    'post_type' => 'avaliacao',
    'meta_query' => array(
        array(
            'key' => 'ordem',
        )
    )

);
$context['posts'] = Timber::get_posts($query);
$category_slug = get_query_var('category_name');
$obj_category = get_category_by_slug( $category_slug );
$context['step_avaliacao'] = 'step_selected';
$context['show_step_2'] = true;

$context['nome_projeto'] = $obj_category->name;
$context['id_projeto'] = $obj_category->term_id;
$context['slug_projeto'] = $obj_category->slug;
$context['cor_projeto'] = get_tax_meta($obj_category->term_id,'cda_color_field_id');
$context['username'] = get_query_var('username');


global $current_user;
get_currentuserinfo();

$context['profile'] = $current_user;
// $context['compartilhar_link'] = get_bloginfo('home') . '/projetos/' . $category_slug . '/'. $current_user->user_login;


if ($_GET['answered']==1) {
    $context['mensagem'] = 'Você já deu sua opnião sobre esse projeto. Nevegue pelos outros para contribuir mais.';
}

$time = current_time('mysql');

$comments = get_comments(array('order'=>'desc', 'meta_key' => 'projeto', 'meta_value'=> $category_slug));

$votes = array();
$votes_first = array();

foreach ($comments as $i => $comment) {

    if ($comment->comment_content != '') 
        $votes[$comment->comment_post_ID][$comment->comment_content]++;

    arsort($votes[$comment->comment_post_ID]);

    $key = $votes[$comment->comment_post_ID];

    reset($key);
    $key = key($key);

    $votes_first[$comment->comment_post_ID] = $key;

}

foreach ($votes as $x => $post) {

    $sum = array_sum($post);

    foreach ($post as $y => $vote) {
        $votes[$x][$y] = intval($vote * 100 / $sum);
    }

}

$context['votes'] = $votes;
$context['votes_first'] = $votes_first;

print_r($context['votes_first']);

Timber::render('resultado-passo-1-geral.twig', $context);
