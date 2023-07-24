<?php

namespace romanzaytsev\cms\models;

use romanzaytsev\cms\Module;
use Yii;

use yii\db\ActiveRecord;

class User extends ActiveRecord implements \yii\web\IdentityInterface
{

    public static function tableName()
    {
        $tablePrefix = Module::getTablePrefix();
        return "{{%{$tablePrefix}user}}";
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'password'], 'required'],
            [['user_level'], 'string'],
            [['email'], 'email'],
            [['username', 'email'], 'unique'],
            [['phone_number'], 'string', 'max' => 30],
            [['username', 'password', 'password_reset_token', 'first_name', 'last_name'], 'string', 'max' => 250],
            [['user_image', 'email'], 'string', 'max' => 500],
            [['user_image'], 'file'],
        ];
    }

    public static function findIdentity($id)
    {
        $user = self::find()
            ->where([
                "id" => $id
            ])
            ->one();
        return new static($user);
    }

    public function uniqueUsername()
    {
        $string = $this->first_name . $this->last_name;
        $username = Yii::$app->MyTools::makeLinkUrl($string);
        $sufix = "";
        $increment = 1;
        $exists = true;
        while ($exists) {
            $result = $username . $sufix;
            $exists = self::find()
                ->where(["username" => $result])
                ->exists();
            $sufix = "_" . ($increment++);
        }
        return $result;
    }

    public function setPassword($password)
    {
        $this->password = strtolower(md5($password));
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $userType = null)
    {

        $user = self::find()
            ->where(["accessToken" => $token])
            ->one();
        return new static($user);
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        $user = self::find()
            ->where([
                "username" => $username
            ])
            ->one();
        return new static($user);
    }

    public static function findByUser($username)
    {
        $user = self::find()
            ->where([
                "username" => @$username
            ])
            ->one();
        return $user;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    public function isAdmin()
    {
        return $this->user_level === "Admin";
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return strtolower($this->password) === strtolower(md5($password));
    }

}
