<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Inscription';
$this->params['breadcrumbs'][] = $this->title;

$session = Yii::$app->session;
$errors = $session->getFlash('errorMessages');
$success = $session->getFlash('successMessage');
if(isset($errors) && (count($errors) > 0))
{
    foreach($session->getFlash('errorMessages') as $error)
    {
        echo "<div class='alert alert-danger' role='alert'>$error[0]</div>";
    }
}

if(isset($success))
{
    echo "<div class='alert alert-success' role='alert'>$success</div>";
}

?>
<?php
$this->registerCss("
    .required label::after {
        content: ' *';
        color: red;
    }
");
?>
<div class="site-signup">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Veuillez remplir les champs suivants pour vous inscrire :</p>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin([ 'id' => 'signup-form' ]); ?> 

                <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>

                <?= $form->field($model, 'email')->input('email') ?>

                <?= $form->field($model, 'password')->passwordInput(['value' => '']) ?>

                <?= $form->field($model, 'birthdate')->input('date') ?> 

                <div class="form-group">
                    <?= Html::submitButton('S\'inscrire', ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
                </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
