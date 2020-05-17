<?php
declare(strict_types=1);

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use ReflectionClass;

class BaseTestCase extends TestCase
{
    /**
     * Method allows to call Private or Protected method from given object
     * @param object $object
     * @param string $methodName
     * @param array $arguments
     * @return type
     */
    protected function callMethod(
        object $object, 
        string $methodName, 
        array $arguments=[]
    ){
        $class = new ReflectionClass($object);
        $method = $class->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $arguments);
    }
}