<?php

class Auth {

	protected $db;

	public function Auth($db) {
		$this->db = $db;
	}

	public function Login($username, $password) {
		$results = $this->db->query('SELECT * FROM accounts WHERE username LIKE "'. $username .'" AND password LIKE "' . sha1($password) . '"');
		$result = $results->fetchArray();

		if(!empty($result)) {
			$date = new DateTime();
			$hash = sha1($username . ($date->getTimestamp()) );
			setcookie('session',$hash, time()+60*60*1);
			$updatedSession = $this->db->query('UPDATE accounts SET  hash="'. $hash .'" WHERE username="'. $username .'"');
			return true;
		}

		return false;
	}

	public function Check($hash) {
		$results = $this->db->query('SELECT * FROM accounts WHERE hash="' . $hash . '"');
		$result = $results->fetchArray();
		$_SESSION['username'] = $result['username'];
		return !empty($result);
	}
}