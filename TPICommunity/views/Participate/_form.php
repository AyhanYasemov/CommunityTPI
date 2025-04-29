<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Participate $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="participate-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'FKid_user')->textInput() ?>

    <?= $form->field($model, 'FKid_session')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
