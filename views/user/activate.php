<?php
/* @var $this UsersController */
/* @var $model Users */

$this->breadcrumbs=array(
	'Users'=>array('index'),
	$model->username,
);
?>

<h1>Welcome <?php echo $model->firstname; ?></h1>

<?php 
    if ($status=='success') {
		echo "<p>Your login has been activated.  You may now access the Diary System.</p>";
	} else 
	echo "<p>I think you already activated your account - You can login normally ".CHtml::link('here', '/site/login')."</p>";
?>
