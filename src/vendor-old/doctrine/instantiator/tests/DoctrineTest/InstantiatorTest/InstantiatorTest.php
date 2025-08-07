<?php

/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <http://www.doctrine-project.org>.
 */

namespace DoctrineTest\InstantiatorTest;

use Doctrine\Instantiator\Exception\UnexpectedValueException;
use Doctrine\Instantiator\Instantiator;
use PHPUnit_Framework_TestCase;
use ReflectionClass;

/**
 * Tests for {@see \Doctrine\Instantiator\Instantiator}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 *
 * @covers \Doctrine\Instantiator\Instantiator
 */
class InstantiatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Instantiator
     */
    private $instantiator;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $this->instantiator = new Instantiator;
    }

    /**
     * @param  string  $className
     *
     * @dataProvider getInstantiableClasses
     */
    public function test_can_instantiate($className)
    {
        $this->assertInstanceOf($className, $this->instantiator->instantiate($className));
    }

    /**
     * @param  string  $className
     *
     * @dataProvider getInstantiableClasses
     */
    public function test_instantiates_separate_instances($className)
    {
        $instance1 = $this->instantiator->instantiate($className);
        $instance2 = $this->instantiator->instantiate($className);

        $this->assertEquals($instance1, $instance2);
        $this->assertNotSame($instance1, $instance2);
    }

    public function test_exception_on_un_serialization_exception()
    {
        if (defined('HHVM_VERSION')) {
            $this->markTestSkipped(
                'As of facebook/hhvm#3432, HHVM has no PDORow, and therefore '
                .' no internal final classes that cannot be instantiated'
            );
        }

        $className = 'DoctrineTest\\InstantiatorTestAsset\\UnserializeExceptionArrayObjectAsset';

        if (\PHP_VERSION_ID >= 50600) {
            $className = 'PDORow';
        }

        if (\PHP_VERSION_ID === 50429 || \PHP_VERSION_ID === 50513) {
            $className = 'DoctrineTest\\InstantiatorTestAsset\\SerializableArrayObjectAsset';
        }

        $this->setExpectedException('Doctrine\\Instantiator\\Exception\\UnexpectedValueException');

        $this->instantiator->instantiate($className);
    }

    public function test_notice_on_un_serialization_exception()
    {
        if (\PHP_VERSION_ID >= 50600) {
            $this->markTestSkipped(
                'PHP 5.6 supports `ReflectionClass#newInstanceWithoutConstructor()` for some internal classes'
            );
        }

        try {
            $this->instantiator->instantiate('DoctrineTest\\InstantiatorTestAsset\\WakeUpNoticesAsset');

            $this->fail('No exception was raised');
        } catch (UnexpectedValueException $exception) {
            $wakeUpNoticesReflection = new ReflectionClass('DoctrineTest\\InstantiatorTestAsset\\WakeUpNoticesAsset');
            $previous = $exception->getPrevious();

            $this->assertInstanceOf('Exception', $previous);

            // in PHP 5.4.29 and PHP 5.5.13, this case is not a notice, but an exception being thrown
            if (! (\PHP_VERSION_ID === 50429 || \PHP_VERSION_ID === 50513)) {
                $this->assertSame(
                    'Could not produce an instance of "DoctrineTest\\InstantiatorTestAsset\WakeUpNoticesAsset" '
                    .'via un-serialization, since an error was triggered in file "'
                    .$wakeUpNoticesReflection->getFileName().'" at line "36"',
                    $exception->getMessage()
                );

                $this->assertSame('Something went bananas while un-serializing this instance', $previous->getMessage());
                $this->assertSame(\E_USER_NOTICE, $previous->getCode());
            }
        }
    }

    /**
     * @param  string  $invalidClassName
     *
     * @dataProvider getInvalidClassNames
     */
    public function test_instantiation_from_non_existing_class($invalidClassName)
    {
        $this->setExpectedException('Doctrine\\Instantiator\\Exception\\InvalidArgumentException');

        $this->instantiator->instantiate($invalidClassName);
    }

    public function test_instances_are_not_cloned()
    {
        $className = 'TemporaryClass'.uniqid();

        eval('namespace '.__NAMESPACE__.'; class '.$className.'{}');

        $instance = $this->instantiator->instantiate(__NAMESPACE__.'\\'.$className);

        $instance->foo = 'bar';

        $instance2 = $this->instantiator->instantiate(__NAMESPACE__.'\\'.$className);

        $this->assertObjectNotHasAttribute('foo', $instance2);
    }

    /**
     * Provides a list of instantiable classes (existing)
     *
     * @return string[][]
     */
    public function getInstantiableClasses()
    {
        $classes = [
            ['stdClass'],
            [__CLASS__],
            ['Doctrine\\Instantiator\\Instantiator'],
            ['Exception'],
            ['PharException'],
            ['DoctrineTest\\InstantiatorTestAsset\\SimpleSerializableAsset'],
            ['DoctrineTest\\InstantiatorTestAsset\\ExceptionAsset'],
            ['DoctrineTest\\InstantiatorTestAsset\\FinalExceptionAsset'],
            ['DoctrineTest\\InstantiatorTestAsset\\PharExceptionAsset'],
            ['DoctrineTest\\InstantiatorTestAsset\\UnCloneableAsset'],
            ['DoctrineTest\\InstantiatorTestAsset\\XMLReaderAsset'],
        ];

        if (\PHP_VERSION_ID === 50429 || \PHP_VERSION_ID === 50513) {
            return $classes;
        }

        $classes = array_merge(
            $classes,
            [
                ['PharException'],
                ['ArrayObject'],
                ['DoctrineTest\\InstantiatorTestAsset\\ArrayObjectAsset'],
                ['DoctrineTest\\InstantiatorTestAsset\\SerializableArrayObjectAsset'],
            ]
        );

        if (\PHP_VERSION_ID >= 50600) {
            $classes[] = ['DoctrineTest\\InstantiatorTestAsset\\WakeUpNoticesAsset'];
            $classes[] = ['DoctrineTest\\InstantiatorTestAsset\\UnserializeExceptionArrayObjectAsset'];
        }

        return $classes;
    }

    /**
     * Provides a list of instantiable classes (existing)
     *
     * @return string[][]
     */
    public function getInvalidClassNames()
    {
        $classNames = [
            [__CLASS__.uniqid()],
            ['Doctrine\\Instantiator\\InstantiatorInterface'],
            ['DoctrineTest\\InstantiatorTestAsset\\AbstractClassAsset'],
        ];

        if (\PHP_VERSION_ID >= 50400) {
            $classNames[] = ['DoctrineTest\\InstantiatorTestAsset\\SimpleTraitAsset'];
        }

        return $classNames;
    }
}
