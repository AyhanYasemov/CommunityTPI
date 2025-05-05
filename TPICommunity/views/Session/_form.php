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

    <?= $form->field($model,'name')->textInput() ?>

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
    'options' => ['multiple' => true],
    'select2Options' => [
        'pluginOptions' => ['allowClear' => true],
        'options' => ['placeholder' => 'Sélectionnez des plateformes…']
    ],
    'pluginOptions' => [
        'depends'    => ['game-id'],                    // le champ Dépendant
        'placeholder'=> 'Choisissez d’abord un jeu…',
        'url'        => Url::to(['session/platform-list']),  // ton endpoint JSON
    ],
]) ?>


<?= $form->field($model,'participantIds')->widget(Select2::class, [
    'data' => ArrayHelper::map(
        User::find()
            ->where(['<>', 'id_user', Yii::$app->user->id]) // <-- exclut l’hôte
            ->all(),
        'id_user',
        'username'
    ),
    'options'=>['placeholder'=>'Ajouter des participants...','multiple'=>true],
    'pluginOptions'=>['allowClear'=>true],
]) ?>


    <?= $form->field($model,'start_date')->input('datetime-local',['step'=>60]) ?>
    <?= $form->field($model,'end_date')->input('datetime-local',['step'=>60]) ?>

    <div class="form-group">
        <?= Html::submitButton('Sauvegarder',['class'=>'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
