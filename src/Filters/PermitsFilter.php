<?php namespace Tatter\Permits\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class PermitsFilter implements FilterInterface
{
    public function before(RequestInterface $request, $params = null)
    {
        var_dump($params);
        die();
    }

    //--------------------------------------------------------------------

    public function after(RequestInterface $request, ResponseInterface $response)
    {

    }
}
