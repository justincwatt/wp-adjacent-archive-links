<?php
/*
Plugin Name: Adjacent Archive Links
Plugin URI: http://justinsomnia.org/2012/11/adjacent-archive-links-for-wordpress/
Description: Adds the <code>previous_archive_link</code> and <code>next_archive_link</code> template tags, which generate links to your previous/next date archive pages (day, month, and year). 
Version: 1.0
Author: Justin Watt
Author URI: http://justinsomnia.org/
*/

function previous_archive_link( $format = '&laquo; %link', $link = '%date') {
	adjacent_archive_link( $format, $link, true );
}

function next_archive_link( $format = '%link &raquo;', $link = '%date') {
	adjacent_archive_link( $format, $link, false );
}

// modeled after the WordPress functions, adjacent_post_link() and get_adjacent_post()
function adjacent_archive_link( $format, $link, $previous = true ) {
	global $wpdb;

	if ( !is_date() ) {
		return;
	}

	if ( is_year() ) {
		$current_year = get_the_time( 'Y' );
		$current_month = ( $previous ? 1 : 12 ) ;
		// conveniently both January and December are 31 day months
		$current_day = ( $previous ? 1 : 31 );

	} elseif ( is_month() ) {
		$current_year = get_the_time( 'Y' );
		$current_month = get_the_time( 'm' );
		// In order to find the next post after the current month, we need to 
		// know last day of the current month, which is the reason for this weird code.
		$current_day = ( $previous ? 1 : date( 't', strtotime( $current_year . '-' . $current_month . '-01' ) ) );

	} else {
		$current_year = get_the_time( 'Y' );
		$current_month = get_the_time( 'm' );
		$current_day = get_the_time( 'd' );
	}

	$current_date = $current_year . '-' . $current_month . '-' . $current_day . ' ' . ( $previous ? '00:00:00' : '23:59:59' );
	$op = $previous ? '<' : '>';
	$order = $previous ? 'DESC' : 'ASC';
	$sql = "
		SELECT post_date from $wpdb->posts
		WHERE post_date $op '$current_date'
		AND post_type = 'post'
		AND post_status = 'publish'
		ORDER BY post_date $order
		LIMIT 1
	";

	$adjacent_post_date = $wpdb->get_var( $wpdb->prepare( $sql ) );

	if ( !$adjacent_post_date ) {
		return;
	} else {
		$adjacent_post_date = strtotime( $adjacent_post_date );
	}

	if ( is_year() ) {
		$href = date( '/Y/', $adjacent_post_date );
		/* translators: format for year archive links, see http://php.net/date */
		$date = date( __( 'Y', 'adjacent-archive-links' ), $adjacent_post_date );
	} elseif ( is_month() ) {
		$href = date( '/Y/m/', $adjacent_post_date );
		/* translators: format for month archive links, see http://php.net/date */
		$date = date( __( 'F Y', 'adjacent-archive-links' ), $adjacent_post_date );
	} else {
		$href = date( '/Y/m/d/', $adjacent_post_date );
		/* translators: format for day archive links, see http://php.net/date */
		$date = date( __( 'F j, Y', 'adjacent-archive-links' ), $adjacent_post_date );
	}

	$rel = $previous ? 'prev' : 'next';
	$string = '<a href="' . $href . '" rel="' . $rel . '">';
	$link = str_replace( '%date', $date, $link );
	$link = $string . $link . '</a>';
	$format = str_replace( '%link', $link, $format );

	print $format;
}

function adjacent_archive_links_init() {
	load_plugin_textdomain( 'adjacent-archive-links', false, basename( dirname( __FILE__ ) ) . '/languages' );
}
add_action('plugins_loaded', 'adjacent_archive_links_init');