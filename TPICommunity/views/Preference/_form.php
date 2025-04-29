<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Preference $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="preference-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'FKid_user')->textInput() ?>

    <?= $form->field($model, 'FKid_game')->textInput() ?>

    <?= $form->field($model, 'FKid_genre')->textInput() ?>

    <?= $form->field($model, 'level')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
