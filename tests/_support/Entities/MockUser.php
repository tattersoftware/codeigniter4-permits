<?php

namespace Tests\Support\Entities;

use CodeIgniter\Entity\Entity;
use Tatter\Users\UserEntity;

/**
 * Mock User Entity
 *
 * A set of dummy methods to fulfill UserEntity
 * without HasPermission.
 *
 * @see TraitTest::testRequiresHasPermission()
 */
class MockUser extends Entity implements UserEntity
{
    /**
     * Returns the name of the column used to
     * uniquely identify this user, typically 'id'.
     */
    public function getIdentifier(): string
    {
        return 'id';
    }

    /**
     * Returns the value for the identifier,
     * or `null` for "uncreated" users.
     *
     * @return int|string|null
     */
    public function getId()
    {
        return null;
    }

    /**
     * Returns the email address.
     */
    public function getEmail(): ?string
    {
        return null;
    }

    /**
     * Returns the username.
     */
    public function getUsername(): ?string
    {
        return null;
    }

    /**
     * Returns the name for this user.
     * If names are stored as parts "first",
     * "middle", "last" they should be
     * concatenated with spaces.
     */
    public function getName(): ?string
    {
        return null;
    }

    /**
     * Returns whether this user is eligible
     * for authentication.
     */
    public function isActive(): bool
    {
        return false;
    }
}
