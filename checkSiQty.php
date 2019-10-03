<!DOCTYPE html>
<html lang="en-gb">
<head>
	<?php require_once 'includes/baseHeader.html'; ?>
</head>

<body>

    
<?php
require_once ("db.php");
require_once ("generalUtils.php");
    
// Check Service Item quantity check
global $dbconn,  $dbErrorMsg;

    $qry = 'SELECT boats.name as Boat, CONCAT(engineTemplates.make, " ", engineTemplates.model) as Engine, serviceItemNames.name as Part, siTObeMap.qty as Qty ';
    $qry .= 'FROM boats, engines, serviceItems, serviceItemNames, siTObeMap, engineTemplates ';
    $qry .= 'WHERE engines.id = siTObeMap.eId ';
    $qry .= 'AND boats.id = engines.boatId ';
    $qry .= 'AND engineTemplates.id = engines.engineTemplateId ';
    $qry .= 'AND serviceItems.id = siTObeMap.siId ';
    $qry .= 'AND serviceItemNames.id = serviceItems.siNameId ';
    $qry .= 'ORDER BY boats.name ';

    $result = dbQuery($dbconn, $qry);
    if (!$result) {
        echo $dbErrorMsg . "<br>";
    } else {
        echo "<b><table style='padding-left:40px'><tr><td width='15%'>Boat</td><td width='15%'>Engine</td><td width='15%'>Part</td><td width='15%'>Qty</td></tr></b>";
    }
	
    while ($row = mysql_fetch_assoc($result)) {
        echo "<tr><td>" . urldecode($row['Boat']) . "</td><td>" .  urldecode($row['Engine']) . "</td><td>" . urldecode($row['Part']) . "</td><td>" . $row['Qty'] . "</td></tr>";
    }
    echo "</table>";
?>
</body>
</html>