<?php
namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Acceptance extends \Codeception\Module
{
    public function seeClosedHtmlTag()
    {
        $content = $this->getModule('PhpBrowser')->_getResponseContent();

        $this->assertContains('</html>', $content);
    }
}
