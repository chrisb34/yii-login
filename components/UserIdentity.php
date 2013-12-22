<?php
/**
 * class UserIdentity
 * @author Igor Ivanović
 * Main extended core yii framework class used for auth system 
 */
class UserIdentity extends CUserIdentity
{

private $_id;

	/** 
	 * Used to create an identity when you know that it has already
	 * been authenticated
	 * eg: on activation using activation code
	 * 
	 * @param type $username
	 * @return \self 
	 */
	
	public static function createAuthenticatedIdentity($username,$id)
	{
		$identity=new self($username,'');
		$identity->errorCode=self::ERROR_NONE;
		$identity->_id=$id;
		return $identity;
	}
	
	
	public function authenticate()
	{
		$user=User::model()->find("LOWER(username) = '" . strtolower($this->username) . "'");
		if ( $user === null ) 
			$this->errorCode = self::ERROR_USERNAME_INVALID;
		
		else if ($user->password !== crypt($this->password, $user->password))
			$this->errorCode = self::ERROR_PASSWORD_INVALID;
		
		else
		{
			$this->_id = $user->id;
			$this->username = $user->username;
			$this->errorCode = self::ERROR_NONE;
		}
		return $this->errorCode == self::ERROR_NONE;
	}

	/**
	 * @return integer the ID of the user record
	 */
	public function getId()
	{
		return $this->_id;
	}
}
