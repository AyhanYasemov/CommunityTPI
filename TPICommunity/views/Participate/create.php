<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Participate $model */

$this->title = 'Create Participate';
$this->params['breadcrumbs'][] = ['label' => 'Participates', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="participate-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
