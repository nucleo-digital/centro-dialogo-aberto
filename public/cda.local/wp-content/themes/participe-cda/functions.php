<?php

require_once("Tax-meta-class/Tax-meta-class.php");

/*
 * List of pages available:
 *
 * entrar
 * meu-perfil
 * minhas-avaliacoes
 *
**/

    add_theme_support('post-formats');
    add_theme_support('post-thumbnails');
    add_theme_support('menus');

    add_filter('get_twig', 'add_to_twig');
    add_filter('timber_context', 'add_to_context');

    add_action('wp_enqueue_scripts', 'load_scripts');

    define('THEME_URL', get_template_directory_uri());
    function add_to_context($data){
        /* this is where you can add your own data to Timber's context object */
        $categories = get_categories();
        foreach ($categories as $k => $val) {
            $categories[$k]->tipo_projeto          = get_tax_meta($val->term_id,'cda_radio_field_id');
            if (get_tax_meta($val->term_id,'cda_radio_field_id') == 'conceito') {
                $categories[$k]->tipo_projeto_label  = 'Projeto Conceito';
            } else {
                $categories[$k]->tipo_projeto_label  = 'Projeto Piloto';
            }
            $categories[$k]->imagem_representativa = get_tax_meta($val->term_id,'cda_image_field_id');
            $categories[$k]->imagem_representativa['src'] = wp_get_attachment_url( $categories[$k]->imagem_representativa['id'] );
            $categories[$k]->cor_representativa    = get_tax_meta($val->term_id,'cda_color_field_id');
        }
        $data['categories'] = $categories;

        $data['is_user_logged_in'] = is_user_logged_in();
        $data['current_user'] = wp_get_current_user();

        $data['menu'] = new TimberMenu();
        return $data;
    }

    function add_to_twig($twig){
        /* this is where you can add your own fuctions to twig */
        $twig->addExtension(new Twig_Extension_StringLoader());
        $twig->addFilter('myfoo', new Twig_Filter_Function('myfoo'));
        return $twig;
    }

    function myfoo($text){
        $text .= ' bar!';
        return $text;
    }

    function load_scripts(){
        wp_enqueue_script( 'site-vendor', get_template_directory_uri() . '/scripts/vendor.js', null, '0.1', true );
        wp_enqueue_script( 'site-plugins', get_template_directory_uri() . '/scripts/plugins.js', null, '0.1', true );
        wp_enqueue_script( 'site-main', get_template_directory_uri() . '/scripts/main.js', null, '0.1', true );

    }


// Register Custom Post Type
function avaliacoes_post_type() {

    $labels = array(
        'name'                => _x( 'Avaliações', 'Post Type General Name', 'text_domain' ),
        'singular_name'       => _x( 'Avaliação', 'Post Type Singular Name', 'text_domain' ),
        'menu_name'           => __( 'Avaliação', 'text_domain' ),
        'parent_item_colon'   => __( 'Avaliação Pai', 'text_domain' ),
        'all_items'           => __( 'Todas Avaliações', 'text_domain' ),
        'view_item'           => __( 'Ver Avaliação', 'text_domain' ),
        'add_new_item'        => __( 'Adicionar Nova Avaliação', 'text_domain' ),
        'add_new'             => __( 'Novas Avaliação', 'text_domain' ),
        'edit_item'           => __( 'Editar Avaliação', 'text_domain' ),
        'update_item'         => __( 'Atualizar Avaliação', 'text_domain' ),
        'search_items'        => __( 'Procurar Avaliações', 'text_domain' ),
        'not_found'           => __( 'Nenhuma avaliação encontrada', 'text_domain' ),
        'not_found_in_trash'  => __( 'Nenhuma avaliação encontrada na lixeira', 'text_domain' ),
    );
    $args = array(
        'label'               => __( 'avaliacao', 'text_domain' ),
        'description'         => __( 'Perguntas para usuário descrever o projeto', 'text_domain' ),
        'labels'              => $labels,
        'supports'            => array( 'title', 'editor', 'comments', ),
        'taxonomies'          => array( 'category' ),
        'hierarchical'        => false,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_nav_menus'   => true,
        'show_in_admin_bar'   => true,
        'menu_position'       => 5,
        'menu_icon'           => '',
        'can_export'          => true,
        'has_archive'         => true,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'capability_type'     => 'page',
    );
    register_post_type( 'avaliacao', $args );

}

// Hook into the 'init' action
add_action( 'init', 'avaliacoes_post_type', 0 );


if (is_admin()){

    /*
    * prefix of meta keys, optional
    */
    $prefix = 'cda_';

    /* 
    * configure your meta box
    */
    $config = array(
    'id' => 'cda_meta_box',          // meta box id, unique per meta box
    'title' => '',          // meta box title
    'pages' => array('category'),        // taxonomy name, accept categories, post_tag and custom taxonomies
    'context' => 'normal',            // where the meta box appear: normal (default), advanced, side; optional
    'fields' => array(),            // list of meta fields (can be added by field arrays)
    'local_images' => false,          // Use local or hosted images (meta box images for add/remove)
    'use_with_theme' => true          //change path if used with theme set to true, false for a plugin or anything else for a custom path(default false).
    );

    /*
    * Initiate your meta box
    */
    $my_meta =  new Tax_Meta_Class($config);


    //radio field
    $my_meta->addRadio($prefix.'radio_field_id',array('piloto'=>'Projeto Piloto','conceito'=>'Projeto Conceito'),array('name'=> __('Tipo do Projeto','tax-meta'), 'std'=> array('conceito')));
    //Image field
    $my_meta->addImage($prefix.'image_field_id',array('name'=> __('Imagem representativa ','tax-meta')));
    //Color field
    $my_meta->addColor($prefix.'color_field_id',array('name'=> __('Cor representativa ','tax-meta')));
 
      

    //Finish Meta Box Decleration
    $my_meta->Finish();
}

Timber::add_route('projetos/:name', function($params){
    if (!is_user_logged_in()) {
        wp_redirect( get_bloginfo('url') . '/entrar/?redirect_to='.get_bloginfo('url').'/projetos/'.$params['name'] ); exit;
    }
    
    global $current_user;
    get_currentuserinfo();
    
    $has_comment = get_comments(array('user_id' => $current_user->ID, 'meta_key' => 'projeto', 'meta_value'=> $params['name']));

    if ( count($has_comment) > 0 ) {
        //redirecionar para pagina de resultado
    }

    $query = 'category_name='.$params['name'];
    Timber::load_template('capa-passo-1.php', $query);
});

Timber::add_route('projetos/:name/votar', function($params){
    $query = 'posts_per_page=12&post_type=avaliacao';
    Timber::load_template('archive.php', $query);
});

Timber::add_route('projetos/:name/resultados', function($params){
    $query = 'category_name='.$params['name'] . '&posts_per_page=12&post_type=avaliacao';
    Timber::load_template('resultado-passo-1.php', $query);
});