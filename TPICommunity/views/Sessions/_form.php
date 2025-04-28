<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Sessions $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="sessions-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'startTime')->textInput() ?>

    <?= $form->field($model, 'sessionName')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'endTime')->textInput() ?>

    <?= $form->field($model, 'FKgameID')->dropDownList(
        \yii\helpers\ArrayHelper::map(\app\models\Games::find()->all(), 'id', 'gameName'),
        ['prompt' => 'Select Game']
    ) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>