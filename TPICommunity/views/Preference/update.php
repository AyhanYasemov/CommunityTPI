<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Preference $model */

$this->title = 'Update Preference: ' . $model->id_preference;
$this->params['breadcrumbs'][] = ['label' => 'Preferences', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id_preference, 'url' => ['view', 'id_preference' => $model->id_preference]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="preference-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
