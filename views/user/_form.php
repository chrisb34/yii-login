<?php
/* @var $this UsersController */
/* @var $model Users */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'users-form',
	'enableAjaxValidation'=>true,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>
	<?php 
            foreach (Yii::app()->user->getFlashes() as $type=>$flash) {
                echo "<div class='{$type}'>{$flash}</div>";
            }
        ?>
	<?php echo $form->errorSummary($model); ?>

	<div class="block">

	    <?php if ($model->isNewRecord) { ?>
	    <div class="row">
		    <?php echo $form->labelEx($model,'username'); ?>
		    <?php echo $form->textField($model,'username',array('size'=>60,'maxlength'=>128)); ?>
		    <?php echo $form->error($model,'username'); ?>
	    </div>
	    <?php } ?>
	    
	    <div class="row">
		    <?php echo $form->labelEx($model,'firstname'); ?>
		    <?php echo $form->textField($model,'firstname',array('size'=>60,'maxlength'=>128)); ?>
		    <?php echo $form->labelEx($model,'lastname'); ?>
		    <?php echo $form->textField($model,'lastname',array('size'=>60,'maxlength'=>128)); ?>
		    <?php echo $form->error($model,'firstname'); ?>
	    </div>


	    <div class="row">
		    <?php echo $form->labelEx($model,'email'); ?>
		    <?php echo $form->textField($model,'email',array('size'=>60,'maxlength'=>128)); ?>
		    <?php echo $form->error($model,'email'); ?>
	    </div>

	    <?php if ($model->isNewRecord && Yii::app()->user->isAdmin()) { ?>
	    <div class="row">
		    <?php echo $form->labelEx($model,'role'); ?>
		    <?php echo $form->textField($model,'role'); ?>
		    <?php echo $form->error($model,'role'); ?>
	    </div>
	    <?php } ?>
	</div>
	<div class="block">

	    <div class="row">
		    <?php echo $form->labelEx($model,'passwordSave'); ?>
		    <?php echo $form->passwordField($model,'passwordSave',array('size'=>60,'maxlength'=>256)); ?>
		    <?php echo $form->error($model,'passwordSave'); ?>
	    </div>
	    <div class="row">
		    <?php echo $form->labelEx($model,'repeatPassword'); ?>
		    <?php echo $form->passwordField($model,'repeatPassword',array('size'=>60,'maxlength'=>256)); ?>
		    <?php echo $form->error($model,'repeatPassword'); ?>
	    </div>

	    <div class="row buttons">
		    <?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	    </div>
	</div>
	
	<br class="clear">
	
<?php $this->endWidget(); ?>

</div><!-- form -->