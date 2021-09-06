<?php

declare(strict_types=1);

namespace Exaream;

error_reporting(E_ALL);

use Exaream\FileTool;

class NewLineConvertor
{
    const LINE_FEED = "\n";
    private $fileTool;
    private $incorrectNewLines = "/\r\n|\r/";
    private $exclusionPaths = [];
    private $exclusionExtensions = [];

    public function __construct()
    {
        $this->fileTool = new FileTool();
    }

    /**
     * Set incorrect new lines using regular expression
     * 不適切な改行コードを正規表現の形式で設定
     *
     * @param string $incorrectNewLines
     * @return void
     */
    public function setIncorrectNewLines(string $incorrectNewLines): void
    {
        $this->incorrectNewLines = $incorrectNewLines;
    }

    /**
     * Set paths of directories and files to exclude
     * 除外対象のディレクトリやファイルのパスを設定
     *
     * @param array $exclusionDirectories
     * @return void
     */
    public function setExclusionPaths(array $exclusionPaths): void
    {
        foreach ($exclusionPaths as $exclusionPath) {
            $this->exclusionPaths[$exclusionPath] = mb_strlen($exclusionPath);
        }
    }

    /**
     * Set exclusion extensions
     * 除外対象の拡張子を設定
     *
     * @param array $exclusionExtensions
     * @return void
     */
    public function setExclusionExtensions(array $exclusionExtensions): void
    {
        $this->exclusionExtensions = $exclusionExtensions;
    }

    /**
     * Get file paths including incorrect new line
     * 不適切な改行コードを含むファイルの一覧を取得
     *
     * @param string $directory
     * @param string $toNewLine
     * @return array
     */
    public function getFilePathsIncorrectNewLine(string $directory, string $toNewLine = self::LINE_FEED): array
    {
        return $this->execute($directory, false, $toNewLine);
    }

    /**
     * Convert new lines of files under target directory
     * 対象ディレクトリ配下のファイルの改行コードを変換
     *
     * @param string $directory
     * @param string $toNewLine
     * @return array
     */
    public function convertNewLine(string $directory, string $toNewLine = self::LINE_FEED): array
    {
        return $this->execute($directory, true, $toNewLine);
    }

    /**
     * Execute common process
     * 共通処理を実行
     *
     * @param string $directory
     * @param boolean $convertMode
     * @param string $toNewLine
     * @return array
     */
    private function execute(string $directory, bool $convertMode = true, string $toNewLine = self::LINE_FEED): array
    {
        $filePaths = $this->fileTool->getFilePathsByDirectory($directory);
        if (empty($filePaths)) {
            exit('There is no file.');
        }
        $targetFilePaths = [];
        foreach ($filePaths as $filePath) {
            // Files and directories to exclude are skipped
            // 除外対象ディレクトリやファイルをスキップ
            foreach ($this->exclusionPaths as $exclusionPath => $exclusionPathLength) {
                if (substr($filePath, 0, $exclusionPathLength) === $exclusionPath) {
                    continue 2;
                }
            }
            $extension = $this->fileTool->getExtensionByFilePath($filePath);
            if (!empty($this->exclusionExtensions) && in_array(strtolower($extension), $this->exclusionExtensions)) {
                continue;
            }
            if (!$fileContents = file_get_contents($filePath)) {
                throw new \Exception("Failed to get file contents: {$filePath}");
            }
            if (preg_match($this->incorrectNewLines, $fileContents) !== 1) {
                continue;
            }
            if ($convertMode === true && !file_put_contents($filePath, $this->convertStringNewline($fileContents, $toNewLine))) {
                throw new \Exception("Failed to replace file contents: {$filePath}");
            }
            $targetFilePaths[] = $filePath;
        }
        return $targetFilePaths;
    }

    /**
     * Convert new line of string
     * 文字列の改行コードを変換
     *
     * @param string $string
     * @param string $to
     * @return string
     */
    public function convertStringNewline(string $string, string $to = self::LINE_FEED)
    {
        return preg_replace($this->incorrectNewLines, $to, $string);
    }
}
