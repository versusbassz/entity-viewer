<?php
use Codeception\Util\HttpCode;

class MiscCest
{
    public function testFrontendAvailable(AcceptanceTester $I)
    {
        $I->amOnPage('/');
        $I->see('Just another WordPress site');
    }

    public function testAdminPanelAvailable(AcceptanceTester $I)
    {
        $I->loginAsAdmin();
        $I->see('Dashboard');
    }

    public function testPostMetabox(AcceptanceTester $I)
    {
        $I->loginAsAdmin();
        $I->amOnPage('/wp-admin/post.php?post=1&action=edit');

        $I->waitForElement('#vsm-post-meta', 1); // The root metabox
        $I->waitForElement('.vsm-refresh', 3); // "Refresh" button
    }
}
