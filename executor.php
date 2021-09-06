<?php

declare(strict_types=1);

error_reporting(E_ALL);

use Exaream\NewLineConvertor;

//-----------------------------------------
// Overview
//-----------------------------------------

// Check new lines of all files under the specified directories and convert them to LF
// 指定ディレクトリ配下の全ファイルの改行コードを確認しLFに変換

//-----------------------------------------
// Configuration
//-----------------------------------------

// Set paths of directories and files to exclude
// 変換から除外するディレクトリやファイルをフルパスで設定
$exclusionPaths = [];

// Set exclusion extensions
// 変換から除外するファイルの拡張子を設定
$exclusionExtensions = [
    'csv',
    'png',
    'gif',
    'jpg',
];

// Set a target directory
// 対象のディレクトリを設定
$targetDirectory = __DIR__;

// Set a correct new line
// 適切な改行コードを設定
$toNewLine = "\n";

// Set incorrect new lines using regular expression
// 不適切な改行コードを正規表現の形式で設定
$incorrectNewLines = "/\r\n|\r/";

//-----------------------------------------
// Execution
//-----------------------------------------
$convertor = new NewLineConvertor();
$convertor->setExclusionPaths($exclusionPaths);
$convertor->setExclusionExtensions($exclusionExtensions);
$convertor->setIncorrectNewLines($incorrectNewLines);

// Get file paths including incorrect new lines
// 不適切な改行コードを含むファイルの一覧を取得
$targetFilePaths = $convertor->getFilePathsIncorrectNewLine($targetDirectory, $toNewLine);
if (empty($targetFilePaths)) {
    echo "There is no target files." . PHP_EOL;
    exit(0);
}
echo 'Target file path list:' . PHP_EOL;
echo implode(PHP_EOL, $targetFilePaths) . PHP_EOL;

// Convert new lines of files under a target directory
// 対象ディレクトリ配下のファイルの改行コードを変換
$convertedFilePaths = $convertor->convertNewLine($targetDirectory, $toNewLine);
echo 'Converted file path list:' . PHP_EOL;
echo implode(PHP_EOL, $convertedFilePaths) . PHP_EOL;
