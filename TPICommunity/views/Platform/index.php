<?php

use app\models\Platforms;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/** @var app\models\PlatformsSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Plateformes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="platforms-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Créer plateforme de jeu', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id_platform',
            'name',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Platforms $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id_platform]);
                 }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
