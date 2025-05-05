<?php
namespace app\models;

use Yii;
use yii\base\Model;
use app\models\User;

class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;

    /** @var null|User */
    private $_user = null;

    public function rules()
    {
        return [
            [['username', 'password'], 'required'],
            ['rememberMe',   'boolean'],
            ['password',     'validatePassword'],
        ];
    }

    public function validatePassword($attribute)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !Yii::$app->getSecurity()->validatePassword($this->password, $user->password)) {
                $this->addError($attribute, 'Nom d’utilisateur ou mot de passe incorrect.');
            }
        }
    }

    public function login(): bool
    {
        if ($this->validate()) {
            return Yii::$app->user->login(
                $this->getUser(),
                $this->rememberMe ? 3600*24*30 : 0
            );
        }
        return false;
    }


        /**
     * Retourne l'instance User correspondant au username saisi,
     * ou null si introuvable.
     *
     * @return User|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::find()->where(['username' => $this->username])->one();
        }
        return $this->_user;
    }
}
