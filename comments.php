<?php
/**
 * The template for displaying comments
 *
 * The area of the page that contains both current comments
 * and the comment form.  The actual display of comments is
 * handled by a callback to rw_comment which is
 * located in the functions.php file.
 *
 * @package     WordPress
 * @subpackage  Hoverboard
 * @since       0.1.0
 */

if (post_password_required()) {
    return;
}

if (have_comments()):

?>
        <h3 id="comments-title">Comments for This Entry</h3>

        <ul id="post-comments" class="media-list">
            <?php wp_list_comments('callback=Hoverboard::comment_walker'); ?> 
        </ul>
<?php 

endif;

Hoverboard::comments_form();
