<?php

require_once 'config/config.php';

// https://blog.mayflower.de/561-Import-and-export-data-using-PHPExcel.html

require_once 'vendor/autoload.php';

$parser = new \Smalot\PdfParser\Parser();

$files = [];

$path = realpath(SVN_PATH);
$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));

$excludes = [
    '7 Hauptordner SC/',
    '.svn/',
    '~'
];

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

    $content = "";

    if ($fileType == 'docx') {
        $content = read_docx($name);
    } elseif ($fileType == 'doc') {
        $content = read_doc($name);
    } elseif ($fileType == 'xlsx') {
        $objPHPExcel = PHPExcel_IOFactory::load($name);
        foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
            $worksheetTitle     = $worksheet->getTitle();
            $highestRow         = $worksheet->getHighestRow(); // e.g. 10
            $highestColumn      = $worksheet->getHighestColumn(); // e.g 'F'
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
            $nrColumns = ord($highestColumn) - 64;
            $content .= "<br>The worksheet ".$worksheetTitle." has ";
            $content .= $nrColumns . ' columns (A-' . $highestColumn . ') ';
            $content .= ' and ' . $highestRow . ' row.';
            $content .= '<br>Data: <table border="1"><tr>';
            for ($row = 1; $row <= $highestRow; ++ $row) {
                $content .= '<tr>';
                for ($col = 0; $col < $highestColumnIndex; ++ $col) {
                    $cell = $worksheet->getCellByColumnAndRow($col, $row);
                    $val = $cell->getValue();
                    $dataType = PHPExcel_Cell_DataType::dataTypeForValue($val);
                    $content .= '<td>' . $val . '</td>';
                }
                $content .= '</tr>';
            }
            $content .= '</table>';
            $content = strip_tags($content);
        }
    } elseif ($fileType == 'pdf') {
        // $pdf = $parser->parseFile($name);
        //$content = $pdf->getText();
    }

    array_push($files, array("filename" => pathinfo($name, PATHINFO_FILENAME), "extension" => pathinfo($name, PATHINFO_EXTENSION), "path" => $name/* , "content" => $content */));
}

function read_doc($filename) {
    $fileHandle = fopen($filename, "r");
    $line = @fread($fileHandle, filesize($filename));
    $lines = explode(chr(0x0D),$line);
    $outtext = "";
    foreach($lines as $thisline)
    {
        $pos = strpos($thisline, chr(0x00));
        if (($pos !== FALSE)||(strlen($thisline)==0))
        {
        } else {
            $outtext .= $thisline." ";
        }
    }
    $outtext = preg_replace("/[^a-zA-Z0-9\s\,\.\-\n\r\t@\/\_\(\)]/","",$outtext);
    return $outtext;
}

function read_docx($filename){

    $content = '';

    $zip = zip_open($filename);

    if (!$zip || is_numeric($zip)) return false;

    while ($zip_entry = zip_read($zip)) {
        if (zip_entry_open($zip, $zip_entry) == FALSE) continue;
        if (zip_entry_name($zip_entry) != "word/document.xml") continue;
        $content .= zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
        zip_entry_close($zip_entry);
    }// end while

    zip_close($zip);

    $content = str_replace('</w:r></w:p></w:tc><w:tc>', " ", $content);
    $content = str_replace('</w:r></w:p>', "\r\n", $content);
    $striped_content = strip_tags($content);

    return $striped_content;
}

$dataFile = fopen('data.js', 'w');
fwrite($dataFile, 'window.documentRoot = "' . realpath(SVN_PATH) .  '"; window.files = ' . json_encode($files) . ';');
fclose($dataFile);

die();