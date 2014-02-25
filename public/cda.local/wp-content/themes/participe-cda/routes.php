<?php

Timber::add_route('projetos/:name', function($params){
    $query = 'category_name='.$params['name'];
    Timber::load_template('capa-passo-1.php', $query);
});

Timber::add_route('projetos/:name/propostas', function($params){

    if (!is_user_logged_in()) {
        wp_redirect( get_bloginfo('url') . '/entrar/?redirect_to='.get_bloginfo('url').'/projetos/'.$params['name'].'/propostas' ); exit;
    }

    $query = 'category_name='. $params['name'] . '&post_type=proposta&username='.$params['user'];
    Timber::load_template('passo-2.php', $query);
});

Timber::add_route('projetos/:name/propostas/:aba', function($params){

    if (!is_user_logged_in()) {
        wp_redirect( get_bloginfo('url') . '/entrar/?redirect_to='.get_bloginfo('url').'/projetos/'.$params['name'].'/propostas/'.$params['aba'] ); exit;
    }
    
    $query = 'category_name='.$params['name'] . '&post_name=' . $params['aba'] . '&post_type=proposta&username='.$params['user'];
    Timber::load_template('passo-2.php', $query);
});

Timber::add_route('projetos/:name/propostas/:aba/comment', function($params){
    $query = 'category_name='.$params['name'] . '&post_name=' . $params['aba'] . '&post_type=proposta&username='.$params['user'];
    Timber::load_template('passo-2_comment.php', $query);
});

Timber::add_route('projetos/:name/propostas/:aba/vote/:thumb/:action', function($params){
    $query = 'thumb=' . $params['thumb'] . '&category_name='. $params['name'] . '&action='. $params['action'] . '&post_name=' . $params['aba'] . '&post_type=proposta&username='.$params['user'];
    Timber::load_template('passo-2_vote.php', $query);
});

Timber::add_route('projetos/:name/propostas/:aba/v', function($params){
    $query = 'category_name='. $params['name'] . '&post_name=' . $params['aba'] . '&post_type=proposta&username='.$params['user'];
    Timber::load_template('passo-2_vote.php', $query);
    Timber::load_template('passo-2_v.php', $query);
});


Timber::add_route('projetos/:name/votar', function($params){
    if (!is_user_logged_in()) {
        wp_redirect( get_bloginfo('url') . '/entrar/?redirect_to='.get_bloginfo('url').'/projetos/'.$params['name'] ); exit;
    }
    
    global $current_user;
    get_currentuserinfo();
    
    $has_comment = get_comments(array('user_id' => $current_user->ID, 'meta_key' => 'projeto', 'meta_value'=> $params['name']));

    if ( count($has_comment) > 0 ) {
        //redirecionar para pagina de resultado
        $voting = '';
        foreach ($has_comment as $comment) {
            $voting .= $comment->comment_content.',';
        }
        header('Location: '.get_bloginfo('url').'/projetos/'.$params['name'].'/resultados?answered=1&voting='.$voting);
        exit;
    }
    
    $query = 'posts_per_page=12&post_type=avaliacao&cat_name=' . $params['name'];
    Timber::load_template('archive.php', $query);
});

Timber::add_route('projetos/:name/resultados', function($params){
    $query = 'category_name='.$params['name'] . '&posts_per_page=12&post_type=avaliacao';
    Timber::load_template('resultado-passo-1.php', $query);
});

Timber::add_route('projetos/:name/:user', function($params){
    $query = 'category_name='.$params['name'] . '&posts_per_page=12&post_type=avaliacao&username='.$params['user'];
    Timber::load_template('resultado-passo-1.php', $query);
});


