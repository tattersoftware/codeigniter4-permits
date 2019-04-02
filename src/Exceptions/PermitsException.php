<?php namespace Tatter\Permits\Exceptions;

use CodeIgniter\Exceptions\ExceptionInterface;
use CodeIgniter\Exceptions\FrameworkException;

class PermitsException extends FrameworkException implements ExceptionInterface
{
	public static function forMissingDatabaseTable(string $table)
	{
		return new static("Table `{$table}` missing for permissions handling");
	}
	
	public static function forInvalidModeType(string $table)
	{
		return new static("Invalid permit mode type on model for `{$table}`");
	}
	
	public static function forInvalidMode(string $table, string $mode)
	{
		return new static("Invalid permit mode on model for `{$table}`: '{$mode}'");
	}
}
