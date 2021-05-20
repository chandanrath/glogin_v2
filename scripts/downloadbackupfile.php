
<?php
error_reporting(E_ALL); 

	$file_id = $_GET['fileid'];
	// step 1 for authetication //

	$application_key_id = "000b7973cef48db0000000001"; 			// Obtained from your B2 account page
	$application_key = "K000CcHTSt8pXzG8TGZoAx+ItbPLMS8"; 		// Obtained from your B2 account page
	$credentials = base64_encode($application_key_id . ":" . $application_key);
	$url = "https://api.backblazeb2.com/b2api/v2/b2_authorize_account";
	
	$session = curl_init($url);

	// Add headers
	$headers = array();
	$headers[] = "Accept: application/json";
	$headers[] = "Authorization: Basic " . $credentials;
	curl_setopt($session, CURLOPT_HTTPHEADER, $headers);  	// Add headers

	curl_setopt($session, CURLOPT_HTTPGET, true);  			// HTTP GET
	curl_setopt($session, CURLOPT_RETURNTRANSFER, true); 	// Receive server response
	$server_output = curl_exec($session);
	curl_close ($session);
	
	
	$result = json_decode($server_output,true);
	
	echo"<pre>res==";print_r($result);

	$accountId= $result['accountId'];
	$authKey= $result['authorizationToken'];
	$bucketId= $result['allowed']['bucketId'];
	

	// step 2 for bucket details//
	
	$account_id = $accountId; 							// Obtained from your B2 account page
	$api_url = "https://api000.backblazeb2.com"; 		// From b2_authorize_account call
	
	$auth_token = $authKey; 							// From b2_authorize_account call
	
	$valid_duration = 86400; 							// The number of seconds the authorization is valid for
	
	$file_name_prefix = "public"; 						// The file name prefix of files the download authorization will allow

	$session = curl_init($api_url .  "/b2api/v2/b2_get_download_authorization");  //b2_get_file_info

	
	// Add post fields
	

	$data = array("bucketId" => $bucketId, 
              "validDurationInSeconds" => $valid_duration, 
              "fileNamePrefix" => $file_name_prefix);
			  
			  
	$post_fields = json_encode($data);
	
	curl_setopt($session, CURLOPT_POSTFIELDS, $post_fields); 

	// Add headers
	$headers = array();
	$headers[] = "Authorization: " . $auth_token;
	curl_setopt($session, CURLOPT_HTTPHEADER, $headers); 	
	
	curl_setopt($session, CURLOPT_POST, true); 					// HTTP POST
	curl_setopt($session, CURLOPT_RETURNTRANSFER, true);  		// Receive server response
	$server_output1 = curl_exec($session); 						// Let's do this!
	curl_close ($session); 										// Clean up
	
	$result1 = json_decode($server_output1,true);
	
	echo"<pre>res1@@==";print_r($result1);
	
	// step 3  for file download //
	
	$download_url = "https://f000.backblazeb2.com";  			// From b2_authorize_account call
	$file_id = $file_id; 										// The ID of the file you want to download
	//$file_id = "4_z2bc74937835c2e0f74180d1b_f119f41a6e5f7801c_d20200328_m094258_c000_v0001066_t0056";
	
	
	
	
	$uri = $download_url . "/b2api/v2/b2_download_file_by_id?fileId=" . $file_id;

	$session = curl_init($uri);
	
	$headers = array();
	$headers[] = "Authorization: " . $auth_token;
	curl_setopt($session, CURLOPT_HTTPHEADER, $headers); 
	
	curl_setopt($session, CURLOPT_HTTPGET, true); // HTTP GET
	curl_setopt($session, CURLOPT_RETURNTRANSFER, true);  // Receive server response
	$server_output2 = curl_exec($session); // Let's do this!
	
	echo ($server_output2); // Tell me about the rabbits, George!
	
	curl_close ($session); // Clean up
	
	
	$uri = "pragatimetal.tar.gz";
	
    header('Expires: 0'); // no cache
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s', time()) . ' GMT');
    header('Cache-Control: private', false);
    header('Content-Type: application/force-download');
    header('Content-Disposition: attachment; filename="' . basename($uri) . '"');
    header('Content-Transfer-Encoding: binary');
    header('Content-Length: ' . strlen($server_output2)); // provide file size
    header('Connection: close');
    
	
	


?>
