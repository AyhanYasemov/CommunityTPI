<?php

namespace app\controllers;

use Yii;
use app\models\Games;
use app\models\GameSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

class GamesController extends Controller
{
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    public function actionIndex()
    {
        $searchModel = new GameSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate()
    {
        if (Yii::$app->user->identity->type !== 'admin') {
            Yii::$app->session->setFlash('error', 'You do not have permission to create a game.');
            return $this->redirect(['index']);
        }

        $model = new Games();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                // Ajouter les genres associés
                $selectedGenres = Yii::$app->request->post('Game')['fkGenre_id'];
                if ($selectedGenres) {
                    foreach ($selectedGenres as $genreId) {
                        $gameGenre = new \app\models\Genres();
                        $gameGenre->FKid_game = $model->id_game;
                        $gameGenre->FKid_genre = $genreId;
                        $gameGenre->save();
                    }
                }

                // Ajouter les plateformes associées
                $selectedPlatforms = Yii::$app->request->post('Game')['fkPlatform_id'];
                if ($selectedPlatforms) {
                    foreach ($selectedPlatforms as $platformId) {
                        $gamePlatform = new \app\models\Platforms();
                        $gamePlatform->FKid_game = $model->id_game;
                        $gamePlatform->FKid_platform = $platformId;
                        $gamePlatform->save();
                    }
                }

                return $this->redirect(['view', 'id' => $model->id_game]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        if (Yii::$app->user->identity->type !== 'admin') {
            Yii::$app->session->setFlash('error', 'You do not have permission to update a game.');
            return $this->redirect(['index']);
        }

        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            // Mettre à jour les genres associés
            $selectedGenres = Yii::$app->request->post('Game')['fkGenre_id'];
            \app\models\Genres::deleteAll(['FKid_game' => $model->id_game]);
            if ($selectedGenres) {
                foreach ($selectedGenres as $genreId) {
                    $gameGenre = new \app\models\Genres();
                    $gameGenre->FKid_game = $model->id_game;
                    $gameGenre->FKid_genre = $genreId;
                    $gameGenre->save();
                }
            }

            // Mettre à jour les plateformes associées
            $selectedPlatforms = Yii::$app->request->post('Game')['fkPlatform_id'];
            \app\models\Platforms::deleteAll(['FKid_game' => $model->id_game]);
            if ($selectedPlatforms) {
                foreach ($selectedPlatforms as $platformId) {
                    $gamePlatform = new \app\models\Platforms();
                    $gamePlatform->FKid_game = $model->id_game;
                    $gamePlatform->FKid_platform = $platformId;
                    $gamePlatform->save();
                }
            }

            return $this->redirect(['view', 'id' => $model->id_game]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {
        if (Yii::$app->user->identity->type !== 'admin') {
            Yii::$app->session->setFlash('error', 'You do not have permission to delete a game.');
            return $this->redirect(['index']);
        }

        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = Games::findOne(['id_game' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    // Action pour ajouter ou retirer des jeux de la bibliothèque de l'utilisateur
    public function actionAddToLibrary()
    {
        $userId = Yii::$app->user->id;
        $selectedGames = Yii::$app->request->post('games', []);

        // Récupérer les jeux déjà dans la bibliothèque de l'utilisateur
        $existingGames = \app\models\Own::find()->where(['FKid_user' => $userId])->select('FKid_game')->column();

        // Identifier les jeux à ajouter et à retirer
        $gamesToAdd = array_diff($selectedGames, $existingGames);
        $gamesToRemove = array_diff($existingGames, $selectedGames);

        // Retirer les jeux non sélectionnés
        \app\models\Own::deleteAll(['FKid_user' => $userId, 'FKid_game' => $gamesToRemove]);

        // Ajouter les jeux sélectionnés
        foreach ($gamesToAdd as $gameId) {
            $userHaveGame = new \app\models\Own();
            $userHaveGame->FKid_user = $userId;
            $userHaveGame->FKid_game = $gameId;

            if (!$userHaveGame->save()) {
                Yii::$app->session->setFlash('error', 'There was an issue adding a game to your library.');
                return $this->redirect(['games/index']);
            }
        }

        Yii::$app->session->setFlash('success', 'Your game library has been updated!');
        return $this->redirect(['games/index']);
    }
}
