<?php
/**
 * © roxblnfk 2019
 */

namespace App\Http\Controller;

use App\Model\Filesystem\FileInfo;
use App\Repository\ImageFileRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpException;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Psr7\UploadedFile;
use Throwable;

class PictureController extends BaseController
{
    /**
     * Загрузка файла бэкапа
     * @param Request $request
     * @param array   $args
     * @return Response
     * @throws HttpException
     */
    public function actionUpload(ImageFileRepository $repository, Request $request, $args = []): Response
    {
        $this->checkAccess($request);
        $dir = $repository->getDir();
        /** @var UploadedFile[] $files */
        $upFiles = $request->getUploadedFiles();
        $files = reset($upFiles) ?? [];
        $results = [];
        $count = 0;
        $this->ajax['count'] = &$count;
        $this->ajax['files'] = &$results;
        if (!$files) {
            return $this->prepareResponse('No uploaded files', true);
        }
        foreach ($files as $file) {
            $filename = preg_replace('/[^0-9a-zа-яё\\. _=+\\)\\(\\[\\]\\!\\-]/ui', '', basename($file->getClientFilename()));
            if (!in_array(strtolower(substr($filename, -4)), ['.jpg', '.gif', '.png'], true))
                continue;
            $results[] = $filename;
            $file->moveTo($dir . '/' . $filename);
            ++$count;
        }
        return $this->prepareResponse($count . ' files uploaded', true);
    }
    /**
     * Загрузка файла бэкапа
     * @param Request $request
     * @param array   $args
     * @return Response
     * @throws HttpException
     */
    public function actionDelete(Request $request, $args = []): Response
    {
        $this->checkAccess($request);
        $file = $this->getFileFromRequest($request, $args, true);
        if (!$file)
            throw new HttpNotFoundException($request, 'File not found');
        $result = unlink($file->path);
        $this->ajax['success'] = $result;
        return $this->prepareResponse($result ? ':-)' : ':-(', true);
    }
    /**
     * @param Request $request
     * @param array   $args
     * @param bool    $local
     * @return null|FileInfo
     * @throws HttpBadRequestException
     */
    protected function getFileFromRequest(Request $request, $args = [], $local = true): ?FileInfo
    {
        $fileName = $_POST['fileName'] ?? $_GET['fileName'] ?? null;
        if (!is_string($fileName))
            throw new HttpBadRequestException($request, 'Bad filename');
        /** @var ImageFileRepository $repository */
        $repository = $this->container->get(ImageFileRepository::class);
        return $repository->getImage($fileName);
    }

    /**
     * @param Request $request
     * @throws HttpUnauthorizedException
     */
    protected function checkAccess(Request $request) {}
}
