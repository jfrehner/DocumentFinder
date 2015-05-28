<?php

require_once 'config/config.php';
$files = file_get_contents('data.json', true);

?>

<html ng-app="myApp" ng-controller="FileViewer">
<head>
    <title></title>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.3.14/angular.min.js"></script>
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
    <script type="text/javascript">
        angular.module('myApp', [])
            .controller('FileViewer', function ($scope, $sce) {
                $scope.documentRoot = "<?php echo realpath(SVN_PATH); ?>";
                $scope.files = <?php echo $files ?>;
                $scope.data = {
                    search: ''
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
                }

                $scope.getIcon = function (extension) {
                    var file = ''

                    switch (extension) {
                        case 'xlsx': case 'xls':
                            file = 'fa-file-excel-o excelColor'
                            break
                        case 'docx': case 'doc':
                            file = 'fa-file-word-o wordColor'
                            break
                        case 'pptx': case 'ppt':
                            file = 'fa-file-powerpoint-o powerpointColor'
                            break
                        case 'pdf':
                            file = 'fa-file-pdf-o pdfColor'
                            break
                        case 'txt':
                            file = 'fa-file-text-o txtColor'
                            break
                    }

                    return file
                }

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

                $scope.getRole = function (path) {
                    return path.split("/")[1].split(" ").pop()
                }
            });
    </script>
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:300,400' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <div class="sidebar">
        <ul class="unstyled-list roles-list">
            <li><span data-role="IM">IM</span></li>
            <li><span data-role="MP">MP</span></li>
            <li><span data-role="QS">QS</span></li>
            <li><span data-role="RE">RE</span></li>
            <li><span data-role="USE">USE</span></li>
            <li><span data-role="ST">ST</span></li>
            <li><span data-role="SA">SA</span></li>
        </ul>
    </div>
    <div class="content-wrapper">
        <div class="input-wrapper">
            <input class="search-input" type="text" ng-model="data.search" placeholder="Enter a document name">
            <div class="search-options">
                <label for="uncommon">
                    <input type="checkbox" ng-model="options.includeAll" id="uncommon"/><span class="label">Include uncommon</span>
                </label>
                <label for="searchInContent">
                    <input type="checkbox" ng-model="options.searchInContent" id="searchInContent"/><span class="label">Search in content</span>
                </label>
                <label for="showPreview">
                    <input type="checkbox" ng-model="options.showPreview" id="showPreview"/><span class="label">Show Preview</span>
                </label>
                <label for="showPath">
                    <input type="checkbox" ng-model="options.showPath" id="showPath"/><span class="label">Show Path</span>
                </label>
            </div>
        </div>
        <div class="content">
            <ul class="files-list unstyled-list">
                <li ng-repeat="file in files | filter:extensionFilter | filter: options.searchInContent ? data.search : {filename:data.search} track by $index">
                    <a href="file://{{file.path}}">
                        <span class="icon fa" ng-class="getIcon(file.extension)"></span>
                        <div ng-if="options.searchInContent && data.search.length > 0">({{ findOccurances(file) }})</div>
                        <div class="file-name">{{ file.filename }}</div>
                        <div ng-show="options.showPath" class="path">{{ getPath(file) }}</span><span data-role="{{ getRole(getPath(file)) }}" class="role">{{ getRole(getPath(file)) }}</div>
                        <div ng-if="options.searchInContent && data.search.length > 0" class="resultContent" ng-bind-html="displayContent(file)"></div>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</body>
</html>
