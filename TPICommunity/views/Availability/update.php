<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Availability $model */

$this->title = 'Mettre à jour la disponibilité: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Mes Disponibilités', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Mettre à jour';
?>
<div class="availability-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
