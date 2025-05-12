<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "participate".
 *
 * @property int $FKid_user
 * @property int $FKid_session
 *
 * @property Session $session
 * @property User $user
 */
class Participate extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'participate';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['FKid_user', 'FKid_session'], 'required'],
            [['FKid_user', 'FKid_session'], 'integer'],
            [['FKid_user', 'FKid_session'], 'unique', 'targetAttribute' => ['FKid_user', 'FKid_session']],
            [['FKid_user'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['FKid_user' => 'id_user']],
            [['FKid_session'], 'exist', 'skipOnError' => true, 'targetClass' => Session::class, 'targetAttribute' => ['FKid_session' => 'id_session']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'FKid_user' => 'id utilisateur',
            'FKid_session' => 'id Session',
        ];
    }

    /**
     * Gets query for [[Session]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSession()
    {
        return $this->hasOne(Session::class, ['id_session' => 'FKid_session']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id_user' => 'FKid_user']);
    }

}
