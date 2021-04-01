<?php

namespace App\Services\Uploader;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Symfony\Component\Security\Core\Security;

class VimeoUploader implements UploaderInterface
{
    private $vimeoToken;

    public function __construct(Security $security)
    {
        $this->vimeoToken = $security->getUser()->getVimeoApiKey();
    }

    public function upload(UploadedFile $file)
    {
    }

    public function delete(string $path)
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => sprintf('https://api.vimeo.com/videos/%s', $path),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'DELETE',
            CURLOPT_HTTPHEADER => [
                'Accept: application/vnd.vimeo.*+json;version=3.4',
                "Authorization: Bearer {$this->vimeoToken}",
                'Cache-Control: no-cache',
                'Content-Type: application/x-www-form-urlencoded',
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            throw new ServiceUnavailableHttpException('Error. Try again later. Message: '.$err);
        } else {
            return true;
        }
    }
}
