<?php

declare(strict_types=1);

namespace Exaream;

error_reporting(E_ALL);

class FileTool
{
    /**
     * Get all file paths under a specified directory
     * 指定ディレクトリ配下の全ファイルのパスを取得
     *
     * @param string $directory
     * @return array
     */
    public function getFilePathsByDirectory(string $directory): array
    {
        $files = glob(rtrim($directory, '/') . '/*');
        $list = [];
        foreach ($files as $file) {
            if (is_file($file)) {
                $list[] = $file;
            }
            if (is_dir($file)) {
                $list = array_merge($list, $this->getFilePathsByDirectory($file));
            }
        }
        return $list;
    }

    /**
     * Get an extension based on a file path
     * ファイルパスを元に拡張子を取得
     *
     * @param string $filePath
     * @return string
     */
    public function getExtensionByFilePath(string $filePath): string
    {
        return file_exists($filePath) ? pathinfo($filePath, PATHINFO_EXTENSION) : '';
    }
}
