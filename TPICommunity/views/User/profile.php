<?php

use app\models\Genres;
use app\models\Platforms;
use yii\helpers\Html;
use yii\bootstrap4\Modal;
use yii\grid\GridView;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $user app\models\User */
/* @var $ownProvider yii\data\ActiveDataProvider */
/* @var $availProvider yii\data\ActiveDataProvider */
/* @var $prefProvider yii\data\ActiveDataProvider */

$this->title = 'Mon profil : ' . Html::encode($user->username);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-profile">

    <h1><?= Html::encode($this->title) ?></h1>

    <!-- Bibliothèque de jeux -->
    <h2>Ma bibliothèque</h2>
    <p>
        <?= Html::a('Ajouter un jeu', ['games/catalogue'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $ownProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'name',
            'release_date:date',
            [
                'class'      => 'yii\grid\ActionColumn',
                'controller' => 'games',
                'template'   => '{remove}',
                'buttons'    => [
                    'remove' => function ($url, $model, $key) {
                        return Html::a('Supprimer', ['games/remove-from-library', 'id' => $model->id_game], [
                            'data'    => ['method' => 'post', 'confirm' => 'Supprimer ce jeu ?'],
                            'class'   => 'btn btn-sm btn-danger',
                        ]);
                    },
                ],
            ],
        ],
    ]); ?>


    <!-- Disponibilités -->
    <h2>Mes disponibilités</h2>

    <p class="mb-3">
        <?= Html::beginForm(['availability/quick'], 'post', ['style' => 'display:inline']) ?>
        <?= Html::submitButton('Ajouter une disponibilité immédiate', [
            'class' => 'btn btn-warning',
            'data'  => ['confirm' => 'Etes-vous disponible les deux prochaines heures ?']
        ]) ?>
        <?= Html::endForm() ?>

        <!-- Bouton modal existant -->
        <?php
        Modal::begin([
            'title'        => 'Ajouter une disponibilité',
            'toggleButton' => [
                'label' => 'Nouvelle disponibilité',
                'class' => 'btn btn-success'
            ],
            'size'         => Modal::SIZE_LARGE,
        ]);
        echo $this->render('/availability/create', [
            'model' => new \app\models\Availability()
        ]);
        Modal::end();

        ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $availProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'start_date',
                'format'    => ['datetime', 'php:d/m/Y H:i']
            ],
            [
                'attribute' => 'end_date',
                'format'    => ['datetime', 'php:d/m/Y H:i']
            ],
            [
                'class'      => 'yii\grid\ActionColumn',
                'controller' => 'availability',
                'template'   => '{delete}',
                'buttons'    => [
                    'delete' => function ($url, $model, $key) {
                        return Html::a('Supprimer', ['availability/delete', 'id' => $model->id_availability], [
                            'data'  => ['method' => 'post', 'confirm' => 'Supprimer cette disponibilité ?'],
                            'class' => 'btn btn-sm btn-danger'
                        ]);
                    }
                ],
            ],
        ],
    ]); ?>


    <!-- Préférences -->
    <h2>Mes préférences</h2>

    <?php $form = ActiveForm::begin([
        'action' => ['user/update-preferences'],
        'method' => 'post',
    ]); ?>

    <?= $form->field($user, 'preferredGenreIds')->widget(Select2::class, [
        'data' => ArrayHelper::map(Genres::find()->all(), 'id_genre', 'name'),
        'options' => [
            'multiple' => true,
            'placeholder' => 'Sélectionnez vos genres…',
            'value' => $user->getPreferredGenres()->select('id_genre')->column(),
        ],
        'pluginOptions' => [
            'allowClear' => true,
            'dropdownAutoWidth' => true,
            'width'             => '100%',
        ],
    ]) ?>

    <?= $form->field($user, 'preferredPlatformIds')->widget(Select2::class, [
        'data' => ArrayHelper::map(Platforms::find()->all(), 'id_platform', 'name'),
        'options' => [
            'multiple' => true,
            'placeholder' => 'Sélectionnez vos plateformes…',
            'value' => $user->getPreferredPlatforms()->select('id_platform')->column(),
        ],
        'pluginOptions' => [
            'allowClear' => true,
            'dropdownAutoWidth' => true,
            'width'             => '100%',
        ],
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton('Enregistrer mes préférences', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>