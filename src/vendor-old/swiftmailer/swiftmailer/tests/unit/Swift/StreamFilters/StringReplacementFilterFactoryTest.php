<?php

class Swift_StreamFilters_StringReplacementFilterFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function test_instances_of_string_replacement_filter_are_created()
    {
        $factory = $this->_createFactory();
        $this->assertInstanceOf(
            'Swift_StreamFilters_StringReplacementFilter',
            $factory->createFilter('a', 'b')
        );
    }

    public function test_same_instances_are_cached()
    {
        $factory = $this->_createFactory();
        $filter1 = $factory->createFilter('a', 'b');
        $filter2 = $factory->createFilter('a', 'b');
        $this->assertSame($filter1, $filter2, '%s: Instances should be cached');
    }

    public function test_differing_instances_are_not_cached()
    {
        $factory = $this->_createFactory();
        $filter1 = $factory->createFilter('a', 'b');
        $filter2 = $factory->createFilter('a', 'c');
        $this->assertNotEquals($filter1, $filter2,
            '%s: Differing instances should not be cached'
        );
    }

    private function _createFactory()
    {
        return new Swift_StreamFilters_StringReplacementFilterFactory;
    }
}
