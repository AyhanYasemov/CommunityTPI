<?php

use app\models\Participate;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\ParticipateSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Participates';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="participate-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Participate', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'FKid_user',
            'FKid_session',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Participate $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'FKid_user' => $model->FKid_user, 'FKid_session' => $model->FKid_session]);
                 }
            ],
        ],
    ]); ?>


</div>
