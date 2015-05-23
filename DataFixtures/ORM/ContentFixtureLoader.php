<?php
namespace Wizin\Bundle\SimpleCmsBundle\DataFixtures\ORM;

use Wizin\Bundle\BaseBundle\DataFixtures\ORM\YamlFixtureLoader;

class ContentFixtureLoader extends YamlFixtureLoader
{
    /**
     * {@inheritDoc}
     */
    protected function getFixtures()
    {
        return  array(
            __DIR__ . '/yaml/content.yml',
        );
    }
}

