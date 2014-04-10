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
}
?>
<H1>
    Exp: <?php echo $expId ?> <br> Node: <?php echo $nodeId ?> &nbsp;&nbsp;&nbsp;&nbsp; Port: <?php echo $portId ?>
</H1>
<hr/>
<?php if($fileSize<=20000) : ?>
<pre><?php
    echo file_get_contents($outFullFilePath);
    ?></pre>
<?php else : ?>
File size: <?php echo intval($fileSize)/1000 . "KB<br>";?>
File is too large to show!
<?php endif; ?>
</body>
</html>