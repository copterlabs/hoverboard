<?php
/**
 * The default template, used when no other template is supplied
 *
 * @package     WordPress
 * @subpackage  Hoverboard
 * @since       0.1.0
 * @see         http://codex.wordpress.org/Template_Hierarchy
 */

get_header();
get_template_part('common/main-column', 'top');

get_template_part( 'loop', 'index' );

get_template_part('common/main-column', 'bottom');
get_footer();
