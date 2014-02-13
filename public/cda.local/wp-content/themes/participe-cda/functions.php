<?php

require_once("Tax-meta-class/Tax-meta-class.php");
show_admin_bar( false );
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
        $categories = get_categories(array('orderby'=>'id'));
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
        $twig->addFilter('hex2rgb', new Twig_Filter_Function('hex2rgb'));
        return $twig;
    }

    function hex2rgb( $colour ) {
        if ( $colour[0] == '#' ) {
                $colour = substr( $colour, 1 );
        }
        if ( strlen( $colour ) == 6 ) {
                list( $r, $g, $b ) = array( $colour[0] . $colour[1], $colour[2] . $colour[3], $colour[4] . $colour[5] );
        } elseif ( strlen( $colour ) == 3 ) {
                list( $r, $g, $b ) = array( $colour[0] . $colour[0], $colour[1] . $colour[1], $colour[2] . $colour[2] );
        } else {
                return false;
        }
        $r = hexdec( $r );
        $g = hexdec( $g );
        $b = hexdec( $b );
        return "($r, $g, $b,.8)";
    }

    function myfoo($text){
        $text .= ' bar!';
        return $text;
    }

    function load_scripts(){
        //wp_enqueue_script( 'site-vendor', get_template_directory_uri() . '/assets/scripts/vendor.js', null, '0.1', true );
        wp_enqueue_script( 'site-plugins', get_template_directory_uri() . '/assets/js/plugins.min.js', null, '0.1', true );
        wp_enqueue_script( 'site-main', get_template_directory_uri() . '/assets/js/main.min.js', null, '0.1', true );

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
        'supports'            => array( 'title', 'editor', 'comments', 'custom-fields'),
        'taxonomies'          => array( 'category', 'post_tag' ),
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
    //List of images to build background and slideshow image
    $my_meta->addText($prefix.'text_field_id',array('name'=> __('Lista de ID para imagens a serem utilizadas no mosaico e slideshow ','tax-meta')));
      

    //Finish Meta Box Decleration
    $my_meta->Finish();
}


/**
 * Redirect users on any wp-admin pages
 */
function wp_admin_no_show_admin_redirect() {
    // Whitelist multisite super admin
    if(function_exists('is_multisite')) {
        if( is_multisite() && is_super_admin() ) {
            return;
        }
    }

    if ($_GET['redirect_to']) {
        $redirect = $_GET['redirect_to'];
    }


    if ( 'none' == get_option( 'wp_admin_no_show_redirect_type' ) ) {
        return;
    }

    global $wp_admin_no_show_wp_user_role;
    $disable = false;

    $blacklist_roles = get_option( 'wp_admin_no_show_blacklist_roles', array('subscriber', 'filiado') );
    if ( false === $disable && !empty( $blacklist_roles ) ) {
        if ( !is_array( $blacklist_roles ) ) {
            $blacklist_roles = array( $blacklist_roles );
        }
        foreach ( $blacklist_roles as $role ) {
            if (preg_match("/administrator/i", $role )) {
                // whitelist administrator for redirect
                continue;
            } else if ( current_user_can( $role ) ) {
                $disable = true;
            }
        }
    }


    if ( false !== $disable ) {
        if ( 'page' == get_option( 'wp_admin_no_show_redirect_type' ) ) {
            $page_id = get_option( 'wp_admin_no_show_redirect_page' );
            $redirect = get_permalink( $page_id );
        } else {
            //$redirect = get_bloginfo( 'url' );
            if (empty($redirect)) {
                $redirect = 'http://'.$_SERVER['SERVER_NAME'];
            }
        }

        if( is_admin() ) {
            if ( headers_sent() ) {
                echo '<meta http-equiv="refresh" content="0;url=' . $redirect . '">';
                echo '<script type="text/javascript">document.location.href="' . $redirect . '"</script>';
            } else {
                wp_redirect($redirect);
                exit();
            }
        }
    }
}
add_action( 'admin_head', 'wp_admin_no_show_admin_redirect', 0 );

require_once("routes.php");