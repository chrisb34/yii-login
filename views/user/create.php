<?php
/* @var $this UsersController */
/* @var $model Users */

$this->breadcrumbs=array(
	'Users'=>array('index'),
	'Create',
);
?>
<h1>Sign-up Form</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>