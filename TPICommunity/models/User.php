<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class User extends ActiveRecord implements IdentityInterface
{

    public $preferredGenreIds   = [];
    public $preferredPlatformIds = [];
    public $authKey;
    public $accessToken;
    public $rememberMe = true;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'email', 'password'], 'required'],
            ['email', 'email'],
            [['username', 'email'], 'string', 'max' => 255],
            [['password'], 'string'],
            [['birthdate', 'created_at', 'updated_at'], 'safe'],
            ['preferredGenreIds', 'each', 'rule' => ['integer']],
            ['preferredPlatformIds', 'each', 'rule' => ['integer']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'username'   => 'Nom d\'utilisateur',
            'email'      => 'Adresse e-mail',
            'password'   => 'Mot de passe',
            'birthdate'  => 'Date de naissance',
            'created_at' => 'Date de création',
            'updated_at' => 'Dernière mise à jour',
        ];
    }

    /**
     * Gets query for [[Availability]].
     * @return \yii\db\ActiveQuery
     */
    public function getAvailabilities()
    {
        return $this->hasMany(Availability::class, ['FKid_user' => 'id_user']);
    }

    /**
     * Gets query for [[Sessions]].
     * @return \yii\db\ActiveQuery
     */
    public function getSessions()
    {
        return $this->hasMany(Session::class, ['FKid_host' => 'id_user']);
    }

    /**
     * Gets query for [[ParticipateSessions]].
     * @return \yii\db\ActiveQuery
     */
    public function getParticipateSessions()
    {
        return $this->hasMany(UsersParticipateSessions::class, ['user_id' => 'id_user']);
    }

    /**
     * Gets query for games owned by the user (table OWN)
     * @return \yii\db\ActiveQuery
     */
    public function getOwnGames()
    {
        return $this->hasMany(Games::class, ['id_game' => 'FKid_game'])
                    ->viaTable('own', ['FKid_user' => 'id_user']);
    }

    /**
     * Gets query for raw preferences of the user
     * @return \yii\db\ActiveQuery
     */
    public function getPreferences()
    {
        return $this->hasMany(Preference::class, ['FKid_user' => 'id_user']);
    }

    /**
     * Gets query for preferred genres via table PREFERENCE
     * @return \yii\db\ActiveQuery
     */
    public function getPreferredGenres()
    {
        return $this->hasMany(Genres::class, ['id_genre' => 'FKid_genre'])
        ->viaTable('preference',
            ['FKid_user' => 'id_user'],
            function($query) {
                // ce WHERE s'applique à la table 'preference'
                $query->andWhere(['NOT', ['FKid_genre' => null]]);
            }
        );
    }

    /**
     * Gets query for preferred platforms via table PREFERENCE
     * @return \yii\db\ActiveQuery
     */
    public function getPreferredPlatforms()
    {
        return $this->hasMany(Platforms::class, ['id_platform' => 'FKid_platform'])
        ->viaTable('preference',
            ['FKid_user' => 'id_user'],
            function($query) {
                // de même, ce WHERE cible la table 'preference'
                $query->andWhere(['NOT', ['FKid_platform' => null]]);
            }
        );
    }

    /**
     * Finds by email
     */
    public static function findByUserEmail($email)
    {
        return self::findOne(["email" => $email]);
    }

    // IdentityInterface methods
    public static function findIdentity($id)
    {
        return static::findOne(['id_user' => $id]);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['accessToken' => $token]);
    }

    public function getId()
    {
        return $this->id_user;
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        if ($insert) {
            $this->auth_key = Yii::$app->security->generateRandomString();
        }
        return true;
    }
}