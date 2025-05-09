<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use app\models\Games;
use app\models\Platforms;
use app\models\User;
use yii\helpers\Url;
use kartik\depdrop\DepDrop;

?>

<div class="session-form">

    <?php $form = ActiveForm::begin(); ?>


    <?= \yii\helpers\Html::a('⬅️ Retour', ['index'], ['class' => 'btn btn-secondary']) ?>

    <?= $form->field($model, 'name')->textInput() ?>

    <?= $form->field($model, 'start_date')
        ->input('datetime-local', ['id' => 'start-id', 'step' => 60]) ?>
    <?= $form->field($model, 'end_date')
        ->input('datetime-local', ['id' => 'end-id', 'step' => 60]) ?>


    <?= $form->field($model, 'FKid_game')->widget(Select2::class, [
        'data' => ArrayHelper::map(Games::find()->all(), 'id_game', 'name'),
        'options' => [
            'id' => 'game-id',              // <-- nécessaire pour DepDrop.depends
            'placeholder' => 'Choisissez un jeu…',
        ],
        'pluginOptions' => ['allowClear' => true],
    ]) ?>


    <?= $form->field($model, 'platformIds')->widget(DepDrop::class, [
        'type' => DepDrop::TYPE_SELECT2,
        'data' => $model->isNewRecord
            ? [] // rien au premier chargement
            : ArrayHelper::map($model->platforms, 'id_platform', 'name'),
            
            'options'        => ['multiple' => true, 'id' => 'platform-id'],        'select2Options' => [
            'pluginOptions' => ['allowClear' => true],
            'options' => ['placeholder' => 'Sélectionnez des plateformes…']
        ],
        'pluginOptions' => [
            'depends'    => ['game-id'],                    // le champ Dépendant
            'placeholder' => 'Choisissez d’abord un jeu…',
            'url'        => Url::to(['session/platform-list']),  // ton endpoint JSON
        ],
    ]) ?>



    <!-- Champ Participants, chargé par DepDrop -->
    <?= $form->field($model, 'participantIds')->widget(DepDrop::class, [
    'type'           => DepDrop::TYPE_SELECT2,
    // data doit impérativement être un tableau clé=>valeur
    'data'           => $model->isNewRecord
        ? []
        : ArrayHelper::map($model->participants, 'id_user', 'username'),
    'options'        => [
        'multiple' => true,
        'id'       => 'participant-id',
        'placeholder' => 'Choisissez des participants…',
    ],
    'select2Options' => [
        'pluginOptions' => ['allowClear' => true],
    ],
    'pluginOptions'  => [
        'depends'      => ['game-id', 'start-id', 'end-id'],
        'depdropParams'=> ['game-id', 'start-id', 'end-id'],
        'placeholder'  => 'Sélectionnez un jeu, une date de début et de fin…',
        'url'          => Url::to(['session/participant-list']),
        'loadingText'  => 'Chargement…',
    ],
]) ?>




    <div class="form-group">
        <?= Html::submitButton('Sauvegarder', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>