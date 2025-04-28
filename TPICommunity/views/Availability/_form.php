<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Availability $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="availability-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'startTime')->input('datetime-local', ['step' => '60']) ?>

<?= $form->field($model, 'endTime')->input('datetime-local', ['step' => '60']) ?>

    <!-- L'ID de l'utilisateur est rempli automatiquement par le modèle -->
    <?= $form->field($model, 'user_id')->hiddenInput()->label(false) ?>

    <div class="form-group">
        <?= Html::submitButton('Sauvegarder', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
