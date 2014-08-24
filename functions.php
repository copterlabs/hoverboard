<?php
/**
 * Theme setup and function definitions
 *
 * @package     WordPress
 * @subpackage  Hoverboard
 * @since       0.1.0
 */

// Prevents direct access by checking for a WP-specific constant
if (!defined('DB_NAME')) {
    header( 'HTTP/1.0 403 Forbidden' );
    die;
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which runs
 * before the init hook. The init hook is too late for some features, such as 
 * indicating support post thumbnails.
 *
 * You can override hoverboard_setup() in a child theme by adding your own 
 * hoverboard_setup() to your child theme's functions.php file.
 *
 * Alternatively, you can remove particular actions using remove_action().
 *
 * @since 0.1.0
 */

/**
 * A class to contain all theme functionality
 */
class Hoverboard
{
    /**
     * Hooks the init() method to the proper action
     * @return void
     * @since  0.1.0
     */
    public function __construct(  ) {
        add_action('after_setup_theme', array($this, 'init'), 20);
    }

    /**
     * Initializes the theme, loads dependencies, and registers actions
     * @return void
     * @since  0.1.0
     */
    public function init(  ) {
        // Adds post thumbnails support
        add_theme_support('post-thumbnails');

        // Creates the top nav menu location
        register_nav_menus(array(
            'primary' => 'Main Navigation', 
        ));

        // Loads required dependencies for the theme
        $bower_path = TEMPLATEPATH . '/bower_components';
        require_once $bower_path . '/wp-bootstrap-navwalker/wp_bootstrap_navwalker.php';

        // See http://codex.wordpress.org/Plugin_API/Action_Reference
        add_action('wp_head',               array($this, 'header'));
        add_action('widgets_init',          array($this, 'widgets'));
        add_action('wp_footer',             array($this, 'footer'));
        add_action('admin_head',            array($this, 'admin_head'));
        add_action('wp_enqueue_scripts',    array($this, 'enqueue_assets'));

        // See http://codex.wordpress.org/Plugin_API/Filter_Reference
        add_filter('wp_title',              array($this, 'filter_title'), 10, 2);
        add_filter('wp_trim_excerpt',       array($this, 'trim_excerpt'));
        add_filter('excerpt_length',        array($this, 'excerpt_length'), 10, 1);
        add_filter('get_the_excerpt',       array($this, 'excerpt_more_link'));

        // Registers stylesheets and scripts for the theme
        add_action('hoverboard/header', array($this, 'add_ie_scripts'));

        // Removes unnecessary actions
        remove_action('wp_head', 'wlwmanifest_link');
        remove_action('wp_head', 'rsd_link');

        // Allows for custom actions to be hooked into Hoverboard
        do_action('hoverboard/init');
    }

    /**
     * Actions for the theme that are executed during the wp_head action
     * @return void
     * @see    http://codex.wordpress.org/Plugin_API/Action_Reference/wp_head
     * @since  0.1.0
     */
    public function header(  ) {
        do_action('hoverboard/header');
    }

    /**
     * Actions for the theme that are executed during the widgets_init action
     * @return void
     * @since  0.1.0
     */
    public function widgets(  ) {
        $sidebar_config = array(
            'name'          => __('Sidebar', 'hoverboard'),
            'id'            => 'main-sidebar',
            'description'   => __('The primary widget area', 'hoverboard'),
            'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
            'after_widget'  => '</li>',
            'before_title'  => '<h3 class="widget-title">',
            'after_title'   => '</h3>',
        );
        register_sidebar($sidebar_config);

        do_action('hoverboard/widgets');
    }

    /**
     * Actions for the theme that are executed during the wp_footer action
     * @return void
     * @since  0.1.0
     * @see    http://codex.wordpress.org/Plugin_API/Action_Reference/wp_footer
     */
    public function footer(  ) {
        do_action('hoverboard/footer');
    }

    /**
     * Actions for the theme that are executed during the admin_head action
     * @return void
     * @since  0.1.0
     * @see    http://codex.wordpress.org/Plugin_API/Action_Reference/admin_head
     */
    public function admin_head(  ) {
        do_action('hoverboard/admin_head');
    }

    /*
     * ASSET MANAGEMENT
     *************************************************************************/

    /**
     * Enqueues scripts and stylesheets for the theme
     *
     * @return  void
     * @since   0.1.0
     */
    public function enqueue_assets(  ) {
        // Set the location of the assets folder
        $assets_dir  = get_template_directory_uri() . '/assets';
        $assets_path = TEMPLATEPATH . '/assets';
        $bootstrap_version = '3.1.1';

        // Registers styles
        if (is_readable($assets_path . '/css/main.min.css')) {
            wp_enqueue_style(
                'hoverboard-main-styles',
                $assets_dir . '/css/main.min.css',
                FALSE,
                filemtime($assets_path . '/css/main.min.css')
            );
        }

        // This is only necessary if an IE-specific stylesheet is required
        if (is_readable($assets_path . '/css/ie.css')) {
            global $wp_styles;
            wp_enqueue_style(
                'theme-ie-styles',
                $assets_dir . '/css/ie.css',
                array(),
                '1.0.0b' . filemtime($assets_path . '/css/ie.css')
            );

            // Adds a conditional tag
            $wp_styles->add_data('theme-ie-styles', 'conditional', 'lte IE 8');
        }

        // Include a CDN copy of jQuery
        wp_dequeue_script('jquery');
        wp_deregister_script('jquery');

        wp_register_script(
            'jquery', 
            '//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js', 
            NULL, 
            '1.11.0', 
            FALSE
        );

        // Main JS
        wp_enqueue_script(
            'hoverboard-main-js',
            $assets_dir . '/js/main.min.js',
            array('jquery', 'bootstrap-scripts'),
            '1.0.0',
            TRUE
        );

        // Bootstrap scripts for built-in JS components
        wp_enqueue_script(
            'bootstrap-scripts',
            get_template_directory_uri() . '/bower_components/bootstrap/dist/js/bootstrap.min.js',
            array('jquery'),
            $bootstrap_version,
            TRUE
        );
    }

    public function add_ie_scripts(  ) {
        echo self::get_mustache()->render('ie-scripts');
    }


    /*
     * FILTERS
     *************************************************************************/

    /**
     * Makes changes to the <title> tag, by filtering the output of wp_title().
     *
     * @param   string $title      Title generated by wp_title()
     * @param   string $separator  The separator passed to wp_title()
     * @return  string             The new title, ready for the <title> tag
     * @since   0.1.0
     */
    public function filter_title( $title, $separator='&rsqauo;' ) {
        // Leaves feed titles alone
        if (is_feed()) {
            return $title;
        }

        // Grabs page values for posts that aren't on the first page
        global $page, $paged;

        // Starts over in the event of a search
        if (is_search()) {
            $title = sprintf('Search results for "%s"', get_search_query());

            if ($paged>2) {
                $title .= sprintf(' %s Page %s', $separator, $paged);
            }

            $title .= sprintf(' %s %s', $separator, get_bloginfo('name', 'display'));

            return $title;
        }

        // Makes sure the title doesn't start with a separator
        $title  = str_replace("$separator ", '', $title);
        $title .= !empty($title) ? " $separator " : NULL;
        $title .= get_bloginfo('name', 'display');

        // Shows site description if it's set and we're on the front page
        $site_description = get_bloginfo('description', 'display');
        if ($site_description && (is_home() || is_front_page())) {
            $title .= " $separator " . $site_description;
        }

        // Adds page numbers where applicable
        if (($curpage=max($page,$paged))>=2) {
            $title .= sprintf(' %s Page %s', $separator, $curpage);
        }

        return $title;
    }

    /**
     * Allows for excerpt formatting outside the loop
     * 
     * For whatever reason, the WP core doesn't allow trimming of 
     * content outside the loop. Also, allowing get_the_excerpt()
     * outside of the loop has been deprecated. What this means to
     * me is that I can either add this filter with duplicate code
     * or create an unecessary loop for pulling a one-off excerpt.
     * 
     * Way to go, WordPress. You ruined Christmas.
     * 
     * @param   string $text    The text to be trimmed
     * @return  string          The trimmed text
     * @since   0.1.0
     */
    public function trim_excerpt( $text='' ) {
        $text = strip_shortcodes( $text );
        $text = apply_filters('the_content', $text);
        $text = str_replace(']]>', ']]&gt;', $text);
        $excerpt_length = apply_filters('excerpt_length', 55);
        $excerpt_more = apply_filters('excerpt_more', '... ' . self::get_more_link());
        return wp_trim_words($text, $excerpt_length, $excerpt_more);
    }

    /**
     * Sets the default post excerpt length to 100 characters.
     *
     * To override this length in a child theme, remove the filter and add 
     * your own function tied to the excerpt_length filter hook.
     *
     * @return  int     The length in characters of the excerpt
     * @since   0.1.0
     */
    public function excerpt_length( $length=100 ) {
        return $length;
    }

    /**
     * Adds a pretty "Continue Reading" link to custom post excerpts.
     *
     * To override this link in a child theme, remove the filter and add your 
     * own function tied to the get_the_excerpt filter hook.
     *
     * @return  string Excerpt with a pretty "Continue Reading" link
     * @since   0.1.0
     */
    function excerpt_more_link( $output ) {
        if (has_excerpt() && !is_attachment()) {
            $output .= self::get_more_link();
        }
        return $output;
    }

    function get_more_link() {
            $variables = array(
                'permalink' => get_permalink(),
            );
            return self::get_mustache()->render('more-link', $variables);
    }


    /*
     * COMMENTS
     *************************************************************************/

    /**
     * A custom walker to output comments using Bootstrap 3 markup
     * @param  object $comment The comment object
     * @param  array  $args    Optional arguments
     * @param  int    $depth   How many levels of nested comments are allowed
     * @return void
     * @since  0.1.0
     */
    static function comment_walker( $comment, $args, $depth ) {
        global $post_id;

        $variables = array();

        // Stores various comment information
        $variables['comment_ID_attr'] = 'comment-' . get_comment_ID();
        $variables['comment_link']    = get_permalink() . '#' . $variables['comment_ID_attr'];
        $variables['comment_date']    = sprintf('%s at %s', get_comment_date(), get_comment_time());
        $variables['comment_text']    = get_comment_text();

        // Checks if the commenter is the post author
        $is_author = FALSE;
        $author_badge = NULL;
        if ($post=get_post($post_id)) {
            if ($comment->user_id===$post->post_author) {
                $is_author = TRUE;
                $author_badge =' <small><span class="label label-info">Author</span></small>';
            }
        }

        $variables['author_link']  = get_comment_author_link();
        $variables['author_badge'] = $author_badge;

        // For reply links
        if ($args['style']==='div') {
            $tag = 'div';
            $add_below = 'comment';
        } else {
            $tag = 'li';
            $add_below = 'div-comment';
        }

        $variables['tag'] = $tag;

        // Generates the comment reply link
        $comment_reply_custom_config = array(
            'reply_text' => 'Reply to this comment',
            'add_below' => $add_below,
            'depth' => $depth,
            'max_depth' => $args['max_depth'],
            'before' => '| ',
        );
        $comment_reply_config = array_merge($args, $comment_reply_custom_config);
        $variables['comment_reply'] = get_comment_reply_link($comment_reply_config);

        // Generates the avatar
        if ($args['avatar_size']!==0) {
            $variables['avatar'] = get_avatar($comment, $args['avatar_size']);
        } else {
            $variables['avatar'] = NULL;
        }

        // Adds a note if the comment is awaiting moderation
        if ($comment->comment_approved==='0') {
            $variables['moderation'] = '<span class="label label-default">'
                               . 'awaiting moderation'
                               . '</span>';
        } else {
            $variables['moderation'] = NULL;
        }

        echo self::get_mustache()->render('comments', $variables);
    }

    /**
     * Generates markup for a Bootstrap 3 form
     * @param  array  $args    Optional arguments
     * @param  int    $post_id The post ID
     * @return void
     * @since  0.1.0
     */
    static function comments_form( $args=array(), $post_id=NULL ) {
        $post_id = $post_id===NULL ? get_the_ID() : $post_id;

        // Sets up variables for the function
        $commenter = wp_get_current_commenter();
        $user = wp_get_current_user();
        $user_identity = $user->exists() ? $user->display_name : '';

        $variables = array();

        // Generates a notice for logged-in users
        $logged_in = sprintf(
            'Logged in as <a href="%1$s" class="alert-link">%2$s</a>. <a href="%3$s" title="Log out of this account" class="alert-link">Log out?</a>', 
            get_edit_user_link(), 
            $user_identity, 
            wp_logout_url(apply_filters('the_permalink', get_permalink($post_id)))
        );
        $variables['logged_in'] = apply_filters(
            'comment_form_logged_in', 
            $logged_in, 
            $commenter, 
            $user_identity
        );

        $variables['form_action'] = site_url('/wp-comments-post.php');

        // TODO Find a way to make this WAY less janky
        $m = self::get_mustache();
        echo $m->render('comment-form/open', $variables);
        do_action('comment_form_top');
        do_action('comment_form_before_fields');
        if (!is_user_logged_in()) {
            echo $m->render('comment-form/logged-out', $commenter);
        } else {
            echo $m->render('comment-form/logged-in', $variables);
            do_action('comment_form_logged_in_after', $commenter, $user_identity);
        }
        echo $m->render('comment-form/textarea');
        do_action('comment_form_after_fields');
        echo $m->render('comment-form/submit');
        do_action('comment_form');
        comment_id_fields(get_the_ID());
        echo $m->render('comment-form/close');
    }


    /*
     * EXTRA THEME FUNCTIONALITY
     *************************************************************************/

    /**
     * Builds a navigation menu and returns or echoes the resulting markup
     * @param  array  $custom_config Optional nav configuration options
     * @return mixed                 void or string if $config['echo']===true
     * @since  0.1.0
     */
    static function get_nav_menu( $custom_config=array() ) {
        // Sets up defaults for the nav
        $default_config = array(
            'theme_location'  => 'primary',
            'container'       => 'div',
            'container_class' => 'collapse navbar-collapse main-nav',
            'menu_class'      => 'nav navbar-nav',
            'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>',
            'depth'           => 0,
            'echo'            => FALSE,
            'walker'          => new wp_bootstrap_navwalker(),
        );

        $config = array_merge($default_config, $custom_config);

        // Checks if the requested nav exists
        if (has_nav_menu($config['theme_location'])) {
            $nav = wp_nav_menu($config);
        } else {
            // If no nav exists, builds a link to create the menu
            $views_path = TEMPLATEPATH . '/assets/views';
            $variables = array(
                'class'    => $config['menu_class'],
                'menu_url' => admin_url('nav-menus.php'),
            );
            $nav = self::get_mustache($views_path)->render('no-nav', $config);
        }

        // Respects the nav config settings to output/return the nav
        if ($config['echo']===FALSE) {
            return $nav;
        } else {
            echo $nav;
        }
    }

    /**
     * Bootstrap Numbered Pagination
     *
     * @param  int    $range  Amount of pages to the left and right of active page
     * @param  int    $pages  Total pages, for use with custom loops
     * @param  string $prev   Markup to use for the previous page button
     * @param  string $next   Markup to use for the next page button
     * @param  bool   $return Flag to set if the markup should echo or be returned
     * @return mixed          The markup if $return is TRUE, else void
     */
    static function pagination( $text_align='center', $range=4, $pages=NULL, $prev=NULL, $next=NULL, $return=TRUE ) { 
        if (empty($prev)) {
            $prev = '&lsaquo;';
        }

        if (empty($next)) {
            $next = '&rsaquo;';
        }

        // Gets the current page
        global $paged;
        if (empty($paged)) {
            $paged = 1;
        }

        // Loads the number of pages from the loop if not set
        if (empty($pages)) {
            global $wp_query;
            $pages = $wp_query->max_num_pages;
            if (!$pages) {
                $pages = 1;
            }
        }   

        // Creates the pagination
        $pagination = NULL;
        if ($pages>1) {
            $page_array = array();

            $first_page = $paged-$range>0 ? $paged-$range : 1;
            $last_page = $first_page+$range*2<$pages ? $first_page+$range*2 : $pages;

            // Adds the "previous" button
            if ($paged>1) {
                $page_array[] = '<li class="first">'
                              . '<a href="' . get_pagenum_link(1)
                              . '" title="First Page">&laquo;</a></li>';
                $page_array[] = '<li>'
                              . '<a href="' . get_pagenum_link($paged-1) 
                              . '">' . $prev . '</a></li>';
            }

            for ($i=$first_page; $i<=$last_page; ++$i) {
                if ($pages>1 && ($i<$paged+$range+1 || $i>$paged-$range-1)) {
                    $active_class = $paged===$i ? ' class="active"' : NULL;
                    $page_array[] = '<li' . $active_class . '>'
                                  . '<a href="'
                                  . get_pagenum_link($i) . '">' . $i . '</a>'
                                  . '</li>';
                }
            }

            // Adds the "next" button
            if ($paged<$pages) {
                $page_array[] = '<li>'
                              . '<a href="' . get_pagenum_link($paged+1) 
                              . '" title="Next Page">' . $next . '</a></li>';
                $page_array[] = '<li class="last">'
                              . '<a href="' . get_pagenum_link($pages)
                              . '" title="Last Page">&raquo;</a></li>';
            }

            $pagination = '<div class="text-'.$text_align.'"><ul class="pagination">'
                        . implode("\n    ", $page_array)
                        . '</ul></div>';
        }

        if ($return===TRUE) {
            echo $pagination;
        } else {
            return $pagination;
        }
    }

    /**
     * Registers custom post types with significantly less code
     *
     * This function must be hooked to 'hoverboard/init' or the child theme 
     * will be overwritten by Hoverboard. Don't forget to call this function 
     * in your child theme.
     *
     * Example configuration:
     * 
        $custom_post_types = array(
            array(
                'singular'      => 'Custom Post Type',
                'plural'        => 'Custom Post Types',
                'menu_position' => 6, // Lower number means higher placement
                'supports'      => array('title'),
                'slug'          => 'custom'
            ),
            array(
                'singular'      => 'Other New Post Type',
                'plural'        => 'Other New Post Types',
                'menu_position' => 7,
                'supports'      => array('title', 'excerpt'),
            ),
        );
        Hoverboard::custom_post_types($custom_post_types);
     *
     * @return  void
     * @since   0.1.0
     * @see     http://codex.wordpress.org/Function_Reference/register_post_type
     * @see     http://melchoyce.github.io/dashicons/
     */
    public function custom_post_types( $custom_post_types=array() ) {
        $defaults = array(
            'singular'              => 'My Custom Post',
            'plural'                => 'My Custom Posts',
            'public'                => TRUE,
            'publicly_queryable'    => TRUE,
            'show_ui'               => TRUE,
            'show_in_menu'          => TRUE,
            'query_var'             => TRUE,
            'rewrite'               => TRUE,
            'capability_type'       => 'post',
            'has_archive'           => TRUE,
            'hierarchical'          => FALSE,
            'menu_position'         => NULL,
            'supports'              => array('title', 'editor'),
            'menu_icon'             => 'dashicons-admin-post',
        );

        // Loops through each CPT and registers it with WordPress
        foreach ($custom_post_types as $cpt) {
            $post = array_merge($defaults, $cpt);

            // Allows for the slug to be set explicitly
            if (!array_key_exists('slug', $cpt)) {
                $cpt['slug'] = $post['singular'];
            }

            $labels = array(
                'name'                  => _x($post['plural'], 'General post type descriptor'),
                'singular_name'         => _x($post['singular'], 'Singular post type descriptor'),
                'add_new'               => _x('Add New', $post['singular']),
                'add_new_item'          => __('Add New '.$post['singular']),
                'edit_item'             => __('Edit '.$post['singular']),
                'new_item'              => __('New '.$post['singular']),
                'all_items'             => __('All '.$post['plural']),
                'view_item'             => __('View '.$post['singular']),
                'search_items'          => __('Search '.$post['plural']),
                'not_found'             => __('No '.strtolower($post['plural']).' found'),
                'not_found_in_trash'    => __('No '.strtolower($post['plural']).' in the trash'),
                'parent_item_colon'     => '',
                'menu_name'             => $post['plural'],
            );
            $args = array(
                'labels'                => $labels,
                'public'                => $post['public'],
                'publicly_queryable'    => $post['publicly_queryable'],
                'show_ui'               => $post['show_ui'],
                'show_in_menu'          => $post['show_in_menu'],
                'query_var'             => $post['query_var'],
                'rewrite'               => $post['rewrite'],
                'capability_type'       => $post['capability_type'],
                'has_archive'           => $post['has_archive'],
                'hierarchical'          => $post['hierarchical'],
                'menu_position'         => $post['menu_position'],
                'supports'              => $post['supports'],
                'menu_icon'             => $post['menu_icon']
            );

            // Add a register_post_type() call for each needed custom post type
            register_post_type(sanitize_title($cpt['slug']), $args);
        }
    }

    /**
     * Gets the page slug or post type and appends it to the body class
     * @return string The slug to be used as a body class
     * @since  0.1.0
     */
    static function get_wrapper_class(  ) {
        global $wp_query;
        $slug = 'default';

        $post_obj = $wp_query->get_queried_object();
        if (is_object($post_obj)) {
            // Gets the post slug
            if (property_exists($post_obj, 'post_name')) {
                $slug = $post_obj->post_name;
            }

            // Posts should end up under the "blog" umbrella class
            if (
                (property_exists($post_obj, 'post_type')
                && $post_obj->post_type==='post')
                || is_category() 
                || is_author()
            ) {
                $slug = 'blog';
            }
        } else if (is_front_page()) {
            $slug = 'home';
        }

        return $slug;
    }

    /**
     * Loads Mustache for templating
     * @return void
     * @since  0.1.0
     */
    static function get_mustache( $views_path=NULL ) {
        require_once dirname(__FILE__) . '/bower_components/mustache/src/Mustache/Autoloader.php';
        Mustache_Autoloader::register();

        if (empty($views_path)) {
            $views_path = TEMPLATEPATH . '/assets/views';
        }

        return new Mustache_Engine(array(
            'loader' => new Mustache_Loader_FilesystemLoader($views_path),
        ));
    }
}

// Actually starts the party
$hoverboard = new Hoverboard;

/* 
 * In an attempt to make this code easier to read, the major chunks have been 
 * broken into smaller files with code pertaining only that functionality.
 */
// require_once TEMPLATEPATH . '/includes/extra.php';
// require_once TEMPLATEPATH . '/includes/shortcodes.php';
// require_once TEMPLATEPATH . '/includes/social.php';
// require_once TEMPLATEPATH . '/includes/comments.php';
// require_once TEMPLATEPATH . '/includes/admin.php';
