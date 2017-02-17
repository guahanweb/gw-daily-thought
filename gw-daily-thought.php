<?php
/**
 * Plugin Name: GW | Daily Thought
 * Plugin URI: http://www.guahanweb.com
 * Description: Create a "Thought of the Day"
 * Version: 0.1
 * Tested With: 4.3.1
 * Author: Garth Henson
 * Author URI: http://www.guahanweb.com
 * Licence: GPLv2 or later
 * Text Domain: gw
 * Domain Path: /languages
 */

use GW\DailyThought;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/autoload.php';
require_once __DIR__ . '/functions.php';
$plugin = DailyThought\Plugin::instance(__FILE__);
