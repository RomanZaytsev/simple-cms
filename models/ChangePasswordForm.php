<?php

namespace romanzaytsev\cms\models;

use Yii;
use yii\base\InvalidParamException;
use yii\base\Model;
use app\models\User;

/**
 * Change password form for current user only
 */
class ChangePasswordForm extends Model
{
    public $id;
    public $password;
    public $confirm_password;

    /**
     * @var \common\models\User
     */
    private $_user;

    /**
     * Creates a form model given a token.
     *
     * @param string $token
     * @param array $config name-value pairs that will be used to initialize the object properties
     * @throws \yii\base\InvalidParamException if token is empty or not valid
     */
    public function __construct($id, $config = [])
    {
        $this->_user = User::find()->where(["id" => $id])->one();

        if (!$this->_user) {
            throw new InvalidParamException('Unable to find user!');
        }

        $this->id = $this->_user->id;
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['password', 'confirm_password'], 'required'],
            [['password', 'confirm_password'], 'string', 'min' => 6],
            ['confirm_password', 'compare', 'compareAttribute' => 'password'],
        ];
    }

    public function attributeLabels()
    {
        return array(
            'password' => 'Новый пароль',
            'confirm_password' => 'Подтвердите пароль',
        );
    }

    /**
     * Changes password.
     *
     * @return boolean if password was changed.
     */
    public function changePassword()
    {
        $user = $this->_user;
        $user->setPassword($this->password);
        if ($user->save()) {
            return true;
        } else {
            foreach ($user->getErrors() as $key => $value) {
                $this->addError($key, $value);
            }
        }
    }
}
