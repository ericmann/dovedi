<?php
/**
 * Plugin Name: Dovedi
 * Plugin URI:  https://github.com/ericmann/dovedi
 * Description: Time-based One Time Password authentication for WordPress.
 * Version:     1.1.1
 * Author:      Eric Mann
 * Author URI:  https://eamann.com
 * License:     GPLv2+
 * Text Domain: dovedi
 * Domain Path: /languages
 */

/**
 * Copyright (c) 2015-2017 Eric Mann (email : eric@eamann.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

/**
 * Built using yo wp-make:plugin
 * Copyright (c) 2015 10up, LLC
 * https://github.com/10up/generator-wp-make
 */

// Useful global constants
define( 'DOVEDI_VERSION', '1.1.1' );
define( 'DOVEDI_URL',     plugin_dir_url( __FILE__ ) );
define( 'DOVEDI_PATH',    dirname( __FILE__ ) . '/' );
define( 'DOVEDI_INC',     DOVEDI_PATH . 'includes/' );

// Include files
require_once DOVEDI_INC . 'functions/core.php';


// Activation/Deactivation
register_activation_hook(   __FILE__, '\EAMann\Dovedi\Core\activate' );
register_deactivation_hook( __FILE__, '\EAMann\Dovedi\Core\deactivate' );

// Bootstrap
EAMann\Dovedi\Core\setup();