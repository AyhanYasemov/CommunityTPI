<?php

use app\models\Genres;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/** @var app\models\GenreSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Genre';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="genres-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Créer genre de jeu', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id_genre',
            'name',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Genres $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id_genre]);
                 }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
