<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Platforms;

/**
 * PlatformsSearch represents the model behind the search form of `app\models\Platforms`.
 */
class PlatformsSearch extends Platforms
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_platform'], 'integer'], // Modifie ici pour 'id_platform'
            [['name'], 'safe'],
        ];
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
        $query = Platforms::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id_platform' => $this->id_platform,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]); // Changer 'name' en 'platformName'

        return $dataProvider;
    }
}
