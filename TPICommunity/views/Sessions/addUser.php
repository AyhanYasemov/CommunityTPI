<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\User; // On récupère la liste des utilisateurs

$this->title = 'Add Users to Session';
$this->params['breadcrumbs'][] = ['label' => 'Sessions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="session-add-users">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'users')->checkboxList(
        ArrayHelper::map(User::find()->all(), 'id', 'username')
    )->label('Select Users to Add'); ?>

    <div class="form-group">
        <?= Html::submitButton('Add Users', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>