<?php
/**
 * @package Brancher
 * @version 1.0.0
 */
/*
Plugin Name: Brancher
Plugin URI: none
Description: A plugin that when activated shows the current branch of other plugins.
Author: Ben Rothman
Version: 1.0.0
Author URI: https://benrothman.org
*/
add_action('wp_enqueue_scripts', 'qg_enqueue');
function filter_plugin_name( $plugins ) {

	$screen = get_current_screen();

	if ( is_null( $screen ) || 'plugins' !== $screen->base ) {

		return $plugins;

	}

	foreach ( $plugins as $path => &$data ) {

		$path = substr( $path, 0, strpos( $path, '/' ) ) . '/.git/HEAD';

		$branch = file_get_contents( $path );

		$data['Name'] = $data['Name'] . ' (branch: ' . $branch . ')';

	}

	return $plugins;

}
add_filter( 'all_plugins', 'filter_plugin_name', PHP_INT_MAX );