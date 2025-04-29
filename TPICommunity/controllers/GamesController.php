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
                return $this->redirect(['games/catalogue']);
            }
        }

        Yii::$app->session->setFlash('success', 'Your game library has been updated!');
        return $this->redirect(['user/profile']);
    }


    public function actionRemoveFromLibrary($id)
    {
        $userId = Yii::$app->user->id;

        // Trouver et supprimer la relation entre l'utilisateur et le jeu
        $ownRecord = \app\models\Own::find()
            ->where(['FKid_user' => $userId, 'FKid_game' => $id])
            ->one();

        if ($ownRecord) {
            if ($ownRecord->delete()) {
                Yii::$app->session->setFlash('success', 'Game successfully removed from your library.');
            } else {
                Yii::$app->session->setFlash('error', 'Failed to remove the game from your library.');
            }
        } else {
            Yii::$app->session->setFlash('warning', 'The game was not found in your library.');
        }

        return $this->redirect(['user/profile']); // Redirige vers la page de profil ou catalogue
    }


    // Ajout d’un seul jeu depuis un bouton (catalogue)
    public function actionAddSingleToLibrary($id)
    {
        $userId = Yii::$app->user->id;

        // Vérifie si le jeu est déjà dans la bibliothèque
        $alreadyExists = \app\models\Own::find()
            ->where(['FKid_user' => $userId, 'FKid_game' => $id])
            ->exists();

        if ($alreadyExists) {
            Yii::$app->session->setFlash('warning', 'This game is already in your library.');
        } else {
            $own = new \app\models\Own();
            $own->FKid_user = $userId;
            $own->FKid_game = $id;

            if ($own->save()) {
                Yii::$app->session->setFlash('success', 'Game successfully added to your library.');
            } else {
                Yii::$app->session->setFlash('error', 'Failed to add the game to your library.');
            }
        }

        return $this->redirect(['games/catalogue']);
    }

    public function actionCatalogue()
    {
        $userId = Yii::$app->user->id;

        $subQuery = (new \yii\db\Query())
            ->select('FKid_game')
            ->from('own')
            ->where(['FKid_user' => $userId]);

        $searchModel = new \app\models\GameSearch();
        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => \app\models\Games::find()->where(['not in', 'id_game', $subQuery]),
        ]);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'catalogueMode' => true,
        ]);
    }
}
