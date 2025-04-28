<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Platforms $model */

$this->title = 'Create Platforms';
$this->params['breadcrumbs'][] = ['label' => 'Platforms', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="platforms-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
