<?php
/**
 * The loop that displays posts.
 *
 * @package     WordPress
 * @subpackage  Hoverboard
 * @since       0.1.0
 * @see         http://codex.wordpress.org/The_Loop
 * @see         http://codex.wordpress.org/Template_Tags
 */

/* If there are no posts to display, such as an empty archive page */
if (!have_posts()):

?>
    <article class="post">
        <h2>No Posts Here</h2>
        <p>
            Sorry, but there are no posts here. 
            <a href="<?php echo home_url('/'); ?>">Back to the home page.</a>
        </p>
    </article>
<?php

else:
    while (have_posts()):
        the_post();

        $link_title = sprintf(
            esc_attr('Permalink to %s'), 
            the_title_attribute('echo=0')
        );

?>
    <article class="post preview">

        <h2>
            <a href="<?php the_permalink(); ?>" 
               title="<?php echo $link_title; ?>" 
               rel="bookmark"><?php the_title(); ?></a>
        </h2>

        <?php the_excerpt(); ?>
        <? if (get_post_type()==='post'): ?> 
        <div class="post-meta">
            <small><?php //rw_posted_on(); ?></small>
        </div>
        <? endif; ?>

    </article>
<?php

    endwhile;
endif;

// Displays pagination nav when applicable
if ($wp_query->max_num_pages>1) {
    Hoverboard::pagination();
}
