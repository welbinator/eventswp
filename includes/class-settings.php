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
		register_setting( 'eventswp_settings', 'eventswp_calendar_page_id' );
		register_setting( 'eventswp_settings', 'eventswp_calendar_title' );
		register_setting( 'eventswp_settings', 'eventswp_hide_calendar_title' );

		add_settings_section(
			'eventswp_main_section',
			__( 'Calendar Settings', 'eventswp' ),
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

		add_settings_field(
			'eventswp_calendar_page_id',
			__( 'Calendar Page', 'eventswp' ),
			[ __CLASS__, 'render_calendar_page_field' ],
			'eventswp-settings',
			'eventswp_main_section'
		);

		add_settings_field(
			'eventswp_calendar_title',
			__( 'Calendar Title', 'eventswp' ),
			[ __CLASS__, 'render_calendar_title_field' ],
			'eventswp-settings',
			'eventswp_main_section'
		);

		add_settings_field(
			'eventswp_hide_calendar_title',
			__( 'Hide Calendar Title', 'eventswp' ),
			[ __CLASS__, 'render_hide_title_field' ],
			'eventswp-settings',
			'eventswp_main_section'
		);
	}

	public static function render_api_key_field() {
		$value = esc_attr( get_option( 'eventswp_google_maps_api_key' ) );
		echo '<input type="text" name="eventswp_google_maps_api_key" value="' . $value . '" class="regular-text">';
	}

	public static function render_calendar_page_field() {
		$selected_id = get_option( 'eventswp_calendar_page_id' );
		$pages = get_pages();

		echo '<select name="eventswp_calendar_page_id">';
		echo '<option value="">' . esc_html__( '-- Select a Page --', 'eventswp' ) . '</option>';

		foreach ( $pages as $page ) {
			$selected = selected( $selected_id, $page->ID, false );
			printf(
				'<option value="%d" %s>%s</option>',
				$page->ID,
				$selected,
				esc_html( $page->post_title )
			);
		}

		echo '</select>';
	}

	public static function render_calendar_title_field() {
		$value = esc_attr( get_option( 'eventswp_calendar_title', 'Event Calendar' ) );
		echo '<input type="text" name="eventswp_calendar_title" value="' . $value . '" class="regular-text">';
	}

	public static function render_hide_title_field() {
		$checked = checked( get_option( 'eventswp_hide_calendar_title' ), '1', false );
		echo '<label><input type="checkbox" name="eventswp_hide_calendar_title" value="1" ' . $checked . '> ';
		echo esc_html__( 'Do not display a title above the calendar.', 'eventswp' );
		echo '</label>';
	}
}
