<?php

require_once 'config/config.php';

$files = [];

$path = realpath(SVN_PATH);
$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
foreach($objects as $name => $object){
    if(strpos($name, "/.") == false && strpos($name, ".js") == false && strpos($name, ".css") == false
        && strpos($name, ".html") == false && strpos($name, ".png") == false
        && strpos($name, ".svg") == false && strpos($name, ".gif") == false
        && strpos($name, ".php") == false && strpos($name, ".jpg") == false
        && strpos($name, "~") == false) {

        $content = "";

        if (strpos($name, ".docx")) {
            $content = read_doc($name);
        } elseif (strpos($name, ".php")) {
            $content = read_doc($name);
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

    $striped_content = '';
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
            .controller('FileViewer', function($scope) {
                $scope.files = <?php echo json_encode($files) ?>;
                $scope.data = {search:""};
                $scope.getIcon = function(extension) {
                    var file = '';
                    if (extension === 'xlsx' || extension === 'xls') file = 'fa-file-excel-o excelColor';
                    if (extension === 'docx' || extension === 'doc') file = 'fa-file-word-o wordColor';
                    if (extension === 'pptx' || extension === 'ppt') file = 'fa-file-powerpoint-o powerpointColor';
                    if (extension === 'pdf') file = 'fa-file-pdf-o pdfColor';
                    if (extension === 'txt') file = 'fa-file-text-o txtColor';
                    return file;
                }
            });
    </script>
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:300,400' rel='stylesheet' type='text/css'>
    <style type="text/css">
        * {
            margin: 0;
            padding: 0;
            font-family: "Open Sans";
        }
        h1 {
            position: relative;
            text-align: center;
            margin-top: 100px;
            margin-bottom: 20px;
            font-size: 45px;
            padding-top: 50px;
        }
        input {
            margin-bottom: 50px;
            width: 100%;
            font-size: 30px;
            font-weight: 300;
            padding: 5px;
            padding-left: 10px;
            border: none;
            background-color: #E1F6FE ;
        }
        .wrapper {
            width: 900px;
            margin: 0 auto;
            box-shadow: 1px 1px 35px 2px grey;
            padding-bottom: 80px;
        }
        li {
            list-style-type: none;
            border-top: 1px solid black;
            font-size: 20px;
        }
        li:hover {
            background-color: #3A98FE ;
        }
        li:hover a, li:hover i {
            color: white;
        }
        a {
            text-decoration: none;
            color: black;
            font-weight: 300;
            display: inline-block;
            padding: 10px;
        }
        i {
            padding-left: 5px;
            display: inline-block;
        }
        .container {
            width: 800px;
            margin: 0 auto;
        }
        .wordColor {
            color:#3e5ba9;
        }
        .excelColor {
            color:#0da056;
        }
        .powerpointColor {
            color:#f89a1d;
        }
        .pdfColor {
            color:#df4430;
        }
        .txtColor {
            color:#000000;
        }

    </style>
</head>
<body >
<div class="wrapper">
    <div class="container">
        <h1>Realtime-Document Search</h1>
        <input type="text" ng-model="data.search" placeholder="Enter a Documentname">
    </div>
    <div class="container">
        <ul>
            <li ng-repeat="file in files | filter: data.search track by $index">
                <i class="fa" ng-class="getIcon(file.extension)"></i>
                <a href="file://{{file.path}}">{{ file.filename }}</a>
            </li>
        </ul>
    </div>
</div>

</body>
</html>