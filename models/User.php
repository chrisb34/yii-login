<?php

class User extends CActiveRecord
{
	/**
	 * The followings are the available columns in table 'tbl_user':
	 * @var integer $id
	 * @var string $username
	 * @var string $password
	 * @var string $salt
	 * @var string $email
	 * @var string $profile
	 */
	public $repeatPassword;
	public $passwordSave;
	public $usernameLegal;
	
	const ROLE_ADMIN=5;
	const ROLE_EDITOR=3;
	const ROLE_AUTHOR=2;
	const ROLE_SUBSCRIBER=1;

	const STATUS_ACTIVE=1;
	
	const PASSWORD_EXPIRY=90;
	
	/**
	 * Returns the static model of the specified AR class.
	 * @return CActiveRecord the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{user}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function beforeValidate()
	{
	    $this->usernameLegal = preg_replace( '/[^A-Za-z0-9@_#?!&-]/' , '', $this->username );
	    return true;
	}
	
	public function validatePassword($password)
	{
	    return CPasswordHelper::verifyPassword($password,$this->password);
	}

	public function hashPassword($password)
	{
	    return CPasswordHelper::hashPassword($password);
	}

	protected function beforeSave()
	{
		if ( parent::beforeSave() )
		{
			if ( $this->isNewRecord )
			{	
				$this->created_dt = new CDbExpression("NOW()");
				if (!empty($this->passwordSave)&&!empty($this->repeatPassword)&&($this->passwordSave===$this->repeatPassword))
				    $password = $this->passwordSave;
				else
				    $password = rand(9999,999999);
				$this->password = $this->hashPassword( $password );
				$this->status=0;
				$this->password_expiry_date=new CDbExpression("DATE_ADD(NOW(), INTERVAL ".self::PASSWORD_EXPIRY." DAY) ");
				
				$this->activate = $this->hashPassword( rand(9999,999999) );
				
			}
			else if (!empty($this->passwordSave)&&!empty($this->repeatPassword)&&($this->passwordSave===$this->repeatPassword)) 
			//if it's not a new password, save the password only if it not empty and the two passwords match
			{
			    $this->password =  $this->hashPassword( $password );
			    $this->password_expiry_date=new CDbExpression("DATE_ADD(NOW(), INTERVAL ".self::PASSWORD_EXPIRY." DAY) ");
			}
			
			$this->last_login_time = new CDbExpression("NOW()");
			return true;
		}
		else
			return false;
	}


	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('passwordSave, repeatPassword', 'required', 'on'=>'insert'),
			array('username', 'checkUnique', 'on'=>'insert'),
			
			array('passwordSave, repeatPassword', 'length', 'min'=>6, 'max'=>40),
			array('passwordSave','checkStrength','score'=>20),
			array('passwordSave', 'compare', 'compareAttribute'=>'repeatPassword'),
 
			array('username, email,  role', 'required'),
			array('username, password, email, firstname, lastname', 'length', 'max'=>128),
			array('email','email'),
			array('last_login_time', 'safe','on'=>'validation'),
			array('username', 'compare', 'compareAttribute'=>'usernameLegal', 'message'=>'Username contains illegal characters','on'=>'validation'),
			
		    
			array('role', 'numerical',  'integerOnly'=>true),
			
			array('profile', 'safe'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		//	'posts' => array(self::HAS_MANY, 'Post', 'author_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'Id',
			'username' => 'Username',
			'firstname' => 'First Name',
			'lastname' => 'Last Name',
			'password' => 'Password',
			'salt' => 'Salt',
			'email' => 'Email',
			'profile' => 'Profile',
			'role' => 'Role'
		);
	}
	/**
	 * Compare Expiry date and today's date
	 * @return type - positive number equals valid user
	 */
	public function checkExpiryDate() {
		$expDate=DateTime::createFromFormat('Y-m-d H:i:s',$this->password_expiry_date);
		$today=new DateTime("now");
		return ($today->diff($expDate)->format('%a'));
	}

	public function getfullName() {
            $fullName=(!empty($this->firstname))? $this->firstname : '';
            $fullName.=(!empty($this->lastname))?( (!empty($fullName))? " ".$this->lastname : $this->lastname ) : '';
            return $fullName;
        }
	
	public function checkUnique($attribute,$params) {
	    $sql='Select count(*) from tbl_user where username=:username';
	    //DATE_ADD(:end ,INTERVAL 7 DAY)
	    $command = Yii::app()->db->createCommand($sql);

	    $username=$this->$attribute;
	    $command->bindParam(":username",$username,PDO::PARAM_STR);
	    $count=$command->queryScalar();
	    if ( $count > 0 )
		$this->addError($attribute,"This Username is not available or Valid"); 
	    else
		return true;
	   
	}
	/** score password strength
	 * where score is increased based on
	 * - password length
	 * - number of unqiue chars
	 * - number of special chars
	 * - number of numbers
	 * 
	 * A medium score is around 20
	 * 
	 * @param type $attribute
	 * @param type $params
	 * @return boolean 
	 */
	function CheckStrength($attribute,$params) 
	{
		$password=$this->$attribute;
		if ( strlen( $password ) == 0 )
		   return 20;
		else
		    $strength = 0;

		/*** get the length of the password ***/
		$length = strlen($password);

		/*** check if password is not all lower case ***/
		if(strtolower($password) != $password)
		{
		    $strength += 1;
		}

		/*** check if password is not all upper case ***/
		if(strtoupper($password) == $password)
		{
		    $strength += 1;
		}

		/*** check string length is 8 -15 chars ***/
		if($length >= 8 && $length <= 15)
		{
		    $strength += 2;
		}

		/*** check if lenth is 16 - 35 chars ***/
		if($length >= 16 && $length <=35)
		{
		    $strength += 2;
		}

		/*** check if length greater than 35 chars ***/
		if($length > 35)
		{
		    $strength += 3;
		}

		/*** get the numbers in the password ***/
		preg_match_all('/[0-9]/', $password, $numbers);
		$strength += count($numbers[0]);

		/*** check for special chars ***/
		preg_match_all('/[|!@#$%&*\/=?,;.:\-_+~^\\\]/', $password, $specialchars);
		$strength += sizeof($specialchars[0]);

		/*** get the number of unique chars ***/
		$chars = str_split($password);
		$num_unique_chars = sizeof( array_unique($chars) );
		$strength += $num_unique_chars * 2;

		/*** strength is a number 1-100; ***/
		$strength = $strength > 99 ? 99 : $strength;
		//$strength = floor($strength / 10 + 1);

		//fb($strength,"PASSWORD STRENGTH ".$password.": ");
		if ($strength<$params['score']) 
		        $this->addError($attribute,"Password is too weak - try using CAPITALS, Num8er5, AND sp€c!al characters. Your score was ".$strength." and minimum is ".$params['score']); 
		else
		    return true;
	}
	
	public function sendActivation() {
	    $name='=?UTF-8?B?'.base64_encode($this->username).'?=';
	    $subject='=?UTF-8?B?'.base64_encode('Signup Activation for Diary System').'?=';
	    $headers="From: Diary Admin <signup@diary-system.com>\r\n".
		    "Reply-To: {signup@diary-system.com}\r\n".
		    "MIME-Version: 1.0\r\n".
		    "Content-type: text/plain; charset=UTF-8";

	    $body="Dear ".$this->username."\r\n";
	    $body.="Thank you for signing up to diary-system.com. \r\n \r\n";
	    $body.="Please click this <a href='http://n5diary.lan/user/activate?a=$this->activate'>link</a> to activate your account \r\n";
	    $body.="or copy and paste the following link into your browser \r\n \r\n";
	    $body.="http://n5diary.lan/user/activate?a=".$this->activate." \r\n \r\n";
	    $body.="Many thanks";
	    $body.="Diary Admin";

	    return mail('signup@diary.com',$subject,$body,$headers);
				
	}
	
}