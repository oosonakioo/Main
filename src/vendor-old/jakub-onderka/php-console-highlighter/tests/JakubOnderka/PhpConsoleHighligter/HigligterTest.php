<?php

namespace JakubOnderka\PhpConsoleHighlighter;

class HighlighterTest extends \PHPUnit_Framework_TestCase
{
    /** @var Highlighter */
    private $uut;

    protected function getConsoleColorMock()
    {
        $mock = $this->getMock('\JakubOnderka\PhpConsoleColor\ConsoleColor');

        $mock->expects($this->any())
            ->method('apply')
            ->will($this->returnCallback(function ($style, $text) {
                return "<$style>$text</$style>";
            }));

        $mock->expects($this->any())
            ->method('hasTheme')
            ->will($this->returnValue(true));

        return $mock;
    }

    protected function setUp()
    {
        $this->uut = new Highlighter($this->getConsoleColorMock());
    }

    protected function compare($original, $expected)
    {
        $output = $this->uut->getWholeFile($original);
        $this->assertEquals($expected, $output);
    }

    public function test_variable()
    {
        $this->compare(
            <<<'EOL'
<?php
echo $a;
EOL
            ,
            <<<'EOL'
<token_default><?php</token_default>
<token_keyword>echo </token_keyword><token_default>$a</token_default><token_keyword>;</token_keyword>
EOL
        );
    }

    public function test_integer()
    {
        $this->compare(
            <<<'EOL'
<?php
echo 43;
EOL
            ,
            <<<'EOL'
<token_default><?php</token_default>
<token_keyword>echo </token_keyword><token_default>43</token_default><token_keyword>;</token_keyword>
EOL
        );
    }

    public function test_float()
    {
        $this->compare(
            <<<'EOL'
<?php
echo 43.3;
EOL
            ,
            <<<'EOL'
<token_default><?php</token_default>
<token_keyword>echo </token_keyword><token_default>43.3</token_default><token_keyword>;</token_keyword>
EOL
        );
    }

    public function test_hex()
    {
        $this->compare(
            <<<'EOL'
<?php
echo 0x43;
EOL
            ,
            <<<'EOL'
<token_default><?php</token_default>
<token_keyword>echo </token_keyword><token_default>0x43</token_default><token_keyword>;</token_keyword>
EOL
        );
    }

    public function test_basic_function()
    {
        $this->compare(
            <<<'EOL'
<?php
function plus($a, $b) {
    return $a + $b;
}
EOL
            ,
            <<<'EOL'
<token_default><?php</token_default>
<token_keyword>function </token_keyword><token_default>plus</token_default><token_keyword>(</token_keyword><token_default>$a</token_default><token_keyword>, </token_keyword><token_default>$b</token_default><token_keyword>) {</token_keyword>
<token_keyword>    return </token_keyword><token_default>$a </token_default><token_keyword>+ </token_keyword><token_default>$b</token_default><token_keyword>;</token_keyword>
<token_keyword>}</token_keyword>
EOL
        );
    }

    public function test_string_normal()
    {
        $this->compare(
            <<<'EOL'
<?php
echo 'Ahoj světe';
EOL
            ,
            <<<'EOL'
<token_default><?php</token_default>
<token_keyword>echo </token_keyword><token_string>'Ahoj světe'</token_string><token_keyword>;</token_keyword>
EOL
        );
    }

    public function test_string_double()
    {
        $this->compare(
            <<<'EOL'
<?php
echo "Ahoj světe";
EOL
            ,
            <<<'EOL'
<token_default><?php</token_default>
<token_keyword>echo </token_keyword><token_string>"Ahoj světe"</token_string><token_keyword>;</token_keyword>
EOL
        );
    }

    public function test_instanceof()
    {
        $this->compare(
            <<<'EOL'
<?php
$a instanceof stdClass;
EOL
            ,
            <<<'EOL'
<token_default><?php</token_default>
<token_default>$a </token_default><token_keyword>instanceof </token_keyword><token_default>stdClass</token_default><token_keyword>;</token_keyword>
EOL
        );
    }

    /*
     * Constants
     */
    public function test_constant()
    {
        $constants = [
            '__FILE__',
            '__LINE__',
            '__CLASS__',
            '__FUNCTION__',
            '__METHOD__',
            '__TRAIT__',
            '__DIR__',
            '__NAMESPACE__',
        ];

        foreach ($constants as $constant) {
            $this->compare(
                <<<EOL
<?php
$constant;
EOL
                ,
                <<<EOL
<token_default><?php</token_default>
<token_default>$constant</token_default><token_keyword>;</token_keyword>
EOL
            );
        }
    }

    /*
     * Comments
     */
    public function test_comment()
    {
        $this->compare(
            <<<'EOL'
<?php
/* Ahoj */
EOL
            ,
            <<<'EOL'
<token_default><?php</token_default>
<token_comment>/* Ahoj */</token_comment>
EOL
        );
    }

    public function test_doc_comment()
    {
        $this->compare(
            <<<'EOL'
<?php
/** Ahoj */
EOL
            ,
            <<<'EOL'
<token_default><?php</token_default>
<token_comment>/** Ahoj */</token_comment>
EOL
        );
    }

    public function test_inline_comment()
    {
        $this->compare(
            <<<'EOL'
<?php
// Ahoj
EOL
            ,
            <<<'EOL'
<token_default><?php</token_default>
<token_comment>// Ahoj</token_comment>
EOL
        );
    }

    public function test_hash_comment()
    {
        $this->compare(
            <<<'EOL'
<?php
# Ahoj
EOL
            ,
            <<<'EOL'
<token_default><?php</token_default>
<token_comment># Ahoj</token_comment>
EOL
        );
    }

    public function test_empty()
    {
        $this->compare(
            '',
            ''
        );
    }
}
