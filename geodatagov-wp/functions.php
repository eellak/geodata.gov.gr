<?php
/**
 * Twenty Thirteen functions and definitions
 *
 * Sets up the theme and provides some helper functions, which are used in the
 * theme as custom template tags. Others are attached to action and filter
 * hooks in WordPress to change core functionality.
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development
 * and http://codex.wordpress.org/Child_Themes), you can override certain
 * functions (those wrapped in a function_exists() call) by defining them first
 * in your child theme's functions.php file. The child theme's functions.php
 * file is included before the parent theme's file, so the child theme
 * functions would be used.
 *
 * Functions that are not pluggable (not wrapped in function_exists()) are
 * instead attached to a filter or action hook.
 *
 * For more information on hooks, actions, and filters, @link http://codex.wordpress.org/Plugin_API
 *
 * @package WordPress
 * @subpackage Twenty_Thirteen
 * @since Twenty Thirteen 1.0
 */

/*
 * Set up the content width value based on the theme's design.
 *
 * @see twentythirteen_content_width() for template-specific adjustments.
 */

$NEWS_ID_EN = 91;

$APPS_ID_EN = 96;

$ABOUT_ID_EN = 123;

if ( ! isset( $content_width ) )
	$content_width = 604;

function get_news_id_en() {
    global $NEWS_ID_EN;
    return $NEWS_ID_EN;
}

function get_apps_id_en() {
    global $APPS_ID_EN;
    return $APPS_ID_EN;
}

function get_about_id_en() {
    global $ABOUT_ID_EN;
    return $ABOUT_ID_EN;
}

/**
 * Add support for a custom header image.
require get_template_directory() . '/inc/custom-header.php';

 */
/**
 * Twenty Thirteen only works in WordPress 3.6 or later.
 */
if ( version_compare( $GLOBALS['wp_version'], '3.6-alpha', '<' ) )
	require get_template_directory() . '/inc/back-compat.php';

/**
 * Twenty Thirteen setup.
 *
 * Sets up theme defaults and registers the various WordPress features that
 * Twenty Thirteen supports.
 *
 * @uses load_theme_textdomain() For translation/localization support.
 * @uses add_editor_style() To add Visual Editor stylesheets.
 * @uses add_theme_support() To add support for automatic feed links, post
 * formats, and post thumbnails.
 * @uses register_nav_menu() To add support for a navigation menu.
 * @uses set_post_thumbnail_size() To set a custom post thumbnail size.
 *
 * @since Twenty Thirteen 1.0
 */
function twentythirteen_setup() {
	/*
	 * Makes Twenty Thirteen available for translation.
	 *
	 * Translations can be added to the /languages/ directory.
	 * If you're building a theme based on Twenty Thirteen, use a find and
	 * replace to change 'twentythirteen' to the name of your theme in all
	 * template files.
     */

    add_filter( 'locale', 'localize_theme' );
	load_theme_textdomain( 'twentythirteen', get_template_directory() . '/languages' );

	/*
	 * This theme styles the visual editor to resemble the theme style,
	 * specifically font, colors, icons, and column width.
	 */
	add_editor_style( array( 'css/editor-style.css', 'genericons/genericons.css', twentythirteen_fonts_url() ) );

	// Adds RSS feed links to <head> for posts and comments.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Switches default core markup for search form, comment form,
	 * and comments to output valid HTML5.
	 */
//	add_theme_support( 'html5', array(
//		'search-form', 'comment-form', 'comment-list', 'gallery', 'caption'
//	) );

	/*
	 * This theme supports all available post formats by default.
	 * See http://codex.wordpress.org/Post_Formats
	 */
	add_theme_support( 'post-formats', array(
		'aside', 'audio', 'chat', 'gallery', 'image', 'link', 'quote', 'status', 'video'
	) );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menu( 'primary', __( 'Navigation Menu', 'twentythirteen' ) );

	/*
	 * This theme uses a custom image size for featured images, displayed on
	 * "standard" posts and pages.
	 */
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 604, 270, true );

	// This theme uses its own gallery styles.
	add_filter( 'use_default_gallery_style', '__return_false' );
}
//add_action('before_setup_theme', 'localize_theme');
function localize_theme( $locale )
{
        if ( isset( $_GET['lang'] ) )
                {
            return sanitize_key( $_GET['lang'] );
        }

    return $locale;
}
add_action( 'after_setup_theme', 'twentythirteen_setup' );

/**
 * Return the Google font stylesheet URL, if available.
 *
 * The use of Source Sans Pro and Bitter by default is localized. For languages
 * that use characters not supported by the font, the font can be disabled.
 *
 * @since Twenty Thirteen 1.0
 *
 * @return string Font stylesheet or empty string if disabled.
 */
function twentythirteen_fonts_url() {
	$fonts_url = '';

	/* Translators: If there are characters in your language that are not
	 * supported by Source Sans Pro, translate this to 'off'. Do not translate
	 * into your own language.
	 */
	$source_sans_pro = _x( 'on', 'Source Sans Pro font: on or off', 'twentythirteen' );

	/* Translators: If there are characters in your language that are not
	 * supported by Bitter, translate this to 'off'. Do not translate into your
	 * own language.
	 */
	$bitter = _x( 'on', 'Bitter font: on or off', 'twentythirteen' );

	if ( 'off' !== $source_sans_pro || 'off' !== $bitter ) {
		$font_families = array();

		if ( 'off' !== $source_sans_pro )
			$font_families[] = 'Source Sans Pro:300,400,700,300italic,400italic,700italic';

		if ( 'off' !== $bitter )
			$font_families[] = 'Bitter:400,700';

		$query_args = array(
			'family' => urlencode( implode( '|', $font_families ) ),
			'subset' => urlencode( 'latin,latin-ext' ),
		);
		$fonts_url = add_query_arg( $query_args, "//fonts.googleapis.com/css" );
	}

	return $fonts_url;
}

/**
 * Enqueue scripts and styles for the front end.
 *
 * @since Twenty Thirteen 1.0
 */
function twentythirteen_scripts_styles() {
	/*
	 * Adds JavaScript to pages with the comment form to support
	 * sites with threaded comments (when in use).
	 */
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );

	// Adds Masonry to handle vertical alignment of footer widgets.
	if ( is_active_sidebar( 'sidebar-1' ) )
		wp_enqueue_script( 'jquery-masonry' );

	// Loads JavaScript file with functionality specific to Twenty Thirteen.
	wp_enqueue_script( 'twentythirteen-script', get_template_directory_uri() . '/js/functions.js', array( 'jquery' ), '2014-06-08', true );

	wp_enqueue_script( 'menu-script', get_template_directory_uri() . '/js/main.js', array( 'jquery' ), '2014-06-08');
	wp_enqueue_script( 'jquery-min', get_template_directory_uri() . '/js/jquery.min.js', array( 'jquery' ), '2014-06-08');
	wp_enqueue_script( 'bootstrap-min', get_template_directory_uri() . '/js/bootstrap.min.js', array( 'jquery' ), '2014-06-08');
	// Add Source Sans Pro and Bitter fonts, used in the main stylesheet.
	wp_enqueue_style( 'twentythirteen-fonts', twentythirteen_fonts_url(), array(), null );

	// Add Genericons font, used in the main stylesheet.
	wp_enqueue_style( 'genericons', get_template_directory_uri() . '/genericons/genericons.css', array(), '3.03' );

	// Loads our main stylesheet.
	wp_enqueue_style( 'twentythirteen-style', get_stylesheet_uri(), array(), '2013-07-18' );

    wp_enqueue_style( 'twentythirteen-geodata-debug', get_template_directory_uri() . '/css/main.debug.css', array( 'twentythirteen-style' ), '2013-07-18' );
    wp_enqueue_style( 'twentythirteen-geodata', get_template_directory_uri() . '/css/main.css', array( 'twentythirteen-style' ), '2013-07-18' );
    wp_enqueue_style( 'twentythirteen-fontawesome', get_template_directory_uri() . '/css/font-awesome.css', array( 'twentythirteen-style' ), '2013-07-18' );
    

	wp_enqueue_style( 'twentythirteen-ie', get_template_directory_uri() . '/css/ie.css', array( 'twentythirteen-style' ), '2013-07-18' );
	// Loads the Internet Explorer specific stylesheet.
	wp_enqueue_style( 'twentythirteen-ie', get_template_directory_uri() . '/css/ie.css', array( 'twentythirteen-style' ), '2013-07-18' );
	wp_style_add_data( 'twentythirteen-ie', 'conditional', 'lt IE 9' );
}
add_action( 'wp_enqueue_scripts', 'twentythirteen_scripts_styles' );

/**
 * Filter the page title.
 *
 * Creates a nicely formatted and more specific title element text for output
 * in head of document, based on current view.
 *
 * @since Twenty Thirteen 1.0
 *
 * @param string $title Default title text for current view.
 * @param string $sep   Optional separator.
 * @return string The filtered title.
 */
function twentythirteen_wp_title( $title, $sep ) {
	global $paged, $page;

	if ( is_feed() )
		return $title;

	// Add the site name.
	$title .= get_bloginfo( 'name', 'display' );

	// Add the site description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		$title = "$title $sep $site_description";

	// Add a page number if necessary.
	if ( ( $paged >= 2 || $page >= 2 ) && ! is_404() )
		$title = "$title $sep " . sprintf( __( 'Page %s', 'twentythirteen' ), max( $paged, $page ) );

	return $title;
}
add_filter( 'wp_title', 'twentythirteen_wp_title', 10, 2 );

/**
 * Register two widget areas.
 *
 * @since Twenty Thirteen 1.0
 */
function twentythirteen_widgets_init() {
	register_sidebar( array(
	    'name'          => __( 'Main Widget Area', 'twentythirteen' ),
		'id'            => 'sidebar-1',
		'description'   => __( 'Appears in the footer section of the site.', 'twentythirteen' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => __( 'Secondary Widget Area', 'twentythirteen' ),
		'id'            => 'sidebar-2',
		'description'   => __( 'Appears on posts and pages in the sidebar.', 'twentythirteen' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
}
add_action( 'widgets_init', 'twentythirteen_widgets_init' );

if ( ! function_exists( 'twentythirteen_paging_nav' ) ) :
/**
 * Display navigation to next/previous set of posts when applicable.
 *
 * @since Twenty Thirteen 1.0
 */
function twentythirteen_paging_nav() {
	global $wp_query;

	// Don't print empty markup if there's only one page.
	if ( $wp_query->max_num_pages < 2 )
		return;
?>
    <section class="module">
        <div class="container">
        <div class="pagination pagination-centered" >
		
            <?php 
                global $wp_query;
                $args = array(
                        'posts_per_page' => get_option('posts_per_page'),
                        'category_name' => 'news',
                        'total' => $wp_query->max_num_pages,
                        'current' => max(1, get_query_var('paged')),
                        'next_text' => __('>>'),
                        'prev_text' => __('<<'),
                        'before_page_number' => '',
                        'after_page_number' => ''
                    );
                ?>
                <div class="page-numbers-holder"> 
                    <?php echo paginate_links($args); ?>
                </div>

                </div>
		</div><!-- .nav-links -->
    </section>
	<?php
}
endif;

if ( ! function_exists( 'twentythirteen_post_nav' ) ) :
/**
 * Display navigation to next/previous post when applicable.
*
* @since Twenty Thirteen 1.0
 */

function twentythirteen_post_nav() {
	global $post;

	// Don't print empty markup if there's nowhere to navigate.
	$previous = ( is_attachment() ) ? get_post( $post->post_parent ) : get_adjacent_post( false, '', true );
    $next     = get_adjacent_post( false, '', false ); 
    if( get_option( 'show_on_front' ) == 'page' ) 
        $home_link = get_option('page_for_posts' ) ; 
    else
        $home_link = 0;
?>

            
<?php
	//if ( ! $next && ! $previous )
	//	return;
	?>
	<nav class="navigation post-navigation" role="navigation">
        <h1 class="screen-reader-text"><?php _e( 'Post navigation', 'twentythirteen' ); ?></h1>
        <div class="container">
        <div class="nav-links">
            <div class="post-section next-post">
            <?php
            if ( is_a( $next , 'WP_Post' ) ): ?>
                 <a href="<?php echo get_permalink( $next->ID ); ?>"><span><&nbsp;<?php echo get_the_title( $next->ID ); ?></a>
            <?php endif ?></div>
            <div class="post-section back-post"><a href="<?php echo get_permalink( pll_get_post(get_news_id_en()))  ?>"> <?php _e('Back to all news', 'twentythirteen') ?></a></div>
            <div class="post-section previous-post">
            <?php
            if ( is_a( $previous , 'WP_Post' ) ): ?>
                 <a href="<?php echo get_permalink( $previous->ID ); ?>"><?php echo get_the_title( $previous->ID ); ?>&nbsp;></a>
            <?php endif ?>
            </div>


        </div><!-- .nav-links -->
        </div><!-- container -->
	</nav><!-- .navigation -->
	<?php
}
endif;

if ( ! function_exists( 'twentythirteen_entry_meta' ) ) :
/**
 * Print HTML with meta information for current post: categories, tags, permalink, author, and date.
 *
 * Create your own twentythirteen_entry_meta() to override in a child theme.
 *
 * @since Twenty Thirteen 1.0
 */
function twentythirteen_entry_meta() {
	if ( is_sticky() && is_home() && ! is_paged() )
		echo '<span class="featured-post">' . __( 'Sticky', 'twentythirteen' ) . '</span>';

	if ( ! has_post_format( 'link' ) && 'post' == get_post_type() )
		twentythirteen_entry_date();

	// Translators: used between list items, there is a space after the comma.
	//$categories_list = get_the_category_list( __( ', ', 'twentythirteen' ) );
	//if ( $categories_list ) {
	//	echo '<span class="categories-links">' . $categories_list . '</span>';
	//}

	// Translators: used between list items, there is a space after the comma.
	$tag_list = get_the_tag_list( '', __( ', ', 'twentythirteen' ) );
	if ( $tag_list ) {
		echo '<span class="tags-links">' . $tag_list . '</span>';
	}

	// Post author
	if ( 'post' == get_post_type() ) {
		printf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s" rel="author">%3$s</a></span>',
			esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
			esc_attr( sprintf( __( 'View all posts by %s', 'twentythirteen' ), get_the_author() ) ),
			get_the_author()
		);
	}
}
endif;

if ( ! function_exists( 'twentythirteen_entry_date' ) ) :
/**
 * Print HTML with date information for current post.
 *
 * Create your own twentythirteen_entry_date() to override in a child theme.
 *
 * @since Twenty Thirteen 1.0
 *
 * @param boolean $echo (optional) Whether to echo the date. Default true.
 * @return string The HTML-formatted post date.
 */
function twentythirteen_entry_date( $echo = true ) {
	if ( has_post_format( array( 'chat', 'status' ) ) )
		$format_prefix = _x( '%1$s on %2$s', '1: post format name. 2: date', 'twentythirteen' );
	else
		$format_prefix = '%2$s';

	$date = sprintf( '<span class="date"><time class="entry-date" datetime="%1$s">%2$s</time></span>',
		esc_attr( date_i18n( 'c' ) ),
		esc_html( sprintf( $format_prefix, get_post_format_string( get_post_format() ), get_the_date() ) )
	);

	if ( $echo )
		echo $date;

	return $date;
}
endif;

if ( ! function_exists( 'twentythirteen_the_attached_image' ) ) :
/**
 * Print the attached image with a link to the next attached image.
 *
 * @since Twenty Thirteen 1.0
 */
function twentythirteen_the_attached_image() {
	/**
	 * Filter the image attachment size to use.
	 *
	 * @since Twenty thirteen 1.0
	 *
	 * @param array $size {
	 *     @type int The attachment height in pixels.
	 *     @type int The attachment width in pixels.
	 * }
	 */
	$attachment_size     = apply_filters( 'twentythirteen_attachment_size', array( 724, 724 ) );
	$next_attachment_url = wp_get_attachment_url();
	$post                = get_post();

	/*
	 * Grab the IDs of all the image attachments in a gallery so we can get the URL
	 * of the next adjacent image in a gallery, or the first image (if we're
	 * looking at the last image in a gallery), or, in a gallery of one, just the
	 * link to that image file.
	 */
	$attachment_ids = get_posts( array(
		'post_parent'    => $post->post_parent,
		'fields'         => 'ids',
		'numberposts'    => -1,
		'post_status'    => 'inherit',
		'post_type'      => 'attachment',
		'post_mime_type' => 'image',
		'order'          => 'ASC',
		'orderby'        => 'menu_order ID'
	) );

	// If there is more than 1 attachment in a gallery...
	if ( count( $attachment_ids ) > 1 ) {
		foreach ( $attachment_ids as $attachment_id ) {
			if ( $attachment_id == $post->ID ) {
				$next_id = current( $attachment_ids );
				break;
			}
		}

		// get the URL of the next image attachment...
		if ( $next_id )
			$next_attachment_url = get_attachment_link( $next_id );

		// or get the URL of the first image attachment.
		else
			$next_attachment_url = get_attachment_link( array_shift( $attachment_ids ) );
	}

	printf( '<a href="%1$s" title="%2$s" rel="attachment">%3$s</a>',
		esc_url( $next_attachment_url ),
		the_title_attribute( array( 'echo' => false ) ),
		wp_get_attachment_image( $post->ID, $attachment_size )
	);
}
endif;

/**
 * Return the post URL.
 *
 * @uses get_url_in_content() to get the URL in the post meta (if it exists) or
 * the first link found in the post content.
 *
 * Falls back to the post permalink if no URL is found in the post.
 *
 * @since Twenty Thirteen 1.0
 *
 * @return string The Link format URL.
 */
function twentythirteen_get_link_url() {
	$content = get_the_content();
	$has_url = get_url_in_content( $content );

	return ( $has_url ) ? $has_url : apply_filters( 'the_permalink', get_permalink() );
}

if ( ! function_exists( 'twentythirteen_excerpt_more' ) && ! is_admin() ) :
/**
 * Replaces "[...]" (appended to automatically generated excerpts) with ...
 * and a Continue reading link.
 *
 * @since Twenty Thirteen 1.4
 *
 * @param string $more Default Read More excerpt link.
 * @return string Filtered Read More excerpt link.
 */
function twentythirteen_excerpt_more( $more ) {
	$link = sprintf( '<a href="%1$s" class="more-link">%2$s</a>',
		esc_url( get_permalink( get_the_ID() ) ),
        //esc_url(add_query_arg(array('p' => get_the_ID(), 'paged' => False, 'page_id' => False, 's' => False))),
			/* translators: %s: Name of current post */
			sprintf( __( 'Continue reading %s <span class="meta-nav"></span>', 'twentythirteen' ), '<span class="screen-reader-text">' . get_the_title( get_the_ID() ) . '</span>' )
		);
	return ' &hellip; ' . $link;
}
add_filter( 'excerpt_more', 'twentythirteen_excerpt_more' );
endif;

function get_current_posts_page(){
    if ( isset( $_GET['paged'] ) )
                {
            return sanitize_key( $_GET['paged'] );
                }
    else{
        return 1;
    }

}
/**
 * Extend the default WordPress body classes.
 *
 * Adds body classes to denote:
 * 1. Single or multiple authors.
 * 2. Active widgets in the sidebar to change the layout and spacing.
 * 3. When avatars are disabled in discussion settings.
 *
 * @since Twenty Thirteen 1.0
 *
 * @param array $classes A list of existing body class values.
 * @return array The filtered body class list.
 */
function twentythirteen_body_class( $classes ) {
	if ( ! is_multi_author() )
		$classes[] = 'single-author';

	if ( is_active_sidebar( 'sidebar-2' ) && ! is_attachment() && ! is_404() )
		$classes[] = 'sidebar';

	if ( ! get_option( 'show_avatars' ) )
		$classes[] = 'no-avatars';

	return $classes;
}
add_filter( 'body_class', 'twentythirteen_body_class' );

/**
 * Adjust content_width value for video post formats and attachment templates.
 *
 * @since Twenty Thirteen 1.0
 */
function twentythirteen_content_width() {
	global $content_width;

	if ( is_attachment() )
		$content_width = 724;
	elseif ( has_post_format( 'audio' ) )
		$content_width = 484;
}
add_action( 'template_redirect', 'twentythirteen_content_width' );

/**
 * Add postMessage support for site title and description for the Customizer.
 *
 * @since Twenty Thirteen 1.0
 *
 * @param WP_Customize_Manager $wp_customize Customizer object.
 */
function twentythirteen_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';
}
add_action( 'customize_register', 'twentythirteen_customize_register' );

/**
 * Enqueue Javascript postMessage handlers for the Customizer.
 *
 * Binds JavaScript handlers to make the Customizer preview
 * reload changes asynchronously.
 *
 * @since Twenty Thirteen 1.0
 */
function twentythirteen_customize_preview_js() {
	wp_enqueue_script( 'twentythirteen-customizer', get_template_directory_uri() . '/js/theme-customizer.js', array( 'customize-preview' ), '20141120', true );
}
add_action( 'customize_preview_init', 'twentythirteen_customize_preview_js' );


add_filter('get_search_form', 'my_search_form' , 99);
function my_search_form( $form ){
    $search = __('Search News...',  'twentythirteen');
    $clause = add_query_arg('s', '123');
    $form = '<form role="search" class="search-form-top search-form" data-module="select-switch" method="get" id="searchform"  class="searchform" >
        <div class="searchfw search-input search-giant">
        <div class="container">
            <input type="hidden" name="paged" value="1" />
            <input type="search" class="search simple-input search-field" value="' . get_search_query() . '" name="s" id="s"   placeholder="' . $search . '" >
            <button type="submit" class="btn-search" value="Search">
            <i class="icon-search">
                </i>
                </button>
                </input>
            </div>
        </div>
        </form>';

        //<input type="hidden" name="lang" value="'. get_active_locale()['code'] .'" />
    return $form;

}

add_filter('excerpt_length', 'my_excerpt_length');
function my_excerpt_length($length) {
    return 50; // Or whatever you want the length to be.
}

add_filter('get_available_locales', 'get_available_locales');
function get_available_locales(){
    return array(array( code => 'el', name => 'Ελληνικά'), array( code => 'en', name => 'English'));
}

add_filter('get_active_locale', 'get_active_locale');
function get_active_locale(){
    $curr_locale = get_locale();
    
    foreach(get_available_locales() as $loc){
        if ($loc['code'] == $curr_locale){
            return $loc;
        }
    }
    return array_values(get_available_locales())[0];
}


add_filter('show_admin_bar', '__return_false');
//add_filter( 'get_search_form', create_function( '$a', "return null;" ) );

// INSPIRE Groups
function get_inspire_groups(){
    return
        array(
                array('name' =>'utilities-communication',
                'display_name'=> __('Utilities Communication', 'twentythirteen'),)
                ,
                array('name' =>'transportation',
                'display_name'=> __('Transportation', 'twentythirteen'),)
                ,
                array('name' =>'structure',
                'display_name'=> __('Structure', 'twentythirteen'),)
                ,
                array('name' =>'society',
                'display_name'=> __('Society', 'twentythirteen'),)
                ,
                array('name' =>'planning-cadastre',
                'display_name'=> __('Planning Cadastre', 'twentythirteen'),)
                ,
                array('name' =>'oceans',
                'display_name'=>__('Oceans', 'twentythirteen'),)
                ,
                array('name' =>'location',
                'display_name'=>__('Location', 'twentythirteen'),)
                ,
                array('name' =>'intelligence-military',
                'display_name'=>__('Intelligence Military', 'twentythirteen'),)
                ,
                array('name' =>'inland-waters',
                'display_name'=>__('Inland Waters', 'twentythirteen'),)
                ,
                array('name' =>'imagery-base-maps-earth-cover',
                'display_name'=>__('Imagery Base Maps Earth Cover', 'twentythirteen'),)
                ,
                array('name' =>'health',
                'display_name'=>__('Health', 'twentythirteen'),)
                ,
                array('name' =>'geoscientific-information',
                'display_name'=>__('Geoscientific Information', 'twentythirteen'),)
                ,
                array('name' =>'climatology-meteorology-atmosphere',
                'display_name'=>__('Climatology Meteorology Atmosphere', 'twentythirteen'),)
                ,
                array('name' =>'environment',
                'display_name'=>__('Environment', 'twentythirteen'),)
                ,
                array('name' =>'elevation',
                'display_name'=>__('Elevation', 'twentythirteen'),)
                ,
                array('name' =>'economy',
                'display_name'=>__('Economy', 'twentythirteen'),)
                ,
                array('name' =>'farming',
                'display_name'=>__('Farming', 'twentythirteen'),)
                ,
                array('name' =>'boundaries',
                'display_name'=>__('Boundaries', 'twentythirteen'),)
                ,
                array('name' =>'biota',
                'display_name'=>__('Biota', 'twentythirteen'),)
            );
}

function create_geodata_group_menu(){
    $menu = '<ul class="icons-grid">';
    //$lang = get_active_locale()['code'];
    $lang = substr(pll_current_language("locale"), 0, 2);

    foreach (get_inspire_groups() as $group){

        $menu .= '<li class="menu-item">
        <a href="/'.$lang .'/group/'. $group['name'] .'">
        
            <img src="'. get_bloginfo('template_directory') .'/images/topics/'. $group['name'] .'.svg" alt="'. $group['name'] .'" class="menu-image">
            <span class="menu-heading">'. $group['display_name'] .'</span>
        </a>
        </li>';
    }

    $menu .= '</ul>';
    return $menu;
}

function get_menu_items(){
    return array(
        array('name' => 'dataset',
            'display_name' => __('Datasets' , 'twentythirteen')),
        array('name' => 'group',
            'display_name' => __('Topics', 'twentythirteen')),
        array('name' => 'organization',
            'display_name' => __('Organizations', 'twentythirteen')),
        array('name' => 'maps',
            'display_name' => __('Maps', 'twentythirteen')),
        array('name' => 'applications',
            'display_name' => __('Apps', 'twentythirteen')),
        array('name' => 'news',
            'display_name' => __('News', 'twentythirteen')),
        array('name' => 'about',
            'display_name' => __('About', 'twentythirteen'))
        );
}

function create_geodata_menu(){
    $menu = '';
    //$lang = get_active_locale()['code'];
    $lang = substr(pll_current_language("locale"), 0, 2);

    foreach (get_menu_items() as $item){
        if ($item['name'] == 'maps'){
            $menu .= '<li><a href="/'. $item['name'] .'?locale='. $lang.'">'. $item['display_name'] .'</a></li>';
        }
        else if( $item['name'] == 'news') {
                $menu .= '<li><a href="'. get_permalink(pll_get_post(get_news_id_en())) .'">'. $item['display_name'] .'</a></li>';
        }
        else if( $item['name'] == 'applications') {
                $menu .= '<li><a href="'. get_permalink(pll_get_post(get_apps_id_en())) .'">'. $item['display_name'] .'</a></li>';

        }
        else if( $item['name'] == 'about') {
                $menu .= '<li><a href="'. get_permalink(pll_get_post(get_about_id_en())) .'">'. $item['display_name'] .'</a></li>';
        }

        else{
            $menu .= '<li><a href="/'.$lang .'/'. $item['name'] .'">'. $item['display_name'] .'</a></li>';
        }
    }

    return $menu;

}
function create_breadcrumb() {
    global $post; 
    $parents = array_reverse(get_post_ancestors( $post->ID ));
    
    $items = '';
    foreach ($parents as $parent){
            $items .= '<li> <a href="'. get_permalink($parent) .'">'. get_the_title($parent) .'</a> </li>';
    }
    $items.= '<li><a href="'. get_permalink() .'">'. get_the_title() .'</a></li>';

    return $items;
}
function create_news_breadcrumb() {
    $page_id = get_queried_object_id();
    
    $items = '<li>
                <a href="'. get_permalink(pll_get_post(get_news_id_en())) .'">'. __("News", "twentythirteen") .'</a>
            </li>';
    if (is_single()){
        $is_post = True;
        $items .= '<li>
                <a class="active" href="'. get_permalink() .'">'. get_the_title() .'</a>
            </li>';
    }
    
    

    return $items;
}

function create_language_menu() {
    $menu = '<li class="language">';
    $menu .= '<a href="'. esc_url( add_query_arg(array("p"=>False, "page_id"=>False, "s"=>False, "lang"=> get_active_locale()["code"]))) .'"><span class="down-arrow">'. get_active_locale()["name"] .'</span></a>';
    $menu .= '<ul>';
    
    foreach (get_available_locales() as $loc){
    //foreach ($languages as $lang){
        if ($loc['code'] != get_active_locale()["code"]){ 
            $menu .= '<li value="'. $loc["code"].'">';
            $menu .= '<a href="'. esc_url( add_query_arg(array("p"=>False,  "page_id"=>False, "s"=>False, "lang"=> $loc["code"]))) .'"><span>'. $loc["name"] .'</span></a>';
            $menu .= '</li>';
        }
    }
    $menu .= '</ul>';
    $menu .= '</li>';
    
    return $menu;
}
function create_pll_language_menu() {
    $languages = pll_the_languages(array('raw'=>1));
    $menu = '<li class="language">';
    $menu .= '<a href=""><span class="down-arrow">'. pll_current_language("name").'</span></a>';
    $menu .= '<ul>';
    
    //foreach (get_available_locales() as $loc){
    foreach ($languages as $lang){
        //if ($loc['code'] != get_active_locale()["code"]){ 
        //    $menu .= '<li value="'. $loc["code"].'">';
        //    $menu .= '<a href="'. esc_url( add_query_arg(array("p"=>False,  "page_id"=>False, "s"=>False, "lang"=> $loc["code"]))) .'"><span>'. $loc["name"] .'</span></a>';
        //    $menu .= '</li>';
        if (!( $lang["current_lang"])){
            $menu .= '<li value='. $lang["slug"] .'">';
            $menu .= '<a href="'. $lang["url"] .'"><span>'. $lang["name"] .'</span></a>';
            $menu .= '</li>';
        }
    }
    $menu .= '</ul>';
    $menu .= '</li>';

	//return wp_nav_menu( array( 'menu' => 'lang_menu', 'menu_class' => 'nav-menu' ) ); 
    return $menu;
}

function create_side_menu(){
    global $post;
    if ( $post->post_parent){
        $children = get_pages( array( "child_of" => $post->post_parent, "sort_order" => "asc", "sort_column" => "menu_order" ) );
    }
    else{
        $children = get_pages( array("child_of" => $post->ID,  "sort_order" => "asc", "sort_column" => "menu_order" ) );
    }
    $menu = '<ul id="'.$post->ID.'_list" class="unstyled nav nav-simple nav-facet count_desc li-hidden">';
    foreach ($children as $item){
        $menu .= '<li class="nav-item">';
        $menu .= '<a href="'. get_permalink( $item->ID) .'" title="'. get_the_title($item->ID) .'">';
        $menu .= '<span>'. get_the_title($item->ID) .'</span>';
        $menu .= '</a>';
        $menu .= '</li>';
    }
    $menu .= '</ul>';

    return $menu;
}
