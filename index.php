<?php
/**
 * @package Brancher
 * @version 1.0.0
 */
/*
 * Plugin Name: Brancher
 * Plugin URI: none
 * Description: A simple plugin that, when activated, shows the current branch of other plugins that use github.
 * Author: Ben Rothman and Evan Herman
 * Version: 1.0.0
 * Author URI: https://benrothman.org
 */

/**
 * Display the Plugin's git branch on the plugins.php plugin table
 *
 * @param  array $plugins Array of install plugin data.
 *
 * @return array          Filtered array of installed plugins data.
 */
function show_plugin_git_branch( $plugins ) {

	$screen = get_current_screen();

	if ( is_null( $screen ) || 'plugins' !== $screen->base ) {

		return $plugins;

	}

	foreach ( $plugins as $path => &$data ) {

		$file = trailingslashit( trailingslashit( WP_PLUGIN_DIR ) . dirname( $path ) ) . '.git/HEAD';

		if ( ! file_exists( $file ) ) {

			continue;

		}

		$head = file_get_contents( $file );

		if ( ! $head ) {

			continue;

		}

		$branch = trim( basename( str_replace( 'ref: ', '', $head ) ) );

		$data['Name'] = $data['Name'] . " (Branch: ${branch})";

	}

	return $plugins;

}
add_filter( 'all_plugins', 'show_plugin_git_branch', PHP_INT_MAX );
