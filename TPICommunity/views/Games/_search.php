<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\GameSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="games-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id_game') ?> 

    <?= $form->field($model, 'release_date') ?> 

    <?= $form->field($model, 'name') ?> 

    <?= $form->field($model, 'fkGenre_id') ?> 

    <?= $form->field($model, 'fkPlatform_id') ?> 

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
