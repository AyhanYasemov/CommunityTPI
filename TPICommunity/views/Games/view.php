<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Games $model */

$this->title = $model->id_game;
$this->params['breadcrumbs'][] = ['label' => 'Games', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="games-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id_game], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id_game], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id_game', // Utilisation du bon nom de champ
            'release_date', // Mise à jour du champ publicationDate en release_date
            'name', // Mise à jour du champ gameName en name
            [
                'label' => 'Genre',
                'value' => $model->genres ? implode(', ', array_map(function($genres) { return $genres->name; }, $model->genres)) : 'N/A',
            ],
            [
                'label' => 'Platform',
                'value' => $model->platforms ? implode(', ', array_map(function($platforms) { return $platforms->name; }, $model->platforms)) : 'N/A',
            ],
        ],
    ]) ?>

</div>
