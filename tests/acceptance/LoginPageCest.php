<?php

namespace BayardTest\Tests\Codeception\Acceptance;

class LoginPageCest
{
    public function _before(\AcceptanceTester $I)
    {
    }

    public function _after(\AcceptanceTester $I)
    {
    }

    /**
     * @return array
     */
    public function userFeature()
    {
        return array(
            array(
                // "form" => array(
                    "username" => "remi",
                    "password" => "remi"
                // )
            )
        );
    }

    public function tryToAccesLoginPageTest(\AcceptanceTester $I)
    {
        $I->amOnPage("/login");
        $I->see("OpenClassRoom");
    }

    /**
     * @dataProvider userFeature
     */
    public function tryToConnectTest(\AcceptanceTester $I, \Codeception\Example $example)
    {
        $I->amOnPage("/login");
        $I->wait(10);
        // $I->wait(100);
        // $I->fillField("_username", $example["username"]);
        // $I->fillField("_password", $example["password"]);
        // $I->click('Connexion');
        // $I->seeInCurrentUrl("/platform/view");
        // $I->seeLink("Logout");
    }
}
