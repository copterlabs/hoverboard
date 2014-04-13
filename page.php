<?php
/**
 * The template for displaying all pages
 *
 * @package     WordPress
 * @subpackage  Hoverboard
 * @since       0.1.0
 */

get_header();
get_template_part('common/main-column', 'top');

if( have_posts() ):
    while( have_posts() ):
        the_post();

?>
    <article class="post">

        <h1><?php the_title(); ?></h1>

        <?php the_content(); ?>

    </article>
<?php 

    endwhile;
endif;

get_template_part('common/main-column', 'bottom');
get_footer();

