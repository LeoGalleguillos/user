<?php
namespace LeoGalleguillos\User\Model\Service;

use LeoGalleguillos\User\Model\Entity as UserEntity;
use LeoGalleguillos\User\Model\Factory as UserFactory;
use LeoGalleguillos\User\Model\Table as UserTable;

class Login
{
    /**
     * Construct.
     *
     * @param UserTable\User $userTable
     * @param UserTable\User\LoginHash $loginHashTable
     */
    public function __construct(
        UserFactory\User $userFactory,
        UserTable\User $userTable,
        UserTable\User\LoginHash $loginHashTable,
        UserTable\User\LoginIp $loginIpTable
    ) {
        $this->userFactory    = $userFactory;
        $this->userTable      = $userTable;
        $this->loginHashTable = $loginHashTable;
        $this->loginIpTable   = $loginIpTable;
    }

    /**
     * Login
     *
     * @return bool
     */
    public function login() : bool
    {
        if (empty($_POST['username'])
            || empty($_POST['password'])) {
            return false;
        }

        $userArray = $this->userTable->selectWhereUsername($_POST['username']);
        if (empty($userArray)) {
            return false;
        }

        $username     = $userArray['username'];
        $passwordHash = $userArray['password_hash'];

        if (!password_verify($_POST['password'], $passwordHash)) {
            return false;
        }

        $userEntity = $this->userFactory->buildFromUsername($username);
        $loginHash  = password_hash($userEntity->getUserId() . time(), PASSWORD_DEFAULT);
        $loginIp    = $_SERVER['REMOTE_ADDR'];

        $this->loginHashTable->updateWhereUserId(
            $loginHash,
            $userEntity->getUserId()
        );
        $this->loginIpTable->updateWhereUserId(
            $loginIp,
            $userEntity->getUserId()
        );

        $this->setCookies(
            $userEntity,
            $loginHash
        );

        $_SESSION['username'] = $userEntity->getUsername();
        return true;
    }

    protected function setCookies(
        UserEntity\User $userEntity,
        string $loginHash
    ) {
        $name   = 'userId';
        $value  = $userEntity->getUserId();
        $expire = empty($_POST['keep']) ? 0 : time() + 30 * 24 * 60 * 60;
        $path   = '/';
        $domain = $_SERVER['HTTP_HOST'];
        $secure = true;
        @setcookie(
            $name,
            $value,
            $expire,
            $path,
            $domain,
            $secure
        );

        $name   = 'loginHash';
        $value  = $loginHash;
        @setcookie(
            $name,
            $value,
            $expire,
            $path,
            $domain,
            $secure
        );

        $name  = 'loginIp';
        $value = $_SERVER['REMOTE_ADDR'];
        @setcookie(
            $name,
            $value,
            $expire,
            $path,
            $domain,
            $secure
        );
    }
}
