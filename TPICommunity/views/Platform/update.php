<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Platforms $model */

$this->title = 'Update Platforms: ' . $model->platformName;
$this->params['breadcrumbs'][] = ['label' => 'Platforms', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->platformName, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="platforms-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
