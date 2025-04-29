<?php
use yii\helpers\Html;
use yii\grid\GridView;

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
            'class' => 'yii\grid\ActionColumn',
            'controller' => 'games',
            'template' => '{remove}',
            'buttons' => [
                'remove' => function ($url, $model) {
                    return Html::a('Supprimer', ['games/remove-from-library', 'id' => $model->id_game], [
                        'data' => ['method' => 'post'],
                    ]);
                },
            ],
        ],
    ],
]); ?>


    <h2>Mes disponibilités</h2>
    <?= GridView::widget([
        'dataProvider' => $availProvider,
        'columns' => [
            ['class' => 'yii\\grid\\SerialColumn'],
            'start_date:datetime',
            'end_date:datetime',
            ['class' => 'yii\\grid\\ActionColumn', 'controller' => 'availability'],
        ],
    ]); ?>

    <h2>Mes préférences</h2>
    <?= GridView::widget([
        'dataProvider' => $prefProvider,
        'columns' => [
            ['class' => 'yii\\grid\\SerialColumn'],
            [
                'label' => 'Genre',
                'value' => function ($model) { return $model->genre->name; },
            ],
            [
                'label' => 'Jeu',
                'value' => function ($model) { return $model->game->name; },
            ],
            'level',
            ['class' => 'yii\\grid\\ActionColumn', 'controller' => 'preference'],
        ],
    ]); ?>

</div>
