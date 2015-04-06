<?php
include_once ('../../classes/ErrorControl.php');
include_once ('../../../smarty/libs/Smarty.class.php');
include_once ('../../classes/Grund/DatabaseControl.php');
global $sqlrader;
$sqlrader = array();

$dc = new DatabaseControl();

$line = $dc->runSql("SELECT activityname, a.description, activityslotid, notifytime FROM pl_activity a, pl_activityslot aslot WHERE a.notifytime > 0 AND aslot.startdate > NOW() AND aslot.activityid = a.activityid AND aslot.isnotified != 1;");

while ( $row = $dc->getRow($line) )
{
//   print_r($row);
//   echo "<br/>";
   $line2 = $dc->runSql("SELECT activityslotid, startdate, stopdate, description FROM pl_activityslot WHERE activityslotid=$row[2] AND DATE_ADD(NOW(), INTERVAL ".round($row[3]+7)." HOUR) > startdate;");   
   while ( $row2 = $dc->getRow($line2) )
   {
//      print_r($row2);
//      echo "<br/>";
      $line3 = $dc->runSql("SELECT name, email FROM pl_activitytime atime, pl_user u WHERE activityslotid=$row2[0] AND u.userid = atime.userid;");   
      while ( $row3 = $dc->getRow($line3) )
      {
//         print_r($row2);
         echo "To: ".$row3[0]." &lt;".$row3[1]."&gt;<br/>";
         echo "Subject: Påminnelse från planlite.se om aktiviteten ".$row[0]."<br/>";
		 echo "Aktiviteten $row[0] påbörjas $row2[1] och håller på fram till $row2[2]</br>";
		 echo "Beskrivning av aktivitet: </br>".$row[1];
		 $dc->sendMail("Påminnelse från planlite.se om aktiviteten ".$row[0], 
		               "Aktiviteten $row[0] påbörjas $row2[1] och håller på fram till $row2[2]
Beskrivning av aktivitet:
".$row[1], "info@planlite.se", "Planlite.se", $row3[1], $row[0]); 
      }
	  $line4 = $dc->runSql("UPDATE pl_activityslot SET isnotified=1 WHERE activityslotid=$row2[0]");   
   }
}

?>