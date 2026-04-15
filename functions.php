<?php
/**
 * Theme Functions
 *
 * @author Dan Davidson
 * @package lifeawesome
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function custom_seo_meta() {
    if (is_singular()) {
        global $post;
        // 1. Meta Description (Manual Field or Excerpt fallback)
        $desc = get_post_meta($post->ID, 'meta_description', true);
        if (!$desc) { $desc = get_the_excerpt(); }
        if ($desc) {
            echo '<meta name="description" content="' . esc_attr($desc) . '">' . "\n";
        }

        // 2. Robots Tag (Manual Field or 'index, follow' fallback)
        $robots = get_post_meta($post->ID, 'meta_robots', true);
        if (!$robots) { $robots = 'index, follow'; }
        echo '<meta name="robots" content="' . esc_attr($robots) . '">' . "\n";

        // 3. Canonical URL
        $canonical = get_post_meta($post->ID, 'meta_canonical', true);
        if (!$canonical) { $canonical = get_permalink(); }
        echo '<link rel="canonical" href="' . esc_url($canonical) . '" />' . "\n";

        // 4. Social Media (Open Graph for FB/Twitter)
        echo '<meta property="og:title" content="' . esc_attr(get_the_title()) . '" />' . "\n";
        echo '<meta property="og:url" content="' . esc_url(get_permalink()) . '" />' . "\n";
        echo '<meta property="og:type" content="article" />' . "\n";
        if (has_post_thumbnail()) {
            echo '<meta property="og:image" content="' . esc_url(get_the_post_thumbnail_url($post->ID, 'large')) . '" />' . "\n";
        }
    }
}
add_action('wp_head', 'custom_seo_meta');
add_theme_support('title-tag');


defined( 'LIFEAWESOME_VERSION' ) || define( 'LIFEAWESOME_VERSION', '1.0.7' );
defined( 'LIFEAWESOME_DIR' ) || define( 'LIFEAWESOME_DIR', trailingslashit( get_template_directory() ) );


// defined( 'GUTENVERSE_COMPANION_REQUIRED_VERSION' ) || define( 'GUTENVERSE_COMPANION_REQUIRED_VERSION', '2.0.0' );

require get_parent_theme_file_path( 'inc/autoload.php' );

LifeAwesome\Init::instance();
