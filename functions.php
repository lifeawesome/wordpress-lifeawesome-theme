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

defined( 'LIFEAWESOME_VERSION' ) || define( 'LIFEAWESOME_VERSION', '1.0.7' );
defined( 'LIFEAWESOME_DIR' ) || define( 'LIFEAWESOME_DIR', trailingslashit( get_template_directory() ) );

defined( 'GUTENVERSE_COMPANION_REQUIRED_VERSION' ) || define( 'GUTENVERSE_COMPANION_REQUIRED_VERSION', '2.0.0' );

require get_parent_theme_file_path( 'inc/autoload.php' );

LifeAwesome\Init::instance();
