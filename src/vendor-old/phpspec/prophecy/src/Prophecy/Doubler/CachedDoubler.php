<?php

/*
 * This file is part of the Prophecy.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prophecy\Doubler;

use ReflectionClass;

/**
 * Cached class doubler.
 * Prevents mirroring/creation of the same structure twice.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class CachedDoubler extends Doubler
{
    private $classes = [];

    /**
     * {@inheritdoc}
     */
    public function registerClassPatch(ClassPatch\ClassPatchInterface $patch)
    {
        $this->classes[] = [];

        parent::registerClassPatch($patch);
    }

    /**
     * {@inheritdoc}
     */
    protected function createDoubleClass(?ReflectionClass $class, array $interfaces)
    {
        $classId = $this->generateClassId($class, $interfaces);
        if (isset($this->classes[$classId])) {
            return $this->classes[$classId];
        }

        return $this->classes[$classId] = parent::createDoubleClass($class, $interfaces);
    }

    /**
     * @param  ReflectionClass[]  $interfaces
     * @return string
     */
    private function generateClassId(?ReflectionClass $class, array $interfaces)
    {
        $parts = [];
        if ($class !== null) {
            $parts[] = $class->getName();
        }
        foreach ($interfaces as $interface) {
            $parts[] = $interface->getName();
        }
        sort($parts);

        return md5(implode('', $parts));
    }
}
