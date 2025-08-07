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

use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

/**
 * @group tty
 */
class QuestionHelperTest extends \PHPUnit_Framework_TestCase
{
    public function test_ask_choice()
    {
        $questionHelper = new QuestionHelper;

        $helperSet = new HelperSet([new FormatterHelper]);
        $questionHelper->setHelperSet($helperSet);

        $heroes = ['Superman', 'Batman', 'Spiderman'];

        $questionHelper->setInputStream($this->getInputStream("\n1\n  1  \nFabien\n1\nFabien\n1\n0,2\n 0 , 2  \n\n\n"));

        $question = new ChoiceQuestion('What is your favorite superhero?', $heroes, '2');
        $question->setMaxAttempts(1);
        // first answer is an empty answer, we're supposed to receive the default value
        $this->assertEquals('Spiderman', $questionHelper->ask($this->createInputInterfaceMock(), $this->createOutputInterface(), $question));

        $question = new ChoiceQuestion('What is your favorite superhero?', $heroes);
        $question->setMaxAttempts(1);
        $this->assertEquals('Batman', $questionHelper->ask($this->createInputInterfaceMock(), $this->createOutputInterface(), $question));
        $this->assertEquals('Batman', $questionHelper->ask($this->createInputInterfaceMock(), $this->createOutputInterface(), $question));

        $question = new ChoiceQuestion('What is your favorite superhero?', $heroes);
        $question->setErrorMessage('Input "%s" is not a superhero!');
        $question->setMaxAttempts(2);
        $this->assertEquals('Batman', $questionHelper->ask($this->createInputInterfaceMock(), $output = $this->createOutputInterface(), $question));

        rewind($output->getStream());
        $stream = stream_get_contents($output->getStream());
        $this->assertContains('Input "Fabien" is not a superhero!', $stream);

        try {
            $question = new ChoiceQuestion('What is your favorite superhero?', $heroes, '1');
            $question->setMaxAttempts(1);
            $questionHelper->ask($this->createInputInterfaceMock(), $output = $this->createOutputInterface(), $question);
            $this->fail();
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals('Value "Fabien" is invalid', $e->getMessage());
        }

        $question = new ChoiceQuestion('What is your favorite superhero?', $heroes, null);
        $question->setMaxAttempts(1);
        $question->setMultiselect(true);

        $this->assertEquals(['Batman'], $questionHelper->ask($this->createInputInterfaceMock(), $this->createOutputInterface(), $question));
        $this->assertEquals(['Superman', 'Spiderman'], $questionHelper->ask($this->createInputInterfaceMock(), $this->createOutputInterface(), $question));
        $this->assertEquals(['Superman', 'Spiderman'], $questionHelper->ask($this->createInputInterfaceMock(), $this->createOutputInterface(), $question));

        $question = new ChoiceQuestion('What is your favorite superhero?', $heroes, '0,1');
        $question->setMaxAttempts(1);
        $question->setMultiselect(true);

        $this->assertEquals(['Superman', 'Batman'], $questionHelper->ask($this->createInputInterfaceMock(), $this->createOutputInterface(), $question));

        $question = new ChoiceQuestion('What is your favorite superhero?', $heroes, ' 0 , 1 ');
        $question->setMaxAttempts(1);
        $question->setMultiselect(true);

        $this->assertEquals(['Superman', 'Batman'], $questionHelper->ask($this->createInputInterfaceMock(), $this->createOutputInterface(), $question));
    }

    public function test_ask()
    {
        $dialog = new QuestionHelper;

        $dialog->setInputStream($this->getInputStream("\n8AM\n"));

        $question = new Question('What time is it?', '2PM');
        $this->assertEquals('2PM', $dialog->ask($this->createInputInterfaceMock(), $this->createOutputInterface(), $question));

        $question = new Question('What time is it?', '2PM');
        $this->assertEquals('8AM', $dialog->ask($this->createInputInterfaceMock(), $output = $this->createOutputInterface(), $question));

        rewind($output->getStream());
        $this->assertEquals('What time is it?', stream_get_contents($output->getStream()));
    }

    public function test_ask_with_autocomplete()
    {
        if (! $this->hasSttyAvailable()) {
            $this->markTestSkipped('`stty` is required to test autocomplete functionality');
        }

        // Acm<NEWLINE>
        // Ac<BACKSPACE><BACKSPACE>s<TAB>Test<NEWLINE>
        // <NEWLINE>
        // <UP ARROW><UP ARROW><NEWLINE>
        // <UP ARROW><UP ARROW><UP ARROW><UP ARROW><UP ARROW><TAB>Test<NEWLINE>
        // <DOWN ARROW><NEWLINE>
        // S<BACKSPACE><BACKSPACE><DOWN ARROW><DOWN ARROW><NEWLINE>
        // F00<BACKSPACE><BACKSPACE>oo<TAB><NEWLINE>
        $inputStream = $this->getInputStream("Acm\nAc\177\177s\tTest\n\n\033[A\033[A\n\033[A\033[A\033[A\033[A\033[A\tTest\n\033[B\nS\177\177\033[B\033[B\nF00\177\177oo\t\n");

        $dialog = new QuestionHelper;
        $dialog->setInputStream($inputStream);
        $helperSet = new HelperSet([new FormatterHelper]);
        $dialog->setHelperSet($helperSet);

        $question = new Question('Please select a bundle', 'FrameworkBundle');
        $question->setAutocompleterValues(['AcmeDemoBundle', 'AsseticBundle', 'SecurityBundle', 'FooBundle']);

        $this->assertEquals('AcmeDemoBundle', $dialog->ask($this->createInputInterfaceMock(), $this->createOutputInterface(), $question));
        $this->assertEquals('AsseticBundleTest', $dialog->ask($this->createInputInterfaceMock(), $this->createOutputInterface(), $question));
        $this->assertEquals('FrameworkBundle', $dialog->ask($this->createInputInterfaceMock(), $this->createOutputInterface(), $question));
        $this->assertEquals('SecurityBundle', $dialog->ask($this->createInputInterfaceMock(), $this->createOutputInterface(), $question));
        $this->assertEquals('FooBundleTest', $dialog->ask($this->createInputInterfaceMock(), $this->createOutputInterface(), $question));
        $this->assertEquals('AcmeDemoBundle', $dialog->ask($this->createInputInterfaceMock(), $this->createOutputInterface(), $question));
        $this->assertEquals('AsseticBundle', $dialog->ask($this->createInputInterfaceMock(), $this->createOutputInterface(), $question));
        $this->assertEquals('FooBundle', $dialog->ask($this->createInputInterfaceMock(), $this->createOutputInterface(), $question));
    }

    public function test_ask_with_autocomplete_with_non_sequential_keys()
    {
        if (! $this->hasSttyAvailable()) {
            $this->markTestSkipped('`stty` is required to test autocomplete functionality');
        }

        // <UP ARROW><UP ARROW><NEWLINE><DOWN ARROW><DOWN ARROW><NEWLINE>
        $inputStream = $this->getInputStream("\033[A\033[A\n\033[B\033[B\n");

        $dialog = new QuestionHelper;
        $dialog->setInputStream($inputStream);
        $dialog->setHelperSet(new HelperSet([new FormatterHelper]));

        $question = new ChoiceQuestion('Please select a bundle', [1 => 'AcmeDemoBundle', 4 => 'AsseticBundle']);
        $question->setMaxAttempts(1);

        $this->assertEquals('AcmeDemoBundle', $dialog->ask($this->createInputInterfaceMock(), $this->createOutputInterface(), $question));
        $this->assertEquals('AsseticBundle', $dialog->ask($this->createInputInterfaceMock(), $this->createOutputInterface(), $question));
    }

    public function test_ask_hidden_response()
    {
        if ('\\' === DIRECTORY_SEPARATOR) {
            $this->markTestSkipped('This test is not supported on Windows');
        }

        $dialog = new QuestionHelper;
        $dialog->setInputStream($this->getInputStream("8AM\n"));

        $question = new Question('What time is it?');
        $question->setHidden(true);

        $this->assertEquals('8AM', $dialog->ask($this->createInputInterfaceMock(), $this->createOutputInterface(), $question));
    }

    /**
     * @dataProvider getAskConfirmationData
     */
    public function test_ask_confirmation($question, $expected, $default = true)
    {
        $dialog = new QuestionHelper;

        $dialog->setInputStream($this->getInputStream($question."\n"));
        $question = new ConfirmationQuestion('Do you like French fries?', $default);
        $this->assertEquals($expected, $dialog->ask($this->createInputInterfaceMock(), $this->createOutputInterface(), $question), 'confirmation question should '.($expected ? 'pass' : 'cancel'));
    }

    public function getAskConfirmationData()
    {
        return [
            ['', true],
            ['', false, false],
            ['y', true],
            ['yes', true],
            ['n', false],
            ['no', false],
        ];
    }

    public function test_ask_confirmation_with_custom_true_answer()
    {
        $dialog = new QuestionHelper;

        $dialog->setInputStream($this->getInputStream("j\ny\n"));
        $question = new ConfirmationQuestion('Do you like French fries?', false, '/^(j|y)/i');
        $this->assertTrue($dialog->ask($this->createInputInterfaceMock(), $this->createOutputInterface(), $question));
        $question = new ConfirmationQuestion('Do you like French fries?', false, '/^(j|y)/i');
        $this->assertTrue($dialog->ask($this->createInputInterfaceMock(), $this->createOutputInterface(), $question));
    }

    public function test_ask_and_validate()
    {
        $dialog = new QuestionHelper;
        $helperSet = new HelperSet([new FormatterHelper]);
        $dialog->setHelperSet($helperSet);

        $error = 'This is not a color!';
        $validator = function ($color) use ($error) {
            if (! in_array($color, ['white', 'black'])) {
                throw new \InvalidArgumentException($error);
            }

            return $color;
        };

        $question = new Question('What color was the white horse of Henry IV?', 'white');
        $question->setValidator($validator);
        $question->setMaxAttempts(2);

        $dialog->setInputStream($this->getInputStream("\nblack\n"));
        $this->assertEquals('white', $dialog->ask($this->createInputInterfaceMock(), $this->createOutputInterface(), $question));
        $this->assertEquals('black', $dialog->ask($this->createInputInterfaceMock(), $this->createOutputInterface(), $question));

        $dialog->setInputStream($this->getInputStream("green\nyellow\norange\n"));
        try {
            $dialog->ask($this->createInputInterfaceMock(), $this->createOutputInterface(), $question);
            $this->fail();
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals($error, $e->getMessage());
        }
    }

    /**
     * @dataProvider simpleAnswerProvider
     */
    public function test_select_choice_from_simple_choices($providedAnswer, $expectedValue)
    {
        $possibleChoices = [
            'My environment 1',
            'My environment 2',
            'My environment 3',
        ];

        $dialog = new QuestionHelper;
        $dialog->setInputStream($this->getInputStream($providedAnswer."\n"));
        $helperSet = new HelperSet([new FormatterHelper]);
        $dialog->setHelperSet($helperSet);

        $question = new ChoiceQuestion('Please select the environment to load', $possibleChoices);
        $question->setMaxAttempts(1);
        $answer = $dialog->ask($this->createInputInterfaceMock(), $this->createOutputInterface(), $question);

        $this->assertSame($expectedValue, $answer);
    }

    public function simpleAnswerProvider()
    {
        return [
            [0, 'My environment 1'],
            [1, 'My environment 2'],
            [2, 'My environment 3'],
            ['My environment 1', 'My environment 1'],
            ['My environment 2', 'My environment 2'],
            ['My environment 3', 'My environment 3'],
        ];
    }

    /**
     * @dataProvider mixedKeysChoiceListAnswerProvider
     */
    public function test_choice_from_choicelist_with_mixed_keys($providedAnswer, $expectedValue)
    {
        $possibleChoices = [
            '0' => 'No environment',
            '1' => 'My environment 1',
            'env_2' => 'My environment 2',
            3 => 'My environment 3',
        ];

        $dialog = new QuestionHelper;
        $dialog->setInputStream($this->getInputStream($providedAnswer."\n"));
        $helperSet = new HelperSet([new FormatterHelper]);
        $dialog->setHelperSet($helperSet);

        $question = new ChoiceQuestion('Please select the environment to load', $possibleChoices);
        $question->setMaxAttempts(1);
        $answer = $dialog->ask($this->createInputInterfaceMock(), $this->createOutputInterface(), $question);

        $this->assertSame($expectedValue, $answer);
    }

    public function mixedKeysChoiceListAnswerProvider()
    {
        return [
            ['0', '0'],
            ['No environment', '0'],
            ['1', '1'],
            ['env_2', 'env_2'],
            [3, '3'],
            ['My environment 1', '1'],
        ];
    }

    /**
     * @dataProvider answerProvider
     */
    public function test_select_choice_from_choice_list($providedAnswer, $expectedValue)
    {
        $possibleChoices = [
            'env_1' => 'My environment 1',
            'env_2' => 'My environment',
            'env_3' => 'My environment',
        ];

        $dialog = new QuestionHelper;
        $dialog->setInputStream($this->getInputStream($providedAnswer."\n"));
        $helperSet = new HelperSet([new FormatterHelper]);
        $dialog->setHelperSet($helperSet);

        $question = new ChoiceQuestion('Please select the environment to load', $possibleChoices);
        $question->setMaxAttempts(1);
        $answer = $dialog->ask($this->createInputInterfaceMock(), $this->createOutputInterface(), $question);

        $this->assertSame($expectedValue, $answer);
    }

    /**
     * @expectedException        \InvalidArgumentException
     *
     * @expectedExceptionMessage The provided answer is ambiguous. Value should be one of env_2 or env_3.
     */
    public function test_ambiguous_choice_from_choicelist()
    {
        $possibleChoices = [
            'env_1' => 'My first environment',
            'env_2' => 'My environment',
            'env_3' => 'My environment',
        ];

        $dialog = new QuestionHelper;
        $dialog->setInputStream($this->getInputStream("My environment\n"));
        $helperSet = new HelperSet([new FormatterHelper]);
        $dialog->setHelperSet($helperSet);

        $question = new ChoiceQuestion('Please select the environment to load', $possibleChoices);
        $question->setMaxAttempts(1);

        $dialog->ask($this->createInputInterfaceMock(), $this->createOutputInterface(), $question);
    }

    public function answerProvider()
    {
        return [
            ['env_1', 'env_1'],
            ['env_2', 'env_2'],
            ['env_3', 'env_3'],
            ['My environment 1', 'env_1'],
        ];
    }

    public function test_no_interaction()
    {
        $dialog = new QuestionHelper;
        $question = new Question('Do you have a job?', 'not yet');
        $this->assertEquals('not yet', $dialog->ask($this->createInputInterfaceMock(false), $this->createOutputInterface(), $question));
    }

    /**
     * @requires function mb_strwidth
     */
    public function test_choice_output_formatting_question_for_utf8_keys()
    {
        $question = 'Lorem ipsum?';
        $possibleChoices = [
            'foo' => 'foo',
            'żółw' => 'bar',
            'łabądź' => 'baz',
        ];
        $outputShown = [
            $question,
            '  [<info>foo   </info>] foo',
            '  [<info>żółw  </info>] bar',
            '  [<info>łabądź</info>] baz',
        ];
        $output = $this->getMock('\Symfony\Component\Console\Output\OutputInterface');
        $output->method('getFormatter')->willReturn(new OutputFormatter);

        $dialog = new QuestionHelper;
        $dialog->setInputStream($this->getInputStream("\n"));
        $helperSet = new HelperSet([new FormatterHelper]);
        $dialog->setHelperSet($helperSet);

        $output->expects($this->once())->method('writeln')->with($this->equalTo($outputShown));

        $question = new ChoiceQuestion($question, $possibleChoices, 'foo');
        $dialog->ask($this->createInputInterfaceMock(), $output, $question);
    }

    protected function getInputStream($input)
    {
        $stream = fopen('php://memory', 'r+', false);
        fwrite($stream, $input);
        rewind($stream);

        return $stream;
    }

    protected function createOutputInterface()
    {
        return new StreamOutput(fopen('php://memory', 'r+', false));
    }

    protected function createInputInterfaceMock($interactive = true)
    {
        $mock = $this->getMock('Symfony\Component\Console\Input\InputInterface');
        $mock->expects($this->any())
            ->method('isInteractive')
            ->will($this->returnValue($interactive));

        return $mock;
    }

    private function hasSttyAvailable()
    {
        exec('stty 2>&1', $output, $exitcode);

        return $exitcode === 0;
    }
}
