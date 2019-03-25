<?php namespace Tatter\Permits\Exceptions;

use CodeIgniter\Exceptions\ExceptionInterface;
use CodeIgniter\Exceptions\FrameworkException;

class PermitsException extends FrameworkException implements ExceptionInterface
{
	public static function forMissingDatabaseTable(string $table)
	{
		return new static("Table `{$table}` missing for permissions handling");
	}
}
