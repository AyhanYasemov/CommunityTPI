<?php

use yii\widgets\Pjax;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use app\models\Genres;
use app\models\Platforms;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel app\models\UserSearch  */  /* <- Il te faut un UserSearch qui étend ActiveRecord + ajoute les relations de filtre */

$this->title = 'Liste des joueurs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-playerlist">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,        // <-- on active les filtres
        'tableOptions' => ['class' => 'table table-striped'],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'username',

            // Filtre multi-sélection Genres
            [
                'label'     => 'Genres préférés',
                'attribute' => 'genreFilter',     // champs virtuel dans UserSearch
                'format'    => 'raw',
                'value'     => function ($user) {
                    $names = ArrayHelper::getColumn($user->preferredGenres, 'name');
                    return empty($names)
                        ? '<span class="text-muted">Aucun</span>'
                        : Html::encode(implode(', ', $names));
                },
                'filter'    => Select2::widget([
                    'model'         => $searchModel,
                    'attribute'     => 'genreFilter',
                    'data'          => ArrayHelper::map(Genres::find()->all(), 'id_genre', 'name'),
                    'options'       => ['placeholder' => 'Filtrer par genre...', 'multiple' => true],
                    'pluginOptions' => ['allowClear' => true],
                ]),
            ],

            // Filtre multi-sélection Plateformes
            [
                'label'     => 'Plateformes préférées',
                'attribute' => 'platformFilter',   // champs virtuel dans UserSearch
                'format'    => 'raw',
                'value'     => function ($user) {
                    $names = ArrayHelper::getColumn($user->preferredPlatforms, 'name');
                    return empty($names)
                        ? '<span class="text-muted">Aucune</span>'
                        : Html::encode(implode(', ', $names));
                },
                'filter'    => Select2::widget([
                    'model'         => $searchModel,
                    'attribute'     => 'platformFilter',
                    'data'          => ArrayHelper::map(Platforms::find()->all(), 'id_platform', 'name'),
                    'options'       => ['placeholder' => 'Filtrer par plateforme...', 'multiple' => true],
                    'pluginOptions' => ['allowClear' => true],
                ]),
            ],

            [
                'label'  => 'Statut',
                'format' => 'raw',
                'value'  => function ($user) {
                    switch ($user->ComputedStatus) {
                        case 4:
                            return '<span class="badge bg-warning">En session</span>';
                        case 3:
                            return '<span class="badge bg-success">Disponible</span>';
                        case 2:
                            return '<span class="badge bg-info">Connecté</span>';
                        default:
                            return '<span class="badge bg-secondary">Déconnecté</span>';
                    }
                },
                // on peut aussi filtrer sur le statut
                'filter' => [
                    1 => 'Déconnecté',
                    2 => 'Connecté',
                    3 => 'Disponible',
                    4 => 'En session',
                ],
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>