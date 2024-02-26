<?php


namespace Itekcom_Oussamasamia;


use Customer;
use Db;

class CustomerGithub extends Customer
{
    /** @var int Github id */
    public $github_id;


    public function __construct($id = null)
    {

        self::$definition['fields']['github_id'] = [
            'type' => self::TYPE_INT,
            'validate' => 'isUnsignedId',
            'copy_post' => false
        ];

        parent::__construct($id);

    }


    public static function checkIfUserExists($githubUserId)
    {
        // Perform a database query to check if the user exists
        $sql = 'SELECT COUNT(*) 
            FROM `' . _DB_PREFIX_ . 'customer` 
            WHERE `github_id` = \'' . pSQL($githubUserId) . '\' ';

        $count = Db::getInstance()->getValue($sql);

        // If count is greater than 0, the user exists
        return $count > 0;
    }


    public static function getIdByGithubId($githubId)
    {
        $sql = 'SELECT id_customer FROM ' . _DB_PREFIX_ . 'customer WHERE github_id = ' . (int)$githubId;
        $result = Db::getInstance()->getValue($sql);

        if ($result) {
            return (int)$result;
        }

        return false;
    }


}