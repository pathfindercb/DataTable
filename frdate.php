<?php
/* 	// check if logged in 
	session_start();
	if(!isset($_SESSION["userid"])) {
		header("Location:frwebform.php");
	}
 */?>


<!DOCTYPE html>

<!-- Financial Reporting Demo for JOMASC 09/13/17 -->
<html>
<head>
<meta charset="utf-8">

<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">
<link rel="stylesheet" type="text/css" href="DataTables/datatables.css"/>
<script type="text/javascript" src="DataTables/datatables.js"></script>
<script type="text/javascript" language="javascript" class="init">

$(document).ready(function() {
		var xldate = function (inDate) {
			return (new Date(inDate).getTime()/86400000+25569 );
		};
	   var buttonCommon = {
			exportOptions: {
				format: {
					body: function ( data, row, column, node ) {
						// Change date column from yyyy-mm-dd to dd/mm/yyyy
						return column === 0 ?
							'=' + xldate(data) :
							data;
					}
				}
	   }};
	$('#report').dataTable( {
		"processing": true,
		"serverSide": true,
		"fixedHeader": true,
		"ajax": "frdate-response.php",
		"lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "All"] ],
        dom: '<"top"ilf>rt<"bottom"pB>',
        buttons: [ $.extend( true, {}, buttonCommon, {
                extend: 'excelHtml5'
            } )],

		"footerCallback": function ( row, data, start, end, display ) {
			var api = this.api(), data;

			// Remove the formatting to get integer data for summation
			var intVal = function ( i ) {
				return typeof i === 'string' ?
					i.replace(/[\$,]/g, '')*1 :
					typeof i === 'number' ?
						i : 0;
			};

			// Total over all pages
			total = api
				.column( 4 )
				.data()
				.reduce( function (a, b) {
					return intVal(a) + intVal(b);
				}, 0 );

			// Total over this page
			pageTotal = api
				.column( 4, { page: 'current'} )
				.data()
				.reduce( function (a, b) {
					return intVal(a) + intVal(b);
				}, 0 );

			// Update footer
			$( api.column( 4 ).footer() ).html(
				'$' + pageTotal +' ( $' + total +' total)'
			);
        }
 	} );
} );
</script>
</head>

<body class="dt-report">
<a href="frwebform.php"> <img src="images\Header.jpg" alt="Financial Reporting"> </a>

<table id="report" class="display" align="center" cellspacing="5" width="90%">
<thead>
<tr>
<th>date2</th>
<th>invoice</th>
<th>manufacturer</th>
<th>customer</th>
<th>amount</th>
</tr>
</thead>
</table>

<p><p><p><small>Powered by JOMASC, LLC</small>
</body>
</html>