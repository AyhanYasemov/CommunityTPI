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
            Yii::$app->session->setFlash('error', 'Action reservée à l\'administrateur.');
            return $this->redirect(['index']);
        }

        $model = new Games();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            // on récupère vraiment les données postées
            $post = Yii::$app->request->post('Games', []);
            $selectedGenres    = $post['fkGenre_id']    ?? [];
            $selectedPlatforms = $post['fkPlatform_id'] ?? [];
    
            // genres pivot
            \app\models\GameGenre::deleteAll(['FKid_game' => $model->id_game]);
            foreach ($selectedGenres as $gId) {
                $pivot = new \app\models\GameGenre();
                $pivot->FKid_game  = $model->id_game;
                $pivot->FKid_genre = $gId;
                $pivot->save(false);
            }
    
            // plateformes pivot
            \app\models\GamePlatform::deleteAll(['FKid_game' => $model->id_game]);
            foreach ($selectedPlatforms as $pId) {
                $pivot = new \app\models\GamePlatform();
                $pivot->FKid_game      = $model->id_game;
                $pivot->FKid_platform  = $pId;
                $pivot->save(false);
            }
    
            return $this->redirect(['view', 'id' => $model->id_game]);
        }
        return $this->render('create', ['model' => $model]);
    }
    

    public function actionUpdate($id)
    {
        if (Yii::$app->user->identity->type !== 'admin') {
            Yii::$app->session->setFlash('error', 'Action reservée à l\'administrateur.');
            return $this->redirect(['index']);
        }

        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $post = Yii::$app->request->post('Games', []);
            $selectedGenres    = $post['fkGenre_id']    ?? [];
            $selectedPlatforms = $post['fkPlatform_id'] ?? [];
    
            \app\models\GameGenre::deleteAll(['FKid_game' => $model->id_game]);
            foreach ($selectedGenres as $gId) {
                $pivot = new \app\models\GameGenre();
                $pivot->FKid_game  = $model->id_game;
                $pivot->FKid_genre = $gId;
                $pivot->save(false);
            }
    
            \app\models\GamePlatform::deleteAll(['FKid_game' => $model->id_game]);
            foreach ($selectedPlatforms as $pId) {
                $pivot = new \app\models\GamePlatform();
                $pivot->FKid_game      = $model->id_game;
                $pivot->FKid_platform  = $pId;
                $pivot->save(false);
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
            Yii::$app->session->setFlash('error', 'Action reservée à l\'administrateur.');
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
                Yii::$app->session->setFlash('error', 'Problème lors de l\'ajout du jeu à votre bibliothèque.');
                return $this->redirect(['games/catalogue']);
            }
        }

        Yii::$app->session->setFlash('success', 'Votre bibliothèque de jeux à été mis à jour.');
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
                Yii::$app->session->setFlash('success', 'Jeu retiré de votre bibliothèque.');
            } else {
                Yii::$app->session->setFlash('error', 'Problème lors du retrait du jeu de votre bibliothèque.');
            }
        } else {
            Yii::$app->session->setFlash('warning', 'ce jeu n\'a pas été trouvé dans votre bibliothèque.');
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
            Yii::$app->session->setFlash('warning', 'Ce jeu est déjà dans votre bibliothèque.');
        } else {
            $own = new \app\models\Own();
            $own->FKid_user = $userId;
            $own->FKid_game = $id;

            if ($own->save()) {
                Yii::$app->session->setFlash('success', 'Jeu ajouté à votre bibliothèque.');
            } else {
                Yii::$app->session->setFlash('error', 'echec lors de l\'ajout du jeu à votre bibliothèque.');
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
