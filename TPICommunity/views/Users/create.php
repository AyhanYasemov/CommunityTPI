<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\UsersManagement $model */

$this->title = 'Create Users Management';
$this->params['breadcrumbs'][] = ['label' => 'Users Managements', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="users-management-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
