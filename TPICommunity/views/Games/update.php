<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Games $model */

$this->title = 'Mise a jour jeux: ' . $model->id_game;
$this->params['breadcrumbs'][] = ['label' => 'Games', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id_game, 'url' => ['view', 'id' => $model->id_game]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="games-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
