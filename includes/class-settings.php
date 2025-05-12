<?php
namespace EventsWP;

defined( 'ABSPATH' ) || exit;

class Settings {

	public static function init() {
		add_action( 'admin_menu', [ __CLASS__, 'add_settings_page' ] );
		add_action( 'admin_init', [ __CLASS__, 'register_settings' ] );
	}

	public static function add_settings_page() {
		add_options_page(
			__( 'EventsWP Settings', 'eventswp' ),
			'EventsWP',
			'manage_options',
			'eventswp-settings',
			[ __CLASS__, 'render_settings_page' ]
		);
	}

	public static function render_settings_page() {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'EventsWP Settings', 'eventswp' ); ?></h1>
			<form method="post" action="options.php">
				<?php
				settings_fields( 'eventswp_settings' );
				do_settings_sections( 'eventswp-settings' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	public static function register_settings() {
		register_setting( 'eventswp_settings', 'eventswp_google_maps_api_key' );

		add_settings_section(
			'eventswp_main_section',
			__( 'Google Maps Settings', 'eventswp' ),
			null,
			'eventswp-settings'
		);

		add_settings_field(
			'eventswp_google_maps_api_key',
			__( 'Google Maps API Key', 'eventswp' ),
			[ __CLASS__, 'render_api_key_field' ],
			'eventswp-settings',
			'eventswp_main_section'
		);
	}

	public static function render_api_key_field() {
		$value = esc_attr( get_option( 'eventswp_google_maps_api_key' ) );
		echo '<input type="text" name="eventswp_google_maps_api_key" value="' . $value . '" class="regular-text">';
	}
}
