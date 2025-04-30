<?php
namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class AvailabilitySearch extends Availability
{
    public function rules()
    {
        return [
            [['id_availability', 'FKid_user'], 'integer'],
            [['start_date', 'end_date'], 'safe'],
        ];
    }

    public function scenarios() { return Model::scenarios(); }

    public function search($params)
    {
        $query = Availability::find()
            ->andWhere(['FKid_user' => Yii::$app->user->id]);

        $dataProvider = new ActiveDataProvider([
            'query'      => $query,
            'pagination' => ['pageSize' => 10],
        ]);

        $this->load($params, '');

        if (!$this->validate()) {
            return $dataProvider;
        }

        if ($this->start_date) {
            $query->andFilterWhere(['>=', 'start_date', $this->start_date]);
        }
        if ($this->end_date) {
            $query->andFilterWhere(['<=', 'end_date', $this->end_date]);
        }

        return $dataProvider;
    }
}
