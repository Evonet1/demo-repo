
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/1999/REC-html401-19991224/loose.dtd">
<html xmlns:syd="urn:schemas-evonet-com" xmlns="http://www.w3.org/TR/REC-html40">
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
/*
 * PHP QR Code encoder
 *
 * Exemplatory usage
 *
 * PHP QR Code is distributed under LGPL 3
 * Copyright (C) 2010 Dominik Dzienia <deltalab at poczta dot fm>
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 */

    ini_set('display_errors', 1);
    error_reporting(E_ALL);	
	
//	print_r($_POST);
	
	$total_groups = 3;
	$dataset = array();
	
	$dataset['pname'] = $_POST['pname'];
	$dataset['type'] = $_POST['type'];
	$dataset['vimin'] = $_POST['vimin'];
	$dataset['vimax'] = $_POST['vimax'];
	$dataset['acdc'] = $_POST['acdc'];
	$dataset['ioma'] = $_POST['ioma'];
	$dataset['conntype'] = $_POST['conntype'];
	$dataset['outside'] = $_POST['outside'];
	$dataset['inside'] = $_POST['inside'];
	$dataset['pin'] = $_POST['pin'];
	$dataset['aux'] = $_POST['aux'];
	for ( $i = 1; $i <= $total_groups; $i++ ) {
		$group = array();
		$group['gname'] = $_POST['gname-' . $i];
		$group['vo'] = $_POST['vo-' . $i];
		$group['acdc'] = $_POST['acdc-' . $i];
		$group['ioma'] = $_POST['ioma-' . $i];
		$group['conntype'] = $_POST['conntype-' . $i];
		$group['outside'] = $_POST['outside-' . $i];
		$group['inside'] = $_POST['inside-' . $i];
		$dataset['group-'.$i] = $group;
	}
	
	$code_str = json_encode( $dataset );
	print ($code_str);
    
    //set it to writable location, a place for temp generated PNG files
    $PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR;
    
    //html PNG location prefix
    $PNG_WEB_DIR = 'temp/';

    include "qrlib.php";    
    
    //ofcourse we need rights to create temp dir
    if (!file_exists($PNG_TEMP_DIR))
        mkdir($PNG_TEMP_DIR);
    
    
    $filename = $PNG_TEMP_DIR.'test.png';
    
    //processing form input
    //remember to sanitize user input in real-life solution !!!
    $errorCorrectionLevel = 'L';
    if (isset($_REQUEST['level']) && in_array($_REQUEST['level'], array('L','M','Q','H')))
        $errorCorrectionLevel = $_REQUEST['level'];    

    $matrixPointSize = 4;
    if (isset($_REQUEST['size']))
        $matrixPointSize = min(max((int)$_REQUEST['size'], 1), 10);

    // This is where we must assemble our QR code string

    //Field 0 is the format identifier which says this QR code can be interpreted by the smartphone app

    // user data
    $filename = $PNG_TEMP_DIR.'test'.md5($code_str.'|'.$errorCorrectionLevel.'|'.$matrixPointSize).'.png';
    QRcode::png($code_str, $filename, $errorCorrectionLevel, $matrixPointSize, 2);    
    

        
    //display generated file
    echo '<img src="'.$PNG_WEB_DIR.basename($filename).'" /><hr/>';  

?>    
	<p><a href="../PT63255/index.htm">Return to data entry</a></p>



    