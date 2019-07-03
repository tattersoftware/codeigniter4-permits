<?php namespace Tatter\Permits\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class PermitsFilter implements FilterInterface
{
    public function before(RequestInterface $request, $params = null)
    {
	    if (empty($params))
			return;

        $permits = services('permits');
        $userId = $permits->sessionUserId();
        
        if (empty($userId))
        	return;
        
        // Check each requested permission
        $result = true;
		foreach ($params as $permission)
			$result = $result && $permits->hasPermit($userId, $permission);
		
        return $result;
    }

    //--------------------------------------------------------------------

    public function after(RequestInterface $request, ResponseInterface $response)
    {

    }
}
