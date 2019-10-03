<!DOCTYPE html>
<html lang="en-gb">
<head>
	<?php require_once 'includes/baseHeader.html'; ?>
    
    <link rel='stylesheet' href='fullcalendar/fullcalendar.css' />
    <script src='fullcalendar/moment.min.js'></script>
    <script src='fullcalendar/fullcalendar.js'></script>
</head>

<body>
	<?php require_once 'includes/basePage.html'; ?>

    
    <script>
        $('#pageContainer').html("<div id='calendar'></div>");
        $('#calendar').fullCalendar({
            eventClick: (function(calEvent, jsEvent, view) {
                showJobsheet(calEvent.id);
            }),
            eventAfterAllRender: (function(){
                $('.fc-title').each(function() {
                    $(this).html( $("<div/>").html($(this).html()).text());
                });              
            })
        });
        
        $.post("dlmFunctionDispatch.php?f=getSchedule", function(data) {
         if (data.status == "OK") {
            for (var i = 0; i < data.resultcount; i++) {
                var id = data.results[i].id;
                var boat = "&#x"+data.results[i].emoji+"; <b>"+safeDecode(data.results[i].name+"</b>");
                var date = data.results[i].date;
                var color;
                switch(data.results[i].stage) {  // Draft, Scheduled, Underway, Completed, Closed
                    case "1":
                        color = "#FFFFFF";      // Draft - SHOULD NEVER EXIST
                        break;
                    case "2":
                        color = "#FFFF33";      // Scheduled
                        break;
                    case "3":
                        color = "#FFB266";      // Underway
                        break;
                    case "4": 
                        color = "#CCFF99";      // Complete
                        break;
                    case "5":
                        color = "#00FF00";      // Closed
                        break;
                   }
                // Now adjust for due date
                if ((data.results[i].stage < 4) && (Date.parse(date) <= Date.parse(mysqlDate()))) {
                    color = "#FF9933";
                }

                $('#calendar').fullCalendar( 'renderEvent', {id:id, title:boat, start:date, color:color, textColor:"darkslategray"}, true);
            }

            $('.fc-title').each(function() {
                $(this).html( $("<div/>").html($(this).html()).text());
            });
         }
        }, "json");
        
    </script>

</body>
</html>