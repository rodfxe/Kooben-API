<?php

# Use this function for check kooben session in the database.
function checkKoobenSession( &$slim )
{
	global $sessions;
	$hash = $slim->request->headers->get('KOOBEN-SESSION-ID');
	$session = $sessions->findById( 'hash', $hash );
	$session->id = ( $session->status->found ? $session->realId : -1 );
	$session->hash = $hash;
	return $session;
}

# User this function for register actions in kooben kardex.
function saveInKoobenKardex( $row, $session, $table, $operation = 'insert' ){
	global $kardex;
	$kardex->rowId = $row;
	$kardex->sessionId = $session;
	$kardex->tableName = $table;
	$kardex->operation = $operation;
	$kardex->create();
}


# use this function for create an empty status, the syntax for status name is [Get, Post, Put, Delete], the result is 'GetModelStatus'.
function createEmptyModelWithStatus( $statusname, $message = empty_str ){
	$statusClass = ( $statusname."ModelStatus" );
	$result = new StdModel();
	$result->status = new $statusClass();
	if( !$message == empty_str ){
		$result->status->message = $message;
	}
	return $result;
}


function createSessionInvalidResponse( $statusname ) {
	$session = createEmptyModelWithStatus( $statusname );
	$session->status->sessionInvalid = true;
	return $session;
}


function getSessionIdFrom( $application ) {
	return $application->request->headers->get( 'KOOBEN-SESSION-ID' );
}


# create session
function createLogSession( $userId, $username, $application ) {
	global $sessions;
	$valForHash = ( rand() . $username . date( 'Y-m-d H:i:s', time() ) );
	$sessions->hash = hash( 'md5', $valForHash );
	$sessions->username = $username;
	$sessions->application = $application;
	$sessions->type = 'log';
	$sessions->userId = $userId;

	$session = $sessions->create();
	return $session;
}


# User this function for generate weeks between in a date range.
function getWeeksBetween( $begin, $end ){
	$period = new DatePeriod( new DateTime($begin), new DateInterval('P1W'), new DateTime($end) );
	$weeks = array();

	foreach ( $period as $w ){
		$item = array(
			'number' => intval( $w->format('W') ),
			"year" => intval( $w->format('Y') ),
			'date' => date( "Y-m-d", strtotime( $w->format('Y').'W'.$w->format('W') ) )
		);
		array_push( $weeks, $item );
	}

	return $weeks;
}

# Use this function for clear an array.
function clearArray( &$Array ){
	unset( $Array );
	$Array = array();
}

function getApplication($agent){
	$start = strpos( $agent, "(" ) + 1;
	$end = strpos( $agent, ';' );
	$length = $end - $start;
	$platform = substr( $agent, $start, $length );
	$application = '';

	switch ( $platform ) {
		case 'Macintosh':
			$application = 'os-x';
			break;
		case 'iPhone':
			$application = 'ios';
			break;
	}
	return array(
		"platform" => $platform,
		"application" => $application
	);
}

function lastIndexOfString( &$str )
{
	return ( strlen( $str ) - 1 );
}


function lastIndexOfArray( &$array )
{
	return ( count( $array ) - 1 );
}

function getDistanceBetweenTwoLocations( $from, $to, $unit ) {
	$radius = 6378.137;
	$dlon = $from[ 'lon' ] - $to[ 'lon' ]; 
	$distance = ( acos(
		sin( deg2rad( $from[ 'lat' ] ) ) *
		sin( deg2rad( $to[ 'lat' ] ) ) + 
		cos( deg2rad( $from[ 'lat' ] ) ) *
		cos( deg2rad( $to[ 'lat' ] ) ) *
		cos( deg2rad( $dlon ) )
	) * $radius );

	if ( $unit == 'K') {
		return ( $distance );
	} else if ($unit == 'M') {
		return ( $distance * 0.621371192 );
	} else if ($unit == 'N') {
		return ( $distance * 0.539956803 );
	} else {
		return -1;
	}
}

?>
