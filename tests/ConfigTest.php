<?php

use Tatter\Permits\Config\Permits;
use Tests\Support\TestCase;

/**
 * @internal
 */
final class ConfigTest extends TestCase
{
    public function testInvalidAccess()
    {
        $this->expectException('DomainException');
        $this->expectExceptionMessage('Undefined access value: 42');

        $result = Permits::check(42, null);
    }

    /**
     * @dataProvider accessProvider
     */
    public function testCheck(int $access, ?int $userId, ?bool $owner, bool $expected)
    {
        $result = Permits::check($access, $userId, $owner);

        $this->assertSame($expected, $result);
    }

    public function accessProvider()
    {
        return [
            [
                Permits::ANYBODY,
                null,
                null,
                true,
            ],
            [
                Permits::ANYBODY,
                3,
                null,
                true,
            ],
            [
                Permits::ANYBODY,
                null,
                false,
                true,
            ],
            [
                Permits::ANYBODY,
                3,
                true,
                true,
            ],
            [
                Permits::USERS,
                null,
                null,
                false,
            ],
            [
                Permits::USERS,
                null,
                true,
                false,
            ],
            [
                Permits::USERS,
                1,
                null,
                true,
            ],
            [
                Permits::USERS,
                1,
                false,
                true,
            ],
            [
                Permits::OWNERS,
                null,
                null,
                false,
            ],
            [
                Permits::OWNERS,
                2,
                null,
                false,
            ],
            [
                Permits::OWNERS,
                2,
                false,
                false,
            ],
            [
                Permits::OWNERS,
                null,
                true,
                false,
            ],
            [
                Permits::OWNERS,
                2,
                true,
                true,
            ],
            [
                Permits::NOBODY,
                null,
                null,
                false,
            ],
            [
                Permits::NOBODY,
                1,
                null,
                false,
            ],
            [
                Permits::NOBODY,
                null,
                false,
                false,
            ],
            [
                Permits::NOBODY,
                1,
                true,
                false,
            ],
        ];
    }
}
