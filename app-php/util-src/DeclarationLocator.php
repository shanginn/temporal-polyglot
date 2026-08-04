<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Temporal\SampleUtils;

use Spiral\Tokenizer\ClassesInterface;
use Spiral\Tokenizer\ClassLocator;
use Symfony\Component\Finder\Finder;

class DeclarationLocator
{
    private ClassesInterface $classLocator;

    /**
     * @return list<class-string>
     */
    public function getCommands(): array
    {
        $commands = [];
        foreach ($this->classLocator->getClasses(Command::class) as $class) {
            if (!$class->isAbstract()) {
                $commands[] = $class->getName();
            }
        }

        return $commands;
    }

    /**
     * Finds all activity declarations using Activity suffix.
     *
     * @return list<class-string>
     */
    public function getActivityTypes(): array
    {
        $activities = [];
        foreach ($this->getAvailableDeclarations() as $class) {
            if ($this->endsWith($class->getName(), 'Activity')) {
                $activities[] = $class->getName();
            }
        }

        return $activities;
    }

    /**
     * Finds all workflow declarations using Workflow suffix.
     *
     * @return list<class-string>
     */
    public function getWorkflowTypes(): array
    {
        $workflows = [];
        foreach ($this->getAvailableDeclarations() as $class) {
            if ($this->endsWith($class->getName(), 'Workflow')) {
                $workflows[] = $class->getName();
            }
        }

        return $workflows;
    }

    /**
     * @return list<\ReflectionClass<object>>
     */
    private function getAvailableDeclarations(): array
    {
        $declarations = [];
        foreach ($this->classLocator->getClasses() as $class) {
            if ($class->isAbstract() || $class->isInterface()) {
                continue;
            }

            $declarations[] = $class;
        }

        return $declarations;
    }

    /**
     * @param string $haystack
     * @param string $needle
     * @return bool
     */
    private function endsWith(string $haystack, string $needle): bool
    {
        $length = strlen($needle);
        if (!$length) {
            return true;
        }
        return substr($haystack, -$length) === $needle;
    }

    /**
     * @param string $dir
     * @return $this
     */
    public static function create(string $dir): self
    {
        $locator = new self();
        $locator->classLocator = new ClassLocator(
            Finder::create()->files()->in($dir)
        );

        return $locator;
    }


}
