<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * @property int    $id_session
 * @property int    $status
 * @property int    $FKid_host
 * @property int    $FKid_game
 * @property string $name
 * @property string $start_date
 * @property string $end_date
 *
 * @property User   $host
 * @property Game   $game
 * @property Platform[] $platforms
 * @property User[] $participants
 */
class Session extends ActiveRecord
{
    public $platformIds = [];
    public $participantIds = [];

    public static function tableName()
    {
        return 'session';
    }

    public function rules()
    {
        return [
            [['name', 'start_date', 'end_date', 'FKid_game', 'FKid_host'], 'required'],
            [['start_date', 'end_date'], 'safe'],
            [['FKid_host', 'FKid_game'], 'integer'],
            [['name'], 'string', 'max' => 255],
            ['participantIds', 'each', 'rule' => ['integer']],
            ['platformIds',    'each', 'rule' => ['integer']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name'      => 'Titre de la session',
            'FKid_game' => 'Jeu',
            'start_date' => 'Date et heure de début',
            'end_date'  => 'Date et heure de fin',
            'platformIds'    => 'Plateformes',
            'participantIds' => 'Participants',
        ];
    }

    public function getHost()
    {
        return $this->hasOne(User::class, ['id_user' => 'FKid_host']);
    }

    public function getGame()
    {
        return $this->hasOne(Games::class, ['id_game' => 'FKid_game']);
    }

    public function getPlatforms()
    {
        return $this->hasMany(Platforms::class, ['id_platform' => 'FKid_platform'])
            ->viaTable('session_platform', ['FKid_session' => 'id_session']);
    }

    public function getParticipants()
    {
        return $this->hasMany(User::class, ['id_user' => 'FKid_user'])
            ->viaTable('participate', ['FKid_session' => 'id_session']);
    }

    public function isUserParticipant($userId)
    {
        return (new \yii\db\Query())
            ->from('participate')
            ->where(['FKid_session' => $this->id_session, 'FKid_user' => $userId])
            ->exists();
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

    public function afterFind()
    {
        parent::afterFind();
        // Pré-remplir les selects
        $this->platformIds    = ArrayHelper::getColumn($this->platforms,    'id_platform');
        $this->participantIds = ArrayHelper::getColumn($this->participants, 'id_user');
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        // sync plateformes
        static::getDb()->createCommand()
            ->delete('session_platform', ['FKid_session' => $this->id_session])
            ->execute();
        foreach ($this->platformIds as $pid) {
            static::getDb()->createCommand()
                ->insert('session_platform', [
                    'FKid_session' => $this->id_session,
                    'FKid_platform' => $pid,
                ])->execute();
        }

        // sync participants
        // on vide d'abord l’ancienne liste

        \app\models\Participate::deleteAll(['FKid_session' => $this->id_session]);
        // puis seulement si participantIds est un tableau, on boucle
        if (is_array($this->participantIds)) {
            foreach ($this->participantIds as $uid) {
                $link = new \app\models\Participate();
                $link->FKid_session = $this->id_session;
                $link->FKid_user    = $uid;
                $link->save(false);
            }
        }
    }
}
