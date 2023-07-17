<?php

use Mimey\MimeTypes;

if (! function_exists('fileHelper'))
{
    function fileHelper()
    {
        return new class
        {
            /**
             * Retorna o mimetype de um arquivo pelos dados binários dele
             * @param string $dadosBinarios dados binários do arquivo
             * @return string $mimeType mimetype do arquivo
             */
            public function getMimeTypeByBinary(string $dadosBinarios): string
            {
                $finfo = finfo_open();
                $mimeType = finfo_buffer($finfo, $dadosBinarios, FILEINFO_MIME_TYPE);
                finfo_close($finfo);

                return $mimeType;
            }

            public function getContentsOfBase64ComData(string $base64ComData): string
            {
                $base64 = explode(',', $base64ComData)[1];

                $contentsBinary = base64_decode($base64);

                return $contentsBinary;
            }

            public function getContentsOfBase64SemData(string $base64SemData): string
            {
                $contentsBinary = base64_decode($base64SemData);

                return $contentsBinary;
            }

            public function getBas64DeBase64WithData(string $base64ComData): string
            {
                $base64 = explode(',', $base64ComData)[1];

                return $base64;
            }

            public function getMimeTypeByExtension(string $extension): string
            {
                $mimeType = (new MimeTypes())->getMimeType($extension);

                return $mimeType;
            }

            public function getExtensionByMimeType(string $mimeType): string
            {
                $extension = (new MimeTypes())->getExtension($mimeType);

                return $extension;
            }
        };
    }
}