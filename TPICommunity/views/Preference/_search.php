<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\PreferenceSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="preference-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id_preference') ?>

    <?= $form->field($model, 'FKid_user') ?>

    <?= $form->field($model, 'FKid_game') ?>

    <?= $form->field($model, 'FKid_genre') ?>

    <?= $form->field($model, 'level') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
