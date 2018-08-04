<html>
    <head>
        
    </head>
    <body>
        <style>
body {
    color: blue;
}

h3 {
    color: green;
}
</style>
<?php
   $host        = "host = localhost";
   $port        = "port = 5432";
   $dbname      = "dbname = chunkmain";
   $credentials = "user = postgres password=syspass";
   $conn_string = ( "$host $port $dbname $credentials" );
   $db = pg_connect($conn_string);

   //$db = pg_connect( "$host $port $dbname $credentials"  );
   
   if(!$db) {
      echo "Error : Unable to open database\n";
   } else {
      echo "<h3>Daily Statement For  ". date('l jS \of F Y') . "</h3>\n";
   }

   $sql =<<<EOF
SELECT  HEAD,DATATYPE,SALESAMOUNT,CLOSING,OPENING FROM DAILYSTATEMENTTSET_V
where datenew=now()::date
           AND DATATYPE = 1
EOF;
      $sql2 =<<<EOF
SELECT  HEAD,DATATYPE,SALESAMOUNT,CLOSING,OPENING FROM DAILYSTATEMENTTSET_V
where datenew=now()::date
           AND DATATYPE = 2
EOF;
?>
<table border="1" cellspacing="0">
        <tr>
            <td> <b>Head</b> </td><td><b> Amount</b> </td>
        </tr> 
        <tr>
            <td><b> Income </b></td><td>  </td>
        </tr>
 
<?php
   $ret = pg_query($db, $sql);
   $rows2 = 0;
   $rows3 = 0;
   $rows4 = 0;
 
   if(!$ret) {
      echo pg_last_error($db);
      exit;
   } 
   while($row = pg_fetch_row($ret)) {
?>
 
 <tr>
            <td><?php echo $row[0] ?> </td><td align="right"><?php echo number_format($row[2],2,'.',',') ?> </td>
 </tr> 
<?php
$rows2 = $rows2 + $row[2];
$rows3 = $row[3];
$rows4 = $row[4];
}
   ?>
  <tr>
      <td><b> Total Count <?php echo pg_num_rows($ret); ?></b> </td><td align="right"><b> <?php echo number_format($rows2,2,'.',',') ; ?></b> </td>
        </tr> 

        <tr>
            <td><b> Expenses </b></td><td>  </td>
        </tr>
        <?php
   $ret2 = pg_query($db, $sql2);
   $rows22 = 0;
 
   if(!$ret2) {
      echo pg_last_error($db);
      exit;
   } 
   while($row2 = pg_fetch_row($ret2)) {
?>
        <tr>
        <td><?php echo $row2[0] ?> </td><td align="right"><?php echo number_format($row2[2],2,'.',',') ?> </td>
        </tr>
        <?php
$rows22 = $rows22 + $row2[2];
}
?>
<tr>
    <td> <b>Total Count <?php echo pg_num_rows($ret2); ?> </b></td><td align="right"><b> <?php echo number_format($rows22,2,'.',',') ; ?> </b></td>
        </tr> 
        <tr>
            <td> <b>Opening Balance </b></td><td align="right"><b> <?php echo number_format($rows4,2,'.',',') ; ?> </b></td>
        </tr>
         <tr>
             <td> <b>Closing Balance</b> </td><td align="right"> <b><?php echo number_format($rows3,2,'.',',') ; ?> </b></td>
        </tr> 
        <?php
   echo "<a href='http://www.rawntech.com'>Support by rawntech</a>\n";
   pg_close($db);
?>

</table>
    </body>
</html>