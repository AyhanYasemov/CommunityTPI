<?php

namespace app\controllers;

use app\models\Games;
use Yii;
use app\models\Session;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\web\Response;

class SessionController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index', 'create', 'update', 'delete', 'cancel'],
                'rules' => [['allow' => true, 'roles' => ['@']]],
            ],
        ];
    }

    /**
     * Liste des sessions que l'utilisateur a créées ou rejointes.
     */
    public function actionIndex()
    {
        $userId = Yii::$app->user->id;

        // On construit un dataProvider qui récupère :
        //  - toutes les sessions dont on est l'hôte
        //  - ainsi que celles où on est inscrit (via la table PARTICIPATE)
        $query = Session::find()
            ->alias('s')
            ->leftJoin('participate p', 'p.FKid_session=s.id_session AND p.FKid_user=:uid', [':uid' => $userId])
            ->where([
                'or',
                ['s.FKid_host' => $userId],
                ['p.FKid_user'  => $userId],
            ])
            ->orderBy(['s.start_date' => SORT_ASC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 10],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Suppression ou désinscription.
     */
    public function actionCancel($id)
    {
        $session = $this->findModel($id);

        $userId = Yii::$app->user->id;
        if ($session->FKid_host != $userId && !$session->isUserParticipant($userId)) {
            throw new \yii\web\ForbiddenHttpException('Accès refusé.');
        }

        if ($session->FKid_host == $userId) {
            // Hôte ➔ supprime toute la session
            // Supprimer les plateformes liées
            Yii::$app->db->createCommand()
                ->delete('session_platform', ['FKid_session' => $session->id_session])
                ->execute();

            // Supprimer les participations
            Yii::$app->db->createCommand()
                ->delete('participate', ['FKid_session' => $session->id_session])
                ->execute();

            $session->delete();
            Yii::$app->session->setFlash('success', 'Session supprimée.');
        } else {
            // Participant ➔ désinscription
            Yii::$app->db
                ->createCommand()
                ->delete('participate', [
                    'FKid_session' => $id,
                    'FKid_user'    => $userId,
                ])->execute();
            Yii::$app->session->setFlash('success', 'Participation annulée.');
        }

        return $this->redirect(['index']);
    }

    /**
     * Création
     */
    public function actionCreate()
{
    $model = new Session();

    // Charge les données POST dans $model
    if ($model->load(Yii::$app->request->post())) {
        // 1) On force l’hôte courant
        $model->FKid_host = Yii::$app->user->id;

        // 2) Essaye de sauver AVEC validation
        if ($model->save()) {
            Yii::$app->session->setFlash('success', 'Session créée avec succès.');
            return $this->redirect(['index']);
        }

        // 3) Si on arrive là, la validation a échoué
        //     On récupère toutes les erreurs pour les afficher
        $errors = $model->getErrors();
        $flat   = [];
        foreach ($errors as $attr => $msgs) {
            $flat[] = $attr . ' : ' . implode(', ', $msgs);
        }
        Yii::$app->session->setFlash(
            'error',
            'Impossible de créer la session :<br/>' . nl2br(implode("<br/>", $flat))
        );
    }

    // 4) Affiche (ou ré-affiche) le formulaire
    return $this->render('create', ['model' => $model]);
}

    

    /**
     * Modification
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->FKid_host !== Yii::$app->user->id) {
            throw new \yii\web\ForbiddenHttpException;
        }
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Session mise à jour.');
            return $this->redirect(['index']);
        }
        return $this->render('update', ['model' => $model]);
    }

    /**
     * Endpoint pour DepDrop → renvoie les plateformes d'un jeu donné.
     */
    public function actionPlatformList()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = [];
        if (($parents = Yii::$app->request->post('depdrop_parents')) !== null) {
            $gameId = $parents[0];
            if ($game = Games::findOne($gameId)) {
                foreach ($game->platforms as $p) {
                    $out[] = ['id' => $p->id_platform, 'name' => $p->name];
                }
            }
        }
        return ['output' => $out, 'selected' => ''];
    }
    /**
     * Récupère ou lance 404.
     */
    protected function findModel($id)
    {
        if (($m = Session::findOne($id)) !== null) {
            return $m;
        }
        throw new NotFoundHttpException("Session introuvable");
    }
}
