<?php
    include 'authCheck.php';
    include 'dbConfig.php';
    include 'userConfig.php';
?>
<html>
<head>
    <title>Avalon-Experiment</title>
    <script src="TableFilter/tablefilter_all_min.js" language="javascript" type="text/javascript"></script>
    <script src="TableFilter/sortabletable.js" language="javascript" type="text/javascript"></script>
    <script src="TableFilter/tfAdapter.sortabletable.js" language="javascript" type="text/javascript"></script>
    <!-- Include the GoJS library. -->
    <script language="javascript" src="go.js"></script>

    <link rel="stylesheet" href="table.css">
    <link rel="stylesheet" href="form.css">
    <link href="generalBody.css" rel="stylesheet">
    <!--
    <META HTTP-EQUIV="refresh" CONTENT="60">
    -->
</head>
<body onload="loadAndGetExpVis('<?php echo $_GET['expId'] ?>')">
<script language="JavaScript">
    var expIdStr = '<?php echo $_GET['expId'] ?>';
</script>
<?php
include "htmlHeader.php";
?>
<h1>
<?php
    $expId = $_GET['expId'];

    $con=getDBConnection();
    $sqlStr = "select ExpStatus,UserName from Experiment where ExpId = ?";
    $stmt = $con->prepare($sqlStr);
    $stmt->bind_param("s", $expId);
    $stmt->execute();
    $results = $stmt->get_result();
    $row = mysqli_fetch_row($results);
    echo "<span class='" . $row[0] . "'>" . "Experiment:" . $expId  .  "</span>";
    echo "<span style='float:right' class='" . $row[0] . "'>" . $row[0] .  "</span>";

    echo "<br>";
    $cloneStr = sprintf("<a href='newExp.php?expId=%s'>Clone</a>", $expId);
    $resultStr = $cloneStr . "&nbsp;&nbsp;&nbsp;";
    echo $resultStr;
    if($_SESSION['user'] == $row[1] || isAdmin())
        echo getActionStrByStatus($expId, $row[0]);

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
</h1>
<hr>
        <div id="myDiagramDiv" style="height:70%; width:98%; border-style:solid; border-width:5px;">
        </div>
            <table style="margin-top:0px; width:100%; height: 30%" id="expList" cellpadding="0" cellspacing="0">
                <thead>
                <tr>
                    <th>Node Id</th>
                    <th>Node Type</th>
                    <th>Node Name</th>
                    <th>Node Status</th>
                    <th>Message</th>
                    <th>Parameters</th>
                    <th>Download</th>
                </tr>
                </thead>
                <tbody>
                <?php

                $con=getDBConnection();
                $sqlStr = "select NodeId,NodeType,NodeStatus, Message, Parameters,itemId from Node where ExpId= ?";
                $stmt = $con->prepare($sqlStr);
                $stmt->bind_param("s", $expId);
                $stmt->execute();
                $results = $stmt->get_result();
                while($row = mysqli_fetch_row($results)) {
                    echo "<tr class=\"" . $row[2] . "\">";
                    echo "<td>" . $row[0] . "</td>";
                    echo "<td>" . $row[1] . "</td>";
                    echo "<td>" . getItemName($row[5], $row[1]) . "</td>";
                    echo "<td>" . $row[2] . "</td>";
                    echo "<td>" . $row[3] ."</td>";
                    echo "<td>" . $row[4] ."</td>";
                    echo "<td>" . getDownloadFileLink($expId, $row[0], $row[5], $row[1], $row[2])
                        . getOutputFileLink($expId, $row[0], $row[1], $row[2]) ."</td>";
                    echo "</tr>";
                }
                function getItemName($itemId, $itemType) {
                    if($itemType == 'Data') {
                        $sqlStr = "select Name from Data where DataId = ?";
                    }
                    else if($itemType == 'Module') {
                        $sqlStr = "select Name from Module where ModuleId = ?";
                    }
                    $con=getDBConnection();
                    $stmt = $con->prepare($sqlStr);
                    $stmt->bind_param("s", $itemId);
                    $stmt->execute();
                    $results = $stmt->get_result();
                    $row = mysqli_fetch_row($results);
                    return $row[0];
                }
                function getOutputFileLink($expId, $nodeId, $nodeType, $status) {
                    if($nodeType == 'Data') {
                        return '';
                    } else if($status!="New") {
                        $urlStr = "showStdOutput.php?expId=" . $expId . "&nodeId=" .$nodeId;
                        $hrefStr = "javascript: openNewSmallWindows('" . $urlStr . "')";
                        $resultStr = "<a href=\"" . $hrefStr . "\">StdOut</a>&nbsp;";
                        return $resultStr;
                    } else {
                        return "";
                    }
                }
                function getDownloadFileLink($expId, $nodeId, $itemId, $nodeType, $status) {
                    if($status!="Finished") {
                        return '';
                    } else {
                        if($nodeType == 'Data') {
                            $hrefStr = "downloadFile.php?expId=" . $expId . "&nodeId=" .$nodeId . "&portId=1";
                            $resultStr = "<a href=" . $hrefStr . ">Out_1</a>";
                        } else if($nodeType == 'Module') {
                            // get output arguments from DB
                            $con=getDBConnection();
                            $sqlStr = "select OutputArguments from Module where ModuleId= ?";
                            $stmt = $con->prepare($sqlStr);
                            $stmt->bind_param("s", $itemId);
                            $stmt->execute();
                            $results = $stmt->get_result();

                            $row = mysqli_fetch_row($results);
                            $portNum = intval(explode(';', $row[0])[0]);
                            $resultStr = "";
                            for($i =1; $i<=$portNum; $i++) {
                                $hrefStr = "downloadFile.php?expId=" . $expId . "&nodeId=" .$nodeId . "&portId=" . $i;
                                $resultStr .= "<a href=" . $hrefStr . ">Out_" . $i .  "</a>&nbsp;";
                            }
                        }
                        return $resultStr;
                    }
                }

                ?>
                </tbody>
            </table>
<script language="JavaScript">
    function openNewSmallWindows(urlStr) {
        popupwindow(urlStr, "StdOut and StdErr", 600, 400);
    }
    function getXMLHttp() {
        if (window.XMLHttpRequest)
        {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        }
        else
        {// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
        return xmlhttp;
    }
    function loadAndGetExpVis(expId) {
        xmlHttp = getXMLHttp();
        xmlhttp.onreadystatechange = function()
        {
            if (xmlhttp.readyState==4 && xmlhttp.status==200)
            {
                try
                {
                    myDiagram.model = go.Model.fromJson(xmlhttp.responseText);
                    myDiagram.isModelReadOnly = true;
                }
                catch(err) {
                    alert("No or Incorrect experiment visualization file.")
                    var element = document.getElementById("myDiagramDiv")
                    element.parentNode.removeChild(element)
                }
            }
        }
        xmlhttp.open("GET","getExpVisJsonWithStatus.php?expId="+expId,true);
        xmlhttp.send();
    }
    function popupwindow(url, title, w, h) {
        var left = (screen.width/2)-(w/2);
        var top = (screen.height/2)-(h/2);
        return window.open(url, title, 'directories=0,titlebar=0,toolbar=no,location=no,status=no,menubar=0,scrollbars=0, resizable=no, copyhistory=0, width='+w+', height='+h+', top='+top+', left='+left);
    }
</script>


<script language="JavaScript">
    var $ = go.GraphObject.make;  //for conciseness in defining node templates

    myDiagram =
        $(go.Diagram, "myDiagramDiv",  //Diagram refers to its DIV HTML element by id
            {
                initialContentAlignment: go.Spot.Center
            });
    var portSize = 8;
    var nodeMenu = // context menu for each node
        $(go.Adornment, "Vertical",
            $("ContextMenuButton",
                $(go.TextBlock, "ShowStdOutput"),
                { click: function (e, obj) {
                    try {
                        urlStr = obj.part.adornedObject.data.stdOutLink;
                        if(urlStr) {
                            openNewSmallWindows(urlStr);
                        } else {
                            alert("Output Data is not available!")
                        }

                    }
                    catch(err) {
                        alert("Output Data is not available!")
                    }
                } }),
            $("ContextMenuButton",
                $(go.TextBlock, "ShowNodeInfo"),
                { click: function (e, obj) {
                    try {
                        urlStr = obj.part.adornedObject.data.infoLink;
                        if(urlStr) {
                            openNewSmallWindows(urlStr);
                        } else {
                            alert("Node info is not available!")
                        }

                    }
                    catch(err) {
                        alert("Node info is not available!")
                    }
                } })
        );
    var portMenu =  // context menu for each port
        $(go.Adornment, "Vertical",
            $("ContextMenuButton",
                $(go.TextBlock, "Show"),
                { click: function (e, obj) {
                    try {
                        urlStr = obj.part.adornedObject.data.showLink;
                        popupwindow(urlStr, "Show Output", 600, 400);
                    }
                    catch(err) {
                        alert("Output Data is not available!")
                    }
                }}),
            $("ContextMenuButton",
                $(go.TextBlock, "Download"),
                { click: function (e, obj) {
                    try {
                        urlStr = obj.part.adornedObject.data.downloadLink;
                        if(urlStr) {
                            location.href = urlStr
                        } else {
                            alert("Output Data is not available!")
                        }

                    }
                    catch(err) {
                        alert("Output Data is not available!")
                    }
                } }),
            $("ContextMenuButton",
                $(go.TextBlock, "SaveAsData"),
                { click: function (e, obj) {
                    try {
                        urlStr = obj.part.adornedObject.data.saveLink;
                        if(urlStr.substr(-2)=="-1") {
                            alert("Please use original Data");
                        } else {
                            popupwindow(urlStr, "Save Output as Data", 600, 400);
                        }
                    }
                    catch(err) {
                        alert("Output Data is not available!")
                    }
                }})
        );



    myDiagram.nodeTemplate =
        $(go.Node, "Table",
            { locationObjectName: "BODY",
                locationSpot: go.Spot.Center,
                selectionObjectName: "BODY",
                contextMenu: nodeMenu
            },
            new go.Binding("location", "loc", go.Point.parse).makeTwoWay(go.Point.stringify),

            // the body
            $(go.Panel, "Auto",
                { row: 1, column: 1, name: "BODY",
                    stretch: go.GraphObject.Fill },
                $(go.Shape, "Rectangle",
                    { fill: "#BFBFBF",
                        minSize: new go.Size(156, 56) },
                    new go.Binding("fill", "nodeStatus", function(v) {
                        if(v=="Failed" || v == "Aborted") {
                            return "#FF0000";
                        }
                        else if(v== "Running") {
                            return "#00CCFF";
                        }
                        else if(v=="Finished") {
                            return "#00FF00";
                        } else if(v=="Pausing") {
                            return "#FAFAD2";
                        } else {
                            return "#BFBFBF";
                        }
                    })),
                $(go.TextBlock,
                    { margin: 10, textAlign: "center" },
                    new go.Binding("text", "itemName"))
            ),  // end Auto Panel body

            // the Panel holding the top port elements, which are themselves Panels,
            // created for each item in the itemArray, bound to data.topArray
            $(go.Panel, "Horizontal",
                { row: 0, column: 1,
                    itemTemplate:
                        $(go.Panel, "Vertical",
                            { _side: "top",
                                fromSpot: go.Spot.Top, toSpot: go.Spot.Top,
                                fromLinkable: false, toLinkable: true, toMaxLinks: 1, cursor: "pointer"
                            },
                            new go.Binding("portId", "portId"),
                            $(go.TextBlock,
                                {textAlign: "center",
                                    text: "In",
                                    margin: 2},
                                new go.Binding("text", "portTypeName")
                            ),

                            $(go.Shape, "Rectangle",
                                { stroke: null,
                                    fill : "Blue",
                                    desiredSize: new go.Size(portSize, portSize),
                                    margin: new go.Margin(0, 1)
                                }
                            )
                        )  // end itemTemplate
                },
                new go.Binding("itemArray", "topArray")
            ),  // end Horizontal Panel

            // the Panel holding the bottom port elements, which are themselves Panels,
            // created for each item in the itemArray, bound to data.bottomArray
            $(go.Panel, "Horizontal",
                { row: 2, column: 1,
                    itemTemplate:
                        $(go.Panel, "Vertical",
                            { _side: "bottom",
                                fromSpot: go.Spot.Bottom, toSpot: go.Spot.Bottom,
                                fromLinkable: true, toLinkable: false, cursor: "pointer",
                                contextMenu: portMenu
                            },
                            new go.Binding("portId", "portId"),
                            $(go.Shape, "Rectangle",
                                { stroke: null,
                                    desiredSize: new go.Size(portSize, portSize),
                                    fill : "Blue",
                                    margin: new go.Margin(0, 1) }
                            ),

                            $(go.TextBlock,
                                {textAlign: "center",
                                    text: "Out",
                                    margin: 2},
                                new go.Binding("text", "portTypeName")
                            )
                        )  // end itemTemplate
                },
                new go.Binding("itemArray", "bottomArray")
            )  // end Horizontal Panel
        );  // end Node

    myDiagram.model =
        $(go.GraphLinksModel,
            {
                linkFromPortIdProperty: "fromPort",  // required information:
                linkToPortIdProperty: "toPort",      // identifies data property names
                nodeDataArray:
                    [ // a JavaScript Array of JavaScript objects, one per node
                        //{"key":"unit One", "nodeStatus" : "Running",  "loc":"101 204", "topArray":[ {"portId":"top0", "portTypeName": "CSV"} ], "bottomArray":[ {"portId":"bottom0"} ] },
                        //{"key":"unit Two", "nodeStatus" : "Failed","loc":"320 152", "topArray":[ {"portId":"top0"} ], "bottomArray":[ {"portId":"bottom0"},{"portId":"bottom1"},{"portId":"bottom2"} ] },
                        //{"key":"unit Three", "nodeStatus" : "New","loc":"384 319", "topArray":[ {"portId":"top0"} ], "bottomArray":[ {"portId":"bottom0"} ]},
                        //{"key":"unit Four", "nodeStatus" : "Finished","loc":"138 351", "topArray":[ {"portId":"top0"} ], "bottomArray":[ {"portId":"bottom0","portTypeName": "CSV"} ] }
                    ],
                linkDataArray: [

                ]});
    myDiagram.undoManager.isEnabled = true;
</script>
<script language="javascript" type="text/javascript">
    var props = {
        sort: true,
        filters_row_index:1,
        paging: true,
        paging_length: 10,
        results_per_page: ['# rows per page',[10,20,50]],
        col_0: "none",
        col_1: "select",
        col_3: "select",
        col_4: "none",
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
