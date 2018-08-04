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
      echo "<h3>Weekly Sales Chart From  ". date('l jS \of F Y') ." 10:00:00 AM to ". date('l jS \of F Y') . " 11:00:00PM</h3>\n";
   }

   $sql =<<<EOF
           
select * from dailystatementtset_v
where datenew between now()::date-7 and now()::date 
and head = 'Dine In';
           
EOF;
?>


<html>
  <head>
    <!--Load the AJAX API-->
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">

      // Load the Visualization API and the corechart package.
      google.charts.load('current', {'packages':['corechart']});

      // Set a callback to run when the Google Visualization API is loaded.
      google.charts.setOnLoadCallback(drawChart);

      // Callback that creates and populates a data table,
      // instantiates the pie chart, passes in the data and
      // draws it.
      function drawChart() {

        // Create the data table.
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Topping');
        data.addColumn('number', 'Slices');
        data.addRows([
            <?php
            $ret = pg_query($db, $sql);
            $rows2 = 0;
             if(!$ret) {
      echo pg_last_error($db);
      exit;
   } 
   while($row = pg_fetch_row($ret)) {
       $rows2 = $rows2 + 1;
     if ($rows2 = pg_num_rows($ret)){
       echo "[".$row[2].",".$row[5]."]";
       }
  
       echo "[".$row[2].",".$row[5]."],";
   }
    echo "Support by <a href='http://www.rawntech.com'>rawntech</a>\n";
   pg_close($db);
                        ?>
        ]);

        // Set chart options
        var options = {'title':'How Much Pizza I Ate Last Night',
                       'width':400,
                       'height':300};

        // Instantiate and draw our chart, passing in some options.
        var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
        chart.draw(data, options);
      }
    </script>
  </head>

  <body>
    <!--Div that will hold the pie chart-->
    <div id="chart_div"></div>
   <?php
   $db1 = pg_connect($conn_string);

   //$db = pg_connect( "$host $port $dbname $credentials"  );
   
   if(!$db) {
      echo "Error : Unable to open database\n";
   } else {
      echo "<h3>Weekly Sales Chart From  ". date('l jS \of F Y') ." 10:00:00 AM to ". date('l jS \of F Y') . " 11:00:00PM</h3>\n";
   }

   $sql1 =<<<EOF
           
select * from dailystatementtset_v
where datenew between now()::date-7 and now()::date 
and head = 'Dine In';
           
EOF;
            $ret1 = pg_query($db1, $sql);
            $rows21 = 0;
             if(!$ret) {
      echo pg_last_error($db1);
      exit;
   } 
   while($row1 = pg_fetch_row($ret1)) {
       $rows21 = $rows21 + 1;
 
       echo "[".$row1[2].",".$row1[5]."],<br>";
   }
    echo "Support by <a href='http://www.rawntech.com'>rawntech</a>\n";
   pg_close($db1);
                        ?>
  </body>
</html>

