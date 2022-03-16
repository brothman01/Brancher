<?php
/**
 * @package Brancher
 * @version 1.0.0
 */
/*
Plugin Name: Brancher
Plugin URI: http://wordpress.org/plugins/hello-dolly/
Description: A plugin that when activated shows the current branch of other plugins.
Author: Ben Rothman
Version: 1.0.0
Author URI: https://benrothman.org
*/
add_action('wp_enqueue_scripts', 'qg_enqueue');
function qg_enqueue() {
    wp_enqueue_script(
        'qgjs',
        plugin_dir_url(__FILE__).'branch.js'
    );
}