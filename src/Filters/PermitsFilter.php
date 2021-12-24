<?php

namespace Tatter\Permits\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Tatter\Permits\Exceptions\PermitsException;

class PermitsFilter implements FilterInterface
{
    /**
     * @param array|null $arguments
     *
     * @throws PermitsException
     *
     * @return RedirectResponse|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        if (empty($arguments)) {
            return;
        }
        $permits = service('permits');

        if (! $userId = $permits->sessionUserId()) {
            return;
        }

        // Check each requested permission
        foreach ($arguments as $permission) {
            if (! $permits->hasPermit($userId, $permission)) {
                if (config('Permits')->silent) {
                    return redirect()->back()->with('error', lang('Permits.notPermitted'));
                }

                throw PermitsException::forNotPermitted();
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
