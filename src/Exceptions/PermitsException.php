<?php namespace Tatter\Permits\Exceptions;

use CodeIgniter\Exceptions\ExceptionInterface;
use CodeIgniter\Exceptions\FrameworkException;

class PermitsException extends FrameworkException implements ExceptionInterface
{
	public static function forMissingDatabaseTable(string $table)
	{
		return new static(lang('Permits.missingDatabaseTable', [$table]));
	}
	
	public static function forInvalidModeType(string $table)
	{
		return new static(lang('Permits.invalidModelType', [$table]));
	}
	
	public static function forInvalidMode(string $table, string $mode)
	{
		return new static(lang('Permits.invalidMode', [$table, $mode]));
	}
	
	// Generic 'not allowed' exception
	public static function forNotPermitted()
	{
		return new static(lang('Permits.notPermitted');
	}
}
