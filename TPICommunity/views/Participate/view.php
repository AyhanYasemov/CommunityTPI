<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Participate $model */

$this->title = $model->FKid_user;
$this->params['breadcrumbs'][] = ['label' => 'Participates', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="participate-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'FKid_user' => $model->FKid_user, 'FKid_session' => $model->FKid_session], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'FKid_user' => $model->FKid_user, 'FKid_session' => $model->FKid_session], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'FKid_user',
            'FKid_session',
        ],
    ]) ?>

</div>
