<?php

declare(strict_types=1);

namespace DontTest;

use Dont\DontDeserialise;
use Dont\Exception\NonDeserialisableObject;
use DontTestAsset\NonDeserialisable;
use DontTestAsset\NonDeserialisableImplementingSerializable;
use DontTestAsset\DontDoIt;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Dont\DontDeserialise
 */
final class DontDeserialiseTest extends TestCase
{
    /**
     * @dataProvider nonDeserialisableObject
     *
     * @param string $className
     */
    public function testWillThrowOnSerialisationAttempt($className) : void
    {
        $this->expectException(NonDeserialisableObject::class);
        if($className === 'DontTestAsset\\NonDeserialisableImplementingSerializable'){
            unserialize(\sprintf('C:55:"DontTestAsset\NonDeserialisableImplementingSerializable":6:{a:0:{}}'));
        } else {
            unserialize(\sprintf('O:%d:"%s":0:{}', \strlen($className), $className));
        }
    }

    /**
     * @return string[]
     */
    public function nonDeserialisableObject() : array
    {
        return [
            [NonDeserialisable::class],
            [NonDeserialisableImplementingSerializable::class],
            [DontDoIt::class],
        ];
    }

    public function testSerialisePreventionIsFinal() : void
    {
        self::assertTrue((new \ReflectionMethod(DontDeserialise::class, '__wakeup'))->isFinal());
        self::assertTrue((new \ReflectionMethod(DontDeserialise::class, 'unserialize'))->isFinal());
        self::assertTrue((new \ReflectionMethod(DontDeserialise::class, '__unserialize'))->isFinal());
    }

    public function testManuallyInvokingMethods() : void
    {
        $dont = new DontDoIt();

        try {
            $dont->__unserialize([]);
            self::markAsFailed('This method must always throw');
        } catch (NonDeserialisableObject $_) {
        }

        try {
            $dont->unserialize([]);
            self::markAsFailed('This method must always throw');
        } catch (NonDeserialisableObject $_) {
        }

        try {
            $dont->__wakeup([]);
            self::markAsFailed('This method must always throw');
        } catch (NonDeserialisableObject $_) {
        }

        self::assertTrue(true);
    }
}
