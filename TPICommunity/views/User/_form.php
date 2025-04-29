<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\UsersManagement $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="users-management-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'birthdate')->textInput() ?>

    <?= $form->field($model, 'creationDate')->$today = date("Y-m-d H:i:s"); // 2001-03-10 17:16:18 (the MySQL DATETIME format)?>

    <?= $form->field($model, 'lastUpdated')->$today = date("Y-m-d H:i:s"); // 2001-03-10 17:16:18 (the MySQL DATETIME format)?>


    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
