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
$context['id_projeto'] = $obj_category->term_id;
$context['cor_projeto'] = $context['categories'][$obj_category->term_id]->cor_representativa;

if ($_GET['answered']==1) {
    $context['mensagem'] = 'Você já deu sua opnião sobre esse projeto. Nevegue pelos outros para contribuir mais.';
}

global $current_user;
get_currentuserinfo();

$time = current_time('mysql');
$answers = explode(',', $_GET['voting']);

$i=0;

$has_comment = get_comments(array('user_id' => $current_user->ID, 'meta_key' => 'projeto', 'meta_value'=> $category_slug));

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
