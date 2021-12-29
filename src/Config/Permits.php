<?php

namespace Tatter\Permits\Config;

use CodeIgniter\Config\BaseConfig;
use DomainException;

/**
 * Permits Config
 *
 * Configures the access rights and behavior of
 * each Model class that uses the Permits trait.
 * Each Model should have a corresponding property
 * named after its $table.
 * Values in $default will be used for any missing
 * property or subset of property values.
 */
class Permits extends BaseConfig
{
    /**
     * Prohibits all access without explicit permission.
     */
    public const NOBODY = 0;

    /**
     * Requires the authenticated user to own the item.
     */
    public const OWNERS = 10;

    /**
     * Requires any authenticated user.
     */
    public const USERS = 100;

    /**
     * Allows anyone regardless of authentication or ownership.
     */
    public const ANYBODY = 1000;

    /**
     * Verifies a permission against the supplied values.
     *
     * @throws DomainException If an unknown permission is passed
     */
    final public static function check(int $access, ?int $userId, ?bool $owner = null): bool
    {
        if ($access === self::ANYBODY) {
            return true;
        }
        if ($access === self::USERS) {
            return $userId !== null;
        }
        if ($access === self::OWNERS) {
            return isset($userId, $owner) && $owner;
        }

        if ($access === self::NOBODY) {
            return false;
        }

        throw new DomainException('Undefined access value: ' . $access);
    }

    /**
     * The default set of attributes to use for unspecified
     * properties and values.
     * Verbs correspond to each access type.
     * Remaining keys are used to identify ownership:
     * - userKey: Field for the user ID in the item or its pivot table
     * - pivotKey: Field for the item's ID in the pivot tables
     * - pivotTable: Table that joins the items to their owners
     * Setting ownership fields to `null` will disable owner lookup.
     *
     * @var array<string,mixed>
     */
    public $default = [
        'admin'      => self::NOBODY,
        'create'     => self::USERS,
        'list'       => self::ANYBODY,
        'read'       => self::ANYBODY,
        'update'     => self::OWNERS,
        'delete'     => self::OWNERS,
        'userKey'    => null,
        'pivotKey'   => null,
        'pivotTable' => null,
    ];
}
