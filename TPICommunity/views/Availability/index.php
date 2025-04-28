<?php

use app\models\Availability;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var app\models\AvailabilitySearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Mes Disponibilités';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="availability-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <!-- Bouton pour ajouter une nouvelle disponibilité -->
    <p>
        <?= Html::a('Ajouter une disponibilité', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>

    <!-- Affichage du formulaire de recherche -->
    <?= $this->render('_search', ['model' => $searchModel]); ?>

    <!-- Affichage du GridView pour lister les disponibilités -->
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'id',
                'filter' => false,

            ],


            [
                'attribute' => 'startTime',
                'format' => ['datetime'],
                'filter' => false, // Désactive le champ de recherche
            ],
            [
                'attribute' => 'endTime',
                'format' => ['datetime'],
                'filter' => false, // Désactive le champ de recherche
            ],
            [
                'attribute' => 'user_id',
                'value' => function ($model) {
                    return $model->user ? $model->user->username : 'Utilisateur inconnu';
                },
                'label' => 'Utilisateur',
                'filter' => false, // Désactive le champ de recherche
            ],

            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Availability $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>