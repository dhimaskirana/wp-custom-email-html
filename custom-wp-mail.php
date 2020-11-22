<?php

/**
 * Plugin Name:       WP Email Template
 * Plugin URI:        https://pakaiwp.com/cara-memasang-template-email-html-di-wordpress/
 * Description:       Plugin sederhana untuk menggunakan custom html pada fungsi wp_mail()
 * Version:           1.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Dhimas Kirana
 * Author URI:        https://www.dhimaskirana.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

// Don't call the file directly
if (!defined('ABSPATH')) exit;

/**
 * Custom HTML Template to wp_mail()
 *
 * Replace wpet_ with your prefix
 * 
 * The HTML Template saved as custom-email-template.php in the same folder
 *
 */

function wpet_email_template($message) {

	// Render Template
	ob_start();
	include('custom-email-template.php');
	$wpet_template = ob_get_contents();
	ob_end_clean();

	// Replace Placeholder
	$message = str_replace('%%MAILCONTENT%%', $message, $wpet_template);

	// Return Template with Data
	return $message;
}

add_filter('wp_mail_content_type', 'set_content_type');
function set_content_type($type) {
	if ($type != 'text/html') {
		// If not html, work with content and filter it
		add_filter('wpet_filter_email', 'wp_kses_post', 50);
		wpet_content_filters();
	}
	return $content_type = 'text/html';
}

add_filter('wp_mail', 'my_wp_mail_filter');
function my_wp_mail_filter($args) {
	$message = $args['message'];
	$args['message'] = wpet_email_template(apply_filters('wpet_filter_email', $message));
	return $args;
}

function wpet_content_filters() {
	add_filter('wpet_filter_email', 'wptexturize');
	add_filter('wpet_filter_email', 'convert_chars');
	add_filter('wpet_filter_email', 'wpautop');
	add_filter('wpet_filter_email', 'clean_retrieve_password');
}

function clean_retrieve_password($message) {
	return make_clickable(preg_replace('@<(http[^> ]+)>@', '$1', $message));
}
