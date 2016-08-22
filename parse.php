<?php

date_default_timezone_set('Europe/Berlin');

$files = [];

define("SVN_PATH", "/Users/Sacha/Documents/Git/memberdatabase");
define("EXCLUDES", "7.2 Source_Code");

$path = realpath(SVN_PATH);
$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));

$excludes = explode(";", EXCLUDES);

$allowedFileTypes = [
    'docx',
    'doc',
    'xlsx',
    'xls',
    'pptx',
    'ppt',
    'pdf',
    'txt'
];

$nextId = 0;

foreach ($objects as $name => $object){
    $excludeItem = false;

    foreach ($excludes as $exclude) {
        if (strpos($name, $exclude) != false) {
            $excludeItem = true;
        }
    }

    $tmp = explode('/', $name);
    $fileName = end($tmp);
    $tmp = explode('.', $fileName);
    $fileType = end($tmp);

    if ($excludeItem || !in_array($fileType, $allowedFileTypes)) {
        continue;
    }

    array_push($files, [
        "id" => $nextId++,
        "filename" => pathinfo($name, PATHINFO_FILENAME),
        "extension" => pathinfo($name, PATHINFO_EXTENSION),
        "path" => $name
    ]);
}

$dataFile = fopen('data.js', 'w');
fwrite($dataFile, '{ "documents": ' . json_encode($files) . ' }');
fclose($dataFile);

echo "\n\033[0;32mSUCCESS\033[0m: data.js has been created and the Document Finder is ready!\n";

die();
