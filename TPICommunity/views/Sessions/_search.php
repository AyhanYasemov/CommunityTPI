<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\SessionsSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="sessions-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'startTime') ?>

    <?= $form->field($model, 'sessionName') ?>

    <?= $form->field($model, 'endTime') ?>

    <?= $form->field($model, 'sessionStatus') ?>

    <?= $form->field($model, 'FKgameID') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>