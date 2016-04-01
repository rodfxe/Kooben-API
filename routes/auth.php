<?php
/**
* Auth routes for Kooben
*
* Created - Martin Samuel Esteban Diaz
*/


$app->get( '/me', function() use( $app, $mysql, $kooben ) {
	$session = checkKoobenSession( $app );
	if ( !$session->status->found ) {
		echo createEmptyModelWithStatus( 'Get' )->toJson();
		return;
	}

	$users = new Model( 'accounts', $mysql );
	$users->setProperties( $kooben->models->accounts );

	$result = $users->findById( 'id', $session->userId );
	unset( $result->sPassword );
	unset( $result->password );

	echo $result->toJson();
});







$app->post( '/auth', function() use( $app, &$mysql, $kooben){
	$post = $app->request->post();
	$mail = $post[ 'mail' ];
	$applicationType = $app->request->headers->get( 'KOOBEN-APPLICATION-NAME' );

	# create instance of accounts model.
	$accounts = new Model( 'accounts', $mysql );
	$accounts->setProperties( $kooben->models->accounts );

	# search the account in the database.
	$account = $accounts->findById( 'mail', $mail );

	# if not found the account, the response is not found.
	if ( !$account->status->found ){
		echo createEmptyModelWithStatus( 'Get' )->toJson();
		return;
	}

	# check if using facebook oauth and check if password is valid.
	if ( isset( $post[ 'oauth' ] ) ) {

		$oauthService = $post[ 'oauth' ];

		if ( isset( $post[ 'updateToken' ] ) && strval( $post[ 'updateToken' ] ) === 'true' ) {
			$oauthService[ 0 ] = strtoupper( $oauthService[ 0 ] );

			# update token
			$query = new Query( $mysql );
			$query->type = QUERY_UPDATE;
			$oauthService = "s$oauthService".'Token';
			$query->setSql( "update usuarios set $oauthService = \"".$post[ 'token' ]."\" where IdUsuario = $account->id" );
			$query->execQuery();
			$validPassword = true;

		} else {
			$validPassword = ( strval( $post[ 'token' ] ) === strval( $account->$oauthService ) );
		}
	} else {
		$validPassword = ( strval( $post[ 'password' ] ) == strval( $account->sPassword ) );
	}

	# create the result and set status to result
	$result = new StdModel();
	$result->status = new GetModelStatus( true, $validPassword );
	$result->usingOauth = isset( $post[ 'oauth' ] );

	# if password is valid, in the result, set the account information, and generate a new session.
	if ($validPassword){
		$result->username = $mail;
		$result->name = $account->name;
		$result->lastName = $account->lastName;
		$result->image = $account->image;
		$result->app = $applicationType;

		$session = createLogSession( $account->userId, $account->username, $applicationType );

		if( $session->status->created ){
			$result->name = $account->name;
			$result->lastName = $account->lastName;
			$result->sessionId = $session->hash;
		}
	}

	echo $result->toJson();
});


$app->post( '/auth/deliverers', function() use( $app, $mysql, $kooben ) {
	$values = $app->request->post();

	$deliverers = new Model( 'shopDeliverers', $mysql );
	$deliverers->setProperties( $kooben->models->shopDeliverers );
	$delivererPerson = $deliverers->findById( 'email', $values[ 'email' ] );

	if ( !$delivererPerson->status->found ) { echo createEmptyModelWithStatus( 'Post' )->toJson(); return; }

	$delivererPerson->status->valid = ( strval( $values[ 'password' ] ) == strval( $delivererPerson->password ) );
	unset( $delivererPerson->password );
	echo $delivererPerson->toJson();
});


$app->post( '/users', function( ) use( $app, $mysql, $kooben ){
	# create result
	$result = createEmptyModelWithStatus( 'Post' );
	$users = new Model( 'accounts', $mysql );
	$users->setProperties( $kooben->models->accounts );

	# check user
	$check = $users->findBy( [
		'params' => new QueryParams( [
			'mail' => new QueryParamItem( $app->request->post( 'sMail' ), PARAM_STRING ),
			'username' => new QueryParamItem( $app->request->post( 'sIdUsuario' ), PARAM_STRING )
		] ),
		'queryName' => 'validate'
	] );

	# check if the user exists
	if ( $check->status->found ) {
		$result->status->exists = true;
		echo $result->toJson();
		return;
	}

	# set values from post values
	$users->setValuesFromArray( $app->request->post() );
	$user = $users->create();

	if ( $user->status->created ) {
		# remove fields
		unset( $user->sFacebookToken );
		unset( $user->sGoogleToken );
		unset( $user->sPassword );

		# upload avatar
		if ( isset( $_FILES[ 'avatar' ] ) ) {
			$avatar = new Upload( $_FILES[ 'avatar' ], [
				'rules' => [ 'image/jpeg', 'image/jpg', 'image/png' ],
				'maxSize' => $kooben->fileSizes->avatar
			] );

			$user->avatar = $avatar->getResume();
			if( $user->avatar[ 'isValid' ] ) {
				$fileName = ( "user.$user->sTipo.$user->id" );
				$user->avatar[ 'saved' ] = $avatar->save( $fileName );

				if( $user->avatar[ 'saved' ] ) {
					$imgUrl = "http://intelicode.no-ip.org/Apps/Kooben/Storage/$avatar->name";
					$query = new Query( $mysql );
					$query->type = QUERY_UPDATE;
					$query->setSql( 'update usuarios set sImg = :img where IdUsuario = :id;' );
					$query->setParam( 'img', $imgUrl, PARAM_STRING );
					$query->setParam( 'id', $user->id, PARAM_INT );
					$query->execQuery();
					$user->avatar[ 'url' ] = $imgUrl;
				}
			}
		}

		# create session
		$session = createLogSession( $user->id, $user->sIdUsuario, $app->request->headers->get( 'KOOBEN-APPLICATION-NAME' ) );

		if ( $session->status->created ) { $user->sessionId = $session->hash; }
		else {
			$user->session = new stdClass();
			$user->session->error = $session->error;
			$user->session->fieldError = $session->fieldError;
		}
	}

	echo $user->toJson();
});







?>