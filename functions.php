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
        $desc = get_post_meta($post->ID, 'meta_description', true);
        if ($desc) {
            echo '<meta name="description" content="' . esc_attr($desc) . '">' . "\n";
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
