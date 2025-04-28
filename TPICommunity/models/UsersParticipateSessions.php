<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "usersparticipatesessions".
 *
 * @property int $session_id
 * @property int $user_id
 *
 * @property Sessions $session
 * @property User $user
 */
class UsersParticipateSessions extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'usersparticipatesessions';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['session_id', 'user_id'], 'required'],
            [['session_id', 'user_id'], 'integer'],
            [['session_id', 'user_id'], 'unique', 'targetAttribute' => ['session_id', 'user_id']],
            [['session_id'], 'exist', 'skipOnError' => true, 'targetClass' => Sessions::class, 'targetAttribute' => ['session_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'session_id' => 'Session ID',
            'user_id' => 'User ID',
        ];
    }

    /**
     * Gets query for [[Session]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSession()
    {
        return $this->hasOne(Sessions::class, ['id' => 'session_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
