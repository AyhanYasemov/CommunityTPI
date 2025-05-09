<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Session $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Sessions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="session-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id_session' => $model->id_session], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id_session' => $model->id_session], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'êtes-vous sur de vouloir supprimer ?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id_session',
            'start_date',
            'end_date',
            'status',
            'FKid_host',
            'name',
            'FKid_game',
        ],
    ]) ?>

</div>
