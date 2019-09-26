<?php namespace Tatter\Permits\Interfaces;

/**
 *
 * This interface is to ensure that any user model
 * passed to the Permits library has a groups()
 * function defined, which takes a integer user ID
 * and returns and array of objects (e.g. group
 * entities).
 *
 */
 
interface PermitsUserModelInterface
{
	public function groups($userId = null): array;
}
