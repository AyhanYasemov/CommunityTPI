<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Genres $model */

$this->title = 'Update Genres: ' . $model->id_genre;
$this->params['breadcrumbs'][] = ['label' => 'Genres', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id_genre, 'url' => ['view', 'id' => $model->id_genre]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="genres-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
