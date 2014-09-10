<?php
require_once __DIR__.'/../vendor/autoload.php'; 
require_once '../lib/nocsrf.php';
require_once '../lib/auth.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

session_start();

$app = new Silex\Application(); 
$app['debug'] = true;
$db = new SQLite3('./data.db');
$auth = new Auth($db);

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../templates',
));

function dirToArray($dir) {
    $contents = array();
    # Foreach node in $dir
    foreach (scandir($dir) as $node) {
        # Skip link to current and parent folder
        if ($node == '.')  continue;
        if ($node == '..') continue;
        # Check if it's a node or a folder
        if (is_dir($dir . DIRECTORY_SEPARATOR . $node)) {
            # Add directory recursively, be sure to pass a valid path
            # to the function, not just the folder's name
            $contents[$node] = dirToArray($dir . DIRECTORY_SEPARATOR . $node);
        } else {
            # Add node, the keys will be updated automatically
            $contents[] = $node;
        }
    }
    # done
    return $contents;
}

$app->post('/api/connect',function(Request $request) use($app,$db, $auth) {
	$login = $request->get('username');
	$password = $request->get('password');
	if($auth->Login($login, $password)) {
		return $_COOKIE['session'];
	}
	return '';
});

$app->post('/api/disconnect',function(Request $request) use($app,$db, $auth) {
	if($auth->Check($_COOKIE['session'])) {
		$date = new DateTime();
		$hash = sha1((rand()*992301230) . ($date->getTimestamp()) );
		setcookie('session', null, -1, '/');
		$results = $db->query('UPDATE accounts SET  hash="'. $hash .'" WHERE hash="'. $_COOKIE['session'] .'"');
		session_destroy();
		return '1';
	}
	return '';
});

$app->post('/api/synchronize', function() use($app, $db) {

	if(IsValidSession($db)) {
		$uploaddir = '../uploads/' . $_SESSION['username'] . "/";
		$uploadfile = $uploaddir . basename($_FILES['file']['name']);

		if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) {

		} else {

		}
	}
	return $app->redirect('/backend');
}); 

$app->post('/login',function() use($app,$db, $auth) {
	if(isset($_POST['login'])) {
		try
		{
		    // Run CSRF check, on POST data, in exception mode, with a validity of 10 minutes, in one-time mode.
		    NoCSRF::check( 'csrf_token', $_POST, true, 60*10, false );
		    // form parsing, DB inserts, etc.
		}
		catch ( Exception $e )
		{
		    return $app->redirect('/?message=Fehler beim Einloggen!');
		}		
		if($auth->Login($_POST['user'], $_POST['password'])) {
			return $app->redirect('/backend');
		}
	}
	return $app->redirect('/?message=Fehler beim Einloggen!');
});

$app->post('/set_masterpassword',function() use($app,$db, $auth) {
	if($auth->Check($_COOKIE['session'])) {
		try
		{
		    // Run CSRF check, on POST data, in exception mode, with a validity of 10 minutes, in one-time mode.
		    NoCSRF::check( 'csrf_token', $_POST, true, 60*10, false );
		    // form parsing, DB inserts, etc.
		}
		catch ( Exception $e )
		{
		    return $app->redirect('/backend');
		}
		// TODO: Überprüfen, ob password ist richtig?
		$_SESSION['masterpassword'] = $_POST['masterpassword'];
		return $app->redirect('/backend');
	}
	return $app->redirect('/backend');
});

$app->get('/logout',function() use($app,$db, $auth) {
	if($auth->Check($_COOKIE['session'])) {
		$date = new DateTime();
		$hash = sha1($_POST['user'] . ($date->getTimestamp()) );
		setcookie('session', null, -1, '/');
		$results = $db->query('UPDATE accounts SET  hash="'. $hash .'" WHERE hash="'. $_COOKIE['session'] .'"');
		session_destroy();
		return $app->redirect('/');
	}
	return $app->redirect('/backend');
});

$app->get('/create/templink', function() use($app, $db, $auth) {
	if(!$auth->Check($_COOKIE['session'])) {
		return $app->redirect('/?message=Session ungültig oder abgelaufen!');
	}
	$token = NoCSRF::generate( 'csrf_token' );
	$path = $app['request']->get('path');
    return $app['twig']->render('templink.twig', array(
    	'filepath' => $path,
    	'username' => $_SESSION['username'],
    	'token' => $token
    ));
}); 

$app->post('/generate/templink', function() use($app, $db, $auth) {
	if(!$auth->Check($_COOKIE['session'])) {
		return $app->redirect('/?message=Session ungültig oder abgelaufen!');
	}
	try
	{
	    // Run CSRF check, on POST data, in exception mode, with a validity of 10 minutes, in one-time mode.
	    NoCSRF::check( 'csrf_token', $_POST, true, 60*10, false );
	    // form parsing, DB inserts, etc.
	}
	catch ( Exception $e )
	{
	    return $app->redirect('/backend');
	}
	$path = $app['request']->post('filepath');
    return $app->redirect('/templink/show/'.$hash);
}); 

$app->get('/preview', function() use($app, $db, $auth) {
	if(!$auth->Check($_COOKIE['session'])) {
		return $app->redirect('/?message=Session ungültig oder abgelaufen!');
	}
	$path = $app['request']->get('path');
    return $app['twig']->render('preview.twig', array(
    	'filepath' => $path
    ));
}); 

$app->get('/backend', function() use($app, $db, $auth) {
	if(!$auth->Check($_COOKIE['session'])) {
		return $app->redirect('/?message=Session ungültig oder abgelaufen!');
	}
	$userdir = '../uploads/' . $_SESSION['username'];
	if(!is_dir($userdir)) {
		mkdir($userdir);
	}
	$token = NoCSRF::generate( 'csrf_token' );
	#print'<pre>';
	#var_dump(dirToArray($userdir));exit;
    return $app['twig']->render('backend.twig', array(
    	'username' => $_SESSION['username'],
    	'contents' => array('Root'=>dirToArray($userdir)),
    	'token' => $token,
    	'masterpassword' => isset($_SESSION['masterpassword']) ? $_SESSION['masterpassword'] : ''
    ));
}); 

/*
$app->post('/upload', function() use($app, $db) {

	if(IsValidSession($db)) {
		$uploaddir = '../uploads/' . $_SESSION['username'] . "/";
		$uploadfile = $uploaddir . basename($_FILES['file']['name']);

		if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) {
			// Encrypt File
			$cmd = 'openssl aes-256-cbc -a -salt -in "' . $uploadfile . '" -out "'. $uploadfile . '.enc" -pass pass:'.$_POST['password'];
			$output = shell_exec($cmd);
			unlink($uploadfile);
			rename($uploadfile.".enc", $uploadfile);
		} else {

		}
	}
	return $app->redirect('/backend');
}); 
*/

$app->get('/', function() use($app) {
	$token = NoCSRF::generate( 'csrf_token' );
    return $app['twig']->render('login.twig', array(
    	'message' => $app['request']->get('message'),
    	'token' => $token,
    	'username' => ''
    ));
}); 

$app->get('/install', function() use($app) { 
	if(!file_exists('./data.db')) {
		$db->exec('CREATE TABLE accounts (username STRING, password STRING, hash STRING)');
		$db->exec("INSERT INTO accounts VALUES ('admin','d033e22ae348aeb5660fc2140aec35850c4da997','')");

		$content = "Installiert!<br>Nutzer:admin<br>Passwort:admin<p><a href='/'>Weiter</a></p>";
	} else {
		$content = $app->redirect('/');
	}
	return $content; 
}); 

$app->run(); 