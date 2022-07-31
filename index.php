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
 * @return array Filtered array of installed plugins data.
 * 
 * @since 1.0.0
 */
// declare function if < PHP 8  is installed
if (!function_exists('str_starts_with')) {
    function str_starts_with($haystack, $needle) {
        return (string)$needle !== '' && strncmp($haystack, $needle, strlen($needle)) === 0;
    }
}

if (!function_exists('str_contains')) {
    function str_contains($haystack, $needle) {
        return $needle !== '' && mb_strpos($haystack, $needle) !== false;
    }
}

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

		$no_git = false;

		// execute git shell commands
		$check_local = shell_exec( 'cd ' . trailingslashit( WP_PLUGIN_DIR ) . dirname( $path ) . ' && ' . 'git status' );
		$check_remote = shell_exec( 'cd ' . trailingslashit( WP_PLUGIN_DIR ) . dirname( $path ) . ' && ' . 'git remote show origin' );
		
		// check for git
		if( str_contains( $check_local, "command 'git' not found." )) {
			$no_git = true; // no git installed
		}
		if ( ! $no_git ) {
			// check local
			if ( str_contains( $check_local, 'Your branch is up to date') ) {
				$output = '✅  '; // clean local files
			} else {
				$output = '⚠️ '; // local files changed
			}
			
			// remote check
			if ( str_contains( $check_remote, 'local out of date') ) {
				$output = '⚠️ '; // remote changed
			} else {
				$output = '✅  '; // up to date with remote
			}
		}

		// read head to get branch name
		$branch = trim( basename( str_replace( 'ref: ', '', $head ) ) );

		// print plugin name + git info
		$data['Name'] = $output . ' ' . $data['Name'] . ' <em>(Status:' . $output . ' Branch: ' . $branch .')</em>';

	}

	return $plugins;

}
add_filter( 'all_plugins', 'show_plugin_git_branch', PHP_INT_MAX );

/**
 * Proper way to enqueue scripts and styles
 */
function wpdocs_theme_name_scripts() {
    wp_enqueue_style( 'brancher-style', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'brancher.css' );
    // wp_enqueue_script( 'script-name', get_template_directory_uri() . '/js/example.js', array(), '1.0.0', true );
}
add_action( 'admin_enqueue_scripts', 'wpdocs_theme_name_scripts' );