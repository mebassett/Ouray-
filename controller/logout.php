<?php
/*	logout.php
 * 	ends a user's session
 *  written by gegn corp (http://www.gegn.net)
 */
	$user = new User("fromSession");
	$user->logout();
	include('main.php');
?>
