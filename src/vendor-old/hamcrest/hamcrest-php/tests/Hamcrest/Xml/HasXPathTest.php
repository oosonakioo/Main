<?php

namespace Hamcrest\Xml;

class HasXPathTest extends \Hamcrest\AbstractMatcherTest
{
    protected static $xml;

    protected static $doc;

    protected static $html;

    public static function setUpBeforeClass()
    {
        self::$xml = <<<'XML'
<?xml version="1.0"?>
<users>
    <user>
        <id>alice</id>
        <name>Alice Frankel</name>
        <role>admin</role>
    </user>
    <user>
        <id>bob</id>
        <name>Bob Frankel</name>
        <role>user</role>
    </user>
    <user>
        <id>charlie</id>
        <name>Charlie Chan</name>
        <role>user</role>
    </user>
</users>
XML;
        self::$doc = new \DOMDocument;
        self::$doc->loadXML(self::$xml);

        self::$html = <<<'HTML'
<html>
    <head>
        <title>Home Page</title>
    </head>
    <body>
        <h1>Heading</h1>
        <p>Some text</p>
    </body>
</html>
HTML;
    }

    protected function createMatcher()
    {
        return \Hamcrest\Xml\HasXPath::hasXPath('/users/user');
    }

    public function test_matches_when_x_path_is_found()
    {
        assertThat('one match', self::$doc, hasXPath('user[id = "bob"]'));
        assertThat('two matches', self::$doc, hasXPath('user[role = "user"]'));
    }

    public function test_does_not_match_when_x_path_is_not_found()
    {
        assertThat(
            'no match',
            self::$doc,
            not(hasXPath('user[contains(id, "frank")]'))
        );
    }

    public function test_matches_when_expression_without_matcher_evaluates_to_true()
    {
        assertThat(
            'one match',
            self::$doc,
            hasXPath('count(user[id = "bob"])')
        );
    }

    public function test_does_not_match_when_expression_without_matcher_evaluates_to_false()
    {
        assertThat(
            'no matches',
            self::$doc,
            not(hasXPath('count(user[id = "frank"])'))
        );
    }

    public function test_matches_when_expression_is_equal()
    {
        assertThat(
            'one match',
            self::$doc,
            hasXPath('count(user[id = "bob"])', 1)
        );
        assertThat(
            'two matches',
            self::$doc,
            hasXPath('count(user[role = "user"])', 2)
        );
    }

    public function test_does_not_match_when_expression_is_not_equal()
    {
        assertThat(
            'no match',
            self::$doc,
            not(hasXPath('count(user[id = "frank"])', 2))
        );
        assertThat(
            'one match',
            self::$doc,
            not(hasXPath('count(user[role = "admin"])', 2))
        );
    }

    public function test_matches_when_content_matches()
    {
        assertThat(
            'one match',
            self::$doc,
            hasXPath('user/name', containsString('ice'))
        );
        assertThat(
            'two matches',
            self::$doc,
            hasXPath('user/role', equalTo('user'))
        );
    }

    public function test_does_not_match_when_content_does_not_match()
    {
        assertThat(
            'no match',
            self::$doc,
            not(hasXPath('user/name', containsString('Bobby')))
        );
        assertThat(
            'no matches',
            self::$doc,
            not(hasXPath('user/role', equalTo('owner')))
        );
    }

    public function test_provides_convenient_shortcut_for_has_x_path_equal_to()
    {
        assertThat('matches', self::$doc, hasXPath('count(user)', 3));
        assertThat('matches', self::$doc, hasXPath('user[2]/id', 'bob'));
    }

    public function test_provides_convenient_shortcut_for_has_x_path_count_equal_to()
    {
        assertThat('matches', self::$doc, hasXPath('user[id = "charlie"]', 1));
    }

    public function test_matches_accepts_xml_string()
    {
        assertThat('accepts XML string', self::$xml, hasXPath('user'));
    }

    public function test_matches_accepts_html_string()
    {
        assertThat('accepts HTML string', self::$html, hasXPath('body/h1', 'Heading'));
    }

    public function test_has_a_readable_description()
    {
        $this->assertDescription(
            'XML or HTML document with XPath "/users/user"',
            hasXPath('/users/user')
        );
        $this->assertDescription(
            'XML or HTML document with XPath "count(/users/user)" <2>',
            hasXPath('/users/user', 2)
        );
        $this->assertDescription(
            'XML or HTML document with XPath "/users/user/name"'
            .' a string starting with "Alice"',
            hasXPath('/users/user/name', startsWith('Alice'))
        );
    }

    public function test_has_a_readable_mismatch_description()
    {
        $this->assertMismatchDescription(
            'XPath returned no results',
            hasXPath('/users/name'),
            self::$doc
        );
        $this->assertMismatchDescription(
            'XPath expression result was <3F>',
            hasXPath('/users/user', 2),
            self::$doc
        );
        $this->assertMismatchDescription(
            'XPath returned ["alice", "bob", "charlie"]',
            hasXPath('/users/user/id', 'Frank'),
            self::$doc
        );
    }
}
