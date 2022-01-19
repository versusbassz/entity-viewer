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

    public function testTermMetabox(AcceptanceTester $I)
    {
        $I->loginAsAdmin();
        $I->amOnPage('/wp-admin/term.php?taxonomy=category&tag_ID=1&post_type=post');

        $I->waitForElement('#js-vsm-metabox', 1); // The root metabox
        $I->waitForElement('.vsm-refresh', 3); // "Refresh" button
    }

    public function testUserMetabox(AcceptanceTester $I)
    {
        $I->loginAsAdmin();
        $I->amOnPage('/wp-admin/profile.php');

        $I->waitForElement('#js-vsm-metabox', 1); // The root metabox
        $I->waitForElement('.vsm-refresh', 3); // "Refresh" button
    }

    public function testCommentMetabox(AcceptanceTester $I)
    {
        $I->loginAsAdmin();
        $I->amOnPage('/wp-admin/comment.php?action=editcomment&c=2');

        $I->waitForElement('#vsm-comment-meta', 1); // The root metabox
        $I->waitForElement('.vsm-refresh', 3); // "Refresh" button
    }
}
