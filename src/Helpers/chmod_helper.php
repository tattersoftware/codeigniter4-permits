<?php
// https://caboodle.tech/blog/21/06/2017/trusting-user-input-in-phps-chmod-decimal-vs-octal/

if (! function_exists('octal2array'))
{	
	// Parses a perceived octal mode into an array of permissions
	function mode2array($mode)
	{
		if (! is_octal($mode))
			return false;

		$permissions['user']['read']      = $mode & 0600;
		$permissions['user']['write']     = $mode & 0400;
		$permissions['user']['execute']   = $mode & 0100;
		
		$permissions['group']['read']     = $mode & 0060;
		$permissions['group']['write']    = $mode & 0040;
		$permissions['group']['execute']  = $mode & 0010;
		
		$permissions['world']['read']     = $mode & 0006;
		$permissions['world']['write']    = $mode & 0004;
		$permissions['world']['execute']  = $mode & 0001;
		
		return $permissions;		
	}
}		

if (! function_exists('is_octal'))
{	
	// Convert a perceived octal to a decimal and then back to check if it really is an octal
	function is_octal($octal): bool
	{
		if (! is_int($octal))
			return false;
		return decoct(octdec($octal)) == $octal;
	}
}
