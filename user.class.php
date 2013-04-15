<?php

/** 
 * A simple class 
 * 
 * A simple user class.
 * It's my first class.
 * 
 * @author Erdem Ece <erdem@erdemece.com> 
 * @copyright 2013 
 */


class User
{

	var $Username = '';
	var $Email = '';
	var $Password = '';
	var $Salt = '';

	protected $_username;
	protected $_email;
	protected $_password;
	protected $_salt;

	protected $_db;
	protected $_user;

	public function __construct(PDO $db, $Username, $Email, $Password, $Salt)
	{
		$this->_db = $db;
		$this->_username = $Username;
		$this->_email = $Email;
		$this->_password = $Password;
		$this->_salt = $Salt;

	}

	private function saltString($Password) // Creates random string for password
	{
		$Characters = 'PEcN0';
		$string = md5( $Password . $Characters ); // I want to use bcrypt

		return $string;
	}

	public function login() // checks if CheckCredentials is true. if it's true it creates sessions
	{
		$user = $this->_CheckCredentials();
		if($user){
			$this->_user = $user;
			$_SESSION['UserID'] = $user['UserID'];
			$_SESSION['Username'] = $user['Username'];
			$_SESSION['Level'] = $user['Level'];
			$_SESSION['Reputation'] = $user['Reputation'];
			return $user['UserID'];
		}
		return false;
	}

	public function register() // checks if addUser is true.
	{
		$user = $this->_addUser();
		if($user){
			$this->createSession($user);	
		}
		return false;
	}

	public function _CheckCredentials() // checks password and username for login method
	{
		$sql = $this->_db->prepare("SELECT * FROM users WHERE Username = :Username");
		$sql->bindParam(':Username', $this->_username, PDO::PARAM_STR);
		$sql->execute();

		if($sql->rowCount() > 0){
			$user = $sql->fetch(PDO::FETCH_ASSOC);
			$pass = $this->saltString($this->_password);
			if($pass == $user['Password']){
				$sql = $this->_db->query( "UPDATE users SET LastLogin = NOW() WHERE UserID = '" . $user['UserID'] . "'" );
				return $user;
			}
		}
		return false;
	}

	public function _addUser() // add user into database
	{
		$sql = $this->_db->query( "SELECT * FROM users WHERE Username = :Username OR Email = :Email" );
		$sql->bindParam(':Username', $this->_username, PDO::PARAM_STR);
		$sql->bindParam(':Email', $this->_email, PDO::PARA_STR);
		$sql->execute();

		if($sql->rowCount()){
			$user = $sql->fetch(PDO::FETCH_ASSOC);
			if($user['Username'] == $this->_username){
				throw new ExampleException("This username is being used");
			}else{
				throw new ExampleException("E-mail address is being used");
			}
		}else{
			$intert = $sql->query( "INSERT INTO users ( UserID, OauthUid, OauthProvider, Username, Password, Email, CookieKey, Level, Registered, LastLogin, Reputation, Contents )
			VALUES ( NULL, '". $UserID ."', '". $Provider ."', '" . $Username . "', '" . $Password . "', '" . $Email . "', '" . generateKey( 5 ) . "', '1', NOW(), NOW(), '0', '0')" );

			$UserID = $sql->lastInsertId();

			if($instert){
				$_SESSION['UserID'] = $UserID;
				$_SESSION['Username'] = $Username;
				$_SESSION['Level'] = '1';
			}
		}
		return false;

	}

	public function getUser()
	{
		return $this->_username;
	}

}

?>