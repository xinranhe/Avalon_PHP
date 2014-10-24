<?php
include 'authCheck.php';
include 'dbConfig.php';
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
<h1>Manage Module</h1>
<HR>
<table class="full" id="moduleList" cellpadding="0" cellspacing="0">
    <thead>
    <tr>
        <th>Name</th>
        <th>Module Type</th>
        <th>Version</th>
        <th>Create Time</th>
        <th>User Name</th>
        <th>Description</th>
        <th>Input Arguments</th>
        <th>Output Arguments</th>
        <th>Parameters</th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody>
        <?php
        $sqlStr = "select Module.Name, ModuleType.Name, Version, CreateTime , UserName, Module.Description, "
            ."InputArguments, OutputArguments, ModuleParameters, ModuleId from Module, ModuleType where Module.ModuleTypeId = ModuleType.ModuleTypeId";
        $results = execQuery($sqlStr);
        while($row = mysqli_fetch_row($results)) {
            echo "<tr class='New'>";
            echo "<td>" . $row[0] . "</td>";
            echo "<td>" . $row[1] . "</td>";
            echo "<td>" . $row[2] . "</td>";
            echo "<td>" . $row[3] . "</td>";
            echo "<td>" . $row[4] . "</td>";
            echo "<td>" . $row[5] . "</td>";
            echo "<td>" . getArgSelectionFromString($row[6]) . "</td>";
            echo "<td>" . getArgSelectionFromString($row[7]) . "</td>";
            echo "<td>" . getParamsSelectionFromString($row[8]) . "</td>";
            echo "<td>";
            echo "<a href='newModule.php?moduleId=" . $row[9] . "'>Update</a>";
            if($_SESSION['user']==$row[4] || isAdmin()) {
                echo "&nbsp;&nbsp;&nbsp;<a href='deleteModule.php?moduleId=" . $row[9] . "'><font color='red'>Delete</font></a>";
            }
            echo "</td></tr>";
        }
        function getArgSelectionFromString($ioString) {
            $resultStr = '';
            $sqlStr = "select DataTypeId, Name from DataType";
            $results = execQuery($sqlStr);
            $id2name = Array();
            while($row = mysqli_fetch_row($results)) {
                $id2name[$row[0]] = $row[1];
            }
            $fields = explode(';', $ioString);
            $argNum = intval($fields[0]);
            if($argNum==0) {
                return "No arguments";
            }
            $resultStr .= "<select>";
            for($i=1;$i<=$argNum;$i++) {
                $tempFileds = explode(':', $fields[$i]);
                $name = $tempFileds[0];
                $typeName = $id2name[intval($tempFileds[1])];
                $resultStr .= "<option>" . $name . ':' . $typeName . "</option>";
            }
            $resultStr .= "</select>";
            return $resultStr;
        }
        function getParamsSelectionFromString($paraString) {
            $resultStr = '';
            $fields = explode(';', $paraString);
            $argNum = intval($fields[0]);
            if($argNum==0) {
                return "No parameters";
            }
            $resultStr .= "<select>";
            for($i=1;$i<=$argNum;$i++) {
                $resultStr .= "<option>" . $fields[$i] . "</option>";
            }
            $resultStr .= "</select>";
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
        paging_length: 20,
        results_per_page: ['# rows per page',[20,20,50]],
        col_1: "select",
        col_3: "none",
        col_4: "select",
        col_6: "none",
        col_7: "none",
        col_8: "none",
        col_9: "none",
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
    var tf1 = setFilterGrid("moduleList", props);
</script>
<?php
include "htmlFooter.php";
?>
</body>
</html>