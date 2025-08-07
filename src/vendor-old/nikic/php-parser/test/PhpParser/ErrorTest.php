<?php

namespace PhpParser;

class ErrorTest extends \PHPUnit_Framework_TestCase
{
    public function test_construct()
    {
        $attributes = [
            'startLine' => 10,
            'endLine' => 11,
        ];
        $error = new Error('Some error', $attributes);

        $this->assertSame('Some error', $error->getRawMessage());
        $this->assertSame($attributes, $error->getAttributes());
        $this->assertSame(10, $error->getStartLine());
        $this->assertSame(11, $error->getEndLine());
        $this->assertSame(10, $error->getRawLine());
        $this->assertSame('Some error on line 10', $error->getMessage());

        return $error;
    }

    /**
     * @depends test_construct
     */
    public function test_set_message_and_line(Error $error)
    {
        $error->setRawMessage('Some other error');
        $this->assertSame('Some other error', $error->getRawMessage());

        $error->setStartLine(15);
        $this->assertSame(15, $error->getStartLine());
        $this->assertSame('Some other error on line 15', $error->getMessage());

        $error->setRawLine(17);
        $this->assertSame(17, $error->getRawLine());
        $this->assertSame('Some other error on line 17', $error->getMessage());
    }

    public function test_unknown_line()
    {
        $error = new Error('Some error');

        $this->assertSame(-1, $error->getStartLine());
        $this->assertSame(-1, $error->getEndLine());
        $this->assertSame(-1, $error->getRawLine());
        $this->assertSame('Some error on unknown line', $error->getMessage());
    }

    /** @dataProvider provideTestColumnInfo */
    public function test_column_info($code, $startPos, $endPos, $startColumn, $endColumn)
    {
        $error = new Error('Some error', [
            'startFilePos' => $startPos,
            'endFilePos' => $endPos,
        ]);

        $this->assertSame(true, $error->hasColumnInfo());
        $this->assertSame($startColumn, $error->getStartColumn($code));
        $this->assertSame($endColumn, $error->getEndColumn($code));

    }

    public function provideTestColumnInfo()
    {
        return [
            // Error at "bar"
            ['<?php foo bar baz', 10, 12, 11, 13],
            ["<?php\nfoo bar baz", 10, 12, 5, 7],
            ["<?php foo\nbar baz", 10, 12, 1, 3],
            ["<?php foo bar\nbaz", 10, 12, 11, 13],
            ["<?php\r\nfoo bar baz", 11, 13, 5, 7],
            // Error at "baz"
            ['<?php foo bar baz', 14, 16, 15, 17],
            ["<?php foo bar\nbaz", 14, 16, 1, 3],
            // Error at string literal
            ["<?php foo 'bar\nbaz' xyz", 10, 18, 11, 4],
            ["<?php\nfoo 'bar\nbaz' xyz", 10, 18, 5, 4],
            ["<?php foo\n'\nbarbaz\n'\nxyz", 10, 19, 1, 1],
            // Error over full string
            ['<?php', 0, 4, 1, 5],
            ["<?\nphp", 0, 5, 1, 3],
        ];
    }

    public function test_no_column_info()
    {
        $error = new Error('Some error', 3);

        $this->assertSame(false, $error->hasColumnInfo());
        try {
            $error->getStartColumn('');
            $this->fail('Expected RuntimeException');
        } catch (\RuntimeException $e) {
            $this->assertSame('Error does not have column information', $e->getMessage());
        }
        try {
            $error->getEndColumn('');
            $this->fail('Expected RuntimeException');
        } catch (\RuntimeException $e) {
            $this->assertSame('Error does not have column information', $e->getMessage());
        }
    }

    /**
     * @expectedException \RuntimeException
     *
     * @expectedExceptionMessage Invalid position information
     */
    public function test_invalid_pos_info()
    {
        $error = new Error('Some error', [
            'startFilePos' => 10,
            'endFilePos' => 11,
        ]);
        $error->getStartColumn('code');
    }
}
