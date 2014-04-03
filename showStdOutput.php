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
if(isset($_GET["expId"]) && isset($_GET["nodeId"])) {
    $expId = $_GET["expId"];
    $nodeId = $_GET["nodeId"];
}
?>
<H1>
    Exp: <?php echo $expId ?> <br> Node: <?php echo $nodeId ?>
</H1>
<H2>
    StdOut
</H2>
<hr/>
<pre><?php
        $outFileName = $expId . '_' . $nodeId . '_StdOut.txt';
        $outFullFilePath = $expDir . DIRECTORY_SEPARATOR . $expId . DIRECTORY_SEPARATOR . $outFileName;
        echo file_get_contents($outFullFilePath);
    ?></pre>
<H2>
    StdErr
</H2>
<hr/>
<pre><?php
    $errFileName = $expId . '_' . $nodeId . '_StdErr.txt';
    $errFullFilePath = $expDir . DIRECTORY_SEPARATOR . $expId . DIRECTORY_SEPARATOR . $errFileName;
    echo file_get_contents($errFullFilePath);
    ?></pre>
</body>
