<?php

namespace Hamcrest;

class UtilTest extends \PhpUnit_Framework_TestCase
{
    public function test_wrap_value_with_is_equal_leaves_matchers_untouched()
    {
        $matcher = new \Hamcrest\Text\MatchesPattern('/fo+/');
        $newMatcher = \Hamcrest\Util::wrapValueWithIsEqual($matcher);
        $this->assertSame($matcher, $newMatcher);
    }

    public function test_wrap_value_with_is_equal_wraps_primitive()
    {
        $matcher = \Hamcrest\Util::wrapValueWithIsEqual('foo');
        $this->assertInstanceOf('Hamcrest\Core\IsEqual', $matcher);
        $this->assertTrue($matcher->matches('foo'));
    }

    public function test_check_all_are_matchers_accepts_matchers()
    {
        \Hamcrest\Util::checkAllAreMatchers([
            new \Hamcrest\Text\MatchesPattern('/fo+/'),
            new \Hamcrest\Core\IsEqual('foo'),
        ]);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function test_check_all_are_matchers_fails_for_primitive()
    {
        \Hamcrest\Util::checkAllAreMatchers([
            new \Hamcrest\Text\MatchesPattern('/fo+/'),
            'foo',
        ]);
    }

    private function callAndAssertCreateMatcherArray($items)
    {
        $matchers = \Hamcrest\Util::createMatcherArray($items);
        $this->assertInternalType('array', $matchers);
        $this->assertSameSize($items, $matchers);
        foreach ($matchers as $matcher) {
            $this->assertInstanceOf('\Hamcrest\Matcher', $matcher);
        }

        return $matchers;
    }

    public function test_create_matcher_array_leaves_matchers_untouched()
    {
        $matcher = new \Hamcrest\Text\MatchesPattern('/fo+/');
        $items = [$matcher];
        $matchers = $this->callAndAssertCreateMatcherArray($items);
        $this->assertSame($matcher, $matchers[0]);
    }

    public function test_create_matcher_array_wraps_primitive_with_is_equal_matcher()
    {
        $matchers = $this->callAndAssertCreateMatcherArray(['foo']);
        $this->assertInstanceOf('Hamcrest\Core\IsEqual', $matchers[0]);
        $this->assertTrue($matchers[0]->matches('foo'));
    }

    public function test_create_matcher_array_doesnt_modify_original_array()
    {
        $items = ['foo'];
        $this->callAndAssertCreateMatcherArray($items);
        $this->assertSame('foo', $items[0]);
    }

    public function test_create_matcher_array_unwraps_single_array_element()
    {
        $matchers = $this->callAndAssertCreateMatcherArray([['foo']]);
        $this->assertInstanceOf('Hamcrest\Core\IsEqual', $matchers[0]);
        $this->assertTrue($matchers[0]->matches('foo'));
    }
}
