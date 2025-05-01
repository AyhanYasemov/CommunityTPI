<?php

namespace app\controllers;

use app\models\Games;
use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use app\models\User;
use app\models\Availability;
use app\models\Genres;
use app\models\Platforms;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;


class UserController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['profile', 'update_preferences'],
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
    {   // Avant qu'on fasse appel aux providers, on vérifie les disponibilités dans la base de données et supprime les échues
        // Purge des disponiblités expirées avant de charger les providers
        Availability::deleteAll([
            '<',
            'end_date',
            new Expression('NOW()')
        ]);

        // 2) Ensuite, on récupère l’utilisateur et on construit ses providers
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

    /**
     * Ajoute ou retire un genre de la liste de préférences
     */
    public function actionToggleGenrePreference($genreId)
    {
        $user = Yii::$app->user->identity;
        $genre = Genres::findOne($genreId);
        if (!$genre) {
            throw new NotFoundHttpException("Genre introuvable");
        }

        // on regarde si c'est déjà lié
        if ($user->getPreferredGenres()->andWhere(['id_genre' => $genreId])->exists()) {
            $user->unlink('preferredGenres', $genre, true);
            Yii::$app->session->setFlash('info', "Genre de jeu supprimé de vos préférences");
        } else {
            $user->link('preferredGenres', $genre);
            Yii::$app->session->setFlash('success', "Genre de jeu ajouté à vos préférences");
        }

        return $this->redirect(['profile']);
    }

    /**
     * Même chose pour les plateformes
     */
    public function actionTogglePlatformPreference($platformId)
    {
        $user = Yii::$app->user->identity;
        $platform = Platforms::findOne($platformId);
        if (!$platform) {
            throw new NotFoundHttpException("Plateforme introuvable");
        }

        if ($user->getPreferredPlatforms()->andWhere(['id_platform' => $platformId])->exists()) {
            $user->unlink('preferredPlatforms', $platform, true);
            Yii::$app->session->setFlash('info', "Plateforme supprimée de vos préférences");
        } else {
            $user->link('preferredPlatforms', $platform);
            Yii::$app->session->setFlash('success', "Plateforme ajoutée à vos préférences");
        }

        return $this->redirect(['profile']);
    }

    /**
     * Met à jour en une fois les préférences de genres et de plateformes.
     */
    public function actionUpdatePreferences()
    {
        $user = Yii::$app->user->identity;
        $post = Yii::$app->request->post('User', []);
    
        // Si rien n’est posté, on force un tableau vide
        $genreIds    = $post['preferredGenreIds']    ?? [];
        $platformIds = $post['preferredPlatformIds'] ?? [];
    
        // Détache toutes les anciennes préférences
        foreach ($user->getPreferredGenres()->all()    as $g) $user->unlink('preferredGenres',    $g, true);
        foreach ($user->getPreferredPlatforms()->all() as $p) $user->unlink('preferredPlatforms', $p, true);
    
        // Recréé les nouvelles préférences
        foreach ($genreIds    as $gid) if ($g = Genres::findOne($gid))     $user->link('preferredGenres',    $g);
        foreach ($platformIds as $pid) if ($p = Platforms::findOne($pid)) $user->link('preferredPlatforms', $p);
    
        Yii::$app->session->setFlash('success', 'Vos préférences ont été mises à jour.');
        return $this->redirect(['profile']);
    }
    
}
