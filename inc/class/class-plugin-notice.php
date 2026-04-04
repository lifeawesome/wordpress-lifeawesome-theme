<?php
/**
 * Init Configuration
 *
 * @author Jegstudio
 * @package lifeawesome
 */

namespace LifeAwesome;

use WP_Query;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin Notice Class
 *
 * @package lifeawesome
 */
class Plugin_Notice {

	/**
	 * Instance variable
	 *
	 * @var $instance
	 */
	private static $instance;

	/**
	 * Class instance.
	 *
	 * @return Init
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
		$this->load_hooks();
	}

	/**
	 * Load initial hooks.
	 */
	private function load_hooks() {
		add_action( 'admin_notices', array( $this, 'notice_install_plugin' ) );
		add_action( 'wp_ajax_gutenverse_companion_lifeawesome_dismiss_notice', array( $this, 'dismiss_notice' ) );
	}

	/**
	 * Show notification to install Gutenverse Plugin.
	 */
	public function notice_install_plugin() {
		// Skip if gutenverse block activated.
		if ( ( defined( 'GUTENVERSE' ) && defined( 'GUTENVERSE_PRO' ) ) || get_option( 'gutenverse_companion_lifeawesome_notice_dismissed' ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( isset( $screen->parent_file ) && 'themes.php' === $screen->parent_file && 'appearance_page_lifeawesome-dashboard' === $screen->id ) {
			return;
		}

		if ( isset( $screen->parent_file ) && 'plugins.php' === $screen->parent_file && 'update' === $screen->id ) {
			return;
		}

		if ( 'true' === get_user_meta( get_current_user_id(), 'gutenverse_install_notice', true ) ) {
			return;
		}

        $active_plugins = get_option( 'active_plugins' );
		$plugins = array();
		foreach( $active_plugins as $active ) {
			$plugins[] = explode( '/', $active)[0];
		}
		$all_plugin = get_plugins();
		$plugins_required    = array(
            array(
					'slug'       		=> 'gutenverse',
					'title'      		=> esc_html__( 'Gutenverse', 'lifeawesome' ),
					'short_desc' 		=> esc_html__( 'GUTENVERSE – GUTENBERG BLOCKS AND WEBSITE BUILDER FOR SITE EDITOR, TEMPLATE LIBRARY, POPUP BUILDER, ADVANCED ANIMATION EFFECTS, COMPLETE FEATURE ECOSYSTEM, 45+ FREE USER-FRIENDLY BLOCKS', 'lifeawesome' ),
					'active'    		=> in_array( 'gutenverse', $plugins, true ),
					'installed'  		=> $this->is_installed( 'gutenverse' ),
					'req_version'    	=> '3.2.0',
					'installed_version' => isset( $installed_plugins['gutenverse/gutenverse.php']['Version'] ) ? $installed_plugins['gutenverse/gutenverse.php']['Version'] : '',
					'icons'      		=> array (
  '1x' => 'https://ps.w.org/gutenverse/assets/icon-128x128.gif?rev=3132408',
  '2x' => 'https://ps.w.org/gutenverse/assets/icon-256x256.gif?rev=3132408',
),
					'download_url'      => '',
				),
				array(
					'slug'       		=> 'gutenverse-companion',
					'title'      		=> esc_html__( 'Gutenverse Companion', 'lifeawesome' ),
					'short_desc' 		=> esc_html__( 'A companion plugin designed specifically to enhance and extend the functionality of Gutenverse base themes. This plugin integrates seamlessly with the base themes, providing additional features, customization options, and advanced tools to optimize the overall user experience and streamline the development process.', 'lifeawesome' ),
					'active'    		=> in_array( 'gutenverse-companion', $plugins, true ),
					'installed'  		=> $this->is_installed( 'gutenverse-companion' ),
					'req_version'    	=> '2.0.0',
					'installed_version' => isset( $installed_plugins['gutenverse-companion/gutenverse-companion.php']['Version'] ) ? $installed_plugins['gutenverse-companion/gutenverse-companion.php']['Version'] : '',
					'icons'      		=> array (
  '1x' => 'https://ps.w.org/gutenverse-companion/assets/icon-128x128.png?rev=3162415',
),
					'download_url'      => '',
				)
        );
		$actions    = array();
		$count_plugin_active = 0;
		foreach ( $plugins_required as $plugin ) {
			$slug   = $plugin['slug'];
			$path   = "$slug/$slug.php";
			$active = in_array($path, $active_plugins, false);

			if ( isset( $all_plugin[ $path ] ) ) {
				if ( $active ) {
					$actions[ $slug ] = 'active';
					++$count_plugin_active;
				} else {
					$actions[ $slug ] = 'inactive';
				}
			} else {
				$actions[ $slug ] = '';
			}
		}

		$count_plugin_requiored = count( $plugins_required );
		if ( $count_plugin_active === $count_plugin_requiored ) {
			return;
		}

		?>
		<style>
			.lifeawesome-simple-notice {
			
				padding: 24px !important; 
			
				border-left: 4px solid #007cba !important;
			}
			.lifeawesome-simple-notice p:first-child {
			
				margin-bottom: 20px; 
				margin-top: 0;
			}
			.lifeawesome-simple-notice p:last-of-type {
			
				margin-top: 0; 
				margin-bottom: 0;
			}
			.lifeawesome-simple-notice .lifeawesome-notice-title {
				font-size: 17px;
				font-weight: 600; 
				display: block;
				margin-bottom: 6px;
			}
			.lifeawesome-simple-notice .lifeawesome-notice-description {
				font-size: 13px;
				display: block;
			
				margin-top: 0; 
				color: #3c434a;
				line-height: 1.4;
			}
		
			.lifeawesome-simple-notice .button-primary {
				padding: 4px 16px !important;
			}

		
			.lifeawesome-simple-notice .notice-dismiss {
				background: none !important;
				box-shadow: none !important;
				opacity: 1;
			}
			
			.lifeawesome-simple-notice .notice-dismiss:before {
				color: #a7aaad;
				padding: 0; 
			}
			
			.lifeawesome-simple-notice .notice-dismiss:hover:before,
			.lifeawesome-simple-notice .notice-dismiss:focus:before {
				color: #c90000;
			}
		</style>
		<script>
		// Retain the core installation/activation logic script
		var promises = [];
		var actions = <?php echo wp_json_encode( $actions ); ?>;
		let site_url = '<?php echo admin_url(); ?>';

		const LIFEAWESOMEPluginUtils = {
			isVersionGreater(v1, v2) {
				const a = v1.split('.').map(Number);
				const b = v2.split('.').map(Number);
				const len = Math.max(a.length, b.length);

				for (let i = 0; i < len; i++) {
					const n1 = a[i] ?? 0;
					const n2 = b[i] ?? 0;
					if (n1 > n2) return true;
					if (n1 < n2) return false;
				}
				return false;
			}
		};

		function sequenceInstall(plugin, pluginsInstalled) {
			return new Promise((resolve, reject) => {
				if (!plugin) return resolve();

				const slug = plugin.slug;
				const path = `${slug}/${slug}`;
				const needUpdate = plugin.installed
					? LIFEAWESOMEPluginUtils.isVersionGreater(plugin.req_version, pluginsInstalled[`${path}.php`].Version)
					: false;

				let request;

				if (needUpdate) {
					wp.apiFetch({
						path: `wp/v2/plugins/plugin?plugin=${path}`,
						method: 'PUT',
						data: { status: 'inactive' }
					})
						.then(() => {
							return wp.apiFetch({
								path: `wp/v2/plugins/plugin?plugin=${path}`,
								method: 'DELETE'
							});
						})
						.then(() => {
							return wp.apiFetch({
								path: 'wp/v2/plugins',
								method: 'POST',
								data: { slug, status: 'active' }
							});
						})
						.then(() => resolve())
						.catch((error) => {
							console.error(`Failed to update plugin ${slug}:`, error);
							resolve();
						});
				} else {
					switch (actions[slug]) {
						case 'active':
							return resolve();

						case 'inactive':
							request = wp.apiFetch({
								path: `wp/v2/plugins/plugin?plugin=${path}`,
								method: 'POST',
								data: { status: 'active' }
							});
							break;

						default:
							request = wp.apiFetch({
								path: 'wp/v2/plugins',
								method: 'POST',
								data: { slug, status: 'active' }
							});
							break;
					}

					request
						.then(() => resolve())
						.catch((error) => {
							console.error(`Failed to install/activate ${slug}:`, error);
							resolve();
						});
				}
			});
		}
		
		document.addEventListener('DOMContentLoaded', () => {
			const notice = document.querySelector('.notice.is-dismissible.lifeawesome-simple-notice');

			if (notice) {
				setTimeout(() => {
					const dismissBtn = notice.querySelector('.notice-dismiss');
					const nonce      = notice.getAttribute('data-nonce');

					if (dismissBtn) {
						dismissBtn.addEventListener('click', (e) => {
							e.preventDefault();
							jQuery.post(ajaxurl, {
								action: 'gutenverse_companion_lifeawesome_dismiss_notice',
								_ajax_nonce: nonce
							});
						});
					}
				}, 100);
			}
			
			const button = document.getElementById('gutenverse-install-plugin');
			
			if (!button) return;

			button.addEventListener('click', function (e) {
				// Prevent navigation/default action immediately
				e.preventDefault(); 
				
				// Update button text to show loading/processing state
				button.innerHTML = `<?php esc_html_e( 'Installing...', 'lifeawesome' ); ?>`;
				button.classList.add('processing');

				const hasFinishClass = button.classList.contains('finished');

				if (!hasFinishClass) {
					const pluginsRequired = <?php echo wp_json_encode( $plugins_required ); ?>;
					const plugins = [
						{ name: 'Gutenverse', slug: 'gutenverse', version: '3.2.0', url: '' },
						{ name: 'Gutenverse Companion', slug: 'gutenverse-companion', version: '2.0.0', url: '' },
					];

					const combinedPlugins = plugins.map(plugin => {
						const match = pluginsRequired.find(req => req.slug === plugin.slug);
						return { ...plugin, ...match };
					});

					const pluginsInstalled = <?php echo wp_json_encode( $all_plugin ); ?>;
					let sequence = Promise.resolve();

					combinedPlugins.forEach(plugin => {
						sequence = sequence.then(() => sequenceInstall(plugin, pluginsInstalled));
					});

					sequence.then(() => {
						window.location.href = site_url + 'themes.php?page=gutenverse-companion-wizard';
		
					}).catch(() => {
						// Handle errors (optional: show error message)
						button.innerHTML = `<?php esc_html_e( 'Install Failed, Try Again', 'lifeawesome' ); ?>`;
						button.classList.remove('processing');
					});
				}
			});
		});
		</script>
		<div class="notice notice-info is-dismissible lifeawesome-simple-notice" data-nonce="<?php echo esc_attr( wp_create_nonce( "gutenverse_companion_lifeawesome_dismiss" ) ); ?>">
			<p>
				<strong class="lifeawesome-notice-title"><?php esc_html_e( "Thank you For Installing LifeAwesome Theme", "lifeawesome" ); ?></strong>
				<span class="lifeawesome-notice-description">
					<?php esc_html_e( "Unlock the full potential of your website with the recommended plugins.", "lifeawesome" ); ?>
					<br/>
					<?php esc_html_e( "Activate it to explore exclusive extensions, ready-to-use demo templates, and powerful features that make building your site easier and more enjoyable.", "lifeawesome" ); ?>
				</span>
			</p>
			<p><a href="#" class="button button-primary" id="gutenverse-install-plugin"><?php echo esc_html__( "Install Recommended Plugins", "lifeawesome" ); ?></a></p>
		</div>
		<?php
	}

	/**
	 * Dismiss Notice After closed.
	 */
	public function dismiss_notice() {
		check_ajax_referer( 'gutenverse_companion_lifeawesome_dismiss' );

		if ( ! get_option( 'gutenverse_companion_lifeawesome_notice_dismissed' ) ) {
			update_option( 'gutenverse_companion_lifeawesome_notice_dismissed', true );
		}

		wp_send_json_success();
	}
    
    /**
	 * Check if plugin is installed.
	 *
	 * @param string $plugin_slug plugin slug.
	 * 
	 * @return boolean
	 */
	public function is_installed( $plugin_slug ) {
		$all_plugins = get_plugins();
		foreach ( $all_plugins as $plugin_file => $plugin_data ) {
			$plugin_dir = dirname($plugin_file);

			if ($plugin_dir === $plugin_slug) {
				return true;
			}
		}

		return false;
	}
}
