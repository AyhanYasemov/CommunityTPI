<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $model app\models\Availability */
?>

<div class="availability-form">
<?php $form = ActiveForm::begin([
    'action' => ['availability/create'],
    'enableClientValidation' => true,
]); ?>
    <?= $form->field($model, 'start_date')->input('datetime-local', ['step' => 60]) ?>
    <?= $form->field($model, 'end_date')->input('datetime-local', ['step' => 60]) ?>

    <?= Html::submitButton('Sauvegarder', ['class' => 'btn btn-success']) ?>
    <?php ActiveForm::end(); ?>
</div>
