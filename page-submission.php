<?php
/**
 * The plugin bootstrap file
 *
 * @link              https://profiles.wordpress.org/anand000/
 * @since             1.0.0
 * @package           Page_Submission
 *
 * @wordpress-plugin
 * Plugin Name:       Page Submission
 * Plugin URI:        https://wordpress.org/plugins/page-submission
 * Description:       The guest author should be able to create a post from front end.
 * Version:           1.0.0
 * Author:            Anandaraj Balu
 * Author URI:        https://profiles.wordpress.org/anand000/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       page-submission
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PAGE_SUBMISSION_VERSION', '1.0.0' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-page-submission.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_page_submission() {

	$plugin = new Page_Submission();
	$plugin->run();

}
run_page_submission();
