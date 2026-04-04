<?php
/**
 * Block Pattern Class
 *
 * @author Jegstudio
 * @package lifeawesome
 */

namespace LifeAwesome;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WP_Block_Pattern_Categories_Registry;

/**
 * Init Class
 *
 * @package lifeawesome
 */
class Block_Patterns {

	/**
	 * Instance variable
	 *
	 * @var $instance
	 */
	private static $instance;

	/**
	 * Class instance.
	 *
	 * @return BlockPatterns
	 */
	public static function instance() {
		if ( null === static::$instance ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	/**
	 * Class constructor.
	 */
	public function __construct() {
		$this->register_block_patterns();
		$this->register_synced_patterns();
	}

	/**
	 * Register Block Patterns
	 */
	private function register_block_patterns() {
		$block_pattern_categories = array(
			'lifeawesome-core' => array( 'label' => esc_html__( 'LifeAwesome Core Patterns', 'lifeawesome' ) ),
		);

		if ( defined( 'GUTENVERSE' ) ) {
			$block_pattern_categories['lifeawesome-gutenverse'] = array( 'label' => esc_html__( 'LifeAwesome Gutenverse Patterns', 'lifeawesome' ) );
			$block_pattern_categories['lifeawesome-pro'] = array( 'label' => esc_html__( 'LifeAwesome Gutenverse PRO Patterns', 'lifeawesome' ) );
		}

		$block_pattern_categories = apply_filters( 'lifeawesome_block_pattern_categories', $block_pattern_categories );

		foreach ( $block_pattern_categories as $name => $properties ) {
			if ( ! WP_Block_Pattern_Categories_Registry::get_instance()->is_registered( $name ) ) {
				register_block_pattern_category( $name, $properties );
			}
		}

		$block_patterns = array(
			'lifeawesome-single-core-hero',
			'lifeawesome-single-core-content',
			'lifeawesome-archive-core-hero',
			'lifeawesome-core-post-block',
			'lifeawesome-search-core-hero',
			'lifeawesome-search-core-search',
			'lifeawesome-core-404',
			'lifeawesome-index-core-hero',
			'lifeawesome-core-post-block',
			'lifeawesome-page-no-sidebar-core-hero',
			'lifeawesome-page-no-sidebar-core-content',
			'lifeawesome-core-footer-alternate',
			'lifeawesome-core-home-hero',
			'lifeawesome-core-home-blog-hero',
			'lifeawesome-core-home-about',
			'lifeawesome-core-home-services',
			'lifeawesome-core-home-clients',
			'lifeawesome-core-home-authority',
			'lifeawesome-core-home-testimonials',
			'lifeawesome-core-home-blog',
			'lifeawesome-page-core-hero',
			'lifeawesome-page-core-content',
		);

		if ( defined( 'GUTENVERSE' ) ) {
			$block_patterns[] = 'lifeawesome-index-gutenverse-hero';
			$block_patterns[] = 'lifeawesome-gutenverse-post-block';
			$block_patterns[] = 'lifeawesome-archive-gutenverse-hero';
			$block_patterns[] = 'lifeawesome-gutenverse-post-block';
			$block_patterns[] = 'lifeawesome-search-gutenverse-hero';
			$block_patterns[] = 'lifeawesome-search-gutenverse-search';
			$block_patterns[] = 'lifeawesome-single-gutenverse-hero';
			$block_patterns[] = 'lifeawesome-single-gutenverse-content';
			$block_patterns[] = 'lifeawesome-gutenverse-404';
			$block_patterns[] = 'lifeawesome-gutenverse-header';
			$block_patterns[] = 'lifeawesome-gutenverse-footer';
			$block_patterns[] = 'lifeawesome-gutenverse-header-alternate';
			$block_patterns[] = 'lifeawesome-gutenverse-footer-alternate';
			$block_patterns[] = 'lifeawesome-page-no-sidebar-gutenverse-hero';
			$block_patterns[] = 'lifeawesome-page-no-sidebar-gutenverse-content';
			$block_patterns[] = 'lifeawesome-gutenverse-home-hero';
			$block_patterns[] = 'lifeawesome-gutenverse-home-blog-hero';
			$block_patterns[] = 'lifeawesome-gutenverse-home-about';
			$block_patterns[] = 'lifeawesome-gutenverse-home-services';
			$block_patterns[] = 'lifeawesome-gutenverse-home-clients';
			$block_patterns[] = 'lifeawesome-gutenverse-home-authority';
			$block_patterns[] = 'lifeawesome-gutenverse-home-testimonials';
			$block_patterns[] = 'lifeawesome-gutenverse-home-blog';
			$block_patterns[] = 'lifeawesome-page-gutenverse-hero';
			$block_patterns[] = 'lifeawesome-page-gutenverse-content';
			
		}

		$block_patterns = apply_filters( 'lifeawesome_block_patterns', $block_patterns );
		$pattern_list   = get_option( 'lifeawesome_synced_pattern_imported', false );
		if ( ! $pattern_list ) {
			$pattern_list = array();
		}

		$active_slug = get_stylesheet();
		$inserted_content = get_option(
			"gutenverse_{$active_slug}_content_inserted",
			array(
				'pages'    => array(),
				'patterns' => array(),
				'menus'    => array(),
				'content_has_menus' => array(),
			)
		);

		if ( function_exists( 'register_block_pattern' ) ) {
			foreach ( $block_patterns as $block_pattern ) {
				$pattern_file = get_theme_file_path( '/inc/patterns/' . $block_pattern . '.php' );
				$pattern_data = require $pattern_file;

				if ( (bool) $pattern_data['is_sync'] ) {
					$post = get_page_by_path( $block_pattern . '-synced', OBJECT, 'wp_block' );
					$post_id = $post ? $post->ID : null;
					if ( empty( $post ) ) {
						/**Download Image */
						$content = wp_slash( $pattern_data['content'] );
						$image_importer_ver = $pattern_data['image_importer_ver'] ?? null;
						if ( isset( $pattern_data['images'] ) && ! empty( $pattern_data['images'] ) ) {
							$images = json_decode( $pattern_data['images'] );
							if ( ! $image_importer_ver ) {
								foreach ( $images as $key => $image ) {
									$url  = $image->image_url;
									$data = Helper::check_image_exist( $url );
									if ( ! $data ) {
										$data = Helper::handle_file( $url );
									}
									$content  = str_replace( $url, $data['url'], $content );
									$image_id = $image->image_id;
									if ( $image_id && 'null' !== $image_id ) {
										$content = str_replace( '"imageId\":' . $image_id, '"imageId\":' . $data['id'], $content );
									}
								}
							} else {
								foreach ( $images as $key => $image ) {
									$url     = $key;
									$pattern = $image->pattern;
									$data    = Helper::check_image_exist( $url );
									if ( ! $data ) {
										$data = Helper::handle_file( $url );
									}
									foreach ( $pattern as $p ) {
										$placeholder_arr        = explode( '|', trim( $p, '{}' ) );
										$placeholder_value_type = end( $placeholder_arr );
										switch ( $placeholder_value_type ) {
											case 'url':
												$placeholder_data_type = $placeholder_arr[1];
												if ( 'case2' === $placeholder_data_type ) {
													$placeholder_data_size = $placeholder_arr[3];
													$target                = wp_get_attachment_image_url( $data['id'], $placeholder_data_size );
												} else {
													$target = wp_get_attachment_url( $data['id'] );
												}
												break;
											case 'id':
											default:
												$target = $data['id'];
												break;
										}
										$content = str_replace( $p, $target, $content );
									}
								}
							}
						}
						$content = $this->decode_unicode_sequences($content);
						$post_id = wp_insert_post(
							array(
								'post_name'    => $block_pattern . '-synced',
								'post_title'   => $pattern_data['title'],
								'post_content' => $content,
								'post_status'  => 'publish',
								'post_author'  => 1,
								'post_type'    => 'wp_block',
							)
						);
						if ( isset( $pattern_data['placeholder'] ) ) {
							$inserted_content['patterns'][] = array(
								'id' => $post_id,
								'is_remapped' => false,
								'placeholder' => ! empty( $pattern_data['placeholder'] ) ? $pattern_data['placeholder'] : '',
							);
						}
						if ( ! is_wp_error( $post_id ) ) {
							$pattern_category = $pattern_data['categories'];
							foreach ( $pattern_category as $category ) {
								wp_set_object_terms( $post_id, $category, 'wp_pattern_category' );
							}
						}
						$pattern_data['content']  = '<!-- wp:block {"ref":' . $post_id . '} /-->';
						$pattern_data['inserter'] = false;
						$pattern_data['slug']     = $block_pattern;

						$pattern_list[] = $pattern_data;
						/**Check if content has menu */
						$normalized_content = wp_unslash( $content );
						preg_match_all(
							'/"menuId"\s*:\s*(?:"(\d+)"|(\d+))/',
							$normalized_content,
							$matches
						);

						if ( ! empty( array_filter( array_merge( $matches[1], $matches[2] ) ) ) ) {
							$inserted_content['content_has_menus'][] = $post_id;
						}
					}
					
				} else {
					register_block_pattern(
						'lifeawesome/' . $block_pattern,
						require $pattern_file
					);
				}
			}
			
			update_option( 'lifeawesome_synced_pattern_imported', $pattern_list );
			update_option(
				"gutenverse_{$active_slug}_content_inserted",
				$inserted_content
			);
		}
	}

	/**
	 * Decode unicode sequences
	 *
	 * @param string $content .
	 * @return string
	 */
	private function decode_unicode_sequences( $content ) {
		return preg_replace_callback(
			'/\\\\u([0-9a-fA-F]{4})/',
			function ( $matches ) {

				$hex = strtolower( $matches[1] );

				// Always keep quotes escaped.
				if ( '0022' === $hex ) {
					return '\"';
				}

				$codepoint = hexdec( $hex );

				return mb_convert_encoding(
					pack( 'n', $codepoint ),
					'UTF-8',
					'UTF-16BE'
				);
			},
			$content
		);
	}

	/**
	 * Register Synced Patterns
	 */
	 private function register_synced_patterns() {
		$patterns = get_option( 'lifeawesome_synced_pattern_imported' );

		 foreach ( $patterns as $block_pattern ) {
			 register_block_pattern(
				'lifeawesome/' . $block_pattern['slug'],
				$block_pattern
			);
		 }
	 }
}
