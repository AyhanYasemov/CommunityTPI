<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\widgets\Alert;
use yii\bootstrap4\Breadcrumbs;
use yii\bootstrap4\Html;
use yii\bootstrap4\Nav;
use yii\bootstrap4\NavBar;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">

<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>

<body class="d-flex flex-column h-100">
    <?php $this->beginBody() ?>

    <header>
        <?php
        NavBar::begin([
            'brandLabel' => Yii::$app->name,
            'brandUrl' => Yii::$app->homeUrl,
            'options' => [
                'class' => 'navbar navbar-expand-lg navbar-dark bg-dark',
            ],
        ]);
        echo Nav::widget([
            'options' => ['class' => 'navbar-nav mr-auto'], // Partie gauche (principal)
            'items' => [
                ['label' => 'Page Principale', 'url' => ['/site/index']],

                !Yii::$app->user->isGuest ? ['label' => 'Sessions', 'url' => ['/session/index']] : '',
                !Yii::$app->user->isGuest ? ['label' => 'Liste des joueurs', 'url' => ['/user/player-list']] : '',
                !Yii::$app->user->isGuest && Yii::$app->user->identity->type !== 'USER' ? ['label' => '(Admin)Jeux', 'url' => ['/games/index']] : '',
                !Yii::$app->user->isGuest && Yii::$app->user->identity->type !== 'USER' ? ['label' => '(Admin)Genre', 'url' => ['/genre/index']] : '',
                !Yii::$app->user->isGuest && Yii::$app->user->identity->type !== 'USER' ? ['label' => '(Admin)Plateforme', 'url' => ['/platform/index']] : '',
            ],
        ]);

        echo Nav::widget([
            'options' => ['class' => 'navbar-nav ml-auto'], // Partie droite (connexion/inscription)
            'items' => [
                !Yii::$app->user->isGuest ? ['label' => 'Mon profil', 'url' => ['/user/profile']] : '',
                Yii::$app->user->isGuest ? ['label' => 'Inscription', 'url' => ['/site/signup']] : '',
                Yii::$app->user->isGuest ? ['label' => 'Connexion', 'url' => ['/site/login']] : (
                    '<li>'
                    . Html::beginForm(['/site/logout'], 'post', ['class' => 'form-inline'])
                    . Html::submitButton(
                        'Déconnexion (' . Yii::$app->user->identity->username . ')',
                        ['class' => 'btn btn-link logout']
                    )
                    . Html::endForm()
                    . '</li>'
                ),
            ],
        ]);

        NavBar::end();
        ?>
    </header>

    <main role="main" class="flex-shrink-0">
        <div class="container">
            <?= Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]) ?>
            <?= Alert::widget() ?>
            <?= $content ?>
        </div>
    </main>

    <footer class="footer mt-auto py-3 text-muted">
        <div class="container">
            <p class="float-left">&copy; 639th RC <?= date('Y') ?></p>
            <p class="float-right">GameNightManagement</p>
        </div>
    </footer>

    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>