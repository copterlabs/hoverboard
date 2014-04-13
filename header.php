<?php
/**
 * The default header template
 *
 * @package     WordPress
 * @subpackage  Hoverboard
 * @since       0.1.0
 */

// Sets a title for the site's home link
$title = esc_attr(get_bloginfo('name')) . ' &mdash; '
       . esc_attr(get_bloginfo('description'));

/* 
 * To override defaults, pass wp_nav_menu config options to this method as an array
 * For config options, see http://codex.wordpress.org/Function_Reference/wp_nav_menu
 */
$config = array('menu_class' => 'nav navbar-nav navbar-right');
$nav = Hoverboard::get_nav_menu($config);

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>

<meta charset="<?php bloginfo('charset'); ?>" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />

<title><?php wp_title('&rsaquo;'); ?></title>

<?php wp_head(); ?>

</head>

<body class="<?= Hoverboard::get_wrapper_class() ?>">

    <header>
        <div class="container">
            <div class="row">
                <nav class="navbar navbar-default" role="navigation">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle" 
                                data-toggle="collapse" data-target=".main-nav">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <a class="navbar-brand" 
                           href="<?= home_url('/') ?>" 
                           title="<?= $title ?>" 
                           rel="home"><?php bloginfo('name', 'display'); ?></a>
                    </div>
                    <?= $nav ?> 
                </nav>
            </div>
        </div>
    </header>
