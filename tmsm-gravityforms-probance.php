<?php

/**
 * @link              https://github.com/aflamentTM
 * @since             1.0.0
 *
 * @wordpress-plugin
 * Plugin Name:       TMSM Gravity Forms Probance Add-On
 * Plugin URI:        https://github.com/aflamentTM/tmsm-gravityforms-probance
 * Description:       Integrates Gravity Forms with Probance, allowing form submissions to be automatically sent to your Probance account
 * Version:           1.0.0
 * Author:            Arnaud Flament
 * Author URI:        https://github.com/aflamentTM
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       tmsm-gravityforms-probance
 * Domain Path:       /languages
 * Github Plugin URI: https://github.com/aflamentTM/tmsm-gravityforms-probance
 * Github Branch:     master
 * Requires PHP:      5.6
 */

define( 'GF_PROBANCE_VERSION', '1.0.0' );

// If Gravity Forms is loaded, bootstrap the Probance Add-On.
add_action( 'gform_loaded', array( 'GF_Probance_Bootstrap', 'load' ), 5 );

/**
 * Class GF_Probance_Bootstrap
 *
 * Handles the loading of the Probance Add-On and registers with the Add-On Framework.
 */
class GF_Probance_Bootstrap {

	/**
	 * If the Feed Add-On Framework exists, Probance Add-On is loaded.
	 *
	 * @access public
	 * @static
	 */
	public static function load()
    {

		if ( ! method_exists( 'GFForms', 'include_feed_addon_framework' ) ) {
			return;
		}

		require_once('class-gf-probance.php');

		GFAddOn::register( 'GFProbance' );
	}
}

/**
 * Returns an instance of the GFProbance class
 *
 * @see    GFProbance::get_instance()
 *
 * @return object GFProbance
 */
function gf_probance()
{
    return GFProbance::get_instance();
}
