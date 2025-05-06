<?php
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Rejoindre une session';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="session-join">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class'=>'yii\grid\SerialColumn'],

            'name',

            [
                'label' => 'Jeu',
                'value' => function($m) {
                    return $m->game->name;
                },
            ],

            [
                'label' => 'Genre(s)',
                'value' => function($m) {
                    $names = ArrayHelper::getColumn($m->game->genres, 'name');
                    return implode(', ', $names);
                },
            ],
            [
                'label' => 'Plateforme(s)',
                'value' => function($m) {
                    $names = ArrayHelper::getColumn($m->game->platforms, 'name');
                    return implode(', ', $names);
                },
            ],
            'start_date:datetime',
            'end_date:datetime',

            [
                'class'=>'yii\grid\ActionColumn',
                'template'=>'{join}',
                'buttons'=>[
                    'join'=>function($url, $model) {
                        return Html::a('Rejoindre', ['do-join','id'=>$model->id_session], [
                            'class'=>'btn btn-sm btn-primary',
                            'data'=>[
                                'method'=>'post',
                                'confirm'=>'Vous voulez vraiment rejoindre cette session ?'
                            ]
                        ]);
                    }
                ],
            ],
        ],
    ]); ?>

</div>
