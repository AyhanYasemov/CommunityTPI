<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "session".
 *
 * @property int $id_session
 * @property string $start_date
 * @property string $end_date
 * @property int $status
 * @property int $FKid_host
 * @property string $name
 * @property int|null $FKid_game
 *
 * @property User[] $fKidUsers
 * @property Game $game
 * @property Participate[] $participates
 * @property User $user
 */
class Session extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'session';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['FKid_game'], 'default', 'value' => null],
            [['status'], 'default', 'value' => 1],
            [['start_date', 'end_date', 'FKid_host', 'name'], 'required'],
            [['start_date', 'end_date'], 'safe'],
            [['status', 'FKid_host', 'FKid_game'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['FKid_host'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['FKid_host' => 'id_user']],
            [['FKid_game'], 'exist', 'skipOnError' => true, 'targetClass' => Game::class, 'targetAttribute' => ['FKid_game' => 'id_game']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_session' => 'Id Session',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'status' => 'Status',
            'FKid_host' => 'F Kid Host',
            'name' => 'Name',
            'FKid_game' => 'F Kid Game',
        ];
    }

    /**
     * Gets query for [[FKidUsers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFKidUsers()
    {
        return $this->hasMany(User::class, ['id_user' => 'FKid_user'])->viaTable('participate', ['FKid_session' => 'id_session']);
    }

    /**
     * Gets query for [[Game]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGame()
    {
        return $this->hasOne(Game::class, ['id_game' => 'FKid_game']);
    }

    /**
     * Gets query for [[Participates]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getParticipates()
    {
        return $this->hasMany(Participate::class, ['FKid_session' => 'id_session']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id_user' => 'FKid_host']);
    }

}
