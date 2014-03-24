<?php

require_once("Tax-meta-class/Tax-meta-class.php");
require_once("meta-box-class/my-meta-box-class.php");

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
        $data['is_admin'] = is_admin();
        $data['redirect_to'] = 'http://' . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];

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

function propostas_post_type() {

    $labels = array(
        'name'                => _x( 'Propostas', 'Post Type General Name', 'text_domain' ),
        'singular_name'       => _x( 'Proposta', 'Post Type Singular Name', 'text_domain' ),
        'menu_name'           => __( 'Proposta', 'text_domain' ),
        'parent_item_colon'   => __( 'Proposta Pai', 'text_domain' ),
        'all_items'           => __( 'Todas Propostas', 'text_domain' ),
        'view_item'           => __( 'Ver Proposta', 'text_domain' ),
        'add_new_item'        => __( 'Adicionar Nova Proposta', 'text_domain' ),
        'add_new'             => __( 'Nova Proposta', 'text_domain' ),
        'edit_item'           => __( 'Editar Proposta', 'text_domain' ),
        'update_item'         => __( 'Atualizar Proposta', 'text_domain' ),
        'search_items'        => __( 'Procurar Propostas', 'text_domain' ),
        'not_found'           => __( 'Nenhuma proposta encontrada', 'text_domain' ),
        'not_found_in_trash'  => __( 'Nenhuma proposta encontrada na lixeira', 'text_domain' ),
    );
    $args = array(
        'label'               => __( 'proposta', 'text_domain' ),
        'description'         => __( 'Propostas a serem analisadas', 'text_domain' ),
        'labels'              => $labels,
        'supports'            => array( 'title', 'editor', 'thumbnail', 'excerpt'),
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
    register_post_type( 'proposta', $args );

}

function sugestoes_post_type() {

    $labels = array(
        'name'                => _x( 'Sugestões', 'Post Type General Name', 'text_domain' ),
        'singular_name'       => _x( 'Sugestão', 'Post Type Singular Name', 'text_domain' ),
        'menu_name'           => __( 'Sugestão', 'text_domain' ),
        'parent_item_colon'   => __( 'Sugestão Pai', 'text_domain' ),
        'all_items'           => __( 'Todas as sugestões', 'text_domain' ),
        'view_item'           => __( 'Ver sugestão', 'text_domain' ),
        'add_new_item'        => __( 'Adicionar nova sugestão', 'text_domain' ),
        'add_new'             => __( 'Nova sugestão', 'text_domain' ),
        'edit_item'           => __( 'Editar sugestão', 'text_domain' ),
        'update_item'         => __( 'Atualizar sugestão', 'text_domain' ),
        'search_items'        => __( 'Procurar sugestões', 'text_domain' ),
        'not_found'           => __( 'Nenhuma sugestão encontrada', 'text_domain' ),
        'not_found_in_trash'  => __( 'Nenhuma sugestão encontrada na lixeira', 'text_domain' ),
    );
    $args = array(
        'label'               => __( 'sugestao', 'text_domain' ),
        'description'         => __( 'Sugestões a serem analisadas', 'text_domain' ),
        'labels'              => $labels,
        'supports'            => array( 'title', 'editor', 'thumbnail', 'excerpt'),
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
    register_post_type( 'sugestao', $args );

}


// Hook into the 'init' action
add_action( 'init', 'avaliacoes_post_type', 0 );
add_action( 'init', 'propostas_post_type', 0 );
add_action( 'init', 'sugestoes_post_type', 0 );


if (is_admin()){

    /*
    * prefix of meta keys, optional
    */
    $prefix = 'cda_';

    /* 
    * configure your meta box
    */
    $config_cat = array(
    'id' => 'cda_meta_box_cat',          // meta box id, unique per meta box
    'title' => '',          // meta box title
    'pages' => array('category'),        // taxonomy name, accept categories, post_tag and custom taxonomies
    'context' => 'normal',            // where the meta box appear: normal (default), advanced, side; optional
    'fields' => array(),            // list of meta fields (can be added by field arrays)
    'local_images' => false,          // Use local or hosted images (meta box images for add/remove)
    'use_with_theme' => true          //change path if used with theme set to true, false for a plugin or anything else for a custom path(default false).
    );

    $config_post = array(
    'id'             => 'cda_meta_box_post',          // meta box id, unique per meta box
    'title'          => ' ',          // meta box title
    'pages'          => array('proposta'),      // post types, accept custom post types as well, default is array('post'); optional
    'context'        => 'normal',            // where the meta box appear: normal (default), advanced, side; optional
    'priority'       => 'high',            // order of meta box: high (default), low; optional
    'fields'         => array(),            // list of meta fields (can be added by field arrays)
    'local_images'   => false,          // Use local or hosted images (meta box images for add/remove)
    'use_with_theme' => true          //change path if used with theme set to true, false for a plugin or anything else for a custom path(default false).
    );


    /*
    * Initiate your meta box
    */
    $my_meta_cat =  new Tax_Meta_Class($config_cat);
    $my_meta_post=  new AT_Meta_Box($config_post);


    //radio field
    $my_meta_cat->addRadio($prefix.'radio_field_id',array('piloto'=>'Projeto Piloto','conceito'=>'Projeto Conceito'),array('name'=> __('Tipo do Projeto','tax-meta'), 'std'=> array('conceito')));
    //Image field
    $my_meta_cat->addImage($prefix.'image_field_id',array('name'=> __('Imagem representativa ','tax-meta')));
    //Color field
    $my_meta_cat->addColor($prefix.'color_field_id',array('name'=> __('Cor representativa ','tax-meta')));
    //List of images to build background and slideshow image
    $my_meta_cat->addText($prefix.'text_field_id',array('name'=> __('Lista de ID para imagens a serem utilizadas no mosaico e slideshow ','tax-meta')));

    $my_meta_cat->addText($prefix.'text_field_id_2',array('name'=> __('Lista de ID de ícones do passo 3','tax-meta')));
    $my_meta_cat->addImage($prefix.'image_2_field_id',array('name'=> __('Mapa para Sugestões do Passo 3','tax-meta')));

    //Color field
    $my_meta_post->addColor($prefix.'color_field_id_post',array('name'=> 'Cor representativa '));


    //Finish Meta Box Decleration
    $my_meta_cat->Finish();
    $my_meta_post->Finish();
}

// Renomeia EXERPT para SUBTITULO
function lead_meta_box() {
    add_meta_box( 'postexcerpt', 'Subtitulo', 'post_excerpt_meta_box', 'proposta', 'normal', 'core' );
}
add_action( 'admin_menu', 'lead_meta_box' );


// Renomeia FEATURED IMAGE para ICONE
add_action('do_meta_boxes', 'change_image_box');
function change_image_box()
{
    add_meta_box('postimagediv', __('Ícone (16x16px)'), 'post_thumbnail_meta_box', 'proposta', 'normal', 'high');
}


function category_custom_html() {

    $screen = get_current_screen();
    // print_r($screen);
?>
    <table id="edit_points_wrapper" class="form-table enable">
        <tr class="form-field">
            <th scope="row" valign="top"><label>&nbsp;</label></th>
            <td class="enabled">
                <?php add_thickbox(); ?>
                <a id="edit_points" class="thickbox button button-primary en" href="<?php echo get_bloginfo('url'); ?>/projetos/">Editar os pontos no mapa</a>
             </td>
            <td class="disabled">
                <?php add_thickbox(); ?>
                <a class=" button button-primary" href="javascript://" onclick="alert('Atenção! Clique em Atualizar, depois volte a essa tela, para editar os pontos no mapa.');">Editar os pontos no mapa</a>
             </td>
        </tr>
    </table>

    <script type="text/javascript">
        var wrapper = document.getElementById('edit_points_wrapper');
        var editPoints = document.getElementById('edit_points');
        var slug = document.getElementById('slug');
        var field = document.getElementById('cda_image_2_field_id[src]');
        editPoints.href = editPoints.href + slug.value + '/edit_points/?TB_iframe=true&width=1024';

        function editPointsToggle() {

            if (field.value.length)
                wrapper.style.display = 'table';
            else
                wrapper.style.display = 'none';

        }

        if (!field.value.length) {
            wrapper.className = 'form-table disable';
        }

        field.addEventListener('DOMAttrModified',editPointsToggle);
        editPointsToggle();

    </script>

    <style type="text/css">

        .edit_points_wrapper .button {
            display: inline;
            padding: 5px;
        }

        .disable .enabled,
        .enable .disabled,
        .hide 
        {
            display: none;
        }

    </style>

<?php
}
add_action( 'category_edit_form', 'category_custom_html' );

// add_action('admin_head', 'my_custom_fonts');
// function my_custom_fonts() {
//   echo '<style>
//             #
//     body, td, textarea, input, select {
//       font-family: "Lucida Grande";
//       font-size: 12px;
//     } 
//   </style>';
// }


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