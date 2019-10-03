<!DOCTYPE html>
<html lang="en-gb">
<head>
	<?php require_once 'includes/baseHeader.html'; ?>
    <title>Print Test</title>
    <style type="text/css" media="print">
        @media print
        {
            @page
            {
                margin-top: 10;
                margin-left: 10mm;
                margin-right: 10mm;
                margin-bottom: 10;
            }

            body
            {
                height: 245mm;
                width: 160mm;
            }
        }
        div {
            margin:0;
            padding:0;
/*            border-bottom: solid 1px black; */
        }
    </style>

</head>
<body style='font-family:verdana;font-size:12pt'>
    <div id='A4Page' style='display:block;margin:0 auto;padding-top:0.75cm;padding-left:1cm;height: 29.7cm; width: 21cm;'>
        <div id='top' style='display:block; height:38mm; width:100%; padding:10px;'>
            <div id='topleft' style='float:left;width:45%'>
                <img src='images/DrivelineLogoAndName.jpg'>
            </div>
            <div id='topright' style='float:right;width:30%; text-align:right;padding-right:20px'>
                <div id='toprighttop'>
                    Driveline Marine Ltd,<br>
                    Scours Lane,<br>
                    Reading,<br>
                    RG30 6AY
                </div>
                <div id='toprighttop'>
                    <h4 id='boatName'>boatname</h4>
                    Doc. #<span id='jobsheetnumber'>refno</span><br>
                    <span id='datestamp'>dd-mmm-yyyy</span>
                </div>
            </div> <!-- End of topright -->
            <br class='clear' />
        </div> <!-- End of top -->
        <div id='caddrdiv' style='display:block;height:30mm;padding:10px;'>
            <div id='customerdiv' style='float:left;width:40%'>
                Jon Barrett<br>
                12 Skerritt Way<br>
                Purley-on-Thames,<br>
                Berks, RG8 8DD
            </div>
            <div id='printbtns' style='float:right;width:40%; text-align:right;'>
                <button id='printbutton' class='btn' style='font-weight:bold;' onclick='printPage();'>Print</button>
                <button id='cancelbutton' class='btn' style='font-weight:bold;' onclick='window.close();'>Cancel</button>
            </div>
        </div>

        <div id='desc' style='font-size:10pt'>
            <div style="margin-top:15px;">Description:</div>
            <div id="descriptionDiv" style="min-height:80px;border:thin solid #AAAAAA;padding:5px;">
                Do a load of work and mend the things that are broken.<br>
                Then speed up the things that are slow and dry the things that are wet.
            </div>
        </div>

        <div id='lab' style='font-size:10pt'>
            <div style="margin-top:15px;">Labour:</div>
            <div id="labourDiv" style="min-height:80px;border:thin solid #AAAAAA;padding:5px;">
                <table id="labourTable" style="width:100%;font-size:10pt">
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
        <div id='parts' style='font-size:10pt'>
            <div style="margin-top:15px;">Parts:</div>
            <div id="PartsDiv" style="min-height:80px;border:thin solid #AAAAAA;padding:5px;">
                <table id='partsTable' style='width:100%;font-size:10pt'>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
        <div id='notes' style='font-size:10pt'>
            <div style="margin-top:15px;">Notes:</div>
            <div id="NotesDiv" style="min-height:80px;border:thin solid #AAAAAA;padding:5px;" contenteditable="true">

            </div>
        </div>

           <br class='clear' />

        <div style='display:block'>
            <div style='display:block; width:10px; float:left;'>
            &nbsp;
            </div>
            <div id="invoiceTotals" style="font-size:10pt; width:350px;float:right;;padding-right:10px">
                <table style="width:350px;">
                <tr><th></th><th>Net</th><th>VAT</th><th>Total</th></tr>
                <tr><td>Labour</td><td id='labourNet' style='text-align:right'>&pound;</td><td id='labourVat' style='text-align:right'>&pound;</td><td id='labourGross' style='text-align:right'>&pound;</td></tr>
                <tr><td>Parts</td><td id='partsNet' style='text-align:right'>&pound;</td><td id='partsVat' style='text-align:right'>&pound;</td><td id='partsGross' style='text-align:right'>&pound;</td></tr>
                <tr style='font-size:1.2em;'><td style="font-weight:bold;">Total</td><td id='totalNet' style='text-align:right'>&pound;</td><td id='totalVat' style='text-align:right'>&pound;</td><td id='totalGross' style='text-align:right;font-weight:bold;'>&pound;</td></tr>
                </table>
            </div>
            <br class='clear' />
        </div>
        <div id='hiddenLabour' style='display:none'>JS Labour Table</div>
        <div id='hiddenParts' style='display:none'>JS Parts Table</div>
        
    </div>  <!-- End of A4Page -->


        <div id="invoiceFooter" style='font-size:10pt;text-align:center;padding-bottom:40px;'>  <!-- Company Reg No: 01832605 -->
            <p>Driveline Marine Ltd. VAT Reg. No. 641 8432 43</p>
            <p>Lloyds Bank  Sort Code: 30-96-96   Account:  015730036</p>
            <br>
            <p style='font-weight:bold;'>Payment Terms 7 Days.   </p>
        </div>

<div id='foldhere' style='position:absolute;top:10.2cm;left:1mm'>-</div>
<div id="loadingDiv" />

<script type="text/javascript">
$(document).ready(function(){
    var id = getQueryString("id");
    var totalLabourCost = 0;
    var totalPartsCost = 0;
    var unpricedLabour = "";
    var unpricedParts = "";
    document.title = 'DLM Jobsheet';
    showLoading();
    $('#invoiceFooter').hide();
    $('#datestamp').html(fullDateStr(new Date()));
    $.post("dlmFunctionDispatch.php?f=getJobsheet&jsId="+id, function(jsdata) {
        if ((id > 0)&&(jsdata.resultcount < 1)) {
            hideLoading();
            myAlert("Not Found", "Jobsheet #" + id + " not found!");
        } else {
            $('#jobsheetnumber').html(id);        
            $.post("dlmFunctionDispatch.php?f=getCustomerDetails&cid="+jsdata.custId, function(cddata) {
                var custDetails = "";
                custDetails += safeDecode(cddata.title) + " ";
                custDetails += safeDecode(cddata.firstname) + " ";
                custDetails += safeDecode(cddata.lastname) + "<br>";
                custDetails += ((safeDecode(cddata.address1))? safeDecode(cddata.address1) + ",<br>" : "");
                custDetails += ((safeDecode(cddata.address2))? safeDecode(cddata.address2) + ",<br>" : "");
                custDetails += ((safeDecode(cddata.address3))? safeDecode(cddata.address3) + ",<br>" : "");
                custDetails += ((safeDecode(cddata.county))? safeDecode(cddata.county) + " " : "");
                custDetails += ((safeDecode(cddata.postcode))? safeDecode(cddata.postcode) + "<br>" : "<br>");
                $('#customerdiv').html(custDetails);
            }, "json");
            $.post("dlmFunctionDispatch.php?f=getBoatDetails&bid="+jsdata.boatId, function(boatdata) {
                if (boatdata.resultcount == 0) {
                    $('#boatName').html("Engine Only<br>");
                } else {
                    $('#boatName').html(safeDecode(boatdata.name) + "<br>");
                }               
            }, "json");
            $('#descriptionDiv').html(nTobr(safeDecode(jsdata.description)));
            if (jsdata.notes == "") { 
                $('#notes').html("");
            } else {
                $('#NotesDiv').html(nTobr(safeDecode(jsdata.notes)));
            }
            
            /*
             Now we copy the Labour table into a hidden div so that we can parse the data
             and produce a clean version suitable for the Invoice. 
            */
            var storedJobs = safeDecode(jsdata.labour);
            if (storedJobs.substring(0,6) == '{"jobs') {  // Is this stored as JSON
                $('#hiddenLabour').html(jobsJson2Table(storedJobs));
                if ($('#JSLabour tr').length) {
                    for (var i=0; i<$('#JSLabour tr').length; i++) {
//                        var col = $("#JSLabour > tbody > tr:first > td").length-1;
//                        totalLabourCost += parseFloat($('#JSLabour tr:eq('+i+') td:eq(' + col + ')').html().substring(1));
                        var jobtotal = $('#JSLabour tr:eq('+i+') td:last').html().substring(1);
                        if ($.isNumeric(jobtotal)) {  // Only total up actual costs i.e. ignore blanks and errors
                            totalLabourCost += parseFloat(jobtotal);
                        }
                        $('#JSLabour tr:eq('+i+') td:eq(0)').html($('#JSLabour tr:eq('+i+') td:eq(0) input').attr('value'));  // Remove <input> aspect of column
                    }
                }
                $('#labourDiv').html($('#hiddenLabour').html()); // Now copy reworked Labour table to the Invoice
            } else {
                myAlert("Old-style Jobsheet", "This Jobsheet was stored in an out of date format. <br>Please return to the Jobsheet and 'Save' it in order to update the format before printing the Invoice");
            }

            /* Now do the same with the Parts table */
            var storedParts = safeDecode(jsdata.parts);
            if (storedParts.substring(0,7) == '{"parts') {
                $('#hiddenParts').html(partsJson2Table(storedParts));
                if ($('#JSserviceItems tr').length) {
                    for (var i=0; i<$('#JSserviceItems tr').length; i++) {
                        var parttotal = $('#JSserviceItems tr:eq('+i+') td:last').html().substring(1);
                        if ($.isNumeric(parttotal)) {  // Only total up actual costs i.e. ignore blanks and errors
                            totalPartsCost += parseFloat(parttotal);
                        }
                        $('#JSserviceItems tr:eq('+i+') td:eq(0)').html($('#JSserviceItems tr:eq('+i+') td:eq(0) input').attr('value'));
                    }
                }
                $('#PartsDiv').html($('#hiddenParts').html());
            } else {
                myAlert("Old-style Jobsheet", "This Jobsheet was stored in an out of date format. <br>Please return to the Jobsheet and 'Save' it in order to update the format before printing the Invoice");                
            }
            
            if (unpricedLabour.length + unpricedParts.length > 0) {
                var msg = "";
                if (unpricedLabour.length > 0) { msg += "Unpriced Labour: " + unpricedLabour; }
                if (unpricedParts.length > 0) { msg += "Unpriced Parts: " + unpricedParts; }
                myAlert("Missing Price Information", msg);
            }
            
            $('#labourNet').html("&pound;"+numberStringToCurrencyString(totalLabourCost.toFixed(2)));
            $('#labourVat').html("&pound;"+(totalLabourCost*0.2).toFixed(2));
            $('#labourGross').html("&pound;"+(totalLabourCost*1.2).toFixed(2));
            $('#partsNet').html("&pound;"+numberStringToCurrencyString(totalPartsCost.toFixed(2)));
            $('#partsVat').html("&pound;"+(totalPartsCost*0.2).toFixed(2));
            $('#partsGross').html("&pound;"+(totalPartsCost*1.2).toFixed(2));
            $('#totalNet').html("&pound;"+(totalLabourCost+totalPartsCost).toFixed(2));
            $('#totalVat').html("&pound;"+((totalLabourCost*0.2)+(totalPartsCost*0.2)).toFixed(2));
            $('#totalGross').html("&pound;"+((totalLabourCost*1.2)+(totalPartsCost*1.2)).toFixed(2));
            
            hideLoading();
        }
    }, "json");
});

function printPage() {  // A4 = 21.0 x 29.7cm
    $('#invoiceFooter').show();
    $('#printbtns').hide();
    window.print();
    window.close();
}


//function heightinmm(id, dpi) {  //     centimeters = pixels * 2.54 / dpi
//    if (typeof dpi === "undefined") { dpi = 96; }
//    var h = $('#'+id).height();
//    var cm  = h * (2.54/dpi);
//    return cm*10;
//}

</script>
</body>
</html>