# DataTable
Clone of Allan Jardine's DataTable 1.10.16 to experiment with adding date formats

Add code (see below) to the main php file (not the response file) so trap the Excel button, convert the date string to an Excel Date and out the = sign on front (to distinguish it later from a number or a string.

Then use the new datatable.js that has two important changes:

  1. In the var excelStrings = { in the numfnts section I added the 170 for the date format
  			'<numFmts count="7">'+
				'<numFmt numFmtId="164" formatCode="#,##0.00_-\ [$$-45C]"/>'+
				'<numFmt numFmtId="165" formatCode="&quot;£&quot;#,##0.00"/>'+
				'<numFmt numFmtId="166" formatCode="[$€-2]\ #,##0.00"/>'+
				'<numFmt numFmtId="167" formatCode="0.0%"/>'+
				'<numFmt numFmtId="168" formatCode="#,##0;(#,##0)"/>'+
				'<numFmt numFmtId="169" formatCode="#,##0.00;(#,##0.00)"/>'+
				'<numFmt numFmtId="170" formatCode="yyyy\-mm\-dd"/>'+
			'</numFmts>'+
    
    and then added to the cellsXfs section
    				'<xf numFmtId="170" fontId="0" fillId="0" borderId="0" applyFont="1" applyFill="1" applyBorder="1" xfId="0" applyNumberFormat="1"/>'+

    I also adjusted the counts in these two sections and discovered the the numFmtId=170 was now the 63rd style so...
  
  
  
  2. in the DataTable.ext.buttons.excelHtml5 function, down about 100 lines at line#106055 I add the code in the If statement checking if the value is number or string to also look for the first character being an = sign and then choose the proper date format of the 635r style
  
  					else if ( row[i].charAt(0) == '=' ) {
						cell = _createNode( rels, 'c', {
							attr: {
								s: '63',
								r: cellId
							},
							children: [
								_createNode( rels, 'v', { text: row[i].slice(1) } )
							]
						} );
					}

  CODE FOR EACH PRIMARY DATATABLE FILE:
  
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
