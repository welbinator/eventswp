<?php
namespace EventsWP;

defined( 'ABSPATH' ) || exit;

class Meta {

	public static function init() {
		add_action( 'init', [ __CLASS__, 'register_meta_fields' ] );
	}

	public static function register_meta_fields() {
		$post_type = 'eventswp-event';

        register_post_meta( $post_type, 'event_show_map', [
            'show_in_rest'       => true,
            'single'             => true,
            'type'               => 'boolean',
            'sanitize_callback'  => 'rest_sanitize_boolean',
            'auth_callback'      => function() {
                return current_user_can( 'edit_posts' );
            },
        ] );
        
		register_post_meta( $post_type, 'event_date', [
			'show_in_rest' => true,
			'single'       => true,
			'type'         => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'auth_callback' => function() {
				return current_user_can( 'edit_posts' );
			},
		] );

		register_post_meta( $post_type, 'event_time', [
			'show_in_rest' => true,
			'single'       => true,
			'type'         => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'auth_callback' => function() {
				return current_user_can( 'edit_posts' );
			},
		] );

		register_post_meta( $post_type, 'event_end_time', [
			'type'         => 'string',
			'single'       => true,
			'show_in_rest' => true,
			'sanitize_callback' => 'sanitize_text_field',
			'auth_callback' => function() {
				return current_user_can( 'edit_posts' );
			},
		] );
		

		register_post_meta( $post_type, 'event_venue_name', [
            'show_in_rest'       => true,
            'single'             => true,
            'type'               => 'string',
            'sanitize_callback'  => 'sanitize_text_field',
            'auth_callback'      => function() {
                return current_user_can( 'edit_posts' );
            },
        ] );
        
        register_post_meta( $post_type, 'event_venue_address', [
            'show_in_rest'       => true,
            'single'             => true,
            'type'               => 'string',
            'sanitize_callback'  => 'sanitize_text_field',
            'auth_callback'      => function() {
                return current_user_can( 'edit_posts' );
            },
        ] );
        

		register_post_meta( $post_type, 'event_contact_phone', [
			'show_in_rest' => true,
			'single'       => true,
			'type'         => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'auth_callback' => function() {
				return current_user_can( 'edit_posts' );
			},
		] );

		register_post_meta( $post_type, 'event_contact_email', [
			'show_in_rest' => true,
			'single'       => true,
			'type'         => 'string',
			'sanitize_callback' => 'sanitize_email',
			'auth_callback' => function() {
				return current_user_can( 'edit_posts' );
			},
		] );

		


	}
}
