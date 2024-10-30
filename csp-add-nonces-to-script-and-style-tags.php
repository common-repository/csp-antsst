<?php

/**
 * Plugin Name:       CSP-ANTS&ST
 * Description:       Add a nonce to each script and style tags, and set those nonces in CSP header
 * Version:           1.1.1
 * Requires at least: 5.9
 * Requires PHP:      7.3
 * Author:            Pascal CESCATO
 * Author URI:        https://pascalcescato.gdn/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */
if ( function_exists ( 'litespeed_autoload' ) ):

    // lscache version
    function cspantsst_cspantsst_lscwp_check ( $content ) {

        $uris = implode ( ' ', cspantsst_search_for_sources ( $content ) );

        $sha256_csp = cspantsst_search_for_events ( $content );

        $nonces = [];

        $content = preg_replace_callback ( '#<script.*?\>#', function ( $matches ) use ( &$nonces ) {
            $nonce = wp_create_nonce ( $matches[ 0 ] );
            $nonces[] = $nonce;

            return str_replace ( '<script', "<script nonce='{$nonce}'", $matches[ 0 ] );
        }, $content );

        $content = preg_replace_callback ( '#<style.*?\>#', function ( $matches ) use ( &$nonces ) {
            $nonce = wp_create_nonce ( $matches[ 0 ] );
            $nonces[] = $nonce;

            return str_replace ( '<style', "<style nonce='{$nonce}'", $matches[ 0 ] );
        }, $content );

        $nonces_csp = array_reduce ( $nonces, function ( $header, $nonce ) {
            return "{$header} 'nonce-{$nonce}'";
        }, '' );

        header ( sprintf ( "Content-Security-Policy: base-uri 'self' %1s data:; object-src 'none'; script-src https:%2s %3s 'strict-dynamic'", $uris, $nonces_csp, $sha256_csp ) );

        return $content;
    }

    add_filter ( 'litespeed_buffer_after', 'cspantsst_cspantsst_lscwp_check', 0 );

else:
    // otherwise
    add_action ( 'template_redirect', function () {

        ob_start ( function ( $output ) {

            $uris = implode ( ' ', cspantsst_search_for_sources ( $output ) );

            $sha256_csp = cspantsst_search_for_events ( $output );

            $nonces = [];

            $output = preg_replace_callback ( '#<script.*?\>#', function ( $matches ) use ( &$nonces ) {
                $nonce = wp_create_nonce ( $matches[ 0 ] );
                $nonces[] = $nonce;
                return str_replace ( '<script', "<script nonce='{$nonce}'", $matches[ 0 ] );
            }, $output );

            $output = preg_replace_callback ( '#<style.*?\>#', function ( $matches ) use ( &$nonces ) {
                $nonce = wp_create_nonce ( $matches[ 0 ] );
                $nonces[] = $nonce;
                return str_replace ( '<style', "<style nonce='{$nonce}'", $matches[ 0 ] );
            }, $output );

            $header = '';
            $nonces_csp = array_reduce ( $nonces, function ( $header, $nonce ) {
                return "{$header} 'nonce-{$nonce}'";
            }, '' );

            header ( sprintf ( "Content-Security-Policy: base-uri 'self' %1s data:; object-src 'none'; script-src https:%2s %3s 'strict-dynamic'", $uris, $nonces_csp, $sha256_csp ) );

            return $output;
        } );
    } );
endif;

function cspantsst_search_for_events ( $output ) {

    $sha256 = array ();

    preg_match_all ( '/onload="(?<onload>[^"]+)"|onclick="(?<onclick>[^"]+)"/', $output, $matches );
    foreach ( $matches[ 'onload' ] as $match ):
        if ( !empty ( $match ) )
            $sha256[] = base64_encode ( hash ( 'sha256', $match, true ) );
    endforeach;
    foreach ( $matches[ 'onclick' ] as $match ):
        if ( !empty ( $match ) )
            $sha256[] = base64_encode ( hash ( 'sha256', $match, true ) );
    endforeach;

    if ( class_exists ( 'autoptimizeConfig' ) ):
        $sha256[] = base64_encode ( hash ( 'sha256', "this.onload=null;this.media='all';", true ) );
    endif;


    $header_sha256 = "'unsafe-hashes'";
    $sha256_csp = array_reduce ( $sha256, function ( $header, $sha256_item ) {
        return "{$header} 'sha256-{$sha256_item}'";
    }, '' );

    if ( !empty ( $sha256_csp ) )
        $sha256_csp = $header_sha256 . $sha256_csp;

    return $sha256_csp;
}

function cspantsst_search_for_sources ( $string ) {

    $result = array ();
    if ( strpos ( $string, 'https://secure.gravatar.com/avatar/' ) ):
        $result[] = 'https://secure.gravatar.com/avatar/';
    endif;
    if ( strpos ( $string, 'https://fonts.googleapis.com/' ) ):
        $result[] = 'https://fonts.googleapis.com/';
    endif;
    if ( strpos ( $string, 'https://maxcdn.bootstrapcdn.com/' ) ):
        $result[] = 'https://maxcdn.bootstrapcdn.com/';
    endif;
    return $result;
    
}
