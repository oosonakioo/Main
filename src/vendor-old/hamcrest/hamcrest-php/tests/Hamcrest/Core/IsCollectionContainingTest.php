<?php

namespace Hamcrest\Core;

class IsCollectionContainingTest extends \Hamcrest\AbstractMatcherTest
{
    protected function createMatcher()
    {
        return \Hamcrest\Core\IsCollectionContaining::hasItem(equalTo('irrelevant'));
    }

    public function test_matches_a_collection_that_contains_an_element_matching_the_given_matcher()
    {
        $itemMatcher = hasItem(equalTo('a'));

        $this->assertMatches(
            $itemMatcher,
            ['a', 'b', 'c'],
            "should match list that contains 'a'"
        );
    }

    public function test_does_not_match_collection_that_doesnt_contain_an_element_matching_the_given_matcher()
    {
        $matcher1 = hasItem(equalTo('a'));
        $this->assertDoesNotMatch(
            $matcher1,
            ['b', 'c'],
            "should not match list that doesn't contain 'a'"
        );

        $matcher2 = hasItem(equalTo('a'));
        $this->assertDoesNotMatch(
            $matcher2,
            [],
            'should not match the empty list'
        );
    }

    public function test_does_not_match_null()
    {
        $this->assertDoesNotMatch(
            hasItem(equalTo('a')),
            null,
            'should not match null'
        );
    }

    public function test_has_a_readable_description()
    {
        $this->assertDescription('a collection containing "a"', hasItem(equalTo('a')));
    }

    public function test_matches_all_items_in_collection()
    {
        $matcher1 = hasItems(equalTo('a'), equalTo('b'), equalTo('c'));
        $this->assertMatches(
            $matcher1,
            ['a', 'b', 'c'],
            'should match list containing all items'
        );

        $matcher2 = hasItems('a', 'b', 'c');
        $this->assertMatches(
            $matcher2,
            ['a', 'b', 'c'],
            'should match list containing all items (without matchers)'
        );

        $matcher3 = hasItems(equalTo('a'), equalTo('b'), equalTo('c'));
        $this->assertMatches(
            $matcher3,
            ['c', 'b', 'a'],
            'should match list containing all items in any order'
        );

        $matcher4 = hasItems(equalTo('a'), equalTo('b'), equalTo('c'));
        $this->assertMatches(
            $matcher4,
            ['e', 'c', 'b', 'a', 'd'],
            'should match list containing all items plus others'
        );

        $matcher5 = hasItems(equalTo('a'), equalTo('b'), equalTo('c'));
        $this->assertDoesNotMatch(
            $matcher5,
            ['e', 'c', 'b', 'd'], // 'a' missing
            'should not match list unless it contains all items'
        );
    }
}
