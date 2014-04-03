<?php
    include 'authCheck.php';
    include 'userConfig.php';
?>
<html>
<head>
    <title>Avalon-Current Experiments</title>
    <script src="TableFilter/tablefilter_all_min.js" language="javascript" type="text/javascript"></script>
    <script src="TableFilter/sortabletable.js" language="javascript" type="text/javascript"></script>
    <script src="TableFilter/tfAdapter.sortabletable.js" language="javascript" type="text/javascript"></script>
    <link rel="stylesheet" href="table.css">
    <link href="generalBody.css" rel="stylesheet">
</head>
<body>
<?php
include "htmlHeader.php";
?>
<h1>
    Current Experiments
</h1>
<hr>
    <table class="full" id="expList" cellpadding="0" cellspacing="0">
        <thead>
        <tr>
            <th>Exp name</th>
            <th>User Name</th>
            <th>Exp Status</th>
            <th>Create Time</th>
            <th>Description</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
            <?php
                include 'dbConfig.php';
                $sqlStr = "select Name,UserName,ExpStatus,Description,CreateTime, ExpId from Experiment";
                $results = execQuery($sqlStr);
                while($row = mysqli_fetch_row($results)) {
                    echo "<tr class=\"" . $row[2] . "\">";
                    echo "<td><a href=\"showExpSimple.php?expId=" . $row[5] . "\">" . $row[0] . "</a></td>";
                    echo "<td>" . $row[1] . "</td>";
                    echo "<td>" . $row[2] . "</td>";
                    echo "<td>" . $row[4] . "</td>";
                    echo "<td>" . $row[3] . "</td>";
                    echo "<td>";
		      $cloneStr = sprintf("<a href='newExp.php?expId=%s'>Clone</a>", $row[5]);
                    $resultStr = $cloneStr . "&nbsp;&nbsp;&nbsp;";
		      echo $resultStr;
                    if($_SESSION['user'] == $row[1] || isAdmin())
                        echo getActionStrByStatus($row[5], $row[2]);
                    echo "</td>";
                    echo "</tr>";
                }
            function getActionStrByStatus($expId,$status) {
                $rerunStr = sprintf("<a href='alterExperiment.php?expId=%s&request=%s'>Resubmit</a>", $expId, "Rerun");
                $pauseStr = sprintf("<a href='alterExperiment.php?expId=%s&request=%s'>Pause</a>", $expId, "Pause");
                $resumeStr = sprintf("<a href='alterExperiment.php?expId=%s&request=%s'>Resume</a>", $expId, "Resume");
                $stopStr = sprintf("<a href='alterExperiment.php?expId=%s&request=%s'>Stop</a>", $expId, "Stop");
                $deleteStr = sprintf("<a href='alterExperiment.php?expId=%s&request=%s'>Delete</a>", $expId, "Delete");
                $resultStr = '';
                if($status=='New' || $status=='Running') {
                    $resultStr .= ($pauseStr . "&nbsp;&nbsp;&nbsp;&nbsp;");
                }
                if($status=='New' || $status=='Running' || $status=='Pausing') {
                    $resultStr .= ($stopStr . "&nbsp;&nbsp;&nbsp;&nbsp;");
                }
                if($status=='Pausing') {
                    $resultStr .= ($resumeStr . "&nbsp;&nbsp;&nbsp;&nbsp;");
                }
                if($status=='Failed' || $status=='Finished') {
                    $resultStr .= ($rerunStr  . "&nbsp;&nbsp;&nbsp;&nbsp;");
                }
                $resultStr .= $deleteStr;
                return $resultStr;
            }
            ?>
        </tbody>
    </table>
<script language="javascript" type="text/javascript">
    var props = {
        sort: true,
        filters_row_index:1,
        paging: true,
        paging_length: 10,
        results_per_page: ['# rows per page',[10,20,50]],
        col_1: "select",
        col_2: "select",
        col_3: "none",
        rows_counter: true,
        rows_counter_text: "Rows:",
        btn_reset: true,
        btn_next_page_html: '<a href="javascript:;" style="margin:3px;">Next ></a>',
        btn_prev_page_html: '<a href="javascript:;" style="margin:3px;">< Previous</a>',
        btn_last_page_html: '<a href="javascript:;" style="margin:3px;"> Last >|</a>',
        btn_first_page_html: '<a href="javascript:;" style="margin:3px;"><| First</a>',
        loader: true,
        loader_html: '<h4 style="color:green;">Loading, please wait...</h4>'
    };
    var tf1 = setFilterGrid("expList", props);
</script>
<?php
include "htmlFooter.php";
?>
</body>
</html>