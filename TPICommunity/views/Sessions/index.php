<?php

use app\models\Sessions;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/** @var app\models\SessionsSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Sessions';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sessions-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Sessions', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'startTime',
            'sessionName',
            'endTime',
            [
                'attribute' => 'sessionStatus',
                'value' => function ($model) {
                    switch ($model->sessionStatus) {
                        case 1:
                            return 'Actif';
                        case 2:
                            return 'Prochainement';
                        case 3:
                            return 'Effectué';
                        case 4:
                            return 'Annulée';
                        default:
                            return 'Inconnu';
                    }
                },
            ],
            'FKgameID',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Sessions $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>