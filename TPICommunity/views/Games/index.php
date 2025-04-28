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

$this->title = 'Bibliothèque de jeux';
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

    <?php // Affichage des jeux avec cases à cocher 
    ?>
    <h3>Choisissez les jeux que vous possédez</h3>
    <?= Html::beginForm(['games/add-to-library'], 'post') ?>

    <?= Html::checkboxList(
        'games',
        ArrayHelper::getColumn(\app\models\Own::find()->where(['FKid_user' => Yii::$app->user->id])->all(), 'FKid_game'), // Les jeux que l'utilisateur possède déjà
        ArrayHelper::map(Games::find()->all(), 'id_game', 'name') // Liste de tous les jeux
    ) ?>

    <div class="form-group">
        <?= Html::submitButton('Sauvegarder dans votre bibliothèque', ['class' => 'btn btn-success']) ?>
    </div>

    <?= Html::endForm(); ?>

    <?php // Affichage de la liste des jeux pour l'admin 
    ?>
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
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
