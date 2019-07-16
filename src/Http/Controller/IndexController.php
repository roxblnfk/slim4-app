<?php


namespace App\Http\Controller;


use App\Repository\ImageFileRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class IndexController extends BaseController
{
    /**
     * @return Response
     * @throws \Throwable
     */
    public function pageIndex() {
        return $this->render('index', []);
    }

    // public function pageIndex(Request $request, Response $response, $args = []) {
}
