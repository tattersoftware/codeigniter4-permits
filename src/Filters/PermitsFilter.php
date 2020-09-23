<?php namespace Tatter\Permits\Filters;

use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use Tatter\Permits\Exceptions\PermitsException;

class PermitsFilter implements FilterInterface
{
	/**
	 * Do whatever processing this filter needs to do.
	 * By default it should not return anything during
	 * normal execution. However, when an abnormal state
	 * is found, it should return an instance of
	 * CodeIgniter\HTTP\Response. If it does, script
	 * execution will end and that Response will be
	 * sent back to the client, allowing for error pages,
	 * redirects, etc.
	 *
	 * @param \CodeIgniter\HTTP\RequestInterface $request
	 * @param null                               $arguments
	 *
	 * @return RedirectResponse|void
	 * @throws PermitsException
	 */
	public function before(RequestInterface $request, $arguments = null)
	{
		if (empty($arguments))
		{
			return;
		}
		$permits = service('permits');

		if (! $userId = $permits->sessionUserId())
		{
			return;
		}

		// Check each requested permission
		foreach ($arguments as $permission)
		{
			if (! $permits->hasPermit($userId, $permission))
			{
				if (config('Permits')->silent)
				{
					return redirect()->back()->with('error', lang('Permits.notPermitted'));
				}
				else
				{
					throw PermitsException::forNotPermitted();
				}
			}
		}

		return;
	}

	/**
	 * Allows After filters to inspect and modify the response
	 * object as needed. This method does not allow any way
	 * to stop execution of other after filters, short of
	 * throwing an Exception or Error.
	 *
	 * @param \CodeIgniter\HTTP\RequestInterface  $request
	 * @param \CodeIgniter\HTTP\ResponseInterface $response
	 * @param null                                $arguments
	 *
	 * @return mixed
	 */
	public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
	{
	}
}
