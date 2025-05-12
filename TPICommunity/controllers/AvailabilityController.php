<?php

namespace app\controllers;

use Yii;
use app\models\Availability;
use app\models\AvailabilitySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class AvailabilityController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class'   => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                    'quick'  => ['POST'],  // <-- on force POST
                ],
            ],
        ];
    }

    // Création de la disponibilité (en model )
    public function actionCreate()
    {
        $model = new Availability();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Disponibilité ajoutée.');

                // Si c'est une requête AJAX, renvoyer un succès
                if (Yii::$app->request->isAjax) {
                    return \yii\helpers\Json::encode(['status' => 'success']);
                }

                // Sinon, redirige vers la page de profil
                return $this->redirect(['user/profile']);
            } else {
                // Si sauvegarde échoue, renvoyer les erreurs via AJAX
                if (Yii::$app->request->isAjax) {
                    return \yii\helpers\Json::encode(['status' => 'error', 'errors' => $model->errors]);
                }
            }
        }

        // Retourner le formulaire pour une demande AJAX
        return $this->renderAjax('create', ['model' => $model]);
    }

    /**
     * Crée une disponibilité de maintenant à maintenant+2h
     */
    public function actionQuick()
{
    $userId = Yii::$app->user->identity->id_user;

    $now = new \DateTime();
    $end = (clone $now)->add(new \DateInterval('PT2H'));

    $model = new Availability([
        'start_date' => $now->format('Y-m-d H:i:00'),
        'end_date'   => $end->format('Y-m-d H:i:00'),
        'FKid_user'  => $userId,
    ]);



    try {
        
        if ($model->save()) {
            Yii::$app->session->setFlash('success', 'Ta disponibilité immédiate a bien été ajoutée.');
        } else {
            Yii::$app->session->setFlash('error', 'Erreur (validation) : ' . json_encode($model->getErrors()));
        }
    } catch (\Throwable $e) {
        Yii::$app->session->setFlash('error', 'Exception SQL : ' . $e->getMessage());
    }

    return $this->redirect(['user/profile']);
}



    // Suppression de la disponibilité
    public function actionDelete($id)
    {
        // Appel à la méthode findModel pour récupérer le modèle
        $model = $this->findModel($id);

        if ($model !== null) {
            // Si le modèle est trouvé, on le supprime
            $model->delete();
            Yii::$app->session->setFlash('success', 'Disponibilité supprimée.');
        }

        // Redirection vers la page de profil après la suppression (ou en cas d'absence de modèle)
        return $this->redirect(['user/profile']);
    }

    protected function findModel($id)
    {
        // Vérification si l'objet existe bien avant de tenter de l'utiliser
        $model = Availability::findOne(['id_availability' => $id]);

        if ($model === null) {
            Yii::$app->session->setFlash('error', 'Disponibilité introuvable.');
            return null; // Retourne null si le modèle n'est pas trouvé
        }
        return $model;
    }
}
