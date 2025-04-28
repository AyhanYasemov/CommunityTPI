<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Platforms $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="platforms-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'platformName')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
