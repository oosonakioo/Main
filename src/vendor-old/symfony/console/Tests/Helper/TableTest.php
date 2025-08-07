<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Tests\Helper;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Helper\TableStyle;
use Symfony\Component\Console\Output\StreamOutput;

class TableTest extends \PHPUnit_Framework_TestCase
{
    protected $stream;

    protected function setUp()
    {
        $this->stream = fopen('php://memory', 'r+');
    }

    protected function tearDown()
    {
        fclose($this->stream);
        $this->stream = null;
    }

    /**
     * @dataProvider testRenderProvider
     */
    public function test_render($headers, $rows, $style, $expected)
    {
        $table = new Table($output = $this->getOutputStream());
        $table
            ->setHeaders($headers)
            ->setRows($rows)
            ->setStyle($style);
        $table->render();

        $this->assertEquals($expected, $this->getOutputContent($output));
    }

    /**
     * @dataProvider testRenderProvider
     */
    public function test_render_add_rows($headers, $rows, $style, $expected)
    {
        $table = new Table($output = $this->getOutputStream());
        $table
            ->setHeaders($headers)
            ->addRows($rows)
            ->setStyle($style);
        $table->render();

        $this->assertEquals($expected, $this->getOutputContent($output));
    }

    /**
     * @dataProvider testRenderProvider
     */
    public function test_render_add_rows_one_by_one($headers, $rows, $style, $expected)
    {
        $table = new Table($output = $this->getOutputStream());
        $table
            ->setHeaders($headers)
            ->setStyle($style);
        foreach ($rows as $row) {
            $table->addRow($row);
        }
        $table->render();

        $this->assertEquals($expected, $this->getOutputContent($output));
    }

    public function test_render_provider()
    {
        $books = [
            ['99921-58-10-7', 'Divine Comedy', 'Dante Alighieri'],
            ['9971-5-0210-0', 'A Tale of Two Cities', 'Charles Dickens'],
            ['960-425-059-0', 'The Lord of the Rings', 'J. R. R. Tolkien'],
            ['80-902734-1-6', 'And Then There Were None', 'Agatha Christie'],
        ];

        return [
            [
                ['ISBN', 'Title', 'Author'],
                $books,
                'default',
                <<<'TABLE'
+---------------+--------------------------+------------------+
| ISBN          | Title                    | Author           |
+---------------+--------------------------+------------------+
| 99921-58-10-7 | Divine Comedy            | Dante Alighieri  |
| 9971-5-0210-0 | A Tale of Two Cities     | Charles Dickens  |
| 960-425-059-0 | The Lord of the Rings    | J. R. R. Tolkien |
| 80-902734-1-6 | And Then There Were None | Agatha Christie  |
+---------------+--------------------------+------------------+

TABLE
            ],
            [
                ['ISBN', 'Title', 'Author'],
                $books,
                'compact',
                <<<'TABLE'
 ISBN          Title                    Author           
 99921-58-10-7 Divine Comedy            Dante Alighieri  
 9971-5-0210-0 A Tale of Two Cities     Charles Dickens  
 960-425-059-0 The Lord of the Rings    J. R. R. Tolkien 
 80-902734-1-6 And Then There Were None Agatha Christie  

TABLE
            ],
            [
                ['ISBN', 'Title', 'Author'],
                $books,
                'borderless',
                <<<'TABLE'
 =============== ========================== ================== 
  ISBN            Title                      Author            
 =============== ========================== ================== 
  99921-58-10-7   Divine Comedy              Dante Alighieri   
  9971-5-0210-0   A Tale of Two Cities       Charles Dickens   
  960-425-059-0   The Lord of the Rings      J. R. R. Tolkien  
  80-902734-1-6   And Then There Were None   Agatha Christie   
 =============== ========================== ================== 

TABLE
            ],
            [
                ['ISBN', 'Title'],
                [
                    ['99921-58-10-7', 'Divine Comedy', 'Dante Alighieri'],
                    ['9971-5-0210-0'],
                    ['960-425-059-0', 'The Lord of the Rings', 'J. R. R. Tolkien'],
                    ['80-902734-1-6', 'And Then There Were None', 'Agatha Christie'],
                ],
                'default',
                <<<'TABLE'
+---------------+--------------------------+------------------+
| ISBN          | Title                    |                  |
+---------------+--------------------------+------------------+
| 99921-58-10-7 | Divine Comedy            | Dante Alighieri  |
| 9971-5-0210-0 |                          |                  |
| 960-425-059-0 | The Lord of the Rings    | J. R. R. Tolkien |
| 80-902734-1-6 | And Then There Were None | Agatha Christie  |
+---------------+--------------------------+------------------+

TABLE
            ],
            [
                [],
                [
                    ['99921-58-10-7', 'Divine Comedy', 'Dante Alighieri'],
                    ['9971-5-0210-0'],
                    ['960-425-059-0', 'The Lord of the Rings', 'J. R. R. Tolkien'],
                    ['80-902734-1-6', 'And Then There Were None', 'Agatha Christie'],
                ],
                'default',
                <<<'TABLE'
+---------------+--------------------------+------------------+
| 99921-58-10-7 | Divine Comedy            | Dante Alighieri  |
| 9971-5-0210-0 |                          |                  |
| 960-425-059-0 | The Lord of the Rings    | J. R. R. Tolkien |
| 80-902734-1-6 | And Then There Were None | Agatha Christie  |
+---------------+--------------------------+------------------+

TABLE
            ],
            [
                ['ISBN', 'Title', 'Author'],
                [
                    ['99921-58-10-7', "Divine\nComedy", 'Dante Alighieri'],
                    ['9971-5-0210-2', "Harry Potter\nand the Chamber of Secrets", "Rowling\nJoanne K."],
                    ['9971-5-0210-2', "Harry Potter\nand the Chamber of Secrets", "Rowling\nJoanne K."],
                    ['960-425-059-0', 'The Lord of the Rings', "J. R. R.\nTolkien"],
                ],
                'default',
                <<<'TABLE'
+---------------+----------------------------+-----------------+
| ISBN          | Title                      | Author          |
+---------------+----------------------------+-----------------+
| 99921-58-10-7 | Divine                     | Dante Alighieri |
|               | Comedy                     |                 |
| 9971-5-0210-2 | Harry Potter               | Rowling         |
|               | and the Chamber of Secrets | Joanne K.       |
| 9971-5-0210-2 | Harry Potter               | Rowling         |
|               | and the Chamber of Secrets | Joanne K.       |
| 960-425-059-0 | The Lord of the Rings      | J. R. R.        |
|               |                            | Tolkien         |
+---------------+----------------------------+-----------------+

TABLE
            ],
            [
                ['ISBN', 'Title'],
                [],
                'default',
                <<<'TABLE'
+------+-------+
| ISBN | Title |
+------+-------+

TABLE
            ],
            [
                [],
                [],
                'default',
                '',
            ],
            'Cell text with tags used for Output styling' => [
                ['ISBN', 'Title', 'Author'],
                [
                    ['<info>99921-58-10-7</info>', '<error>Divine Comedy</error>', '<fg=blue;bg=white>Dante Alighieri</fg=blue;bg=white>'],
                    ['9971-5-0210-0', 'A Tale of Two Cities', '<info>Charles Dickens</>'],
                ],
                'default',
                <<<'TABLE'
+---------------+----------------------+-----------------+
| ISBN          | Title                | Author          |
+---------------+----------------------+-----------------+
| 99921-58-10-7 | Divine Comedy        | Dante Alighieri |
| 9971-5-0210-0 | A Tale of Two Cities | Charles Dickens |
+---------------+----------------------+-----------------+

TABLE
            ],
            'Cell text with tags not used for Output styling' => [
                ['ISBN', 'Title', 'Author'],
                [
                    ['<strong>99921-58-10-700</strong>', '<f>Divine Com</f>', 'Dante Alighieri'],
                    ['9971-5-0210-0', 'A Tale of Two Cities', 'Charles Dickens'],
                ],
                'default',
                <<<'TABLE'
+----------------------------------+----------------------+-----------------+
| ISBN                             | Title                | Author          |
+----------------------------------+----------------------+-----------------+
| <strong>99921-58-10-700</strong> | <f>Divine Com</f>    | Dante Alighieri |
| 9971-5-0210-0                    | A Tale of Two Cities | Charles Dickens |
+----------------------------------+----------------------+-----------------+

TABLE
            ],
            'Cell with colspan' => [
                ['ISBN', 'Title', 'Author'],
                [
                    ['99921-58-10-7', 'Divine Comedy', 'Dante Alighieri'],
                    new TableSeparator,
                    [new TableCell('Divine Comedy(Dante Alighieri)', ['colspan' => 3])],
                    new TableSeparator,
                    [
                        new TableCell('Arduino: A Quick-Start Guide', ['colspan' => 2]),
                        'Mark Schmidt',
                    ],
                    new TableSeparator,
                    [
                        '9971-5-0210-0',
                        new TableCell("A Tale of \nTwo Cities", ['colspan' => 2]),
                    ],
                    new TableSeparator,
                    [
                        new TableCell('Cupiditate dicta atque porro, tempora exercitationem modi animi nulla nemo vel nihil!', ['colspan' => 3]),
                    ],
                ],
                'default',
                <<<'TABLE'
+-------------------------------+-------------------------------+-----------------------------+
| ISBN                          | Title                         | Author                      |
+-------------------------------+-------------------------------+-----------------------------+
| 99921-58-10-7                 | Divine Comedy                 | Dante Alighieri             |
+-------------------------------+-------------------------------+-----------------------------+
| Divine Comedy(Dante Alighieri)                                                              |
+-------------------------------+-------------------------------+-----------------------------+
| Arduino: A Quick-Start Guide                                  | Mark Schmidt                |
+-------------------------------+-------------------------------+-----------------------------+
| 9971-5-0210-0                 | A Tale of                                                   |
|                               | Two Cities                                                  |
+-------------------------------+-------------------------------+-----------------------------+
| Cupiditate dicta atque porro, tempora exercitationem modi animi nulla nemo vel nihil!       |
+-------------------------------+-------------------------------+-----------------------------+

TABLE
            ],
            'Cell with rowspan' => [
                ['ISBN', 'Title', 'Author'],
                [
                    [
                        new TableCell('9971-5-0210-0', ['rowspan' => 3]),
                        'Divine Comedy',
                        'Dante Alighieri',
                    ],
                    ['A Tale of Two Cities', 'Charles Dickens'],
                    ["The Lord of \nthe Rings", "J. R. \nR. Tolkien"],
                    new TableSeparator,
                    ['80-902734-1-6', new TableCell("And Then \nThere \nWere None", ['rowspan' => 3]), 'Agatha Christie'],
                    ['80-902734-1-7', 'Test'],
                ],
                'default',
                <<<'TABLE'
+---------------+----------------------+-----------------+
| ISBN          | Title                | Author          |
+---------------+----------------------+-----------------+
| 9971-5-0210-0 | Divine Comedy        | Dante Alighieri |
|               | A Tale of Two Cities | Charles Dickens |
|               | The Lord of          | J. R.           |
|               | the Rings            | R. Tolkien      |
+---------------+----------------------+-----------------+
| 80-902734-1-6 | And Then             | Agatha Christie |
| 80-902734-1-7 | There                | Test            |
|               | Were None            |                 |
+---------------+----------------------+-----------------+

TABLE
            ],
            'Cell with rowspan and colspan' => [
                ['ISBN', 'Title', 'Author'],
                [
                    [
                        new TableCell('9971-5-0210-0', ['rowspan' => 2, 'colspan' => 2]),
                        'Dante Alighieri',
                    ],
                    ['Charles Dickens'],
                    new TableSeparator,
                    [
                        'Dante Alighieri',
                        new TableCell('9971-5-0210-0', ['rowspan' => 3, 'colspan' => 2]),
                    ],
                    ['J. R. R. Tolkien'],
                    ['J. R. R'],
                ],
                'default',
                <<<'TABLE'
+------------------+---------+-----------------+
| ISBN             | Title   | Author          |
+------------------+---------+-----------------+
| 9971-5-0210-0              | Dante Alighieri |
|                            | Charles Dickens |
+------------------+---------+-----------------+
| Dante Alighieri  | 9971-5-0210-0             |
| J. R. R. Tolkien |                           |
| J. R. R          |                           |
+------------------+---------+-----------------+

TABLE
            ],
            'Cell with rowspan and colspan contains new line break' => [
                ['ISBN', 'Title', 'Author'],
                [
                    [
                        new TableCell("9971\n-5-\n021\n0-0", ['rowspan' => 2, 'colspan' => 2]),
                        'Dante Alighieri',
                    ],
                    ['Charles Dickens'],
                    new TableSeparator,
                    [
                        'Dante Alighieri',
                        new TableCell("9971\n-5-\n021\n0-0", ['rowspan' => 2, 'colspan' => 2]),
                    ],
                    ['Charles Dickens'],
                    new TableSeparator,
                    [
                        new TableCell("9971\n-5-\n021\n0-0", ['rowspan' => 2, 'colspan' => 2]),
                        new TableCell("Dante \nAlighieri", ['rowspan' => 2, 'colspan' => 1]),
                    ],
                ],
                'default',
                <<<'TABLE'
+-----------------+-------+-----------------+
| ISBN            | Title | Author          |
+-----------------+-------+-----------------+
| 9971                    | Dante Alighieri |
| -5-                     | Charles Dickens |
| 021                     |                 |
| 0-0                     |                 |
+-----------------+-------+-----------------+
| Dante Alighieri | 9971                    |
| Charles Dickens | -5-                     |
|                 | 021                     |
|                 | 0-0                     |
+-----------------+-------+-----------------+
| 9971                    | Dante           |
| -5-                     | Alighieri       |
| 021                     |                 |
| 0-0                     |                 |
+-----------------+-------+-----------------+

TABLE
            ],
            'Cell with rowspan and colspan without using TableSeparator' => [
                ['ISBN', 'Title', 'Author'],
                [
                    [
                        new TableCell("9971\n-5-\n021\n0-0", ['rowspan' => 2, 'colspan' => 2]),
                        'Dante Alighieri',
                    ],
                    ['Charles Dickens'],
                    [
                        'Dante Alighieri',
                        new TableCell("9971\n-5-\n021\n0-0", ['rowspan' => 2, 'colspan' => 2]),
                    ],
                    ['Charles Dickens'],
                ],
                'default',
                <<<'TABLE'
+-----------------+-------+-----------------+
| ISBN            | Title | Author          |
+-----------------+-------+-----------------+
| 9971                    | Dante Alighieri |
| -5-                     | Charles Dickens |
| 021                     |                 |
| 0-0                     |                 |
| Dante Alighieri | 9971                    |
| Charles Dickens | -5-                     |
|                 | 021                     |
|                 | 0-0                     |
+-----------------+-------+-----------------+

TABLE
            ],
            'Cell with rowspan and colspan with separator inside a rowspan' => [
                ['ISBN', 'Author'],
                [
                    [
                        new TableCell('9971-5-0210-0', ['rowspan' => 3, 'colspan' => 1]),
                        'Dante Alighieri',
                    ],
                    [new TableSeparator],
                    ['Charles Dickens'],
                ],
                'default',
                <<<'TABLE'
+---------------+-----------------+
| ISBN          | Author          |
+---------------+-----------------+
| 9971-5-0210-0 | Dante Alighieri |
|               |-----------------|
|               | Charles Dickens |
+---------------+-----------------+

TABLE
            ],
            'Multiple header lines' => [
                [
                    [new TableCell('Main title', ['colspan' => 3])],
                    ['ISBN', 'Title', 'Author'],
                ],
                [],
                'default',
                <<<'TABLE'
+------+-------+--------+
| Main title            |
+------+-------+--------+
| ISBN | Title | Author |
+------+-------+--------+

TABLE
            ],
            'Row with multiple cells' => [
                [],
                [
                    [
                        new TableCell('1', ['colspan' => 3]),
                        new TableCell('2', ['colspan' => 2]),
                        new TableCell('3', ['colspan' => 2]),
                        new TableCell('4', ['colspan' => 2]),
                    ],
                ],
                'default',
                <<<'TABLE'
+---+--+--+---+--+---+--+---+--+
| 1       | 2    | 3    | 4    |
+---+--+--+---+--+---+--+---+--+

TABLE
            ],
        ];
    }

    public function test_render_multi_byte()
    {
        $table = new Table($output = $this->getOutputStream());
        $table
            ->setHeaders(['■■'])
            ->setRows([[1234]])
            ->setStyle('default');
        $table->render();

        $expected =
<<<'TABLE'
+------+
| ■■   |
+------+
| 1234 |
+------+

TABLE;

        $this->assertEquals($expected, $this->getOutputContent($output));
    }

    public function test_style()
    {
        $style = new TableStyle;
        $style
            ->setHorizontalBorderChar('.')
            ->setVerticalBorderChar('.')
            ->setCrossingChar('.');

        Table::setStyleDefinition('dotfull', $style);
        $table = new Table($output = $this->getOutputStream());
        $table
            ->setHeaders(['Foo'])
            ->setRows([['Bar']])
            ->setStyle('dotfull');
        $table->render();

        $expected =
<<<'TABLE'
.......
. Foo .
.......
. Bar .
.......

TABLE;

        $this->assertEquals($expected, $this->getOutputContent($output));
    }

    public function test_row_separator()
    {
        $table = new Table($output = $this->getOutputStream());
        $table
            ->setHeaders(['Foo'])
            ->setRows([
                ['Bar1'],
                new TableSeparator,
                ['Bar2'],
                new TableSeparator,
                ['Bar3'],
            ]);
        $table->render();

        $expected =
<<<'TABLE'
+------+
| Foo  |
+------+
| Bar1 |
+------+
| Bar2 |
+------+
| Bar3 |
+------+

TABLE;

        $this->assertEquals($expected, $this->getOutputContent($output));

        $this->assertEquals($table, $table->addRow(new TableSeparator), 'fluent interface on addRow() with a single TableSeparator() works');
    }

    public function test_render_multi_calls()
    {
        $table = new Table($output = $this->getOutputStream());
        $table->setRows([
            [new TableCell('foo', ['colspan' => 2])],
        ]);
        $table->render();
        $table->render();
        $table->render();

        $expected =
<<<'TABLE'
+----+---+
| foo    |
+----+---+
+----+---+
| foo    |
+----+---+
+----+---+
| foo    |
+----+---+

TABLE;

        $this->assertEquals($expected, $this->getOutputContent($output));
    }

    public function test_column_style()
    {
        $table = new Table($output = $this->getOutputStream());
        $table
            ->setHeaders(['ISBN', 'Title', 'Author', 'Price'])
            ->setRows([
                ['99921-58-10-7', 'Divine Comedy', 'Dante Alighieri', '9.95'],
                ['9971-5-0210-0', 'A Tale of Two Cities', 'Charles Dickens', '139.25'],
            ]);

        $style = new TableStyle;
        $style->setPadType(STR_PAD_LEFT);
        $table->setColumnStyle(3, $style);

        $table->render();

        $expected =
            <<<'TABLE'
+---------------+----------------------+-----------------+--------+
| ISBN          | Title                | Author          |  Price |
+---------------+----------------------+-----------------+--------+
| 99921-58-10-7 | Divine Comedy        | Dante Alighieri |   9.95 |
| 9971-5-0210-0 | A Tale of Two Cities | Charles Dickens | 139.25 |
+---------------+----------------------+-----------------+--------+

TABLE;

        $this->assertEquals($expected, $this->getOutputContent($output));
    }

    /**
     * @expectedException Symfony\Component\Console\Exception\InvalidArgumentException
     *
     * @expectedExceptionMessage Style "absent" is not defined.
     */
    public function test_is_not_defined_style_exception()
    {
        $table = new Table($this->getOutputStream());
        $table->setStyle('absent');
    }

    /**
     * @expectedException \Symfony\Component\Console\Exception\InvalidArgumentException
     *
     * @expectedExceptionMessage Style "absent" is not defined.
     */
    public function test_get_style_definition()
    {
        Table::getStyleDefinition('absent');
    }

    protected function getOutputStream()
    {
        return new StreamOutput($this->stream, StreamOutput::VERBOSITY_NORMAL, false);
    }

    protected function getOutputContent(StreamOutput $output)
    {
        rewind($output->getStream());

        return str_replace(PHP_EOL, "\n", stream_get_contents($output->getStream()));
    }
}
