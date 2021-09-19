<?php include("login.php"); ?>
<html>
<head>
    <title>Tickets</title>
    <style>
        * {
            font-size: 11px;
            font-family: arial;
        }
    </style>
    <script>

        function reserveSeats() {

            var selectedList = getSelectedList('Reserve Seats');

            if (selectedList) {
                if (confirm('Do you want to reserve 
                               selected seat/s ' 
                             + selectedList + '?')) {
                    document.forms[0].oldStatusCode.value=0;
                    document.forms[0].newStatusCode.value=1;
                    document.forms[0].action='bookseats.php';
                    document.forms[0].submit();
                } else {
                    clearSelection();
                }
            }
        }


        function cancelSeats() {

            var selectedList = getSelectedList('Cancel Reservation');

            if (selectedList) {
                if (confirm('Do you want to cancel reserved seat/s ' + selectedList + '?')) {
                    document.forms[0].oldStatusCode.value=1;
                    document.forms[0].newStatusCode.value=0;
                    document.forms[0].action='bookseats.php';
                    document.forms[0].submit();
                } else {
                    clearSelection();
                }
            }
        }


        function confirmSeats() {

            var selectedList = getSelectedList('Confirm Reservation');

            if (selectedList) {
                if (confirm('Do you want to confirm reserved seat/s ' + selectedList + '?')) {
                    document.forms[0].oldStatusCode.value=1;
                    document.forms[0].newStatusCode.value=2;
                    document.forms[0].action='bookseats.php';
                    document.forms[0].submit();
                } else {
                    clearSelection();
                }
            }
        }


        function getSelectedList(actionSelected) {

            // get selected list
            var obj = document.forms[0].elements;
            var selectedList = '';
            for (var i = 0; i < obj.length; i++) {
                if (obj[i].checked && obj[i].name == 'seats[]') {
                    selectedList += obj[i].value + ', ';
                }
            }

            // no selection error
            if (selectedList == '') {
                alert('Please select a seat before clicking ' + actionSelected);
                return false;
            } else {
                return selectedList;
            }

        }

        function clearSelection() {
            var obj = document.forms[0].elements;
            for (var i = 0; i < obj.length; i++) {
                if (obj[i].checked) {
                    obj[i].checked = false;
                }
            }
        }


        function refreshView() {
            clearSelection();
            document.forms[0].action='<?php echo $_SERVER['PHP_SELF']; ?>';
            document.forms[0].submit();
        }

    </script>
</head>
<body>
<table>
<tr><td width="100%" align="center">
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">

<input type="hidden" name="oldStatusCode" value=""/>
<input type="hidden" name="newStatusCode" value=""/>

<table width='100%' border='0'>
    <tr><td align='center'>
        <input type='button' value='Refresh View' onclick='refreshView();'/>
    </td></tr>
</table>
</td></tr>
<tr><td width="100%" align="center">
<table width='100%' border='0'>
    <tr><td align='center'>
        <input type='button' value='Reserve Seats' onclick='reserveSeats()'/>
        &nbsp;<input type='button' value='Confirm Reservation' onclick='confirmSeats()'/>
        &nbsp;<input type='button' value='Cancel Reservation' onclick='cancelSeats()'/>
    </td></tr>
</table>
</td></tr>
<tr><td width="100%" align="center">
<table width='100%' border='0'>
    <tr><td align='center'>
        <input type='button' value='Clear Selection' onclick='clearSelection()'/></td>
    </tr>
</table>
</td></tr>
<tr><td width="100%" align="center">
<?php
/*
 * Created on Mar 17, 2007
 * Author: dayg
 */

$linkID = @ mysql_connect("localhost", "tickets", "tickets") or die("Could not connect to MySQL server");
@ mysql_select_db("tickets") or die("Could not select database");
/* Create and execute query. */
$query = "SELECT * from seats order by rowId, columnId desc";
$result = mysql_query($query);
$prevRowId = null;
$seatColor = null;
$tableRow = false;
//echo $result;
echo "<table width='100%' border='0' cellpadding='3' cellspacing='3'>";
while (list($rowId, $columnId, $status, $updatedby) = mysql_fetch_row($result))
{
    if ($prevRowId != $rowId) {
        if ($rowId != 'A') {
            echo "</tr></table></td>";
            echo "\n</tr>";
        }
        $prevRowId = $rowId;
        echo "\n<tr><td align='center'><table border='1' cellpadding='8' cellspacing='8'><tr>";
    } else {
        $tableRow = false;
    }
    if ($status == 0) {
        $seatColor = "lightgreen";
    } else if ($status == 1 && $updatedby == 'user1') {
        $seatColor = "FFCC99";
    } else if ($status == 1 && $updatedby == 'user2') {
        $seatColor = "FFCCFF";
    } else if ($status == 2 && $updatedby == 'user1') {
        $seatColor = "FF9999";
    } else if ($status == 2 && $updatedby == 'user2') {
        $seatColor = "CC66FF";
    } else {
        $seatColor = "red";
    }

    echo "\n<td bgcolor='$seatColor' align='center'>";
    echo "$rowId$columnId";
    if ($status == 0 || ($status == 1 && $updatedby == $_SERVER['PHP_AUTH_USER'])) {
        echo "<input type='checkbox' name='seats[]' value='$rowId$columnId'></checkbox>";
    }
    echo "</td>";
        if (($rowId == 'A' && $columnId == 7) 
            || ($rowId == 'B' && $columnId == 9) 
            || ($rowId == 'C' && $columnId == 9) 
            || ($rowId == 'D' && $columnId == 10) 
            || ($rowId == 'E' && $columnId == 8) 
            || ($rowId == 'F' && $columnId == 5) 
            || ($rowId == 'G' && $columnId == 13) 
            || ($rowId == 'H' && $columnId == 14) 
            || ($rowId == 'I' && $columnId == 14) 
            || ($rowId == 'J' && $columnId == 12) 
            || ($rowId == 'K' && $columnId == 14) 
            || ($rowId == 'L' && $columnId == 13) 
            || ($rowId == 'M' && $columnId == 9)) {
            // This fragment is for adding a blank cell which represent the "center aisle"
            echo "<td>&nbsp;</td>";
        }
}