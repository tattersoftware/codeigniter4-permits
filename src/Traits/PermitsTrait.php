<?php

namespace Tatter\Permits\Traits;

use Tatter\Permits\Config\Permits;
use Tatter\Users\Interfaces\HasPermission;
use Tatter\Users\UserEntity;
use UnexpectedValueException;

/**
 * Permits Trait
 *
 * Supplies inferred permissions control for Models
 * and their table rows. Models using this trait
 * should have a corresponding Config entry with
 * access levels and ownership controls.
 */
trait PermitsTrait
{
    /**
     * The configuration values determined
     * from the config file.
     *
     * @var array<string,mixed>|null
     */
    private $permits;

    //--------------------------------------------------------------------
    // Testing Helper Methods
    //--------------------------------------------------------------------

    /**
     * Changes the configuration. Used mostly for testing.
     *
     * @param array<string,mixed>|null $permits Use `null` to restore from the Config file.
     *
     * @return $this
     *
     * @internal
     */
    final public function setPermits(?array $permits): self
    {
        $this->permits = array_merge(config('Permits')->default, $permits ?? []);

        return $this;
    }

    /**
     * Determines and returns the configuration.
     *
     * @return array<string,mixed>
     *
     * @internal
     */
    final public function getPermits(): array
    {
        if ($this->permits === null) {
            $this->setPermits(config('Permits')->{$this->table} ?? null);
        }

        return $this->permits;
    }

    //--------------------------------------------------------------------

    /**
     * Checks whether the current/supplied user may perform any of the other actions.
     */
    public function mayAdmin(?int $userId = null): bool
    {
        return $this->permissible('admin', $userId);
    }

    /**
     * Checks whether the current/supplied user may insert rows into this model's table.
     */
    public function mayCreate(?int $userId = null): bool
    {
        return $this->permissible('create', $userId);
    }

    /**
     * Checks whether the current/supplied user may list rows from this model's table.
     */
    public function mayList(?int $userId = null): bool
    {
        return $this->permissible('list', $userId);
    }

    /**
     * Checks whether the current/supplied user may read the given object.
     *
     * @param mixed $item
     */
    public function mayRead($item, ?int $userId = null): bool
    {
        return $this->permissible('read', $userId, $item);
    }

    /**
     * Checks whether the current/supplied user may update the given object.
     *
     * @param mixed $item
     */
    public function mayUpdate($item, ?int $userId = null): bool
    {
        return $this->permissible('update', $userId, $item);
    }

    /**
     * Checks whether the current/supplied user may delete the given object.
     *
     * @param mixed $item
     */
    public function mayDelete($item, ?int $userId = null): bool
    {
        return $this->permissible('delete', $userId, $item);
    }

    //--------------------------------------------------------------------
    // Support Methods (internal)
    //--------------------------------------------------------------------

    /**
     * Handles the processing of permission checks.
     * Should not be called directly - use the "mayVerb()" methods.
     *
     * @param array|object|null $item An item of $returnType, or null for domain verbs (create, list)
     *
     * @throws UnexpectedValueException If an authenticated user is indicated but unable to be located
     */
    final protected function permissible(string $verb, ?int $userId, $item = null): bool
    {
        // Determine the user (if any)
        $userId = $userId ?? user_id();

        if ($userId !== null) {
            $user = service('users')->findById($userId);
            if ($user === null) {
                throw new UnexpectedValueException('User provider was unable to locate a User with ID: ' . $userId);
            }

            // Check for overriding authorization
            if ($this->userHasPermission($user, $verb)) {
                return true;
            }
        }

        // Using inferred permissions
        $access = $this->getPermits()[$verb];

        // Handle scenarios they do not require ownership lookup
        if (! isset($user) || $access !== Permits::OWNERS || in_array($verb, ['admin', 'create', 'list'], true)) {
            return Permits::check($access, $userId);
        }

        // Everything else needs database lookup, so transform and verify the item
        $item = $this->transformDataToArray($item, 'insert');

        return Permits::check($access, $userId, $this->userOwnsItem($user, $item));
    }

    /**
     * Checks if a User is authorized for the given action.
     */
    private function userHasPermission(UserEntity $user, string $verb): bool
    {
        if (! $user instanceof HasPermission) {
            return false;
        }

        // Always allow admin access
        if ($user->hasPermission($this->table . '.admin')) {
            return true;
        }

        return $user->hasPermission($this->table . '.' . $verb);
    }

    /**
     * Checks if the User owns the item.
     */
    private function userOwnsItem(UserEntity $user, array $item): bool
    {
        $permits = $this->getPermits();

        // Make sure user lookup is enabled
        if ($permits['userKey'] === null) {
            return false;
        }

        // Check if the item itself has $userKey set
        if (isset($item[$permits['userKey']])) {
            return $user->getId() === $item[$permits['userKey']];
        }

        // Make sure there is a valid pivot table and ID
        if ($permits['pivotTable'] === null || $permits['pivotKey'] === null || ! isset($item[$this->primaryKey])) {
            return false;
        }

        return (bool) $this->db
            ->table($permits['pivotTable'])
            ->where($permits['userKey'], $user->getId())
            ->where($permits['pivotKey'], $item[$this->primaryKey])
            ->limit(1)
            ->get()
            ->getUnbufferedRow();
    }
}
