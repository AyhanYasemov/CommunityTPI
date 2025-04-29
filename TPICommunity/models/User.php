<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class User extends ActiveRecord implements IdentityInterface
{
    public $id;
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
            [['birthdate', 'created_at', 'updated_at'], 'safe'], // Permet de passer ces dates sans validation complexe
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'username' => 'Nom d\'utilisateur',
            'email' => 'Adresse e-mail',
            'password' => 'Mot de passe',
            'birthdate' => 'Date de naissance',
            'created_at' => 'Date de création',
            'updated_at' => 'Dernière mise à jour',
        ];
    }

    /**
     * Gets query for [[Availability]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAvailability()
    {
        return $this->hasMany(Availability::class, ['user_id' => 'id']);
    }

    /**
     * Gets query for [[Sessions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSessions()
    {
        return $this->hasMany(Session::class, ['host_id' => 'id']);
    }

    /**
     * Gets query for [[ParticipateSessions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getParticipateSessions()
    {
        return $this->hasMany(UsersParticipateSessions::class, ['user_id' => 'id']);
    }

    /**
     * Gets query for [[Games]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGames()
    {
        return $this->hasMany(Games::class, ['id' => 'game_id'])
            ->viaTable('userhavegames', ['user_id' => 'id']);
    }

    public static function findByUserEmail($email)
    {
        return self::findOne(["email" => $email]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['accessToken' => $token]);
    }

    public static function findByUsername($username)
    {
        Yii::info("Recherche de l'utilisateur : " . $username, __METHOD__);
        $user = self::find()->where(['username' => $username])->one();
        Yii::info("Résultat de la requête : " . print_r($user, true), __METHOD__);
        return $user;
    }

    public function validatePassword($passwordHash)
    {
        return Yii::$app->getSecurity()->validatePassword($this->password, $passwordHash);
    }

    public function getId()
    {
        return $this->getPrimaryKey(); // Yii gère l'ID avec la clé primaire
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            // Générer un auth_key si c'est un nouvel utilisateur
            if ($this->isNewRecord) {
                $this->auth_key = Yii::$app->security->generateRandomString();
            }
            return true;
        }
        return false;
    }

    public function login()
    {
        $user = $this->findByUserEmail($this->email);
        if ($user && $this->validatePassword($user->password)) {
            return Yii::$app->user->login($user, $this->rememberMe ? 3600 * 24 * 30 : 0);
        } else {
            $this->addError('password', 'Nom ou mot de passe incorrect');
        }
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
     * Gets query for the availability slots of the user
     * @return \yii\db\ActiveQuery
     */
    public function getAvailabilities()
    {
        return $this->hasMany(Availability::class, ['FKid_user' => 'id_user']);
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
                    ->via('preference');
    }

    /**
     * Gets query for preferred games via table PREFERENCE
     * @return \yii\db\ActiveQuery
     */
    public function getPreferredGames()
    {
        return $this->hasMany(Games::class, ['id_game' => 'FKid_game'])
                    ->via('preference');
    }
}

