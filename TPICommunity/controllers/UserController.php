<?php

namespace app\controllers;

use app\models\Games;
use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;
use app\models\User;

class UserController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['profile'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Page de profil de l'utilisateur
     */
    public function actionProfile()
    {
        $user = User::findOne(Yii::$app->user->id);

        $ownProvider = new ActiveDataProvider([
            'query' => $user->getOwnGames(),
            'pagination' => ['pageSize' => 10],
        ]);

        $availProvider = new ActiveDataProvider([
            'query' => $user->getAvailabilities(),
            'pagination' => ['pageSize' => 10],
        ]);

        $prefProvider = new ActiveDataProvider([
            'query' => $user->getPreferences(),
            'pagination' => ['pageSize' => 10],
        ]);

        return $this->render('profile', [
            'user' => $user,
            'ownProvider' => $ownProvider,
            'availProvider' => $availProvider,
            'prefProvider' => $prefProvider,
        ]);
    }

    public function actionAddToLibrary($gameId)
{
    $user = User::findOne(Yii::$app->user->id);
    $game = Games::findOne($gameId);

    if ($user && $game) {
        $user->link('games', $game);  // Lier le jeu à l'utilisateur
        Yii::$app->session->setFlash('success', 'Jeu ajouté à votre bibliothèque.');
    } else {
        Yii::$app->session->setFlash('error', 'Impossible d\'ajouter ce jeu.');
    }

    return $this->redirect(['profile']);
}

}



