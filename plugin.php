<?php
/*
Plugin Name: Domain Limiter YOURLS Plugin
Plugin URI: https://github.com/beanworks/yourls-domainlimit-plugin
Description: Only allow URLs from admin-specified domains, with an admin panel. Based on the plugin by nicwaller.
Version: 1.0
Author: Beanworks
Author URI: http://github.com/beanworks
*/

// No direct call
if( !defined( 'YOURLS_ABSPATH' ) ) die();

yourls_add_filter( 'pre_add_new_link', 'domainlimit_link_filter' );

function domainlimit_link_filter( $original_return, $url, $keyword = '', $title = '' ) {
	if ( domainlimit_environment_check() != true ) {
		$err = array();
		$err['status'] = 'fail';
		$err['code'] = 'error:configuration';
		$err['message'] = 'Problem with domain limit configuration. Check PHP error log.';
		$err['errorCode'] = '500';
		return $err;
	}

	global $domainlimit_list;
	$domain_whitelist = $domainlimit_list;
	$requested_domain = yourls_get_domain( $url );
	$allowed = false;
	foreach ( $domain_whitelist as $domain_permitted ) {
		if ( domainlimit_is_subdomain( $requested_domain, $domain_permitted ) ) {
			$allowed = true;
			break;
		}
	}

	if ( $allowed == true ) {
		return null;
	}

	$return = array();
	$return['status'] = 'fail';
	$return['code'] = 'error:disallowedhost';
	$return['message'] = 'URL must be in ' . implode(', ', $domain_whitelist);
	$return['errorCode'] = '400';
	return $return;
}

/*
 * Determine whether test_domain is controlled by $parent_domain
 */
function domainlimit_is_subdomain( $test_domain, $parent_domain ) {
	if ( $test_domain == $parent_domain ) {
		return true;
	}

	// Note that "notunbc.ca" is NOT a subdomain of "unbc.ca"
	// We CANNOT just compare the rightmost characters
	// unless we add a period in there first
	if ( substr( $parent_domain, 1, 1) != '.' ) {
		$parent_domain = '.' . $parent_domain;
	}

	$chklen = strlen($parent_domain);
	return ( $parent_domain == substr( $test_domain, 0-$chklen ) );
}

/*
 * Returns true if everything is configured correctly
 */
function domainlimit_environment_check() {
	// Domain limit exempt users
	global $domainlimit_exempt_users;
	if ( isset( $domainlimit_exempt_users ) && !is_array( $domainlimit_exempt_users ) ) {
		$domain = $domainlimit_exempt_users;
		$domainlimit_exempt_users = array( $domain );
	}

	// Domain limit list
	global $domainlimit_list;
	if ( !isset( $domainlimit_list ) ) {
		error_log('Missing definition of $domainlimit_list in user/config.php');
		return false;
	} else if ( isset( $domainlimit_list ) && !is_array( $domainlimit_list ) ) {
		$domain = $domainlimit_list;
		$domainlimit_list = array( $domain );
	}
	return true;
}

/*
 * Register the plugin admin page
 */
yourls_add_action( 'plugins_loaded', 'domainlimit_init' );
function domainlimit_init() {
    yourls_register_plugin_page( 'domainlimit', 'Domain Limiter Settings', 'domainlimit_display_page' );
}

/*
 * Draw the plugin admin page
 */
function domainlimit_display_page() {
	global $domainlimit_list;
	global $domainlimit_exempt_users;

	?>
	<h3><?php yourls_e( 'Domain Limiter Settings' ); ?></h3>
	<?php if( domainlimit_environment_check() != true ) { ?>
		<p><?php yourls_e( "Error in domain limit configuration"); ?></p>
	<?php } else { ?>
		<p><?php echo $domainlimit_exempt_users; ?></p>
		<p><?php yourls_se( "Domains allowed to be shortened: %s", implode(", ", $domainlimit_list) ); ?></p>
		<?php if( !is_null($domainlimit_exempt_users) ) { ?>
			<p><?php yourls_se( "Users exempt from domain limit: %s", implode(", ", $domainlimit_exempt_users) ); ?></p>
			<p><?php yourls_se( "Current user (%s) %s exempt from domain limit", YOURLS_USER, in_array( YOURLS_USER, $domainlimit_exempt_users ) ? "" : "not" ); ?></p>
		<?php } ?>
	<?php } ?>
	<?php
}