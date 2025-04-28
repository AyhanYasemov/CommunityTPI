<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "availability".
 *
 * @property int $id Primary Key
 * @property string|null $startTime
 * @property string|null $endTime
 * @property int|null $user_id
 *
 * @property Users $user
 */
class Availability extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'availability';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['startTime', 'endTime'], 'required'],
            [['startTime', 'endTime'], 'safe'],
            [['user_id'], 'integer'],
            ['user_id', 'default', 'value' => Yii::$app->user->id],  // L'utilisateur connecté est automatiquement associé à cette disponibilité
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'startTime' => 'Heure de début',
            'endTime' => 'Heure de fin',
            'user_id' => 'Utilisateur',  // Ajout du label pour l'ID utilisateur
        ];
    }

    /**
     * Relation avec le modèle User
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * Fonction pour rechercher les disponibilités de l'utilisateur connecté
     * 
     * @param array $params
     * @return \yii\data\ActiveDataProvider
     */
    public function search($params)
    {
        $query = Availability::find()->where(['user_id' => Yii::$app->user->id]); // Filtre par utilisateur connecté

        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        return $dataProvider;
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            // Supprime les secondes en formatant l'entrée
            $this->startTime = date('Y-m-d H:i:00', strtotime($this->startTime));
            $this->endTime = date('Y-m-d H:i:00', strtotime($this->endTime));
            return true;
        }
        return false;
    }
}
