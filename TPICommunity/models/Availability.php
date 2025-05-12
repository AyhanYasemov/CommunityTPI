<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * @property int $id_availability
 * @property int $FKid_user
 * @property string $start_date
 * @property string $end_date
 */
class Availability extends ActiveRecord
{
    public static function tableName()
    {
        return 'availability';
    }

    public function rules()
    {
        return [
            [['start_date', 'end_date'], 'required'],
            [['start_date', 'end_date'], 'safe'],
            ['end_date', 'compare', 'compareAttribute' => 'start_date', 'operator' => '>', 'type' => 'datetime'],
            ['end_date', function ($attribute, $params, $validator) {
                if (strtotime($this->$attribute) < time()) {
                    $this->addError($attribute, 'La date de fin doit être dans le futur.');
                }
            }],

            ['FKid_user', 'integer'],
            ['FKid_user', 'default', 'value' => Yii::$app->user->id],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id_availability' => 'ID',
            'start_date'      => 'Date et heure de début',
            'end_date'        => 'Date et heure de fin',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id_user' => 'FKid_user']);
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        $this->start_date = date('Y-m-d H:i:00', strtotime($this->start_date));
        $this->end_date   = date('Y-m-d H:i:00', strtotime($this->end_date));

        return true;
    }
}
