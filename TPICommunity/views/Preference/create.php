<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Preference $model */

$this->title = 'Create Preference';
$this->params['breadcrumbs'][] = ['label' => 'Preferences', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="preference-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
