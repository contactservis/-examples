<?php

namespace mklive\dev\user\orm;

use Bitrix\Main\Security\Random;
use Exception;
use mklive\dev\user\User;
use mklive\dev\user\UserAbstract;
use mklive\dev\user\Users;

class UserTable extends \Bitrix\Main\UserTable
{
    public static $type = "user";

    const GROUP_ID = 0;

    const GROUPS_EXCLUDED = [];

    public static function getObjectClass()
    {
        return User::class;
    }

    public static function getCollectionClass()
    {
        return Users::class;
    }

    public static function getMap()
    {

        // получаем битриксовый ORM MAP
        $bitrixOrmMap = parent::getMap();

        // дополняем его нужными нам полями
        $ourOrmMap = array();

        return array_merge($bitrixOrmMap, $ourOrmMap);

    }

    /**
     * Метод добавляет пользователя.
     *
     * @param type $data
     */
    public static function add(array $data)
    {
        return User::__add($data);
    }

    /**
     * Метод обновляет значения полей у пользователя.
     *
     * @param type $primary
     * @param array $data
     */
    public static function update($primary, array $data)
    {
        return User::__update($primary, $data);
    }

    /**
     * Метод удаляет пользователя.
     *
     * @param type $primary
     */
    public static function delete($primary)
    {
        return User::__delete($primary);
    }

    /**
     * Ищем пользователя по логину
     *
     * @param string $login
     * @return type
     */
    public static function getByLogin($login)
    {
        $params = [
            'filter' => array(
                'LOGIN' => $login
            ),
            'select' => ["*", "GROUPS"]
        ];

        if(intval(static::GROUP_ID) > 0)
            $params["filter"]["GROUPS.GROUP_ID"] = [static::GROUP_ID];

        return self::getList($params);
    }

    /**
     * Ищем пользователя по SecureID
     *
     * @param string $login
     * @return type
     */
    public static function getBySecureID($secureID)
    {
        return self::getList([
            'filter' => array(
                'PERSONAL_PAGER' => $secureID
            )
        ]);
    }

    /**
     * Ищем пользователя по EMAIL
     *
     * @param string $login
     * @return type
     */
    public static function getByEmail($login)
    {
        return self::getList([
            'filter' => array(
                'EMAIL' => $login
            )
        ]);
    }

    public static function activate($login)
    {

        $user = static::getByLogin($login)->fetchObject();

        if (!$user instanceof User)
            return false;

        $result = UserTable::update(["ID" => $user->getID()], ["ACTIVE" => "Y"]);

        return $result->isSuccess();

    }

    public static function deactivate($login)
    {

        $user = static::getByLogin($login)->fetchObject();

        if (!$user instanceof User)
            return false;

        $result = UserTable::update(["ID" => $user->getID()], ["ACTIVE" => "N"]);

        return $result->isSuccess();

    }





    public static function setGroups(User $user) {

        $groups = $user->fill("GROUPS")->getGroupIDList();

        if (count($groups) > 0)
            $groups = array_merge($groups, [static::GROUP_ID]);
        else
            $groups = [static::GROUP_ID];

        $groups = array_unique($groups);
        $groups = array_diff($groups, static::GROUPS_EXCLUDED);

        $user->setGroups($groups);
        $user->save();

        return $user;

    }

    public static function getByPrimary($primary, $params = [])
    {
        if (intval(static::GROUP_ID) > 0) {

            $users = parent::getByPrimary($primary, [
                "select" => [
                    "GROUPS"
                ],
                "filter" => [
                    "GROUPS.GROUP_ID" => static::GROUP_ID
                ]
            ]);

            if ($users->getSelectedRowsCount() == 0)
                return $users;

        }

        return parent::getByPrimary($primary, ["select" => ["*", "GROUPS"]]);

    }


}
