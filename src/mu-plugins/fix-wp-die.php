<?php

use Tgc\WordPressPsr\PrematureExitException;
use Tgc\WordPressPsr\Headers;

add_action(
	'wp_exit',
	function( $message ) {
		echo $message;
		throw new PrematureExitException( 'wp_exit' );
	},
	100
);

add_action(
	'wp_header',
	function( $header, $replace = true, $response_code = null ) {
		Headers::add_header( $header, $replace, $response_code );
	},
	100,
	3
);

add_action(
	'wp_header_remove',
	function( $header ) {
		Headers::remove_header( $header );
	}
);

add_action(
	'wp_set_cookie',
	function( $name, $value = '', $expires_or_options = 0, $path = '', $domain = '', $secure = false, $httponly = false ) {
		Headers::set_cookie( $name, $value, $expires_or_options, $path, $domain, $secure, $httponly );
	},
	100,
	7
);

//function swoole_nocache_headers() {
//
//	$headers = wp_get_nocache_headers();
//
//	unset( $headers['Last-Modified'] );
//
//	Headers::remove_header( 'Last-Modified' );
//
//	foreach ( $headers as $name => $field_value ) {
//		Headers::add_header( $name, $field_value );
//	}
//}

//add_filter( 'wp_die_handler', function ( $default_handler ) {
//	return function ($message, $title = '', $args = array() ) use ( $default_handler ) {
//		$exit = $args['exit'] ?? true;
//		$args['exit'] = false;
//		$default_handler($message, $title, $args);
//		if ( $exit ) {
//			throw new EarlyReturnException( 'safe_wp_die' );
//		}
//	};
//}, 9999999 );



//add_filter( 'wp_die_ajax_handler', function ( $default_handler ) {
//	return function ($message, $title = '', $args = array() ) use ( $default_handler ) {
//		// Set default 'response' to 200 for Ajax requests.
//		$args = wp_parse_args(
//			$args,
//			array( 'response' => 200 )
//		);
//
//		list( $message, $title, $parsed_args ) = _wp_die_process_input( $message, $title, $args );
//
//		// This is intentional. For backward-compatibility, support passing null here.
//		if ( null !== $args['response'] ) {
//			Headers::set_status_code( $parsed_args['response'] );
//		}
//		swoole_nocache_headers();
//
//		if ( is_scalar( $message ) ) {
//			$message = (string) $message;
//		} else {
//			$message = '0';
//		}
//
//		if ( $parsed_args['exit'] ) {
//			throw new EarlyReturnException( 'safe_wp_die' );
//		}
//
//		echo $message;
//	};
//}, 9999999 );
//
//add_filter( 'wp_die_json_handler', function ( $default_handler ) {
//	return function ( $message, $title = '', $args = array() ) {
//		list( $message, $title, $parsed_args ) = _wp_die_process_input( $message, $title, $args );
//
//		$data = array(
//			'code'              => $parsed_args['code'],
//			'message'           => $message,
//			'data'              => array(
//				'status' => $parsed_args['response'],
//			),
//			'additional_errors' => $parsed_args['additional_errors'],
//		);
//
//		Headers::add_header( 'Content-Type',"application/json; charset={$parsed_args['charset']}" );
//		if ( null !== $parsed_args['response'] ) {
//			Headers::set_status_code( $parsed_args['response'] );
//		}
//		swoole_nocache_headers();
//
//		echo wp_json_encode( $data );
//		if ( $parsed_args['exit'] ) {
//			throw new EarlyReturnException( 'safe_wp_die' );
//		}
//	};
//} );
//
//
//add_filter( 'wp_die_jsonp_handler', function ( $default_handler ) {
//	return function ( $message, $title = '', $args = array() ) {
//		list( $message, $title, $parsed_args ) = _wp_die_process_input( $message, $title, $args );
//
//		$data = array(
//			'code'              => $parsed_args['code'],
//			'message'           => $message,
//			'data'              => array(
//				'status' => $parsed_args['response'],
//			),
//			'additional_errors' => $parsed_args['additional_errors'],
//		);
//
//		Headers::add_header( 'Content-Type', "application/javascript; charset={$parsed_args['charset']}" );
//		Headers::add_header( 'X-Content-Type-Options', 'nosniff' );
//		Headers::add_header( 'X-Robots-Tag', 'noindex' );
//		if ( null !== $parsed_args['response'] ) {
//			Headers::set_status_code( $parsed_args['response'] );
//		}
//		swoole_nocache_headers();
//
//		$result         = wp_json_encode( $data );
//		$jsonp_callback = $_GET['_jsonp'];
//		echo '/**/' . $jsonp_callback . '(' . $result . ')';
//		if ( $parsed_args['exit'] ) {
//			throw new EarlyReturnException( 'safe_wp_die' );
//		}
//	};
//} );
//
//
//add_filter( 'wp_die_xmlrpc_handler', function ( $default_handler ) {
//	return function ( $message, $title = '', $args = array() ) {
//		global $wp_xmlrpc_server;
//
//		list( $message, $title, $parsed_args ) = _wp_die_process_input( $message, $title, $args );
//
//		if ( ! headers_sent() ) {
//			nocache_headers();
//		}
//
//		if ( $wp_xmlrpc_server ) {
//			$error = new IXR_Error( $parsed_args['response'], $message );
//			$wp_xmlrpc_server->output( $error->getXml() );
//		}
//		if ( $parsed_args['exit'] ) {
//			throw new EarlyReturnException( 'safe_wp_die' );
//		}
//	};
//});
//
//add_filter( 'wp_die_xml_handler', function ( $default_handler ) {
//	return function ( $message, $title = '', $args = array() ) {
//		list( $message, $title, $parsed_args ) = _wp_die_process_input( $message, $title, $args );
//
//		$message = htmlspecialchars( $message );
//		$title   = htmlspecialchars( $title );
//
//		$xml = <<<EOD
//<error>
//    <code>{$parsed_args['code']}</code>
//    <title><![CDATA[{$title}]]></title>
//    <message><![CDATA[{$message}]]></message>
//    <data>
//        <status>{$parsed_args['response']}</status>
//    </data>
//</error>
//
//EOD;
//
//		Headers::add_header( 'Content-Type', "text/xml; charset={$parsed_args['charset']}" );
//		if ( null !== $parsed_args['response'] ) {
//			Headers::set_status_code( $parsed_args['response'] );
//		}
//		swoole_nocache_headers();
//
//		echo $xml;
//		if ( $parsed_args['exit'] ) {
//			throw new EarlyReturnException( 'safe_wp_die' );
//		}
//	};
//});


//add_filter( 'exit_on_http_head', function ( $exit_on_http_head ) {
//	return false;
//} );



//remove_action( 'login_init', 'send_frame_options_header');
//remove_action( 'admin_init', 'send_frame_options_header');
//$send_frame_options_header = function () {
//	Headers::add_header( 'X-Frame-Options', 'SAMEORIGIN' );
//};
//add_action( 'login_init', $send_frame_options_header, 10, 0 );
//add_action( 'admin_init', $send_frame_options_header, 10, 0 );


/**
 * Send a referrer policy header so referrers are not sent externally from administration screens.
 *
 * @since 4.9.0
 */
//function swoole_wp_admin_headers() {
//	$policy = 'strict-origin-when-cross-origin';
//
//	/**
//	 * Filters the admin referrer policy header value.
//	 *
//	 * @since 4.9.0
//	 * @since 4.9.5 The default value was changed to 'strict-origin-when-cross-origin'.
//	 *
//	 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Referrer-Policy
//	 *
//	 * @param string $policy The admin referrer policy header value. Default 'strict-origin-when-cross-origin'.
//	 */
//	$policy = apply_filters( 'admin_referrer_policy', $policy );
//
//	Headers::add_header( 'Referrer-Policy', $policy );
//}
//
//add_action( 'admin_init', function () {
//	remove_action( 'admin_init', 'wp_admin_headers' );
//	remove_action( 'login_init', 'wp_admin_headers' );
//
//	add_action( 'admin_init', 'swoole_wp_admin_headers' );
//	add_action( 'login_init', 'swoole_wp_admin_headers' );
//}, 0);

//add_action( 'admin_init', '_wp_customize_include' );