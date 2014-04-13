<?php
/**
 * The Template for displaying all single posts
 *
 * @package     WordPress
 * @subpackage  Hoverboard
 * @since       0.1.0
 */

get_header();
get_template_part('common/main-column', 'top');

while (have_posts()):
    the_post();

?>
        <article class="post">
            <h1><?php the_title(); ?></h1>

<?php

the_content();

if (get_post_type() == 'post'):

?>

            <ul class="post-meta list-inline well well-sm">
                <li><?php //rw_posted_on(); ?></li>
                <li><?php //rw_posted_in(); ?></li>
                <li><?php comments_popup_link('Leave a comment', '1 Comment', '% Comments'); ?></li>
            </ul>
           
<?php

endif;

comments_template('', TRUE);

?>
        </article>
<?php 

endwhile;

get_template_part('common/main-column', 'bottom');
get_footer();
