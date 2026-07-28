<?php
/**
 * Shared small helpers used across the admin views.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'csd_tooltip' ) ) {
    /**
     * Renders a small help icon with a custom CSS tooltip (shown on hover,
     * focus, or click - see .csd-tooltip in the admin stylesheet and the
     * click-to-toggle handler in the admin script).
     */
    function csd_tooltip( $text ) {
        return ' <span class="dashicons dashicons-editor-help csd-tooltip" tabindex="0" role="button" aria-label="' . esc_attr( $text ) . '" data-tooltip="' . esc_attr( $text ) . '"></span>';
    }
}

if ( ! function_exists( 'csd_update_bool_option' ) ) {
    /**
     * update_option() silently no-ops the first time a boolean option is set
     * to false: its internal old-value lookup (no default, so a missing
     * option itself reads back as false) sees false === false and skips the
     * write, since it can't distinguish "not set" from "set to false".
     * Checking existence with our own unambiguous sentinel default avoids that.
     */
    function csd_update_bool_option( $name, $value ) {
        if ( null === get_option( $name, null ) ) {
            add_option( $name, $value );
        } else {
            update_option( $name, $value );
        }
    }
}
