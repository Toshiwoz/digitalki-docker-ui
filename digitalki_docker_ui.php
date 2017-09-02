<?php
/*
Plugin Name: Docker Compose UI for WordPress
Plugin URI: https://digitalki.net/
Description: Docker Compose UI for Wordpress rely on Docker Compose UI (https://github.com/francescou/docker-compose-ui). Interacts with its APIs in order to control  and monitor docker compose.
Version: 0.1.0
Author: Loenardo Araki
Author URI: https://digitalki.net/
License: GPL2
*/

/*  The MIT License (MIT)

Copyright (c) 2017 Leonardo Araki https://digitalki.net/

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/
?><?php

// some definition we will use
define( 'DIGITALKI_DOCKER_UI_PUGIN_NAME', 'Docker Compose UI for WordPress');
define( 'DIGITALKI_DOCKER_UI_PLUGIN_DIRECTORY', 'digitalki-docker-ui');
define( 'DIGITALKI_DOCKER_UI_CURRENT_VERSION', '0.1.0' );
define( 'DIGITALKI_DOCKER_UI_CURRENT_BUILD', '3' );
define( 'DIGITALKI_DOCKER_UI_LOGPATH', str_replace('\\', '/', WP_CONTENT_DIR).'/dki-docker-ui-logs/');
define( 'DIGITALKI_DOCKER_UI_DEBUG', false);		# never use debug mode on productive systems
// i18n plugin domain for language files
define( 'EMU2_I18N_DOMAIN', 'dki-docker-ui' );

// how to handle log files, don't load them if you don't log
require_once('digitalki_docker_ui_logfilehandling.php');

// load language files
function digitalki_docker_ui_set_lang_file() {
	# set the language file
	$currentLocale = get_locale();
	if(!empty($currentLocale)) {
		$moFile = dirname(__FILE__) . "/lang/" . $currentLocale . ".mo";
		if (@file_exists($moFile) && is_readable($moFile)) {
			load_textdomain(EMU2_I18N_DOMAIN, $moFile);
		}

	}
}
digitalki_docker_ui_set_lang_file();

// create custom plugin settings menu
add_action( 'admin_menu', 'digitalki_docker_ui_create_menu' );

//call register settings function
add_action( 'admin_init', 'digitalki_docker_ui_register_settings' );


register_activation_hook(__FILE__, 'digitalki_docker_ui_activate');
register_deactivation_hook(__FILE__, 'digitalki_docker_ui_deactivate');
register_uninstall_hook(__FILE__, 'digitalki_docker_ui_uninstall');

// activating the default values
function digitalki_docker_ui_activate() {
	add_option('digitalki_docker_ui_option_3', 'any_value');
}

// deactivating
function digitalki_docker_ui_deactivate() {
	// needed for proper deletion of every option
	delete_option('digitalki_docker_ui_option_3');
}

// uninstalling
function digitalki_docker_ui_uninstall() {
	# delete all data stored
	delete_option('digitalki_docker_ui_option_3');
	// delete log files and folder only if needed
	if (function_exists('digitalki_docker_ui_deleteLogFolder')) digitalki_docker_ui_deleteLogFolder();
}

function digitalki_docker_ui_create_menu() {

	// create new top-level menu
	add_menu_page(
	__('Compose UI', EMU2_I18N_DOMAIN),
	__('Compose UI', EMU2_I18N_DOMAIN),
	0,
	DIGITALKI_DOCKER_UI_PLUGIN_DIRECTORY.'/digitalki_docker_ui_settings_page.php',
	'',
	plugins_url('/images/icon.png', __FILE__));


	add_submenu_page(
	DIGITALKI_DOCKER_UI_PLUGIN_DIRECTORY.'/digitalki_docker_ui_settings_page.php',
	__("Compose UI", EMU2_I18N_DOMAIN),
	__("Containers", EMU2_I18N_DOMAIN),
	0,
	DIGITALKI_DOCKER_UI_PLUGIN_DIRECTORY.'/digitalki_docker_ui_settings_page.php'
	);

	// add_submenu_page(
	// digitalki_docker_ui_PLUGIN_DIRECTORY.'/digitalki_docker_ui_settings_page.php',
	// __("HTML Title2", EMU2_I18N_DOMAIN),
	// __("Menu title 2", EMU2_I18N_DOMAIN),
	// 9,
	// digitalki_docker_ui_PLUGIN_DIRECTORY.'/digitalki_docker_ui_settings_page2.php'
	// );

	// // or create options menu page
	// add_options_page(__('HTML Title 3', EMU2_I18N_DOMAIN), __("Menu title 3", EMU2_I18N_DOMAIN), 9,  digitalki_docker_ui_PLUGIN_DIRECTORY.'/digitalki_docker_ui_settings_page.php');

	// // or create sub menu page
	// $parent_slug="index.php";	# For Dashboard
	// #$parent_slug="edit.php";		# For Posts
	// // more examples at http://codex.wordpress.org/Administration_Menus
	// add_submenu_page( $parent_slug, __("HTML Title 4", EMU2_I18N_DOMAIN), __("Menu title 4", EMU2_I18N_DOMAIN), 9, digitalki_docker_ui_PLUGIN_DIRECTORY.'/digitalki_docker_ui_settings_page.php');
}


function digitalki_docker_ui_register_settings() {
	//register settings
	register_setting( 'dki-docker-ui-settings-group', 'new_option_name' );
	register_setting( 'dki-docker-ui-settings-group', 'some_other_option' );
	register_setting( 'dki-docker-ui-settings-group', 'option_etc' );
}

// check if debug is activated
function digitalki_docker_ui_debug() {
	# only run debug on localhost
	if ($_SERVER["HTTP_HOST"]=="localhost" && defined('EPS_DEBUG') && EPS_DEBUG==true) return true;
}
?>
