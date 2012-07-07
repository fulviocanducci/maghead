<?php

class ConfigBuilderTest extends PHPUnit_Framework_TestCase
{
    function test()
    {
        $builder = new LazyRecord\ConfigBuilder;
        ok($builder);

        $builder->read('tests/database.yml');
        $content = $builder->build();
        ok( $content );

        file_put_contents('tests/config.php'
                ,$content);


        $loader = new LazyRecord\ConfigLoader;
        $loader->load( 'tests/config.php');
        $loader->init();

        $conM = LazyRecord\ConnectionManager::getInstance();
        $conn = $conM->getDefault();
        ok( $conn );

        $conM->closeAll();
    }
}

