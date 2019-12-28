<?php


namespace WPEasyLibrary\WordPress;

/**
 * Borrowed from Woocommerce
 * Class TimezoneHelper
 * @package WPEasyLibrary\WordPress
 */
class TimezoneHelper
{
    static function getTimezoneString() {
        // If site timezone string exists, return it.
        $timezone = get_option( 'timezone_string' );
        if ( $timezone ) {
            return $timezone;
        }

        // Get UTC offset, if it isn't set then return UTC.
        $utc_offset = intval( get_option( 'gmt_offset', 0 ) );
        if ( 0 === $utc_offset ) {
            return 'UTC';
        }

        // Adjust UTC offset from hours to seconds.
        $utc_offset *= 3600;

        // Attempt to guess the timezone string from the UTC offset.
        $timezone = timezone_name_from_abbr( '', $utc_offset );
        if ( $timezone ) {
            return $timezone;
        }

        // Last try, guess timezone string manually.
        foreach ( timezone_abbreviations_list() as $abbr ) {
            foreach ( $abbr as $city ) {
                if ( (bool) date( 'I' ) === (bool) $city['dst'] && $city['timezone_id'] && intval( $city['offset'] ) === $utc_offset ) {
                    return $city['timezone_id'];
                }
            }
        }

        // Fallback to UTC.
        return 'UTC';
    }
}