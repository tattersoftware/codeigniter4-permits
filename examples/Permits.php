<?php

namespace Config;

use Tatter\Permits\Config\Permits as PermitsConfig;

/**
 * Permits Config
 *
 * Configures the access rights and behavior of
 * each Model class that uses the Permits trait.
 * Each Model should have a corresponding property
 * named after its $table.
 * Values in $default will be used for any missing
 * property or subset of property values.
 *
 * This file contains example values to alter default library behavior.
 * Recommended usage:
 *	1. Copy the file to app/Config/Permits.php
 *	2. Change any values
 *	3. Remove any lines to fallback to defaults
 */
class Permits extends PermitsConfig
{
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
