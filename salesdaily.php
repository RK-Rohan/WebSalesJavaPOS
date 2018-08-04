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
      echo "<h3>Daily Sales Report From  ". date('l jS \of F Y') ." 10:00:00 AM to ". date('l jS \of F Y') . " 11:00:00PM</h3>\n";
   }

   $sql =<<<EOF
SELECT RECEIPTS.DATENEW AS DATENEW, TICKETS.TICKETID AS TICKETID,    
  (SELECT SUM(TOTAL) FROM PAYMENTS WHERE PAYMENT='cash' and RECEIPT=RECEIPTS.ID) AS TOTALCASH,    
  (SELECT SUM(TOTAL)  FROM PAYMENTS WHERE PAYMENT='magcard' and RECEIPT=RECEIPTS.ID) AS TOTALCARD,    
  (SELECT SUM(TOTAL)  FROM PAYMENTS WHERE PAYMENT='free' and RECEIPT=RECEIPTS.ID) AS TOTALFREE,     
  (SELECT SUM(TOTAL)  FROM PAYMENTS WHERE PAYMENT='debt' and RECEIPT=RECEIPTS.ID) AS TOTALDEBT,     
  (SELECT SUM(PRICE)  FROM TICKETLINES WHERE TICKET=TICKETS.ID AND PRODUCT IS NULL) AS DISCOUNT,     
  (SELECT SUM(TOTAL) FROM PAYMENTS WHERE RECEIPT=RECEIPTS.ID) AS TOTALAMOUNT    
    FROM RECEIPTS, TICKETS       
                WHERE TICKETS.ID=RECEIPTS.ID  
                    AND RECEIPTS.DATENEW BETWEEN (to_char((now()::date)::date,'yyyy-mm-dd')||' 10:00:00')::timestamp and (to_char((now()::date)::date,'yyyy-mm-dd')||' 23:00:00')::timestamp
                                and TICKETS.ID=RECEIPTS.ID 
                  AND  (TICKETS.CUSTOMER is null OR TICKETS.CUSTOMER IN (SELECT ID  FROM CUSTOMERS 
                 WHERE taxcategory =(SELECT ID FROM taxcustcategories WHERE NAME ='Customers')))  
                ORDER BY TICKETS.TICKETID ;
EOF;
?>
<table border="1" cellspacing="0">
        <tr>
            <td> Receipt </td><td> Time </td><td> Cash </td><td> Card </td><td> Free </td><td> Debt </td><td> Discount </td><td> Total Amount </td>
        </tr> 
<?php
   $ret = pg_query($db, $sql);
   $rows2 = 0;
$rows3 = 0;
$rows4 = 0;
$rows5 = 0;
$rows6 = 0;
$rows7 = 0;
   
   if(!$ret) {
      echo pg_last_error($db);
      exit;
   } 
   while($row = pg_fetch_row($ret)) {
?>
 <tr>
            <td><?php echo $row[1] ?> </td><td><?php echo $row[0] ?> </td>
            <td align="right"> <?php echo number_format($row[2],2,'.',',') ?>  </td><td align="right"> <?php echo number_format($row[3],2,'.',',') ?>  </td><td align="right"><?php echo number_format($row[4],2,'.',',') ?> </td><td align="right"> <?php echo number_format($row[5],2,'.',',') ?>  </td><td align="right"> <?php echo number_format($row[6],2,'.',',') ?> </td>
            <td align="right"> <?php echo number_format($row[7],2,'.',',') ?> </td>
 </tr> 
<?php
$rows2 = $rows2 + $row[2];
$rows3 = $rows3 + $row[3];
$rows4 = $rows4 + $row[4];
$rows5 = $rows5 + $row[5];
$rows6 = $rows6 + $row[6];
$rows7 = $rows7 + $row[7];
   }
   ?>
  <tr>
            <td> Total  </td> <td> Count <?php echo pg_num_rows($ret); ?> </td><td align="right"> <?php echo number_format($rows2,2,'.',',') ; ?> </td><td align="right"> <?php echo number_format($rows3,2,'.',','); ?> </td><td align="right"> <?php echo number_format($rows4,2,'.',','); ?> </td><td align="right"> <?php echo number_format($rows5,2,'.',','); ?> </td><td align="right"> <?php echo number_format($rows6,2,'.',','); ?> </td><td align="right"> <?php echo number_format($rows7,2,'.',','); ?> </td>
        </tr> 
 <?php
   echo "<a href='http://www.rawntech.com'>Support by rawntech</a>\n";
   pg_close($db);
?>

</table>
    </body>
</html>