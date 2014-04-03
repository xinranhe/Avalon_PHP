<?php
include 'authCheck.php';
?>
<html>
<head>
    <title>Avalon-Upload Items</title>
    <script src="TableFilter/tablefilter_all_min.js" language="javascript" type="text/javascript"></script>
    <script src="TableFilter/sortabletable.js" language="javascript" type="text/javascript"></script>
    <script src="TableFilter/tfAdapter.sortabletable.js" language="javascript" type="text/javascript"></script>

    <link rel="stylesheet" href="table.css">
    <link href="generalBody.css" rel="stylesheet">
    <style>
        .smallJson {font-size: 10;}
    </style>
</head>
<body>
<?php
include "htmlHeader.php";
?>
<h1>
    Upload Items
</h1>
<hr>
<table style="width:100%" id="expList" cellpadding="0" cellspacing="0" border="3">
    <thead>
    <tr>
        <th>Item Id</th>
        <th>Item Type</th>
        <th>File Location</th>
        <th>Main File</th>
        <th>Status</th>
        <th>Item Json</th>
    </tr>
    </thead>
    <tbody>
    <?php
    include 'dbConfig.php';
    $sqlStr = "select ItemId,ItemType,FileLocation,MainFile,Status,ItemJson from PendingItem";
    $results = execQuery($sqlStr);
    while($row = mysqli_fetch_row($results)) {
        echo "<tr class=\"" . $row[4] . "\">";
        echo "<td>" . $row[0] . "</td>";
        echo "<td>" . $row[1] . "</td>";
        echo "<td>" . $row[2] . "</td>";
        echo "<td>" . $row[3] . "</td>";
        echo "<td>" . $row[4] . "</td>";
        echo "<td><pre class=smallJson>" . $row[5] . "</pre></td>";
        echo "</tr>";
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
        col_4: "select",
        col_5: "none",
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