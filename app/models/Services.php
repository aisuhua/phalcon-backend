<?php
namespace Backend\Models;

use Phalcon\Mvc\Model;

class Services extends Model
{
    public $id;

    public $name;

    public $url;

    public $username;

    public $password;

    public $auth_token;

    public $update_time;

    public $create_time;

    const DEFAULT_USERNAME = 'username';

    const DEFAULT_PASSWORD = 'password';
    
    public function beforeCreate()
    {
        $this->username = $this->username ?: self::DEFAULT_USERNAME;
        $this->password = $this->password ?: self::DEFAULT_PASSWORD;
        $this->create_time = time();
    }

    public function beforeUpdate()
    {
        $this->update_time = time();
    }

}