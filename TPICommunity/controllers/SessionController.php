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
use yii\db\Query;
use yii\db\Expression;

class SessionController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index', 'create', 'update', 'delete', 'cancel', 'join', 'do-join'],
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
            // 1) Supprime la participation
            Yii::$app->db->createCommand()
                ->delete('participate', [
                    'FKid_session' => $id,
                    'FKid_user'    => $userId,
                ])->execute();
            Yii::$app->session->setFlash('success', 'Participation annulée.');

            // 2) Supprime la dispo “bloquée” correspondant à cette session
            \app\models\Availability::deleteAll([
                'FKid_user'  => $userId,
                'start_date' => $session->start_date,
                'end_date'   => $session->end_date,
            ]);
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
     * Affiche la liste des sessions ouvertes auxquelles l'utilisateur peut se joindre.
     */
    /**
     * Rejoindre une session (ajoute une ligne en table participate)
     * @param int $id L'id_session à rejoindre
     * @return \yii\web\Response
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionJoin()
    {
        $user        = Yii::$app->user->identity;
        $userId      = $user->id_user;
        $now         = new Expression('NOW()');
        $genreIds    = $user->getPreferredGenres()->select('id_genre')->column();
        $platformIds = $user->getPreferredPlatforms()->select('id_platform')->column();


        // Si l'utilisateur n'a pas de genres ni de plateformes préférés, on ne retourne rien
        if (empty($genreIds) && empty($platformIds)) {
            Yii::$app->session->setFlash('error', 'Vous devez indiquer des genres ou des plateformes préférées dans votre profil pour rejoindre une session.');
            return $this->redirect(['index']);
        }

        $query = Session::find()
            ->alias('s')
            // 1) Je ne suis pas déjà inscrit
            ->leftJoin(
                'participate p',
                'p.FKid_session=s.id_session AND p.FKid_user=:uid',
                [':uid' => $userId]
            )
            ->andWhere(['p.FKid_user' => null])
            // 2) La session n'est pas terminée
            ->andWhere(['>', 's.end_date', $now]);

        // 3) Filtre sur ses genres préférés 
        if (!empty($genreIds)) {
            $query->innerJoin('game_genre gg', 'gg.FKid_game=s.FKid_game')
                ->andWhere(['gg.FKid_genre' => $genreIds]);
        }

        // 4) Filtre sur ses plateformes préférées 
        if (!empty($platformIds)) {
            $query->innerJoin('session_platform sp', 'sp.FKid_session=s.id_session')
                ->andWhere(['sp.FKid_platform' => $platformIds]);
        }

        $dataProvider = new ActiveDataProvider([
            'query'      => $query->distinct(),
            'pagination' => ['pageSize' => 20],
        ]);

        return $this->render('join', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Rejoindre une session (ajoute une ligne en table participate)
     * @param int $id L'id_session à rejoindre
     * @return \yii\web\Response
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionDoJoin($id)
    {
        $userId  = Yii::$app->user->id;
        $session = $this->findModel($id);

        // 1) Vérifie si l'utilisateur participe déjà
        $already = (new \yii\db\Query())
            ->from('participate')
            ->where(['FKid_session' => $id, 'FKid_user' => $userId])
            ->exists();

        if ($already) {
            Yii::$app->session->setFlash('info', 'Vous êtes déjà inscrit à cette session.');
        } else {
            // 2) Ajoute la participation
            Yii::$app->db->createCommand()
                ->insert('participate', [
                    'FKid_session' => $id,
                    'FKid_user'    => $userId,
                ])->execute();

            // 3) Crée la disponibilité “bloquée” pour cette session
            $avail = new \app\models\Availability([
                'FKid_user'  => $userId,
                'start_date' => $session->start_date,
                'end_date'   => $session->end_date,
            ]);
            if (!$avail->save()) {
                Yii::error('Impossible de créer la dispo bloquée : ' . json_encode($avail->getErrors()));
            }

            Yii::$app->session->setFlash('success', 'Vous avez bien rejoint la session et votre disponibilité a été réservée.');
        }

        return $this->redirect(['join']);
    }


    /**
     * Endpoint JSON pour peupler participantIds via DepDrop.
     * Renvoie uniquement les users disponibles sur [start,end de leur availability]
     * et ayant au moins un genre commun avec le jeu sélectionné.
     */
    public function actionParticipantList()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        // 1) On initialise
        $out      = [];
        $selected = '';

        // 2) On récupère depdrop_parents, 3 valeurs attendues (start_date/end_date/gameid) :
        $parents = Yii::$app->request->post('depdrop_parents', []);

        // 3) Si ce n'est PAS un array ou qu'il n'a pas 3 éléments, on renvoie vide
        if (!is_array($parents) || count($parents) !== 3) {
            return ['output' => $out, 'selected' => $selected];
        }

        // 4) Sinon on décompose en variables claires :
        list($gameId, $startRaw, $endRaw) = $parents;

        // 5) On formate les dates pour le SQL
        try {
            $startDt = (new \DateTime($startRaw))->format('Y-m-d H:i:00');
            $endDt   = (new \DateTime($endRaw))->format('Y-m-d H:i:00');
        } catch (\Exception $e) {
            // Si le format est invalide, on renvoie vide aussi
            return ['output' => $out, 'selected' => $selected];
        }
        // on récupère l'id de l'utilisateur connecté pour en faire l'host
        $hostId = Yii::$app->user->id;

        // Récupère les genres du jeu
        $genreIds = (new \yii\db\Query())
            ->select('FKid_genre')
            ->from('game_genre')
            ->where(['FKid_game' => $gameId])
            ->column();

        // On construit la requête principale
        $query = (new \yii\db\Query())
            ->select(['u.id_user AS id', 'u.username AS name'])
            ->from('user u')
            ->andWhere(['<>', 'u.id_user', $hostId]);

        if (!empty($genreIds)) {
            $query->innerJoin('preference pr', 'pr.FKid_user=u.id_user')
                ->andWhere(['pr.FKid_genre' => $genreIds]);
        }

        $query->innerJoin('availability a', 'a.FKid_user=u.id_user')
            ->andWhere([
                'and',
                ['<=', 'a.start_date', $startDt],
                ['>=', 'a.end_date',  $endDt],
            ]);

        $users = $query->distinct()->all();

        // On peuple la sortie
        foreach ($users as $u) {
            $out[] = ['id' => $u['id'], 'name' => $u['name']];
        }

        return ['output' => $out, 'selected' => $selected];
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
