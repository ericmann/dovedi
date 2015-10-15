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

use TenUp\Dovedi as Base;

class Core_Tests extends Base\TestCase {

	protected $testFiles = [
		'functions/core.php'
	];

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
}