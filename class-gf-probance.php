<?php

GFForms::include_feed_addon_framework();

/**
 * Gravity Forms Probance Add-On.
 *
 * @since     1.0.0
 * @package   GravityForms
 * @author    Arnaud Flament
 */
class GFProbance extends GFFeedAddOn {

	/**
	 * Contains an instance of this class, if available.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    object $_instance If available, contains an instance of this class.
	 */
	private static $_instance = null;

	/**
	 * Defines the version of the Probance Add-On.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string $_version Contains the version, defined from tmsm-gravityforms-probance-old.php
	 */
	protected $_version = GF_PROBANCE_VERSION;

	/**
	 * Defines the minimum Gravity Forms version required.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string $_min_gravityforms_version The minimum version required.
	 */
	protected $_min_gravityforms_version = '1.9.12';

	/**
	 * Defines the plugin slug.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string $_slug The slug used for this plugin.
	 */
	protected $_slug = 'tmsm-gravityforms-probance-old';

	/**
	 * Defines the main plugin file.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string $_path The path to the main plugin file, relative to the plugins folder.
	 */
	protected $_path = 'tmsm-gravityforms-tmsm-gravityforms-probance-old.php';

	/**
	 * Defines the full path to this class file.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string $_full_path The full path.
	 */
	protected $_full_path = __FILE__;

	/**
	 * Defines the URL where this Add-On can be found.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string The URL of the Add-On.
	 */
	protected $_url = 'https://github.com/aflamentTM/tmsm-gravityforms-probance';

	/**
	 * Defines the title of this Add-On.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string $_title The title of the Add-On.
	 */
	protected $_title = 'Gravity Forms Probance Add-On';

	/**
	 * Defines the short title of the Add-On.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string $_short_title The short title.
	 */
	protected $_short_title = 'Probance';

	/**
	 * Defines if Add-On should use Gravity Forms servers for update data.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    bool
	 */
	protected $_enable_rg_autoupgrade = false;

	/**
	 * Defines the capabilities needed for the Probance Add-On
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    array $_capabilities The capabilities needed for the Add-On
	 */
	protected $_capabilities = array( 'gravityforms_probance', 'gravityforms_probance_uninstall' );

	/**
	 * Defines the capability needed to access the Add-On settings page.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string $_capabilities_settings_page The capability needed to access the Add-On settings page.
	 */
	protected $_capabilities_settings_page = 'gravityforms_probance';

	/**
	 * Defines the capability needed to access the Add-On form settings page.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string $_capabilities_form_settings The capability needed to access the Add-On form settings page.
	 */
	protected $_capabilities_form_settings = 'gravityforms_probance';

	/**
	 * Defines the capability needed to uninstall the Add-On.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string $_capabilities_uninstall The capability needed to uninstall the Add-On.
	 */
	protected $_capabilities_uninstall = 'gravityforms_probance_uninstall';

	/**
	 * Defines the Probance list field tag name.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string $merge_var_name The Probance list field tag name; used by gform_probance_field_value.
	 */
	protected $merge_var_name = '';

	/**
	 * Contains an instance of the Probance API library, if available.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    object $api If available, contains an instance of the Probance API library.
	 */
	private $api = null;

	/**
	 * Get an instance of this class.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return GFProbance
	 */
	public static function get_instance() {

		if ( null === self::$_instance ) {
			self::$_instance = new self;
		}

		return self::$_instance;

	}

	/**
	 * Autoload the required libraries.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @uses   GFAddOn::is_gravityforms_supported()
	 */
	public function pre_init() {

		parent::pre_init();

		if ( $this->is_gravityforms_supported() ) {

			// Load the Mailgun API library.
			if ( ! class_exists( 'GF_Probance_API' ) ) {
				require_once('includes/class-gf-probance-api.php');
			}

		}

	}

	/**
	 * Plugin starting point. Handles hooks, loading of language files and PayPal delayed payment support.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @uses   GFFeedAddOn::add_delayed_payment_support()
	 */
	public function init() {

		parent::init();

	}

	/**
	 * Remove unneeded settings.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function uninstall() {

		parent::uninstall();

		GFCache::delete( 'probance_plugin_settings' );
		delete_option( 'gf_probance_settings' );
		delete_option( 'gf_probance_version' );

	}

	/**
	 * Register needed styles.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return array
	 */
	public function styles() {

		$styles = array(
			array(
				'handle'  => $this->_slug . '_form_settings',
				'src'     => $this->get_base_url() . '/css/form_settings.css',
				'version' => $this->_version,
				'enqueue' => array( 'admin_page' => array( 'form_settings' ) ),
			),
		);

		return array_merge( parent::styles(), $styles );

	}

	/**
	 * Configures the settings which should be rendered on the add-on settings tab.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return array
	 */
	public function plugin_settings_fields() {

		return array(
			array(
				'description' => '',
				'fields'      => array(
					array(
						'name'  => 'username',
						'label' => esc_html__( 'Probance Username', 'tmsm-gravityforms-probance-old' ),
						'type'  => 'text',
						'class' => 'medium',
					),
					array(
						'name'              => 'password',
						'label'             => esc_html__( 'Probance Password', 'tmsm-gravityforms-probance-old' ),
						'type'              => 'text',
						'class'             => 'medium',
						'feedback_callback' => array( $this, 'initialize_api' ),
					),

				),
			),


		);

	}

	/**
	 * Configures the settings which should be rendered on the feed edit page.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return array
	 */
	public function feed_settings_fields() {

		return array(
			array(
				'title'  => esc_html__( 'Probance Feed Settings', 'tmsm-gravityforms-probance-old' ),
				'fields' => array(
					array(
						'name'     => 'feedName',
						'label'    => esc_html__( 'Name', 'tmsm-gravityforms-probance-old' ),
						'type'     => 'text',
						'required' => true,
						'class'    => 'medium',
						'tooltip'  => sprintf(
							'<h6>%s</h6>%s',
							esc_html__( 'Name', 'tmsm-gravityforms-probance-old' ),
							esc_html__( 'Enter a feed name to uniquely identify this setup.', 'tmsm-gravityforms-probance-old' )
						),

					),


				),
			),
			array(
                // Get all the fields to map with the gravity form fields
				'fields'     => array(
					array(
						'name'      => 'mappedFields',
						'label'     => esc_html__( 'Map Fields', 'tmsm-gravityforms-probance-old' ),
						'type'      => 'field_map',
						'field_map' => $this->merge_vars_field_map(),
						'tooltip'   => sprintf(
							'<h6>%s</h6>%s',
							esc_html__( 'Map Fields', 'tmsm-gravityforms-probance-old' ),
							esc_html__( 'Associate your Probance merge tags to the appropriate Gravity Form fields by selecting the appropriate form field from the list.',
								'tmsm-gravityforms-probance-old' )
						),
					),
                    // Get the optin fields and what to map with in gravity forms ( for conditional logic ? )
					array(
						'name'      => 'optin_flag',
						'label'     => esc_html__( 'Map Optins', 'tmsm-gravityforms-probance-old' ),
						'type'      => 'field_map',
						'field_map' => $this->merge_vars_optin_map(),
						'tooltip'   => sprintf(
							'<h6>%s</h6>%s',
							esc_html__( 'Map Optins', 'tmsm-gravityforms-probance-old' ),
							esc_html__( 'Associate your Probance optins to the appropriate Gravity Form fields by selecting the appropriate form field from the list.',
								'tmsm-gravityforms-probance-old' )
						),
					),
                    // Conditional logic to purchase the api call to Probance
					array(
						'name'    => 'optinCondition',
						'label'   => esc_html__( 'Conditional Logic', 'tmsm-gravityforms-probance-old' ),
						'type'    => 'feed_condition',
						'tooltip' => sprintf(
							'<h6>%s</h6>%s',
							esc_html__( 'Conditional Logic', 'tmsm-gravityforms-probance-old' ),
							esc_html__( 'When conditional logic is enabled, form submissions will only be exported to Probance when the conditions are met. When disabled all form submissions will be exported.',
								'tmsm-gravityforms-dialoginsight' )
						),
					),
//
					array( 'type' => 'save' ),
                )
            )
        );
	}


	/**
	 * Return an array of Probance list fields which can be mapped to the Form fields/entry meta.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return array
	 */
	public function merge_vars_field_map() {
		// Initialize field map array.
		return $merge_fields = array(
            array(
            'name'          => 'email',
            'label'         => esc_html__( 'Email Address', 'tmsm-gravityforms-probance-old' ),
            'required'      => true,
            'field_type'    => array( 'email',  'hidden'),
            'default_value' => $this->get_first_field_by_type( 'EMail' ),
        ),
            array(
                'name'          => 'name1',
                'label'         => esc_html__( 'Last Name', 'tmsm-gravityforms-probance-old' ),
                'required'      => false,
                'field_type'    => array( 'name', 'text', 'hidden' ),
                'default_value' => $this->get_first_field_by_type( 'name', 3 ),
            ),
            array(
                'name'          => 'name2',
                'label'         => esc_html__( 'First Name', 'tmsm-gravityforms-probance-old' ),
                'required'      => false,
                'field_type'    => array( 'name', 'text', 'hidden' ),
                'default_value' => $this->get_first_field_by_type( 'name', 6 ),
            ),
            array(
                'name'          => "gender",
                'label'         => esc_html__( 'Gender', 'tmsm-gravityforms-probance-old' ),
                'required'      => false,
                'field_type'    => array( 'radio', 'text', 'hidden' ),
            ),
            array(
                'name'          => "birthday",
                'label'         => esc_html__( 'Birthday', 'tmsm-gravityforms-probance-old' ),
                'required'      => false,
                'field_type'    => array( 'date', 'text', 'hidden' ),
            ),

        );
	}

//    TODO see if this is needed or just an exemple from Dialog insight
    /**
     * Fields set up from Probance use as an exemple to feed the fields from Probance
     * @return array[] Of form fields
     */
    public function get_probance_fields() {
       return $fields = array(

            array(
                'Code' => 'EMail',
                'Labels'=> array(
                    array(
                        'Culture' => 'fr-CA',
                        'Value' => 'Courriel',
                        )
                    ),
                'DataType' => 'Email',
                'Length' => '125',
                'isRequired' => '1',
                'isKey '=> '1'
            ),

            array(
            'Code' => 'FirstName',
            'Labels' => array(
                    array(
                        'Culture' => 'fr-CA',
                        'Value' => 'Prénom',
                        )
                    ), '
            DataType' => 'Text',
            'Length' => '50',
            'isRequired' => '0',
            'isKey' => ''
        ),
        array(
            'Code' => 'LastName',
            'Labels' => array(
                    array(
                        'Culture' => 'fr-CA',
                        'Value' => 'Nom',
                        )
                    ), '
            DataType' => 'Text',
            'Length' => '50',
            'isRequired' => '0',
            'isKey' => ''
        ),
//        array(
//            'Code' => 'Address',
//            'Labels' => array(
//                    array(
//                        'Culture' => 'fr-CA',
//                        'Value' => 'Adresse',
//                        )
//                    ), '
//            DataType' => 'Text',
//            'Length' => '50',
//            'isRequired' => '0',
//            'isKey' => ''
//        ),
//        array(
//            'Code' => 'Apartment',
//            'Labels' => array(
//                    array(
//                        'Culture' => 'fr-CA',
//                        'Value' => 'Appartement',
//                        )
//                    ), '
//            DataType' => 'Text',
//            'Length' => '10',
//            'isRequired' => '0',
//            'isKey' => ''
//        ),
//
//        array(
//            'Code' => 'City',
//            'Labels' => array(
//                    array(
//                        'Culture' => 'fr-CA',
//                        'Value' => 'Ville',
//                        )
//                    ), '
//            DataType' => 'Text',
//            'Length' => '50',
//            'isRequired' => '0',
//            'isKey' => ''
//        ),
//        array(
//            'Code' => 'Province',
//            'Labels' => array(
//                    array(
//                        'Culture' => 'fr-CA',
//                        'Value' => 'Province',
//                        )
//                    ), '
//            DataType' => 'Text',
//            'Length' => '25',
//            'isRequired' => '0',
//            'isKey' => ''
//        ),
//        array(
//            'Code' => 'Country',
//            'Labels' => array(
//                    array(
//                        'Culture' => 'fr-CA',
//                        'Value' => 'Pays',
//                        )
//                    ), '
//            DataType' => 'Text',
//            'Length' => '25',
//            'isRequired' => '0',
//            'isKey' => ''
//        ),
//        array(
//            'Code' => 'PostalCode',
//            'Labels' => array(
//                    array(
//                        'Culture' => 'fr-CA',
//                        'Value' => 'Code postal',
//                        )
//                    ), '
//            DataType' => 'Text',
//            'Length' => '7',
//            'isRequired' => '0',
//            'isKey' => ''
//        ),
//        array(
//            'Code' => 'HomePhone',
//            'Labels' => array(
//                    array(
//                        'Culture' => 'fr-CA',
//                        'Value' => 'Téléphone maison',
//                        )
//                    ), '
//            DataType' => 'Text',
//            'Length' => '20',
//            'isRequired' => '0',
//            'isKey' => ''
//        ),
//        array(
//            'Code' => 'MobilePhone',
//            'Labels' => array(
//                    array(
//                        'Culture' => 'fr-CA',
//                        'Value' => 'Téléphone cellulaire',
//                        )
//                    ), '
//            DataType' => 'Text',
//            'Length' => '20',
//            'isRequired' => '0',
//            'isKey' => ''
//        ),
//        array(
//            'Code' => 'Company',
//            'Labels' => array(
//                    array(
//                        'Culture' => 'fr-CA',
//                        'Value' => 'Entreprise',
//                        )
//                    ), '
//            DataType' => 'Text',
//            'Length' => '100',
//            'isRequired' => '0',
//            'isKey' => ''
//        ),
//        array(
//            'Code' => 'Department',
//            'Labels' => array(
//                    array(
//                        'Culture' => 'fr-CA',
//                        'Value' => 'Département',
//                        )
//                    ), '
//            DataType' => 'Text',
//            'Length' => '50',
//            'isRequired' => '0',
//            'isKey' => ''
//        ),
//        array(
//            'Code' => 'WebSite',
//            'Labels' => array(
//                    array(
//                        'Culture' => 'fr-CA',
//                        'Value' => 'Site web',
//                        )
//                    ), '
//            DataType' => 'Text',
//            'Length' => '100',
//            'isRequired' => '0',
//            'isKey' => ''
//        ),
//        array(
//            'Code' => 'WorkPhone',
//            'Labels' => array(
//                    array(
//                        'Culture' => 'fr-CA',
//                        'Value' => 'Téléphone travail',
//                        )
//                    ), '
//            DataType' => 'Text',
//            'Length' => '100',
//            'isRequired' => '0',
//            'isKey' => ''
//        ),
//        array(
//            'Code' => 'WorkPhoneExtention',
//            'Labels' => array(
//                    array(
//                        'Culture' => 'fr-CA',
//                        'Value' => 'Téléphone travail extention',
//                        )
//                    ), '
//            DataType' => 'Text',
//            'Length' => '100',
//            'isRequired' => '0',
//            'isKey' => ''
//        ),
//        array(
//            'Code' => 'FacebookIdentifier',
//            'Labels' => array(
//                    array(
//                        'Culture' => 'fr-CA',
//                        'Value' => 'Identifiant Facebook',
//                        )
//                    ), '
//            DataType' => 'Text',
//            'Length' => '50',
//            'isRequired' => '0',
//            'isKey' => ''
//        ),
//        array(
//            'Code' => 'TwitterIdentifier',
//            'Labels' => array(
//                    array(
//                        'Culture' => 'fr-CA',
//                        'Value' => 'Identifiant Twitter',
//                        )
//                    ), '
//            DataType' => 'Text',
//            'Length' => '50',
//            'isRequired' => '0',
//            'isKey' => ''
//        ),
//        array(
//            'Code' => 'LinkedInIdentifier',
//            'Labels' => array(
//                    array(
//                        'Culture' => 'fr-CA',
//                        'Value' => 'Identifiant LinkedIn',
//                        )
//                    ), '
//            DataType' => 'Text',
//            'Length' => '50',
//            'isRequired' => '0',
//            'isKey' => ''
//        ),


);
    }
	/**
	 * Return an array of DialogInsight list optins which can be mapped to the Form fields/entry meta.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return array
	 */
	public function merge_vars_optin_map() {
        $optin_map ['optin_flag']  =  array(
            'name' => 'optin_flag',
            'fields' => array(
                array(
                    'name'     => 'updateContact',
                    'label'    => esc_html__( 'Update Contact', 'sometextdomain' ),
                    'type'     => 'checkbox_and_select',
                    'checkbox' => array(
                        'name'  => 'updateContactEnable',
                        'label' => esc_html__( 'Update Contact if already exists', 'sometextdomain' ),
                    ),
                    'select'   => array(
                        'name'    => 'updateContactAction',
                        'choices' => array(
                            array(
                                'label' => esc_html__( 'and replace existing data', 'sometextdomain' ),
                                'value' => 'replace'
                            ),
                            array(
                                'label' => esc_html__( 'and append new data', 'sometextdomain' ),
                                'value' => 'append'
                            )
                        )
                    )
                )
            )
        );
        error_log('OPTiN MAP FUNCTION ???? %%%');
        error_log(print_r($optin_map, true));
		return $optin_map;
	}

	/**
	 * Prevent feeds being listed or created if the API key isn't valid.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return bool
	 */
	public function can_create_feed() {

		return $this->initialize_api();

	}

	/**
	 * Configures which columns should be displayed on the feed list page.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return array
	 */
	public function feed_list_columns() {

		return array(
			'feedName' => esc_html__( 'Name', 'tmsm-gravityforms-probance-old' ),
		);

	}
//TODO see if i can simplify this method ( maybe only add the consent type field to the conditional logic)
	/**
	 * Define which field types can be used for the group conditional logic.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @uses   GFAddOn::get_current_form()
	 * @uses   GFCommon::get_label()
	 * @uses   GF_Field::get_entry_inputs()
	 * @uses   GF_Field::get_input_type()
	 * @uses   GF_Field::is_conditional_logic_supported()
	 *
	 * @return array
	 */
	public function get_conditional_logic_fields() {

		// Initialize conditional logic fields array.
		$fields = array();

		// Get the current form.
		$form = $this->get_current_form();

		// Loop through the form fields.
		foreach ( $form['fields'] as $field ) {

			// If this field does not support conditional logic, skip it.
			if ( ! $field->is_conditional_logic_supported() ) {
				continue;
			}

			// Get field inputs.
			$inputs = $field->get_entry_inputs();

			// If field has multiple inputs, add them as individual field options.
			if ( $inputs && 'checkbox' !== $field->get_input_type() ) {

				// Loop through the inputs.
				foreach ( $inputs as $input ) {

					// If this is a hidden input, skip it.
					if ( rgar( $input, 'isHidden' ) ) {
						continue;
					}

					// Add input to conditional logic fields array.
					$fields[] = array(
						'value' => $input['id'],
						'label' => GFCommon::get_label( $field, $input['id'] ),
					);

				}

			} else {

				// Add field to conditional logic fields array.
				$fields[] = array(
					'value' => $field->id,
					'label' => GFCommon::get_label( $field ),
				);

			}

		}

		return $fields;

	}

	// # FEED PROCESSING -----------------------------------------------------------------------------------------------

	/**
	 * Process the feed, subscribe the user to the list.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param array $feed  The feed object to be processed.
	 * @param array $entry The entry object currently being processed.
	 * @param array $form  The form object currently being processed.
	 *
	 * @return array
	 */
	public function process_feed( $feed, $entry, $form ) {

		// Log that we are processing feed.
		$this->log_debug( __METHOD__ . '(): Processing feed.' );

		// If unable to initialize API, log error and return.
		if ( ! $this->initialize_api() ) {
			$this->add_feed_error( esc_html__( 'Unable to process feed because API could not be initialized.', 'tmsm-gravityforms-probance-old' ),
				$feed, $entry, $form );

			$email = wp_mail(
				get_option( 'admin_email' ),
				wp_specialchars_decode( sprintf( __('TMSM Gravity Forms Dialog Insight on %s: API not initialized', 'tmsm-gravityforms-probance-old'), get_option( 'blogname' ) ) ),
				wp_specialchars_decode( sprintf( __('TMSM Gravity Forms Dialog Insight on %s: API not initialized', 'tmsm-gravityforms-probance-old'), get_option( 'blogname' ) ) )
			);

			return $entry;
		}

		// Set current merge variable name.
		$this->merge_var_name = 'email';

		// Get field map values.
		$field_map = $this->get_field_map_fields( $feed, 'mappedFields' );

		// Get optin map values.
		$optin_map = $this->get_field_map_fields( $feed, 'optin_flag' );

		// Get mapped email address.
		$email = $this->get_field_value( $form, $entry, $field_map['email'] );

		// If email address is invalid, log error and return.
		if ( GFCommon::is_invalid_or_empty_email( $email ) ) {
			$this->add_feed_error( esc_html__( 'A valid Email address must be provided.', 'tmsm-gravityforms-probance-old' ), $feed, $entry, $form );

			return $entry;
		}

		/**
		 * Prevent empty form fields erasing values already stored in the mapped Probance MERGE fields
		 * when updating an existing subscriber.
		 *
		 * @param bool  $override If the merge field should be overridden.
		 * @param array $form     The form object.
		 * @param array $entry    The entry object.
		 * @param array $feed     The feed object.
		 */
		$override_empty_fields = gf_apply_filters( 'gform_probance_override_empty_fields', array( $form['id'] ), true, $form, $entry, $feed );
		// Log that empty fields will not be overridden.
		if ( ! $override_empty_fields ) {
			$this->log_debug( __METHOD__ . '(): Empty fields will not be overridden.' );
		}

		// Initialize array to store merge vars.
		$merge_vars = array();
        $merge_vars['customer_id'] = null;


		// Loop through field map.
		foreach ( $field_map as $name => $field_id ) {

			// If no field is mapped, skip it.
			if ( rgblank( $field_id ) ) {
				continue;
			}

			// Set merge var name to current field map name.
			$this->merge_var_name = $name;

			// Get field object.
			$field = GFFormsModel::get_field( $form, $field_id );

			// Get field value.
			$field_value = $this->get_field_value( $form, $entry, $field_id );

			// If field value is empty and we are not overriding empty fields, skip it.
//			if ( empty( $field_value ) && ( ! $override_empty_fields || ( is_object( $field ) && 'address' === $field->get_input_type() ) ) ) {
//				continue;
//			}
            // If field value is null or empty not override the fields with empty values
            if(empty($field_value)) {
                continue;
            }
			$merge_vars[ $name ] = $field_value;

		}

            // Loop through optin map.
            foreach ($optin_map as $name => $field_id) {
            // If no field is mapped, skip it.
            if (rgblank($field_id)) {
                continue;
            }

            // Set merge var name to current field map name.
            $this->merge_var_name = $name;

            // Get field object.
            $field = GFFormsModel::get_field($form, $field_id);
            // Get field value.
            $field_value = $this->get_field_value($form, $entry, $field_id);

            // If field value is empty and we are not overriding empty fields, skip it.
            if (empty($field_value) && (!$override_empty_fields || (is_object($field) && 'address' === $field->get_input_type()))) {
                continue;
            }

        // Set a value to the optin flag for Probance
            if ($field_value == '0' || $field_value == '1' ) {
                $merge_vars[$name] = $field_value;
            }
//            else {
//                $merge_vars[$name] = '0' ;
//            }

        }

		// Define initial member, member found and member status variables.
		$member        = false;
		$member_found  = false;
		$member_status = null;
        // TODO see if we can make the check if email all ready exist here ??
		try {

            $member_exist_response = $this->api->get_member_if_exist($email);
            $member_response = json_decode($member_exist_response['body']);
            // if response
            if (isset($member_response->client)) {
                $member_info_into_array = json_decode(json_encode($member_response->client), true);
                //if the member has an id
                $member_found = true;
//                    error_log("MEMBER_EXIST");
//                    error_log('member' . print_r($member_info_into_array, true));
//                    error_log('customer_id' . print_r($member_info_into_array['customer_id'], true));

            }
			// Log that we are checking if user is already subscribed to list.
			$this->log_debug( __METHOD__ . "(): Checking to see if $email is already on the list (disabled)" );

		} catch ( Exception $e ) {

			// If the exception code is not 404, abort feed processing.
			if ( 404 !== $e->getCode() ) {

				// Log that we could not get the member information.
				$this->add_feed_error( sprintf( esc_html__( 'Unable to check if email address is already used by a member: %s',
					'tmsm-gravityforms-probance-old' ), $e->getMessage() ), $feed, $entry, $form );

				return $entry;

			}

			// Log member status.
			$this->log_debug( __METHOD__ . "(): $email was not found on list." );

		}

		/**
		 * Modify whether a user that currently has a status of unsubscribed on your list is resubscribed.
		 * By default, the user is resubscribed.
		 *
		 * @param bool  $allow_resubscription If the user should be resubscribed.
		 * @param array $form                 The form object.
		 * @param array $entry                The entry object.
		 * @param array $feed                 The feed object.
		 */
		$allow_resubscription = gf_apply_filters( array( 'gform_probance_allow_resubscription', $form['id'] ), true, $form, $entry, $feed );

		// If member is unsubscribed and resubscription is not allowed, exit.
		if ( 'unsubscribed' == $member_status && ! $allow_resubscription ) {
			$this->log_debug( __METHOD__ . '(): User is unsubscribed and resubscription is not allowed.' );

			return;
		}
		// If member status is not defined, set to subscribed.
		$member_status = isset( $member_status ) ? $member_status : 'subscribed';
		// Prepare transaction type for filter.
		$transaction = $member_found ? 'Update' : 'Subscribe';

		$action = $member_found ? 'update' : 'create';
        // Auto update date for Probance. // TODO see if i can change for the registered value
        $merge_vars['registration_date'] = date('Y-m-d');
		// Prepare request parameters.
		$params = array(
                        $merge_vars,
		);

		try {

			// Log the subscriber to be added or updated.
			$this->log_debug( __METHOD__ . "(): Subscriber to be {$action}: " . $email );
			// Add or update subscriber.
			$response = $this->api->update_list_member( $action, $merge_vars, );
//			$response = $this->api->update_list_member( $params );
			//$this->log_debug( __METHOD__ . "(): Params for {$action}: " . print_r( $params, true ) );
			$this->log_debug( __METHOD__ . "(): Params for {$action}: " . json_encode( $params ) );
			$this->log_debug( __METHOD__ . "(): Response for {$action}: " . print_r( $response, true ));

			// Log that the subscription was added or updated.
			$this->log_debug( __METHOD__ . "(): Subscriber successfully {$action}." );

		} catch ( Exception $e ) {

			// Log that subscription could not be added or updated.
			$this->add_feed_error( sprintf( esc_html__( 'Unable to add/update subscriber: %s', 'tmsm-gravityforms-probance-old' ),
				$e->getMessage() ), $feed, $entry, $form );

			// Log field errors.
			if ( $e->hasErrors() ) {
				$this->log_error( __METHOD__ . '(): Field errors when attempting subscription: ' . print_r( $e->getErrors(), true ) );
			}

			return;

		}

	}

	/**
	 * Returns the value of the selected field.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param array  $form     The form object currently being processed.
	 * @param array  $entry    The entry object currently being processed.
	 * @param string $field_id The ID of the field being processed.
	 *
	 * @uses   GFAddOn::get_full_name()
	 * @uses   GF_Field::get_value_export()
	 * @uses   GFFormsModel::get_field()
	 * @uses   GFFormsModel::get_input_type()
	 * @uses   GFDialogInsight::get_full_address()
	 * @uses   GFDialogInsight::maybe_override_field_value()
	 *
	 * @return array
	 */
	public function get_field_value( $form, $entry, $field_id ) {

		// Set initial field value.
		$field_value = '';

		// Set field value based on field ID.
		switch ( strtolower( $field_id ) ) {

			// Form title.
			case 'form_title':
				$field_value = rgar( $form, 'title' );
				break;

			// Entry creation date.
			case 'date_created':

				// Get entry creation date from entry.
				$date_created = rgar( $entry, strtolower( $field_id ) );

				// If date is not populated, get current date.
				$field_value = empty( $date_created ) ? gmdate( 'Y-m-d H:i:s' ) : $date_created;
				break;

			// Entry IP and source URL.
			case 'ip':
			case 'source_url':
				$field_value = rgar( $entry, strtolower( $field_id ) );
				break;

			default:

				// Get field object.
				$field = GFFormsModel::get_field( $form, $field_id );

				if ( is_object( $field ) ) {

					// Check if field ID is integer to ensure field does not have child inputs.
					$is_integer = $field_id == intval( $field_id );

					// Get field input type.
					$input_type = GFFormsModel::get_input_type( $field );

					if ( $is_integer && 'address' === $input_type ) {

						// Get full address for field value.
						$field_value = $this->get_full_address( $entry, $field_id );

					} else if ( $is_integer && 'name' === $input_type ) {

						// Get full name for field value.
						$field_value = $this->get_full_name( $entry, $field_id );

					} else if ( $is_integer && 'checkbox' === $input_type ) {

						// Initialize selected options array.
						$selected = array();

						// Loop through checkbox inputs.
						foreach ( $field->inputs as $input ) {
							$index = (string) $input['id'];
							if ( ! rgempty( $index, $entry ) ) {
								$selected[] = $this->maybe_override_field_value( rgar( $entry, $index ), $form, $entry, $index );
							}
						}

						// Convert selected options array to comma separated string.
						$field_value = implode( ', ', $selected );

					} else if ( 'phone' === $input_type && $field->phoneFormat == 'standard' ) {

						// Get field value.
						$field_value = rgar( $entry, $field_id );

						// Reformat standard format phone to match DialogInsight format.
						// Format: NPA-NXX-LINE (404-555-1212) when US/CAN.
						if ( ! empty( $field_value ) && preg_match( '/^\D?(\d{3})\D?\D?(\d{3})\D?(\d{4})$/', $field_value, $matches ) ) {
							$field_value = sprintf( '%s-%s-%s', $matches[1], $matches[2], $matches[3] );
						}

					} else {

						// Use export value if method exists for field.
						if ( is_callable( array( 'GF_Field', 'get_value_export' ) ) ) {
							$field_value = $field->get_value_export( $entry, $field_id );
						} else {
							$field_value = rgar( $entry, $field_id );
						}

					}

				} else {

					// Get field value from entry.
					$field_value = rgar( $entry, $field_id );

				}

		}

		return $this->maybe_override_field_value( $field_value, $form, $entry, $field_id );

	}

	/**
	 * Use the legacy gform_probance_field_value filter instead of the framework gform_SLUG_field_value filter.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param string $field_value The field value.
	 * @param array  $form        The form object currently being processed.
	 * @param array  $entry       The entry object currently being processed.
	 * @param string $field_id    The ID of the field being processed.
	 *
	 * @return mixed|string
	 */
	public function maybe_override_field_value( $field_value, $form, $entry, $field_id ) {

		return gf_apply_filters( 'gform_probance_field_value', array( $form['id'], $field_id ), $field_value, $form['id'], $field_id, $entry,
			$this->merge_var_name );

	}

	/**
	 * Initializes Probance API if credentials are valid.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param string $username Probance username.
	 *
	 * @uses   GFAddOn::get_plugin_setting()
	 * @uses   GFAddOn::log_debug()
	 * @uses   GFAddOn::log_error()
	 * @uses   GF_Probance_API::account_details()
	 *
	 * @return bool|null
	 */
	public function initialize_api( $username = null ) {

		$password = null;

		// If API is alredy initialized, return true.
		if ( ! is_null( $this->api ) ) {
			return true;
		}
        $username = $this->get_plugin_setting( 'username' );
		$password  = $this->get_plugin_setting( 'password' );

		$this->log_debug( __METHOD__ . '(): Username:' . $username );
		$this->log_debug( __METHOD__ . '(): Password:' . $password );

		// If the API key is blank, do not run a validation check.
		if ( rgblank( $username) || rgblank( $password ) ) {
			$this->log_debug( __METHOD__ . '(): API Key or Key ID empty.' );

			return null;
		}

		// Log validation step.
		$this->log_debug( __METHOD__ . '(): Validating API Info.' );

		// Setup a new Probance object with the API credentials.
		$probance = new GF_Probance_API( $username, $password );
        $this->log_debug(__METHOD__ . '(): Return object'. var_export($probance,true));
		try {

			// Retrieve account information.
			$probance->account_details();
			// Assign API library to class.
			$this->api = $probance;

			// Log that authentication test passed.
			$this->log_debug( __METHOD__ . '(): Probance successfully authenticated.' );
			return true;

		} catch ( Exception $e ) {

			// Log that authentication test failed.
			$this->log_error( __METHOD__ . '(): Unable to authenticate with Probance; ' . $e->getMessage() );;

			return false;

		}

	}
}
