<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Participate $model */

$this->title = 'Update Participate: ' . $model->FKid_user;
$this->params['breadcrumbs'][] = ['label' => 'Participates', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->FKid_user, 'url' => ['view', 'FKid_user' => $model->FKid_user, 'FKid_session' => $model->FKid_session]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="participate-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
