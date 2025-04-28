<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Games $model */

$this->title = 'Créer un jeu';
$this->params['breadcrumbs'][] = ['label' => 'Games', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="games-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
