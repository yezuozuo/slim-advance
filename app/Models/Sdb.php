<?php
/**
 * Created by PhpStorm.
 * User: zoco
 * Date: 16/11/16
 * Time: 17:28
 */

namespace App\Models;

use Venus\Database;

/**
 * Class Main
 *
 * @package App\Models
 */
class Sdb {
    /**
     * @var Database
     */
    public $db;

    /**
     * @var null|\Venus\SelectDB
     */
    public $sdb;

    /**
     * Main constructor.
     */
    public function __construct() {
        $config = $this->dealConfig('mysql');
        $this->db = new Database($config);
        $this->db->connect();
        $this->sdb = $this->db->dbApt;
    }

    private function dealConfig($name, $isLocal = false) {
        $arr = [
            'dbms'       => 'mysql',
            'type'       => Database::TYPE_PDO,
            'host'       => getenv('host'),
            'port'       => getenv('port'),
            'user'       => getenv('user'),
            'password'   => getenv('password'),
            'name'       => $name,
            'charset'    => 'utf8',
            'persistent' => false,
        ];
        if($isLocal || '127.0.0.1' == $_SERVER['SERVER_ADDR']) {
            $arr['host'] = '127.0.0.1';
            $arr['user'] = 'root';
            $arr['password'] = '123456789';
        }
        return $arr;
    }
}