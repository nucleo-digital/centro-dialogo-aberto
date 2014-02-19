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

$context['nome_projeto'] = $obj_category->name;
$context['id_projeto'] = $obj_category->term_id;
$context['slug_projeto'] = $obj_category->slug;
$context['cor_projeto'] = get_tax_meta($obj_category->term_id,'cda_color_field_id');
$context['username'] = get_query_var('username');

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

if ($_GET['answered']==1) {
    $context['mensagem'] = 'Você já deu sua opnião sobre esse projeto. Nevegue pelos outros para contribuir mais.';
}

$time = current_time('mysql');

$has_comment = get_comments(array('order'=>'desc', 'user_id' => $current_user->ID, 'meta_key' => 'projeto', 'meta_value'=> $category_slug));


if (isset($_GET['voting'])) {
    $answers = explode(',', $_GET['voting']);
    foreach ($context['posts'] as $k => $p) {
        $context['voting'][] = array('comment_post_ID'=>$p->ID,'comment_content'=>$answers[$k]);
    }
} else {
    $context['voting'] = $has_comment;
}

$i=0;
if (count($has_comment) == 0) {
    foreach ($context['posts'] as $post) {

        $comment_data = array(
            'comment_post_ID' => $post->ID,
            'comment_author' => $current_user->user_login,
            'comment_author_email' => $current_user->user_email,
            'comment_content' => $answers[$i],
            'user_id' => $current_user->ID,
            'comment_date' => $time,
            'comment_approved' => 1,
        );

        $comment_id = wp_insert_comment($comment_data);

        add_comment_meta( $comment_id, 'projeto', $obj_category->slug, true );

        $i++;
    }
}

Timber::render('resultado-passo-1.twig', $context);
