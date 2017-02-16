<?php

/**
 * Class Give_Donation_Duration_Metabox_Settings
 *
 * @since 1.0
 */
class Give_Donation_Duration_Metabox_Settings {

	/**
	 * Instance.
	 *
	 * @since  1.0
	 * @access static
	 * @var Give_Donation_Duration_Metabox_Settings
	 */
	private static $instance;

	/**
	 * Setting id.
	 *
	 * @since  1.0
	 * @access private
	 *
	 * @var string
	 */
	private $id = '';

	/**
	 * Setting label.
	 *
	 * @since  1.0
	 * @access private
	 * @var string
	 */
	private $label = '';


	/**
	 * Singleton pattern.
	 *
	 * @since  1.0
	 * @access private
	 * Give_Donation_Duration_Metabox_Settings constructor.
	 */
	private function __construct() {
	}


	/**
	 * Get single instance.
	 *
	 * @since  1.0
	 * @access public
	 * @return Give_Donation_Duration_Metabox_Settings
	 */
	public static function get_instance() {
		if ( null === static::$instance ) {
			static::$instance = new static();
		}

		return static::$instance;
	}


	/**
	 * Setup params.
	 *
	 * @since  1.0
	 * @access public
	 * @return Give_Donation_Duration_Metabox_Settings
	 */
	public function setup_params() {
		$this->id    = 'give-donation-duration';
		$this->label = __( 'Donation Duration', 'give-donation-duration' );

		return static::get_instance();

	}

	/**
	 * Give_Donation_Duration_Metabox_Settings constructor.
	 *
	 * @since  1.0
	 * @access public
	 */
	public function setup_hooks() {
		// Add settings.
		add_filter( 'give_metabox_form_data_settings', array( $this, 'setup_setting' ), 999 );

		// Enqueue scripts.
		add_filter( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ), 999 );

		// Validate setting.
		add_action( 'give_post_process_give_forms_meta', array( $this, 'validate_settings' ) );
	}


	/**
	 * Plugin setting.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @param $settings
	 *
	 * @return mixed
	 */
	public function setup_setting( $settings ) {

		// Setup settings.
		$new_settings = array(
			$this->id => array(
				'id'     => $this->id,
				'title'  => $this->label,
				'fields' => array(
					// Close Form.
					array(
						'id'          => 'donation-duration-close-form',
						'name'        => __( 'Enable Duration', 'give-donation-duration' ),
						'type'        => 'radio_inline',
						'default'     => 'disabled',
						'options'     => array(
							'enabled'  => __( 'Enabled', 'give-donation-duration' ),
							'disabled' => __( 'Disabled', 'give-donation-duration' ),
						),
						'description' => __( 'Enable this to set a time when the form will close automatically and a custom message will appear.', 'give-donation-duration' ),
					),

					// Donation duration type.
					array(
						'id'      => 'donation-duration-by',
						'name'    => __( 'Duration Timeframe', 'give-donation-duration' ),
						'type'    => 'radio_inline',
						'default' => 'number_of_days',
						'options' => array(
							'number_of_days'      => __( 'Number of days', 'give-donation-duration' ),
							'end_on_day_and_time' => __( 'Specific day & time', 'give-donation-duration' ),
						),
                        'description' => __( 'Set when the form should close automatically.', 'give-donation-duration' ),
					),

					// Days.
					array(
						'id'      => 'donation-duration-in-number-of-days',
						'name'    => __( 'Number of Days', 'give-donation-duration' ),
						'type'    => 'text-small',
						'default' => '30',
                        'description' => __( 'The number of days from the date of publication that the duration should last.', 'give-donation-duration' ),
					),

					// Date
					array(
						'id'   => 'donation-duration-on-date',
						'name' => __( 'Date', 'give-donation-duration' ),
						'type' => 'text-medium',
                        'description' => __( 'The date for which you want the duration to end.', 'give-donation-duration' ),
					),

					// Time
					array(
						'id'      => 'donation-duration-on-time',
						'name'    => __( 'Time', 'give-donation-duration' ),
						'type'    => 'select',
						'options' => gdd_get_time_list(),
                        'description' => __( 'The time of day you want the duration to end on your designated day.', 'give-donation-duration' ),
					),

					// Duration achieved message.
					array(
						'id'         => 'donation-duration-message',
						'name'       => __( 'Duration ended message', 'give-donation-duration' ),
						'type'       => 'wysiwyg',
						'attributes' => array(
							'placeholder' => __( 'Thank you to all our donors, we have met our fundraising goal.', 'give-donation-duration' ),
						),
                        'description' => __( 'This is the content that will appear in your form when the duration has ended.', 'give-donation-duration' ),
					),
				),
			)
		);

		return array_merge( $settings, $new_settings );
	}


	/**
	 * Load scripts.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @param string $hook
	 */
	function enqueue_admin_scripts( $hook ) {
		// Bailout.
		if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ) ) ) {
			return;
		}

		global $post;


		// Bailout.
		if ( 'give_forms' !== $post->post_type ) {
			return;
		}

		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_script( 'donation-duration-admin-script', GDD_PLUGIN_URL . 'assets/js/admin-script.js' );
	}


	/**
	 * Validate setting.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @param $form_id
	 */
	public function validate_settings( $form_id ) {
		if( ! gdd_get_form_close_date( $form_id ) ){
			update_post_meta( $form_id, 'donation-duration-close-form', 'disabled' );
		}
	}
}


// initialize.
Give_Donation_Duration_Metabox_Settings::get_instance()->setup_params()->setup_hooks();
