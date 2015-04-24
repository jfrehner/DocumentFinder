<?php

require_once 'config/config.php';

// https://blog.mayflower.de/561-Import-and-export-data-using-PHPExcel.html
require_once 'Classes/PHPExcel.php';

require_once 'vendor/autoload.php';

$parser = new \Smalot\PdfParser\Parser();

$files = [];

$path = realpath(SVN_PATH);
$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
foreach($objects as $name => $object){
    if(strpos($name, "/.") == false && strpos($name, ".js") == false && strpos($name, ".css") == false
        && strpos($name, ".png") == false
        && strpos($name, ".svg") == false && strpos($name, ".gif") == false
        && strpos($name, ".php") == false && strpos($name, ".jpg") == false
        && strpos($name, "~") == false) {

        $content = "";

        if (strpos($name, ".docx")) {
            $content = read_docx($name);
        } elseif (strpos($name, ".doc")) {
            $content = read_doc($name);
        } elseif (strpos($name, ".xlsx")) {
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
        } elseif (strpos($name, ".pdf")) {
            $pdf = $parser->parseFile($name);
            //$content = $pdf->getText();
        }

        array_push($files, array("filename" => pathinfo($name, PATHINFO_FILENAME), "extension" => pathinfo($name, PATHINFO_EXTENSION), "path" => $name, "content" => $content));
    }
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
?>

<html ng-app="myApp" ng-controller="FileViewer">
<head>
    <title></title>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.3.14/angular.min.js"></script>
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
    <script type="text/javascript">
        angular.module('myApp', [])
            .controller('FileViewer', function($scope, $sce) {
                $scope.documentRoot = "/Applications/XAMPP/xamppfiles/htdocs/<?php echo SVN_PATH; ?>";
                $scope.files = <?php echo json_encode($files) ?>;
                $scope.data = {
                    search: ""
                };
                $scope.options = {
                    includeAll: false,
                    showPreview: true,
                    showPath: true
                };
                $scope.extensionFilter = function(file) {
                    return !$scope.options.includeAll
                        ? file.extension === 'docx'
                    || file.extension === 'doc'
                    || file.extension === 'xlsx'
                    || file.extension === 'xls'
                    || file.extension === 'pptx'
                    || file.extension === 'ppt'
                    || file.extension === 'pdf'
                    || file.extension === 'txt'
                        : true;
                };
                $scope.getIcon = function(extension) {
                    var file = '';
                    if (extension === 'xlsx' || extension === 'xls') file = 'fa-file-excel-o excelColor';
                    if (extension === 'docx' || extension === 'doc') file = 'fa-file-word-o wordColor';
                    if (extension === 'pptx' || extension === 'ppt') file = 'fa-file-powerpoint-o powerpointColor';
                    if (extension === 'pdf') file = 'fa-file-pdf-o pdfColor';
                    if (extension === 'txt') file = 'fa-file-text-o txtColor';
                    return file;
                };
                $scope.displayContent = function(file) {
                    if ($scope.data.search.length > 0 && $scope.options.showPreview) {
                        var area = 20;
                        var content = file.content;
                        var output = "";
                        var occurances = $scope.findOccurances(file);
                        for (var x = 0; x < occurances; x++) {
                            var pos = content.toLowerCase().indexOf($scope.data.search.toLowerCase());
                            output += nl2br(highlight(content.slice(pos-area,pos+area), $scope.data.search)) + "<br />---<br />";
                            content = content.slice(pos+1);
                        }
                        return $sce.trustAsHtml(output);
                    }
                };
                $scope.findOccurances = function(file) {
                    var string = file.content.toLowerCase();
                    var subString = $scope.data.search.toLowerCase();

                    if(subString.length<=0) return string.length+1;

                    var n=0, pos=0;
                    var step=subString.length;

                    while(true){
                        pos=string.indexOf(subString,pos);
                        if(pos>=0){ n++; pos+=step; } else break;
                    }
                    return(n);
                };
                function nl2br (str) {
                    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1<br />$2');
                }
                function preg_quote(str, delimiter) {
                    return String(str)
                        .replace(new RegExp('[.\\\\+*?\\[\\^\\]$(){}=!<>|:\\' + (delimiter || '') + '-]', 'g'), '\\$&');
                }
                function highlight(data, search)
                {
                    return data.replace( new RegExp( "(" + preg_quote( search ) + ")" , 'gi' ), "<strong style='color:#FF0000'>$1</strong>" );
                }
                $scope.getPath = function(file) {
                    return file.path.slice($scope.documentRoot.length, -(file.filename.length + file.extension.length + 1));
                }
            });
    </script>
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:300,400' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body >
<div class="wrapper">
    <div class="container">
        <h1>Realtime-Document Search</h1>
        <span id="inputWrapper">
            <input type="text" ng-model="data.search" placeholder="Enter a Documentname">
        </span>
        <input type="checkbox" ng-model="options.includeAll" id="uncommon"/><label for="uncommon">Include uncommon</label>
        <input type="checkbox" ng-model="options.searchInContent" id="searchInContent"/><label for="searchInContent">Search in content</label>
        <input type="checkbox" ng-model="options.showPreview" id="showPreview"/><label for="showPreview">Show Preview</label>
        <input type="checkbox" ng-model="options.showPath" id="showPath"/><label for="showPath">Show Path</label>
    </div>
    <div class="container">
        <ul>
            <li ng-repeat="file in files | filter:extensionFilter | filter: options.searchInContent ? data.search : {filename:data.search} track by $index">
                <i class="fa" ng-class="getIcon(file.extension)"></i>
                    <a href="file://{{file.path}}">
                        <span ng-if="options.searchInContent && data.search.length > 0">({{ findOccurances(file) }})
                        </span> {{ file.filename }}
                        <span ng-show="options.showPath" class="path">{{ getPath(file) }}
                        </span>
                    </a>
                    <div ng-if="options.searchInContent && data.search.length > 0" class="resultContent" ng-bind-html="displayContent(file)">
                    </div>
            </li>
        </ul>
    </div>
</div>

</body>
</html>