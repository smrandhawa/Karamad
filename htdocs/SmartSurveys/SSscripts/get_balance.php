<?PHP

//error_reporting (0);
//header('Cache-Control: no-cache');
//$error = 0;

//set_time_limit(60);

$r = getResult();
echo $r;

function getResult()
{
	
	$balance = 0.0;
	$cURLConnection = curl_init();

	$url = 'https://www.hlr-lookups.com/api/?action=getBalance&username=ahmad670&password=Randhawa670';
	curl_setopt($cURLConnection, CURLOPT_URL, $url);
	curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($cURLConnection, CURLOPT_FAILONERROR, true);

	$balance_response = curl_exec($cURLConnection);
	
	
	if (curl_errno($cURLConnection)) 
	{
    	$error_msg = curl_error($cURLConnection);
	}
	
	curl_close($cURLConnection);
	
	
	if (!isset($error_msg)) 
	{	
		//var_dump($balance_response);
		//var_dump(json_decode($balance_response, true));
		//print_r($balance_response);
		$jsonArrayResponse = json_decode($balance_response);

		if($jsonArrayResponse->{"success"} == true)
		{
			$balance = floatval($jsonArrayResponse->{"results"}->{"balance"});
		}
	}
	else
	{
		//error in getting response
		$balance = -1;
	}
	return $balance;
}

?>