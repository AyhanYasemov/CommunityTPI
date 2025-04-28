<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Availability $model */

$this->title = 'Créer une disponibilité';
$this->params['breadcrumbs'][] = ['label' => 'Mes Disponibilités', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="availability-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
