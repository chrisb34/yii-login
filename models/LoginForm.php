<?php

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class LoginForm extends CFormModel
{
	public $username;
	public $password;
	public $rememberMe;

	private $_identity;

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
			// username always required
			array('username', 'required'),
		    
			// use a 'login' scenario for login validation
			array('password', 'required', 'on'=>'login'),
			// rememberMe needs to be a boolean
			array('rememberMe', 'boolean', 'on'=>'login'),
			// password needs to be authenticated
			array('password', 'authenticate', 'on'=>'login'),
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'rememberMe'=>'Remember me next time',
		);
	}

	/**
	 * Authenticates the password.
	 * This is the 'authenticate' validator as declared in rules().
	 */
	public function authenticate($attribute,$params)
	{
		if(!$this->hasErrors())
		{
			$this->_identity=new UserIdentity($this->username,$this->password);
			if(!$this->_identity->authenticate())
				$this->addError('password','Incorrect username or password.');
		}
	}

	/**
	 * Logs in the user using the given username and password in the model.
	 * @return boolean whether login is successful
	 */
	public function login()
	{
		if($this->_identity===null)
		{
			$this->_identity=new UserIdentity($this->username,$this->password);
			$this->_identity->authenticate();
		}
		if($this->_identity->errorCode===UserIdentity::ERROR_NONE)
		{
			$duration=$this->rememberMe ? 3600*24*30 : 0; // 30 days
			Yii::app()->user->login($this->_identity,$duration);
			return true;
		}
		else
			return false;
	}
	
	/**
	 *  reset user activation state for lost password 
	 */
	public function lostPassword() {
		$user=User::model()->find("LOWER(username) = '" . strtolower($this->username) . "'");
		if ( $user !== null ) {
		    $user->status=0;
		    $user->activate = $user->hashPassword( rand(9999,999999) );
		    if ($user->save())
			$user->sendActivation();
		}
		
	}
}
