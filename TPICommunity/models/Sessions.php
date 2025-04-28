<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Sessions extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sessions';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['startTime', 'sessionName', 'endTime', 'FKgameID'], 'required'],
            [['startTime', 'endTime'], 'safe'],
            [['sessionStatus', 'FKgameID'], 'integer'],
            [['sessionName'], 'string', 'max' => 255],
            [['FKgameID'], 'exist', 'skipOnError' => true, 'targetClass' => Games::class, 'targetAttribute' => ['FKgameID' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'startTime' => 'Start Time',
            'sessionName' => 'Session Name',
            'endTime' => 'End Time',
            'sessionStatus' => 'Session Status',
            'FKgameID' => 'FK game ID',
        ];
    }

    /**
     * Gets query for [[FKgame]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFKgame()
    {
        return $this->hasOne(Games::class, ['id' => 'FKgameID']);
    }

    /**
     * Gets query for [[Participants]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getParticipants()
    {
        return $this->hasMany(UsersParticipateSessions::class, ['session_id' => 'id']);
    }

    /**
     * Gets query for [[AvailableUsers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAvailableUsers()
    {
        return User::find()
            ->joinWith(['availability', 'games'])
            ->where(['games.id' => $this->FKgameID])
            ->andWhere(['<=', 'availability.startTime', $this->startTime])
            ->andWhere(['>=', 'availability.endTime', $this->endTime])
            ->all();
    }
}