
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/1999/REC-html401-19991224/loose.dtd">
<html xmlns:syd="urn:schemas-evonet-com" xmlns="http://www.w3.org/TR/REC-html40">

<!--
    Take a valid JSON file POSTed as text, and generate a QR-code from it
-->
<head>
	<title>PT63255 Label Generator</title>
	<meta charset="utf-8">

	<style type="text/css">
		SPAN.hidden {
			display: none;
		}
        body {
            background-color: lightblue;
        }

        h1 {
            text-align: center;
        }
        table.outside, th, td {
            border: 0px;
            margin: 25px;

        }
        /* Outside frame for ratings plate, connector etc*/
        table.frame, th, td {
            border: 1px solid black;
            background-color: white;

        }
        /* Inner table for values */
        table.form, th, td {
            border: 0px;
        }



	</style>
	

</head>
<body >
<h1>PT63255 Label Generator</h1>


<?php

error_reporting(-1);
ini_set("display_errors",1);
session_start();	//allow session variables

include "compress_json.php";
include "make_qr.php";    
    
//$json_string = $_POST['json'];
$json_string = 
<<<TEST
{"IEC63255_0_jsn":{"name":"Sample","total_current":"3000","stored_energy":"85","ports":
    {"port":[{"port_name":"Input","type":"current","vmax":"240","vmin":"100","acdc":"acdc",
        "max_rated_current":"300","connector":{"connector_type":{"iec_mains":{"iec_letter":"G"}},
        "gender":"plug","fixed_or_free":"fixed"}},{"port_name":"Outputs","type":"voltage","vmax":"15.0",
            "vmin":"10.5","number_of_ways":"2","max_rated_current":"3000","connector":{"connector_type":
                {"terminal_block_or_bare_wire":{"number_of_ways":"9"}},"gender":"socket","pinout":
                {"pinout_pair":[{"pole":"pin1","function":"negative"},{"pole":"pin2","function":"positive"},
                {"pole":"pin3","function":"data"},{"pole":"shell","function":"ground"},{"pole":"pin4",
                    "function":"ground"}]},"fixed_or_free":"free"}},{"type":"voltage",
                        "connector":{"connector_type":{"barrel_connector":{"outside":"5.5","inside":"2.1"}},
                        "gender":"socket","pinout":{"pinout_pair":{"pole":"centre","function":"positive"}},
                        "fixed_or_free":"free","digital":{"digital_protocol":"usb Type A","talks_to":"usb Type B"}},
                        "port_name":"Lights","vmax":"15.0","vmin":"10.5","max_rated_current":"500",
                        "number_of_ways":"4"}]},"continuities":{"continuity":[{"p1name":"Input",
                            "p2name":"Lights"},{"p1name":"Input","p2name":"Output"}]}}}
TEST;

$short_json = longshort($json_string);
$qr_string = compress_json($short_json);
$qr_file = show_qr($qr_string);

//display generated file
echo '<img src="'.$qr_file.'" /><hr/>';  

?>
</body>
</html>
