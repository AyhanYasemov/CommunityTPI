<?php
/**
 * @var yii\web\View $this
 * @var app\models\LoginForm $model
 */
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

$this->title = 'Connexion';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="site-login">
    <h1><?= Html::encode($this->title) ?></h1>
    <p>Veuillez remplir les champs suivants pour vous connecter :</p>
    
    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
        'layout' => 'horizontal',
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'labelOptions' => ['class' => 'col-lg-1 col-form-label mr-lg-3'],
            'inputOptions' => ['class' => 'col-lg-3 form-control'],
            'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
        ],
    ]); ?>
    
    <?= $form->field($user, 'email')->input('email') ?>
    
    <?= $form->field($user, 'password')->passwordInput() ?>
    <?= $form->field($user, 'rememberMe')->checkbox([
        'template' => "<div class=\"offset-lg-1 col-lg-3 custom-control custom-checkbox\">{input} {label}</div>\n<div class=\"col-lg-8\">{error}</div>",
    ]) ?>
    
    <div class="form-group">
        <div class="offset-lg-1 col-lg-11">
            <?= Html::submitButton('Connexion', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
        </div>
    </div>
    
    <?php ActiveForm::end(); ?>
</div>
