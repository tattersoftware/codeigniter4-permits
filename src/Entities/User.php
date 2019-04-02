<?php namespace Tatter\Permits\Entities;

/**
 * 
 * This entity is supplied as a bare minimum object class for
 * the Permits library. In most cases you will want to extend this
 * as a starting point, or replace it with your own user entity.
 *
 */

class User extends PEntity
{
	protected $id;
	protected $_mode = 0640;
}
