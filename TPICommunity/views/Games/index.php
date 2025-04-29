<?php

use app\models\Game;
use app\models\Games;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;

/** @var yii\web\View $this */
/** @var app\models\GameSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var bool $catalogueMode */

$catalogueMode = $catalogueMode ?? false;

$this->title = $catalogueMode ?? false ? 'Catalogue des jeux' : 'Liste des jeux';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="games-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (Yii::$app->user->identity->type === 'admin'): ?>
        <p>
            <?= Html::a('Créer un jeu', ['create'], ['class' => 'btn btn-success']) ?>
        </p>
    <?php endif; ?>

    <?php Pjax::begin(); ?>



    <?php // Affichage de la liste des jeux pour l'admin 
    ?>

    <?php if (!empty($catalogueMode)): ?>
        <p>Jeux que vous pouvez ajouter à votre bibliothèque :</p>
    <?php endif; ?>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'release_date',
                'label' => 'Date de sortie',
            ],
            [
                'attribute' => 'name',  // Nom du jeu
                'label' => 'Titre',
            ],

            // 🔹 Afficher les genres associés sous forme de texte
            [
                'attribute' => 'fkGenre_id',  // Relation avec la table GAME_GENRE
                'label' => 'Genres',
                'value' => function ($model) {
                    $genres = ArrayHelper::getColumn($model->genres, 'name');
                    return !empty($genres) ? implode(', ', $genres) : 'Aucun';
                },
            ],

            // 🔹 Afficher les plateformes associées sous forme de texte
            [
                'attribute' => 'fkPlatform_id',  // Relation avec la table GAME_PLATFORM
                'label' => 'Plateformes',
                'value' => function ($model) {
                    $platforms = ArrayHelper::getColumn($model->platforms, 'name');
                    return !empty($platforms) ? implode(', ', $platforms) : 'Aucune';
                },
            ],

            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Games $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id_game]);
                },
                'visible' => Yii::$app->user->identity->type === 'admin', // visible seulement par les administrateurs
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => $catalogueMode ? '{add}' : '{view} {update} {delete}',
                'buttons' => [
                    'add' => function ($url, $model) {
                        return Html::a('Ajouter', ['games/add-single-to-library', 'id' => $model->id_game], [
                            'class' => 'btn btn-success btn-sm',
                            'data' => ['method' => 'post'],
                        ]);
                    },
                ],
            ],
            
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>