<?php
header( 'Content-type: application/json' );

$output = array();
$cats = array( 'developing', 'review', 'published', 'disputed', 'under-review' );

$endpoint = 'https://en.wikinews.org/w/api.php?action=query&format=json';
$endpoint .= '&list=categorymembers&cmdir=desc&cmsort=timestamp&cmlimit=10';
$endpoint .= '&cmtitle=';

foreach ( $cats as $category ) {

	global $endpoint, $output;
	$ch = curl_init();
	curl_setopt( $ch, CURLOPT_URL, $endpoint . 'Category:' . str_replace( '-', '_', $category ) );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt( $ch, CURLOPT_USERAGENT, 'WikinewsDashboard/1.0 https://tools.wmflabs.org/mc8/dashboard' );

	$json = curl_exec( $ch );

	curl_close( $ch );

	if ( !$json ) {
		//header( 'HTTP/1.1 500 Internal Server Error' );
		die( '{"error": "'.curl_error( $ch ) . '"}' );
	}
	$data = json_decode( $json );
	if ( !is_object( $data ) ) {
		//header( 'HTTP/1.1 500 Internal Server Error' );
		var_dump( $data );
		die( '{"error": "unable to decode upstream json ' . json_last_error() . '"}' );
	}
	$data = $data->query;

	$output[$category] = array();
	
	foreach ( $data->categorymembers as $datum ) {
		array_push( $output[$category], $datum->title );
	}

}

die( json_encode( $output ) );
