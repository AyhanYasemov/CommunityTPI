<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Availability;
use Yii;

/**
 * AvailabilitySearch represents the model behind the search form of `app\models\Availability`.
 */
class AvailabilitySearch extends Availability
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user_id'], 'integer'],
            [['startTime', 'endTime'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Availability::find()->where(['user_id' => Yii::$app->user->id]); // Filtre les disponibilités de l'utilisateur connecté
    
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
    
        $this->load($params);
    
        if (!$this->validate()) {
            return $dataProvider;
        }
    

    // Filtrage des dates avec conversion du format datetime-local en format SQL
    if (!empty($this->startTime)) {
        $formattedStartTime = str_replace('T', ' ', $this->startTime) . ':00';
        $query->andFilterWhere(['>=', 'startTime', $formattedStartTime]);
    }

    if (!empty($this->endTime)) {
        $formattedEndTime = str_replace('T', ' ', $this->endTime) . ':00';
        $query->andFilterWhere(['<=', 'endTime', $formattedEndTime]);
    }
        
    
        return $dataProvider;
    }
}    
