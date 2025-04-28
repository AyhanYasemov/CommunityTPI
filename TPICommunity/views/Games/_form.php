<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use app\models\Platforms;  
use app\models\Genres;  

/** @var yii\web\View $this */
/** @var app\models\Games $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="games-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'release_date')->input('date') ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?> 

    <?php // Pour les genres ?>
    <?= $form->field($model, 'fkGenre_id')->checkboxList(
    ArrayHelper::map(Genres::find()->all(),    'id_genre',    'name'),
    ['prompt' => 'Sélectionnez les genres']
) ?>

<?= $form->field($model, 'fkPlatform_id')->checkboxList(
    ArrayHelper::map(Platforms::find()->all(), 'id_platform', 'name'),
    ['prompt' => 'Sélectionnez les plateformes']
) ?>


    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
