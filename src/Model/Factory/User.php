<?php
namespace LeoGalleguillos\User\Model\Factory;

use ArrayObject;
use DateTime;
use LeoGalleguillos\User\Model\Entity as UserEntity;
use LeoGalleguillos\User\Model\Table\User as UserTable;

class User
{
    protected $cache = [];

    public function __construct(UserTable $userTable)
    {
        $this->userTable = $userTable;
    }

    public function buildFromUserId(int $userId)
    {
        if (isset($this->cache[$userId])) {
            return $this->cache[$userId];
        }

        $array = $this->userTable->selectWhereUserId(
            $userId
        );
        $userEntity = $this->buildFromArray($array);

        $this->cache[$userId] = $userEntity;

        return $userEntity;
    }

    public function buildFromArray(array $array) : UserEntity\User
    {
        $userEntity = new UserEntity\User();

        $userEntity->setUserId($array['user_id']);

        if (isset($array['created'])) {
            $userEntity->setCreated(
                new DateTime($array['created'])
            );
        }
        if (isset($array['deleted_datetime'])) {
            $userEntity->setDeletedDateTime(new DateTime($array['deleted_datetime']));
        }
        if (isset($array['deleted_reason'])) {
            $userEntity->setDeletedReason($array['deleted_reason']);
        }
        if (isset($array['deleted_user_id'])) {
            $userEntity->setDeletedUserId($array['deleted_user_id']);
        }
        if (isset($array['display_name'])) {
            $userEntity->setDisplayName($array['display_name']);
        }
        if (isset($array['gender'])) {
            $userEntity->setGender($array['gender']);
        }
        if (isset($array['username'])) {
            $userEntity->setUsername($array['username']);
        }
        if (isset($array['welcome_message'])) {
            $userEntity->setWelcomeMessage($array['welcome_message']);
        }

        $userEntity->setViews(
            (int) ($array['views'] ?? 0)
        );

        return $userEntity;
    }

    public function buildFromUsername(string $username): UserEntity\User
    {
        return $this->buildFromArray(
            $this->userTable->selectWhereUsername($username)
        );
    }
}
