<?php

namespace spec\Prophecy\Doubler\ClassPatch;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Prophecy\Doubler\Generator\Node\ClassNode;

class TraversablePatchSpec extends ObjectBehavior
{
    public function it_is_a_patch()
    {
        $this->shouldBeAnInstanceOf('Prophecy\Doubler\ClassPatch\ClassPatchInterface');
    }

    public function it_supports_class_that_implements_only_Traversable(ClassNode $node)
    {
        $node->getInterfaces()->willReturn(['Traversable']);

        $this->supports($node)->shouldReturn(true);
    }

    public function it_does_not_support_class_that_implements_Iterator(ClassNode $node)
    {
        $node->getInterfaces()->willReturn(['Traversable', 'Iterator']);

        $this->supports($node)->shouldReturn(false);
    }

    public function it_does_not_support_class_that_implements_IteratorAggregate(ClassNode $node)
    {
        $node->getInterfaces()->willReturn(['Traversable', 'IteratorAggregate']);

        $this->supports($node)->shouldReturn(false);
    }

    public function it_has_100_priority()
    {
        $this->getPriority()->shouldReturn(100);
    }

    public function it_forces_node_to_implement_IteratorAggregate(ClassNode $node)
    {
        $node->addInterface('Iterator')->shouldBeCalled();

        $node->addMethod(Argument::type('Prophecy\Doubler\Generator\Node\MethodNode'))->willReturn(null);

        $this->apply($node);
    }
}
