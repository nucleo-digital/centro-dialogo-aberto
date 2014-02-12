<?php

Timber::add_route('projetos/:name', function($params){
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

    $query = 'category_name='.$params['name'];
    Timber::load_template('capa-passo-1.php', $query);
});

Timber::add_route('projetos/:name/votar', function($params){
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