<?php
include 'authCheck.php';
include 'fileLocationConfig.php';
?>

<html>
<head>
    <title>Standard Output</title>
    <link href="generalBody.css" rel="stylesheet">
</head>
<body>


<?php
if(isset($_GET["expId"]) && isset($_GET["nodeId"]) && isset($_GET["portId"])) {
    $expId = $_GET["expId"];
    $nodeId = $_GET["nodeId"];
    $portId = $_GET["portId"];
    $outFileName = $expId . '_' . $nodeId . '_' . $portId . '.out';
    $outFullFilePath = $expDir . DIRECTORY_SEPARATOR . $expId . DIRECTORY_SEPARATOR . $outFileName;
    $fileSize = filesize($outFullFilePath);

    try {
        $nodes = [];
        $edges = [];
        $nodeIdPos = -1;
        $nodeNamePos = -1;
        global $outFullFilePath;
        $fileHandle = fopen($outFullFilePath, "r");
        $isNode = true;
        $sameWeight = -1;
        if($fileHandle) {
            while(($line = fgets($fileHandle))!= false) {
                // first line ndoe definition
                if(substr($line, 0, 7) == "nodedef") {
                    // start node definition
                    $nodeAttrs = explode(",", explode(">", $line)[1]);
                    for($i=0;$i<count($nodeAttrs);$i++) {
                        $attrStr = $nodeAttrs[$i];
                        if(trim($attrStr)=="nodeID") {
                            $nodeIdPos = $i;
                        } else if(trim($attrStr)=="nodeName") {
                            $nodeNamePos = $i;
                        }
                    }
                    if($nodeIdPos < 0) {
                        throw new Exception("Node Id node found");
                    }
                } else if(substr($line, 0, 7) == "edgedef") {
                    $edgeAttrs = explode(",", explode(">", $line)[1]);
                    $isNode = false;
                    if(count($edgeAttrs)==2) {
                        $sameWeight = 1;
                    }
                } else {
                    if($isNode) {
                        $lineValues = explode(",", $line);
                        $tempNode = [];
                        $tempNode["id"] = trim(strval($lineValues[$nodeIdPos]));
                        if($nodeNamePos>=0) {
                            $tempNode["name"] = trim(strval($lineValues[$nodeNamePos]));
                        }
                        array_push($nodes, $tempNode);
                    } else {
                        $lineValues = explode(",", $line);
                        $tempEdge = [];
                        $tempEdge['source'] = intval($lineValues[0]);
                        $tempEdge['target'] = intval($lineValues[1]);
                        if($sameWeight<0) {
                            $tempEdge['weight'] = intval($lineValues[2]);
                        } else {
                            $tempEdge['weight'] = $sameWeight;
                        }
                        array_push($edges, $tempEdge);
                    }
                }
            }
            $resultStr = "<script>";
            $resultStr .= "var nodes = " . json_encode($nodes) . ";\n";
            $resultStr .= "var links2 = " . json_encode($edges) . ";\n";
            $resultStr .= "</script>";
        } else {
            throw new Exception("Graph file not found");
        }
    }
    catch(Exception $e) {
        echo "Exception occurs:" . strval($e) . "<br>";
        exit;
    }
} else {
    exit;
}
?>

<H1>
    Exp: <?php echo $expId ?> <br> Node: <?php echo $nodeId ?> &nbsp;&nbsp;&nbsp;&nbsp; Port: <?php echo $portId ?>
</H1>
<hr/>
<?php if(count($nodes)<256) : ?>
    <div>
        <canvas id="cv" width="1247" height="1247">
        </canvas>
    </div>
    <?php echo $resultStr; ?>
    <script src="matplot.js"></script>
<?php else : ?>
    Number of nodes: <?php echo count($nodes) . "<br>";?>
    The graph is too large to show!
<?php endif; ?>
</body>
</html>