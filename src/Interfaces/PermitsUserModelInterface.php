<?php namespace Tatter\Permits\Interfaces;

interface PermitsUserModelInterface
{
	/**
	 * Returns groups for a single user.
	 *
	 * @param mixed $userId = null
	 *
	 * @return array  Usually Group Entities
	 */
	public function groups($userId = null): array;
}
