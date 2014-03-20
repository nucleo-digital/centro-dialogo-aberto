<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * To generate specific templates for your pages you can use:
 * /mytheme/views/page-mypage.twig
 * (which will still route through this PHP file)
 * OR
 * /mytheme/page-mypage.php
 * (in which case you'll want to duplicate this file and save to the above path)
 *
 * Methods for TimberHelper can be found in the /functions sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */

$context = Timber::get_context();
$post = new TimberPost();
$context['post'] = $post;
$context['form_action'] = get_bloginfo('home').'/senha/';
$context['redirect_to'] = $_GET['redirect_to'];
$context['mensagem'] = '';
$context['mensagem_status'] = 'danger';


switch($_GET['action']) {
    case 'lostpassword':
        $username    = $_POST['user_name'];
            
        if( empty( $username ) ) {

            $context['mensagem'] = " Digite um nome de usuário ou endereço de email.";
            $invalid_form = true;

        } else {
        
            // lets generate our new password
            $random_password = wp_generate_password( 12, false );
            
            // Get user data by field and data, other field are ID, slug, slug and login
            $user = get_user_by( 'email', $username );

            if ( !$user->ID ) {
                $user = get_user_by( 'login', $username );
            }

            if ( !$user->ID ) {

                $context['mensagem'] = "Apelido ou email inválido.";
                $invalid_form = true;

            } else {

                $update_user = wp_update_user( array (
                        'ID' => $user->ID, 
                        'user_pass' => $random_password
                    )
                );
                
                // if  update user return true then lets send user an email containing the new password
                if( $update_user ) {
                    $to = $email;
                    $subject = 'Nova senha';
                    $sender = get_option('name');
                    
                    $message = 'Sua nova senha é: '.$random_password;
                    
                    $headers[] = 'MIME-Version: 1.0' . "\r\n";
                    $headers[] = 'Content-type: text/html; charset=utf-8' . "\r\n";
                    $headers[] = "X-Mailer: PHP \r\n";
                    $headers[] = 'From: '.$sender.' < '.$email.'>' . "\r\n";
                    
                    $mail = wp_mail( $to, $subject, $message, $headers );

                    if( $mail ) {
                        $context['mensagem_status'] = 'info';
                        $context['mensagem'] = 'Confira o link de confirmação em seu email. <br> <a href="../entrar">Clique aqui para fazer login</a>';
                    }

                } else {
                    $context['mensagem'] = "Ocorreu um erro.";
                    $invalid_form = true;
                }
            }
        }
        
        if( ! empty( $error ) )
            echo '<div class="error_login"><strong>ERROR:</strong> '. $error .'</div>';
        
        if( ! empty( $success ) )
            echo '<div class="updated"> '. $success .'</div>';

        break;
    
    default:
        # code...
        break;
}

Timber::render(array('page-' . $post->post_name . '.twig', 'page.twig'), $context);