<?php
include 'authCheck.php';
include 'userConfig.php';
?>
<html>
<head>
    <title>Manage Data</title>
    <link href="form.css" type="text/css" rel="stylesheet">
    <link href="generalBody.css" rel="stylesheet">
    <link href="table.css" rel="stylesheet">

    <script src="TableFilter/tablefilter_all_min.js" language="javascript" type="text/javascript"></script>
    <script src="TableFilter/sortabletable.js" language="javascript" type="text/javascript"></script>
    <script src="TableFilter/tfAdapter.sortabletable.js" language="javascript" type="text/javascript"></script>
</head>
<body>
<?php
include "htmlHeader.php";
?>
<h1>Manage Data</h1>
<HR>
<table class="full" id="dataList" cellpadding="0" cellspacing="0">
    <thead>
    <tr>
        <th>Name</th>
        <th>Data Type</th>
        <th>Version</th>
        <th>Create Time</th>
        <th>User Name</th>
        <th>Description</th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody>
        <?php
        include 'dbConfig.php';
        $sqlStr = "select Data.Name, DataType.Name, Version, CreateTime, UserName, Data.Description, DataId from Data, DataType where Data.DataTypeId = DataType.DataTypeId";
        $results = execQuery($sqlStr);
        while($row = mysqli_fetch_row($results)) {
            echo "<tr class='New'>";
            echo "<td>" . $row[0] . "</td>";
            echo "<td>" . $row[1] . "</td>";
            echo "<td>" . $row[2] . "</td>";
            echo "<td>" . $row[3] . "</td>";
            echo "<td>" . $row[4] . "</td>";
            echo "<td>" . $row[5] . "</td>";
            echo "<td><a href='downloadData.php?dataId=" . $row[6] . "'>Download</a>";
            echo "&nbsp&nbsp&nbsp<a href='newData.php?dataId=" . $row[6] . "'>Update</a>";
            if($_SESSION['user'] == $row[4] || isAdmin()) {
                echo "&nbsp&nbsp&nbsp<a href='deleteData.php?dataId=" . $row[6] . "'><font color='red'>Delete</font></a>";
            }
            echo "</td>";
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
        col_3: "none",
        col_4: "select",
        col_6: "none",
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
    var tf1 = setFilterGrid("dataList", props);
</script>
<?php
include "htmlFooter.php";
?>
</body>
</html>