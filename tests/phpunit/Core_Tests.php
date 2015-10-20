<?php
namespace TenUp\Dovedi\Core;

/**
 * This is a very basic test case to get things started. You should probably rename this and make
 * it work for your project. You can use all the tools provided by WP Mock and Mockery to create
 * your tests. Coverage is calculated against your includes/ folder, so try to keep all of your
 * functional code self contained in there.
 *
 * References:
 *   - http://phpunit.de/manual/current/en/index.html
 *   - https://github.com/padraic/mockery
 *   - https://github.com/10up/wp_mock
 */

use Mockery\Mock;
use TenUp\Dovedi as Base;
use WP_Mock as M;

class Core_Tests extends Base\TestCase {

	protected $testFiles = [
		'functions/core.php'
	];

	public function setUp() {
		if ( ! defined( 'HOUR_IN_SECONDS' ) ) {
			define( 'HOUR_IN_SECONDS', 60 * 60 );
		}

		parent::setUp();
	}

	/** 
	 * Test load method.
	 */
	public function test_setup() {
		// Setup
		\WP_Mock::expectActionAdded( 'init',                     'TenUp\Dovedi\Core\i18n' );
		\WP_Mock::expectActionAdded( 'init',                     'TenUp\Dovedi\Core\init' );
		\WP_Mock::expectActionAdded( 'wp_login',                 'TenUp\Dovedi\Core\wp_login', 10, 2 );
		\WP_Mock::expectActionAdded( 'login_form_validate_totp', 'TenUp\Dovedi\Core\validate_totp' );
		\WP_Mock::expectActionAdded( 'show_user_profile',        'TenUp\Dovedi\Core\user_options' );
		\WP_Mock::expectActionAdded( 'edit_user_profile',        'TenUp\Dovedi\Core\user_options' );
		\WP_Mock::expectActionAdded( 'personal_options_update',  'TenUp\Dovedi\Core\user_update' );
		\WP_Mock::expectActionAdded( 'edit_user_profile_update', 'TenUp\Dovedi\Core\user_update' );

		\WP_Mock::expectAction( 'dovedi_loaded' );

		// Act
		setup();

		// Verify
		$this->assertConditionsMet();
	}

	/**
	 * Test internationalization integration.
	 */
	public function test_i18n() {
		// Setup
		\WP_Mock::wpFunction( 'get_locale', array(
			'times' => 1,
			'args' => array(),
			'return' => 'en_US',
		) );
		\WP_Mock::onFilter( 'plugin_locale' )->with( 'en_US', 'dovedi' )->reply( 'en_US' );
		\WP_Mock::wpFunction( 'load_textdomain', array(
			'times' => 1,
			'args' => array( 'dovedi', 'lang_dir/dovedi/dovedi-en_US.mo' ),
		) );
		\WP_Mock::wpFunction( 'plugin_basename', array(
			'times' => 1,
			'args' => array( 'path' ),
			'return' => 'path',
		) );
		\WP_Mock::wpFunction( 'load_plugin_textdomain', array(
			'times' => 1,
			'args' => array( 'dovedi', false, 'path/languages/' ),
		) );

		// Act
		i18n();

		// Verify
		$this->assertConditionsMet();
	}

	/** 
	 * Test initialization method.
	 */
	public function test_init() {
		// Setup
		\WP_Mock::expectAction( 'dovedi_init' );

		// Act
		init();

		// Verify
		$this->assertConditionsMet();
	}

	/** 
	 * Test activation routine.
	 */
	public function test_activate() {
		// Setup
		\WP_Mock::wpFunction( 'flush_rewrite_rules', array(
			'times' => 1
		) );

		// Act
		activate();

		// Verify
		$this->assertConditionsMet();
	}

	/** 
	 * Test deactivation routine.
	 */
	public function test_deactivate() {
		// Setup

		// Act
		deactivate();

		// Verify
	}

	public function test_user_options() {
		$this->markTestIncomplete();
	}

	/**
	 * If the user submission is blank, delete their stored key
	 */
	public function test_user_update_deletes_key() {
		$_POST['_nonce_totp_options'] = 'totp';
		$_POST['totp-key'] = '';
		M::wpFunction( 'check_admin_referer', [
			'args'  => [ 'totp_options', '_nonce_totp_options' ],
			'times' => 1,
		] );

		M::wpFunction( 'get_user_meta', [
			'args'   => [ 1, '_totp_key', true ],
			'times'  => 1,
			'return' => 'password',
		] );

		M::wpFunction( 'delete_user_meta', [
			'args'  => [ 1, '_totp_key', 'password' ],
			'times' => 1,
		] );

		// Act
		user_update( 1 );

		// Verify
		$this->assertConditionsMet();
	}

	/**
	 * If the user submission matches their previous key (or is invalid), don't do anything
	 */
	public function test_user_update_ignores_key() {
		$_POST['_nonce_totp_options'] = 'totp';
		$_POST['totp-key'] = '';
		M::wpFunction( 'check_admin_referer', [
			'args'  => [ 'totp_options', '_nonce_totp_options' ],
			'times' => 3,
		] );

		M::wpFunction( 'get_user_meta', [
			'args'            => [ 1, '_totp_key', true ],
			'times'           => 3,
			'return_in_order' => [ false, 'password', 'password' ],
		] );

		// These should not be called in this case. Ever
		M::wpFunction( 'delete_user_meta', [ 'times' => 0 ] );
		M::wpFunction( 'update_user_meta', [ 'times' => 0 ] );

		// No existing key, no key POSTed
		user_update( 1 );

		// POSTed key same as existing key
		$_POST['totp-key'] = 'password';
		user_update( 1 );

		// POSTed key is invalid
		$_POST['totp-key'] = '0000';
		user_update( 1 );

		// Verify
		$this->assertConditionsMet();
	}

	/**
	 * If the user submits a new key, update the database
	 */
	public function test_user_update_updates_key() {
		$_POST['_nonce_totp_options'] = 'totp';
		$_POST['totp-key'] = 'NEW';
		$_POST['totp-authcode'] = 'newpassword';
		M::wpFunction( 'check_admin_referer', [
			'args'  => [ 'totp_options', '_nonce_totp_options' ],
			'times' => 1,
		] );

		M::wpFunction( 'get_user_meta', [
			'args'   => [ 1, '_totp_key', true ],
			'times'  => 1,
			'return' => 'password',
		] );

		M::wpFunction( __NAMESPACE__ . '\is_valid_authcode', [
			'args'   => [ 'NEW', 'newpassword' ],
			'times'  => 1,
			'return' => true,
		] );

		M::wpFunction( 'update_user_meta', [
			'args'  => [ 1, '_totp_key', 'NEW' ],
			'times' => 1,
		] );

		// Should never be called
		M::wpFunction( 'delete_user_meta', [ 'times' => 0 ] );

		// Act
		user_update( 1 );

		// Verify
		$this->assertConditionsMet();
	}

	public function test_wp_login() {
		$this->markTestIncomplete();
	}

	public function test_show_two_factor_login() {
		$this->markTestIncomplete();
	}

	public function test_login_html() {
		$this->markTestIncomplete();
	}

	/**
	 * If no auth is set, the function should return before it gets user info.
	 */
	public function test_validate_totp_no_auth() {
		// Logic should return before it gets there
		M::wpFunction( 'get_userdata', [ 'times' => 0 ] );

		validate_totp();

		$this->assertConditionsMet();
	}

	/**
	 * If no user exists, the function should return before verifying the nonce.
	 */
	public function test_validate_totp_no_user() {
		$_POST['wp-auth-id'] = 5;
		$_POST['wp-auth-nonce'] = 'nonce';

		// Logic should return before it gets there
		M::wpFunction( __NAMESPACE__ . '\verify_login_nonce', [ 'times' => 0 ] );

		M::wpFunction( 'get_userdata', [
			'args'   => [ 5 ],
			'times'  => 1,
			'return' => null,
		] );

		validate_totp();

		$this->assertConditionsMet();
	}

	/**
	 * If the nonce is bad, redirect to home
	 */
	public function test_validate_totp_bad_nonce() {
		$_POST['wp-auth-id'] = 5;
		$_POST['wp-auth-nonce'] = 'nonce';

		$user = new \stdClass;
		$user->ID = 5;

		M::wpFunction( 'get_userdata', [
			'args'   => [ 5 ],
			'times'  => 1,
			'return' => $user,
		] );

		M::wpFunction( __NAMESPACE__ . '\verify_login_nonce', [
			'args'   => [ 5, 'nonce'],
			'times'  => 1,
			'return' => false,
		] );

		M::wpFunction( 'get_bloginfo', [
			'args'   => [ 'url' ],
			'times'  => 1,
			'return' => 'info',
		] );
		M::wpFunction( 'wp_safe_redirect', [
			'args'   => [ 'info' ],
			'times'  => 1,
		] );

		M::wpFunction( __NAMESPACE__ . '\safe_exit', [ 'times' => 1 ] );


		validate_totp();

		$this->assertConditionsMet();
	}

	/**
	 * If the authentication is bad, redirect to home
	 */
	public function test_validate_totp_bad_auth() {
		$_POST['wp-auth-id'] = 5;
		$_POST['wp-auth-nonce'] = 'nonce';
		$_REQUEST['redirect_to'] = 'redirect';

		$user = new \stdClass;
		$user->ID = 5;
		$user->user_login = 'login';

		M::wpFunction( 'get_userdata', [
			'args'   => [ 5 ],
			'times'  => 1,
			'return' => $user,
		] );

		M::wpFunction( __NAMESPACE__ . '\verify_login_nonce', [
			'args'   => [ 5, 'nonce' ],
			'times'  => 1,
			'return' => true,
		] );

		M::wpFunction( __NAMESPACE__ . '\validate_authentication', [
			'args'   => [ $user ],
			'times'  => 1,
			'return' => false,
		] );

		M::wpFunction( __NAMESPACE__ . '\create_login_nonce', [
			'args'   => [ 5 ],
			'times'  => 1,
			'return' => [ 'key' => 'key', 'nonce' => 'nonce' ],
		] );

		M::wpPassthruFunction( 'esc_html__' );

		M::wpFunction( __NAMESPACE__ . '\login_html', [
			'args'   => [ $user, 'key', 'redirect', '*' ],
			'times'  => 1,
		] );

		M::wpFunction( __NAMESPACE__ . '\safe_exit', [ 'times' => 1 ] );


		validate_totp();

		$this->assertConditionsMet();
	}

	/**
	 * If the authentication is bad, and the nonce fails, die
	 */
	public function test_validate_totp_bad_auth_and_nonce() {
		$_POST['wp-auth-id'] = 5;
		$_POST['wp-auth-nonce'] = 'nonce';
		$_REQUEST['redirect_to'] = 'redirect';

		$user = new \stdClass;
		$user->ID = 5;
		$user->user_login = 'login';

		M::wpFunction( 'get_userdata', [
			'args'   => [ 5 ],
			'times'  => 1,
			'return' => $user,
		] );

		M::wpFunction( __NAMESPACE__ . '\verify_login_nonce', [
			'args'   => [ 5, 'nonce' ],
			'times'  => 1,
			'return' => true,
		] );

		M::wpFunction( __NAMESPACE__ . '\validate_authentication', [
			'args'   => [ $user ],
			'times'  => 1,
			'return' => false,
		] );

		M::wpFunction( __NAMESPACE__ . '\create_login_nonce', [
			'args'   => [ 5 ],
			'times'  => 1,
			'return' => false,
		] );

		M::wpFunction( __NAMESPACE__ . '\login_html', [ 'times'  => 0 ] );

		M::wpFunction( __NAMESPACE__ . '\safe_exit', [ 'times' => 0 ] );


		validate_totp();

		$this->assertConditionsMet();
	}

	/**
	 * If everything is good, proceed!
	 */
	public function test_validate_totp_success() {
		$_POST['wp-auth-id'] = 5;
		$_POST['wp-auth-nonce'] = 'nonce';
		$_REQUEST['redirect_to'] = 'redirect';

		$user = new \stdClass;
		$user->ID = 5;
		$user->user_login = 'login';

		M::wpFunction( 'get_userdata', [
			'args'   => [ 5 ],
			'times'  => 1,
			'return' => $user,
		] );

		M::wpFunction( __NAMESPACE__ . '\verify_login_nonce', [
			'args'   => [ 5, 'nonce' ],
			'times'  => 1,
			'return' => true,
		] );

		M::wpFunction( __NAMESPACE__ . '\validate_authentication', [
			'args'   => [ $user ],
			'times'  => 1,
			'return' => true,
		] );

		M::wpFunction( __NAMESPACE__ . '\delete_login_nonce', [
			'args'   => [ 5 ],
			'times'  => 1,
		] );

		M::wpFunction( 'wp_set_auth_cookie', [
			'args'  => [ 5, false ],
			'times' => 1,
		] );

		M::wpFunction( 'wp_safe_redirect', [
			'args'  => [ 'redirect' ],
			'times' => 1,
		] );

		M::wpFunction( __NAMESPACE__ . '\login_html', [ 'times'  => 0 ] );

		M::wpFunction( __NAMESPACE__ . '\safe_exit', [ 'times' => 1 ] );


		validate_totp();

		$this->assertConditionsMet();
	}

	/**
	 * Make sure a random nonce is created for the user
	 */
	public function test_create_login_nonce() {
		M::wpFunction( 'update_user_meta', [
			'args'   => [ 1, '_totp_nonce', '*' ],
			'times'  => 1,
			'return' => true,
		] );
		M::wpFunction( 'wp_hash', [
			'args'   => [ '*', 'nonce' ],
			'times'  => 1,
			'return' => 'hash'
		] );

		$nonce = create_login_nonce( 1 );

		$this->assertArrayHasKey( 'key', $nonce );
		$this->assertArrayHasKey( 'expiration', $nonce );
		$this->assertConditionsMet();
	}

	/**
	 * Ensure false is returned should user meta fail to save
	 */
	public function test_create_login_nonce_fail() {
		M::wpFunction( 'update_user_meta', [
			'args'   => [ 1, '_totp_nonce', '*' ],
			'times'  => 1,
			'return' => false,
		] );
		M::wpFunction( 'wp_hash', [
			'args'   => [ '*', 'nonce' ],
			'times'  => 1,
			'return' => 'hash'
		] );

		$nonce = create_login_nonce( 1 );

		$this->assertFalse( $nonce );
		$this->assertConditionsMet();
	}

	/**
	 * Ensure the login nonce is deleted.
	 */
	public function test_delete_login_nonce() {
		M::wpFunction( 'delete_user_meta', [
			'args'  => [ 22, '_totp_nonce' ],
			'times' => 1,
		] );

		delete_login_nonce( 22 );

		$this->assertConditionsMet();
	}

	public function test_authentication_page() {
		$this->markTestIncomplete();
	}

	/**
	 * A valid authcode should be true
	 */
	public function test_validate_authentication() {
		M::wpFunction( 'get_user_meta', [
			'args'   => [ 1, '_totp_key', true ],
			'return' => '1234'
		] );
		M::wpFunction( __NAMESPACE__ . '\is_valid_authcode', [
			'args'   => [ '1234', '5678' ],
			'return' => true,
		] );
		$_REQUEST['authcode'] = '5678';

		$user = new \stdClass;
		$user->ID = 1;

		$valid = validate_authentication( $user );

		$this->assertTrue( $valid );
		$this->assertConditionsMet();
	}

	/**
	 * An invalid authcode should be false
	 */
	public function test_validate_authentication_invalid() {
		M::wpFunction( 'get_user_meta', [
			'args'   => [ 1, '_totp_key', true ],
			'return' => '1234'
		] );
		M::wpFunction( __NAMESPACE__ . '\is_valid_authcode', [
			'args'   => [ '1234', '5678' ],
			'return' => false,
		] );
		$_REQUEST['authcode'] = '5678';

		$user = new \stdClass;
		$user->ID = 1;

		$valid = validate_authentication( $user );

		$this->assertFalse( $valid );
		$this->assertConditionsMet();
	}

	/**
	 * Make sure `wp_die()` is invoked if the key generation parameters are invalid.
	 */
	public function test_generate_key_dies_with_invalid() {
		M::wpFunction( __NAMESPACE__ . '\safe_exit', array(
			'times'  => 2,
		) );

		generate_key( 7 );  // Too small!
		generate_key( 10 ); // Not a multiple of 8!

		$this->assertConditionsMet();
	}

	/**
	 * Make sure the key is generated at the right length and with the right characters.
	 */
	public function test_generate_key_generates_valid_key() {
		$base_32_chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

		// First test case
		$str = generate_key( 8 );
		$this->assertEquals( 1, strlen( $str ) );
		foreach( str_split( $str ) as $char ) {
			$this->assertContains( $char, $base_32_chars );
		}

		// Larger test case
		$str = generate_key( 128 );
		$this->assertEquals( 16, strlen( $str ) );
		foreach( str_split( $str ) as $char ) {
			$this->assertContains( $char, $base_32_chars );
		}
	}

	/**
	 * Test QR code URL generation
	 */
	public function test_get_qr_code() {
		$site = 'Test Site';
		$user = 'jeremiahjohnson';
		$key = '123456789abcdef';

		M::wpFunction( 'sanitize_title', [
			'args'    => [ 'Test Site' ],
			'return' => 'test-site',
			'times'   => 1,
		] );

		$url = get_qr_code( $site, $user, $key );

		$this->assertEquals( 'https://chart.googleapis.com/chart?chs=200x200&chld=M|0&cht=qr&chl=otpauth%3A%2F%2Ftotp%2Ftest-site%3Ajeremiahjohnson%3Fsecret%3D123456789abcdef%26issuer%3DTest%2BSite', $url );

		$this->assertConditionsMet();
	}

	/**
	 * A valid authcode should return true and return in < 9 ticks
	 */
	public function test_is_valid_authcode_if_valid() {
		M::wpFunction( __NAMESPACE__ . '\calc_totp', [
			'args'            => [ 'apple', '*' ],
			'times'           => 4,
			'return_in_order' => [ '1234', '1234', '1234', '2345' ],
		] );

		$valid = is_valid_authcode( 'apple', '2345' );

		$this->assertTrue( $valid );
		$this->assertConditionsMet();
	}

	/**
	 * An invalid authcode will return false and use all 9 ticks
	 */
	public function test_is_valid_authcode_if_invalid() {
		M::wpFunction( __NAMESPACE__ . '\calc_totp', [
			'args'            => [ 'apple', '*' ],
			'times'           => 9,
			'return_in_order' => [ '1234', '1234', '1234', '1234', '1234', '1234', '1234', '1234', '1234' ],
		] );

		$valid = is_valid_authcode( 'apple', '2345' );

		$this->assertFalse( $valid );
		$this->assertConditionsMet();
	}

	/**
	 * Make sure the TOTP code is calculated for some specific keys the same way each time
	 */
	public function test_calc_totp() {
		// Overload `time()` with a namespaced version so we can avoid randomness.
		M::wpFunction( __NAMESPACE__ . '\time', [ 'return' => 1445302841 ] );

		$tests = [
			'first'  => '383468',
			'223ABc' => '544401',
		];

		foreach( $tests as $key => $totp ) {
			$this->assertEquals( $totp, calc_totp( $key ) );
		}

		$this->assertConditionsMet();
	}

	/**
	 * Ensure strings are packed into 64 bits as expected
	 */
	public function test_pack64() {
		$this->assertEquals( "\000\000\000\000\000\000\000\001", pack64( 1 ) );
		$this->assertEquals( "\000\000\000\000\000\000\000\002", pack64( 2 ) );
		$this->assertEquals( "\000\000\000\000\000\000\000\003", pack64( 3 ) );
		$this->assertEquals( "\000\000\000\000\000\000\000\004", pack64( 4 ) );
		$this->assertEquals( "\000\000\000\000\000\000\000\005", pack64( 5 ) );
		$this->assertEquals( "\000\000\000\000\000\000\000\006", pack64( 6 ) );
		$this->assertEquals( "\000\000\000\000\000\000\000\007", pack64( 7 ) );
	}

	/**
	 * Make sure characters outside the charset are rejected
	 */
	public function test_base32_decode_rejects_invalid() {
		$invalid = ['ABC0', 'ABC1', 'ABC8'];

		foreach( $invalid as $test ) {
			$thrown = false;
			try {
				$decoded = base32_decode( $test );
			} catch ( \Exception $e ) {
				$thrown = true;

				$this->assertEquals( 'Invalid characters in the base32 string.', $e->getMessage() );
			}

			$this->assertTrue( $thrown );
		}
	}

	/**
	 * Make sure each valid character is converted to the correct binary equivalent
	 */
	public function test_base32_decode_returns_correct_data() {
		// these strings are taken from the RFC
		$this->assertEquals( 'f',      base32_decode( 'MY' ) );
		$this->assertEquals( 'fo',     base32_decode( 'MZXQ' ) );
		$this->assertEquals( 'foo',    base32_decode( 'MZXW6' ) );
		$this->assertEquals( 'foob',   base32_decode( 'MZXW6YQ' ) );
		$this->assertEquals( 'fooba',  base32_decode( 'MZXW6YTB' ) );
		$this->assertEquals( 'foobar', base32_decode( 'MZXW6YTBOI' ) );
	}

	/**
	 * Test absolute value sorting algorithm
	 */
	public function test_absort() {
		// Verify
		$this->assertEquals( -1, abssort( 1, 10 ) );
		$this->assertEquals( -1, abssort( -5, 10 ) );
		$this->assertEquals( 1, abssort( 5, 2 ) );
		$this->assertEquals( 1, abssort( -10, 5 ) );
		$this->assertEquals( 0, abssort( 3, 3 ) );
		$this->assertEquals( 0, abssort( -6, 6 ) );

	}

	/**
	 * Ensure the safe exit function adds the appropriate filters and
	 * invokes `wp_die()` in the end.
	 */
	public function test_safe_exit() {
		M::wpFunction( 'wp_die', [ 'times' => 1 ] );

		$handler = function() { return function() { die; }; };

		M::expectFilterAdded( 'wp_die_ajax_handler', $handler );
		M::expectFilterAdded( 'wp_die_xmlrpc_handler', $handler );
		M::expectFilterAdded( 'wp_die_handler', $handler );

		safe_exit();

		$this->assertConditionsMet();
	}
}