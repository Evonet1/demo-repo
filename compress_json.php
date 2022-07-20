<?php

error_reporting(-1);
ini_set("display_errors",1);
session_start();	//allow session variables

/*  This suite of functions takes conventional JSON, and reduces it to fit in
    a QR-code.  It also expands it back to valid JSON.  In summary:

    To put JSON into a QR code, longshort() then compress_json()
    To undo this, restore_json() then shortlong()
    
    (You may also want to change the preamble to remove the _jsn from the compressed
    format, but this isn't done here.)

    Note that the end result has the same data as the original JSON, but
    whitespace in the JSON syntax will have been removed, and the case of 
    parameter names and values will match those in the dictionary.  
    
    There are a few dictionary entries that are ambiguous (eg, earth,ground), 
    and these will always be converted to the first entry in the dictionary.

    Words not found in the dictionary will be unchanged, but obviously should be
    kept short if the compressed result is to be kept small. 
    
    There is no constraint on parameter names or values beyond the requirements of good JSON syntax.

*/


/******************************************************************************/

function compress_json ($json_string) {

/*  Even JSON is too verbose for QR-codes.  This routine removes all the double-quotes
    in a safe way, such that they can easily be put back again to make valid JSON
*/
    //The following characters are allowed in a JSON string, but also form part of
    //  the essential JSON syntax
    $syntax_chars = array("[","]","{","}",":",",");
    //These are replaced with their \u00xx equivalents
    $js1 = str_replace('\\"','\\u0022',$json_string);   //deal with any escaped " in a string
    //Note that this could cause an issue with a string ending \\" - this isn't easily fixed
    $elements = explode('"',$js1);
    $result = "";
    $string_flag = false;   //first will always be control char in valid JSON
    foreach ($elements as $element) {
        $this_element = $element;
        if ($string_flag) {     //we are in a string
            foreach ($syntax_chars as $syntax_char) {      //replace with safe equivalent
                $unicode = "\\u00".bin2hex($syntax_char);
                $this_element = str_replace($syntax_char,$unicode,$this_element);
            }   
            $string_flag = false;
        } else {    //this is a series of JSON syntax chars, so leave intact
            $this_element = preg_replace('/\s/','',$this_element);  //remove any whitespace in syntax
            $string_flag = true;
        }
        $result .= $this_element;    //pass syntax characters verbatim

    }

    return ($result);
}


/******************************************************************************/

function restore_json ($qr_string) {

    //  Undo the compression done by compress_json(), by restoring the double-quotes

        $json_string = trim(preg_replace("/[\\[\\]\\{\\}:,]+/",'"$0"',$qr_string),'"');
        //Any sequence of syntax chars has " added before and after.
        //Trim removes the initial and final "
    
        return ($json_string);
    }
    
    

/******************************************************************************/

function load_dict() {

   	//Load CSV dictionary
    //This comprises (short_form),(long_form),(control)
    //Control may be 0,1 or 2. 0=only parameter names,1= only parameter values,2=either
	$row = 0;
	$dict_file = "dict.csv";
	$dict_table = [];
	$dict_handle = fopen($dict_file, "r") or die("Dictionary $dict_file not found!");
	while (($data = fgetcsv($dict_handle, 1000, ",")) !== FALSE) {
		$num = count($data);
		if ($num == 3) {
			array_push($dict_table,$data);
		} else {
			die("Unrecognised dictionary format in $dict_file!");
		}
	}
	fclose($dict_handle);
    return($dict_table);

}

function shortlong($short_text) {
    // Use a dictionary stored in dict.csv to expand parameter names and values into something more readable

    $dict_table = load_dict();
    $result = $short_text;
    foreach ($dict_table as $dict_entry) {
        $control = (int)$dict_entry[2];
        if (($control % 2) == 0) {    //0 or 2, so shorten parameter name
            $result = str_ireplace('"'.$dict_entry[0].'":','"'.$dict_entry[1].'":',$result);
        }
        if ($control > 0) {   //1 or 2, so shorten parameter value
            $result = str_ireplace(':"'.$dict_entry[0].'"',':"'.$dict_entry[1].'"',$result);
        }
    }

    return($result);

}


function longshort($long_text) {
    // Use a dictionary stored in dict.csv to compress parameter names/values into 2 chars

    $dict_table = load_dict();
 
    $result = $long_text;
    foreach ($dict_table as $dict_entry) {
        $control = (int)$dict_entry[2];
        if (($control % 2) == 0) {    //0 or 2, so shorten parameter name
            $result = str_ireplace('"'.$dict_entry[1].'":','"'.$dict_entry[0].'":',$result);
        }
        if ($control > 0) {   //1 or 2, so shorten parameter value
            $result = str_ireplace(':"'.$dict_entry[1].'"',':"'.$dict_entry[0].'"',$result);
        }
    }

    return($result);

}

    

/******************************************************************************/



//  Testing code

echo ("Test compress_json<br>");
$test_array = array('{"Fred"}','{["Fr\"ed\nerick"]}', '["fred,[]{}:":"2"]','{"bill,[]{}:"}','{"bi\\"ll":"[]{}:"}',
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
TEST);
$output = array();
foreach ($test_array as $sample) {
    $result = compress_json($sample);
    if (!$result) {
        echo($sample." = Error");
    } else {
        echo("(".strlen($sample).") ".$sample." = ");
        print_r($result." (".strlen($result).")");
        array_push($output,$result);
    }
    echo("<br>");   
} 

echo ("Restoring.;..<br>");
foreach ($output as $out) {
    $full = restore_json($out);
    echo($full." (".strlen($full).")");
    echo("<br>");   
}


echo ("Compressing.;..<br>");
$longs = array();
foreach ($test_array as $test) {
    $short = longshort($test);
    $qr = compress_json($short);
    echo($qr." (".strlen($qr).")");
    array_push($longs,$qr);
    echo("<br>");   
}


echo ("Decompressing.;..<br>");
foreach ($longs as $tolong) {
    $json = restore_json($tolong);
    $full = shortlong($json);
    echo($full);
    echo("<br>");   
}


echo("<br>testing 19");

?>