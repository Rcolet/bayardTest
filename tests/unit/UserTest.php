<?php

namespace BayardTest\Tests\Codeception\Functional;

use BayardTest\UserBundle\Entity\User;

class UserTest extends \Codeception\Test\Unit
{
    /**
     * @var User
     */
    protected $user;
    
    protected function _before()
    {
        $this->user = new User();
    }

    protected function _after()
    {
    }

     /**
     * @return array
     */
    public function userFeature()
    {
        return array(
            array(
                "remi", "remi", "SHA-256", ["ROLE_ADMIN", "ROLE_MODERATEUR"]
            )
        );
    }

    /**
     * @dataProvider userFeature
     */
    public function testUserCreation($username, $password, $salt, $roles)
    {
        $this->user->setUsername($username);
        $this->user->setPassword($password);
        $this->user->setSalt($salt);
        $this->user->setRoles($roles);
        $this->assertTrue(
            strcmp($this->user->getUsername(), $username) === 0
            && strcmp($this->user->getPassword(), $password) === 0
            && strcmp($this->user->getSalt(), $salt) == 0
            && empty(array_diff($this->user->getRoles(), $roles))
        );
    }
}
