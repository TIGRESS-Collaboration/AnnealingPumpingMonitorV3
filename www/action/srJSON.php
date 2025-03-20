<?php
#Save input to sample rate form to JSON file.

if (isset($_POST['src'])){
	$sr = strip_tags($_POST['src']);
	$array = array("sr"=>$sr);
	$json = json_encode($array);
	if(file_put_contents("/home/sharedjson/jsonFiles/samplerate.json", $json) === FALSE){
		echo "Error saving data";
	} else {
		echo "success";
	}
}
?>
