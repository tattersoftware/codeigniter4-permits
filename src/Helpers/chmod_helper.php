<?php
// https://caboodle.tech/blog/21/06/2017/trusting-user-input-in-phps-chmod-decimal-vs-octal/

if (! function_exists('octal2array'))
{	
	// Parses a perceived octal mode into an array of permissions
	function mode2array($mode)
	{
		if (! is_octal($mode))
			return false;

		$permissions['user']['read']      = $mode & 0400;
		$permissions['user']['write']     = $mode & 0200;
		$permissions['user']['execute']   = $mode & 0100;
		
		$permissions['group']['read']     = $mode & 0040;
		$permissions['group']['write']    = $mode & 0020;
		$permissions['group']['execute']  = $mode & 0010;
		
		$permissions['world']['read']     = $mode & 0004;
		$permissions['world']['write']    = $mode & 0002;
		$permissions['world']['execute']  = $mode & 0001;
		
		return $permissions;		
	}
}		

if (! function_exists('is_octal'))
{	
	// Convert a perceived octal mode to a decimal and then back to check if it really is an octal
	function is_octal($octal): bool
	{
		if (! is_int($octal))
			return false;
		if ($octal < 0 || $octal > 511)
			return false;
			
		return octdec(decoct($octal)) == $octal;
	}
}
