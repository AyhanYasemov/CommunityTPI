<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use app\models\Platforms;
use app\models\Genres;
use kartik\select2\Select2;
/** @var yii\web\View $this */
/** @var app\models\Games $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="games-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'release_date')->input('date') ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fkGenre_id')->widget(Select2::class, [
    'data' => ArrayHelper::map(Genres::find()->all(), 'id_genre', 'name'),
    'options' => ['multiple' => true, 'placeholder' => 'Sélectionnez les genres...'],
    'pluginOptions' => [
        'allowClear' => true,
    ],
]) ?>

<?= $form->field($model, 'fkPlatform_id')->widget(Select2::class, [
    'data' => ArrayHelper::map(Platforms::find()->all(), 'id_platform', 'name'),
    'options' => ['multiple' => true, 'placeholder' => 'Sélectionnez les plateformes...'],
    'pluginOptions' => [
        'allowClear' => true,
    ],
]) ?>
    <div class="form-group">
        <?= Html::submitButton('Sauvegarder', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
