<?php
class rWebUser extends CWebUser {
   private $model = null;
 
    public function getModel()
    {
        if(!isset($this->id)) $this->model = new User;
        if($this->model === null)
            $this->model = User::model()->findByPk($this->id);
	//d2l($this->model->attributes,"rWebUser.getModel");
	return $this->model;
    }
 
    public function __get($name) {
        try {
            return parent::__get($name);
        } catch (CException $e) {
	    //d2l("going to model");
            $m = $this->getModel();
            if($m->__isset($name))
                return $m->{$name};
            else throw $e;
        }
    }
 
    public function __set($name, $value) {
        try {
            return parent::__set($name, $value);
        } catch (CException $e) {
            $m = $this->getModel();
            $m->{$name} = $value;
        }
    }
 
    public function __call($name, $parameters) {
        try {
            return parent::__call($name, $parameters);  
        } catch (CException $e) {
            $m = $this->getModel();
            return call_user_func_array(array($m,$name), $parameters);
        }
    }
     public function isAdmin(){
	$user = $this->getModel();
	$res=false;
	if ($user!==null && $user->role==5) $res=true;
	//d2l($res,"rWebUser.isAdmin");
	return $res;
    }
    


    
    /**
    // Store model to not repeat query.
    private $_model;
    // Return first name.
    // access it by Yii::app()->user->first_name
    function getFullName(){
	$user = $this->loadUser(Yii::app()->user->id);
	return $user->fullName();
    }
    function getFirstName(){
	$user = $this->loadUser(Yii::app()->user->id);
	return $user->firstname;
    }
    function getRole(){
	$user = $this->loadUser(Yii::app()->user->id);
	return $user->role;
    }
    function getPage(){
	$user = $this->loadUser(Yii::app()->user->id);
	return $user->pagination;
    }
    function getPasswordExpires(){
	$user = $this->loadUser(Yii::app()->user->id);
	return $user->checkExpiryDate();
    }
    
    // This is a function that checks the field 'role'
    // in the User model to be equal to 1, that means it's admin
    // access it by Yii::app()->user->isAdmin()
    function isAdmin(){
	$user = $this->loadUser(Yii::app()->user->id);
	if ($user!==null)
	    return intval($user->role) == User::ROLE_ADMIN;
	else return false;
    }

    // Load user model.
    protected function loadUser($id=null)
    {
	if($this->_model===null)
	{
	    if($id!==null)
		$this->_model=User::model()->findByPk($id);
	}

	return $this->_model;
    }        

       **/ 
}?>
