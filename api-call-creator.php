<?php
/*
Plugin Name: Api call Creator
Plugin URI: http://zeidan.info/api-call-creator
Description: A Plugin to create personalized api calls
Version: 1.0
Author: Eric Zeidan
Author URI: http://zeidan.info/
twitter: ericjanzei
License: GPL2
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

require_once 'classes/class-apicallinit.php';
require_once 'classes/class-extrataxfields.php';
require_once 'classes/class-metaboxes.php';

define('API_BASE_DIR', plugin_dir_path(__FILE__));
define('API_TEXT_DOMAIN', 'api_plugin');
/**
* We create the instance
*/
$ap = new apiCallCreate();

/**
* Functions for redirect on activation and include action on activation of plugin
*/
register_activation_hook(__FILE__, array($ap, "apiActivate"));