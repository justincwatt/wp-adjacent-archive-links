<?php
/*
Plugin Name: Adjacent Archive Links
Plugin URI: http://justinsomnia.org/2012/11/adjacent-archive-links-for-wordpress/
Description: Adds the <code>previous_archive_link</code> and <code>next_archive_link</code> template tags, which generate links to your previous/next date archive pages (day, month, and year). 
Version: 2.0
Author: Justin Watt
Author URI: http://justinsomnia.org/

LICENSE
Copyright 2012 Justin Watt justincwatt@gmail.com

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
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
		WHERE post_date $op '%s'
		AND post_type = 'post'
		AND post_status = 'publish'
		ORDER BY post_date $order
		LIMIT 1
	";

	$adjacent_post_date = $wpdb->get_var( $wpdb->prepare( $sql, $current_date ) );

	if ( !$adjacent_post_date ) {
		$output = '';
	} else {
		$adjacent_post_date = strtotime( $adjacent_post_date );
		
		$adjacent_year  = date( 'Y', $adjacent_post_date );
		$adjacent_month = date( 'm', $adjacent_post_date );
		$adjacent_day   = date( 'd', $adjacent_post_date );
		
		if ( is_year() ) {
			$href = get_year_link( $adjacent_year );
			/* translators: format for year archive links, see http://php.net/date */
			$date = date_i18n( __( 'Y', 'adjacent-archive-links' ), $adjacent_post_date );

		} elseif ( is_month() ) {
			$href = get_month_link( $adjacent_year, $adjacent_month );
			/* translators: format for month archive links, see http://php.net/date */
			$date = date_i18n( __( 'F Y', 'adjacent-archive-links' ), $adjacent_post_date );

		} else {
			$href = get_day_link( $adjacent_year, $adjacent_month, $adjacent_day );
			/* translators: format for day archive links, see http://php.net/date */
			$date = date_i18n( __( 'F j, Y', 'adjacent-archive-links' ), $adjacent_post_date );
		}	

		$rel = $previous ? 'prev' : 'next';
		$string = '<a href="' . $href . '" rel="' . $rel . '">';
		$inlink = str_replace( '%date', $date, $link );
		$inlink = $string . $inlink . '</a>';
		$output = str_replace( '%link', $inlink, $format );
	}

	$adjacent = $previous ? 'previous' : 'next';

	echo apply_filters("{$adjacent}_archive_link", $output, $format, $link);
}





function adjacent_archive_links_init() {
	load_plugin_textdomain( 'adjacent-archive-links', false, basename( dirname( __FILE__ ) ) . '/languages' );
}
add_action('plugins_loaded', 'adjacent_archive_links_init');
