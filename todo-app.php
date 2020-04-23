<?php
/**
 * Plugin Name:       ToDo
 * Plugin URI:        #
 * Description:       Simple Todo App.
 * Version:           1.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Przemyslaw Mysiak
 * Author URI:        https://mysiak.net
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */


define('PLUGIN_PATH', plugin_dir_path(__FILE__));
define('PLUGIN_URL', plugins_url('', __FILE__ ));

require PLUGIN_PATH . 'class-todo.php';

new Todo();
