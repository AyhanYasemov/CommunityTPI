<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\AvailabilitySearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="availability-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'startTime')->input('datetime-local', ['step' => '60']) ?>

    <?= $form->field($model, 'endTime')->input('datetime-local', ['step' => '60']) ?>

    <!--  $form->field($model, 'user_id')->dropDownList(
        \yii\helpers\ArrayHelper::map(app\models\User::find()->all(), 'id', 'username'),
        ['prompt' => 'Sélectionner un utilisateur']
    )  -->

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Reset', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script>
function resetForm() {
    // Réinitialisation des champs du formulaire
    $('#search-form')[0].reset();

    // Soumettre le formulaire via PJAX pour recharger les résultats
    $.pjax.reload({container: '#pjax-container'}); // Remplacez '#pjax-container' par l'ID de la zone PJAX
}
</script>