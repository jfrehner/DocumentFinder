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
        array_push($files, array("filename" => basename($name), "path" => $name));
    }
}

?>

<html ng-app="myApp" ng-controller="FileViewer">
<head>
    <title></title>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.3.14/angular.min.js"></script>
    <script type="text/javascript">
        angular.module('myApp', [])
            .controller('FileViewer', function($scope) {
                $scope.files = <?php echo json_encode($files) ?>;
                $scope.data = {search:""};
            });
    </script>
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
        a {
            text-decoration: none;
            color: black;
            font-weight: 300;
            display: block;
            padding: 10px;
        }
        a:hover {
            background-color: #3A98FE ;
            color: white;
        }
        .container {
            width: 800px;
            margin: 0 auto;
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
            <li ng-repeat="file in files | filter: data.search track by $index"><a href="file://{{file.path}}">{{ file.filename }}</a></li>
        </ul>
    </div>
</div>

</body>
</html>