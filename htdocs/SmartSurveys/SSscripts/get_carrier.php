<?PHP

//error_reporting (0);
//header('Cache-Control: no-cache');
//$error = 0;

set_time_limit(60);

$ph_no = $_REQUEST["phno"];


$ph_no = strval($ph_no);

if (substr($ph_no, 0, 1) == '0') {
	$ph_no = substr($ph_no, 1, strlen($ph_no));
	$ph_no = '+92' . $ph_no;
}

$r = getResult($ph_no);
echo $r;

function getResult($ph_no)
{
	$carrier = '';
	$cURLConnection = curl_init();
	//echo $ph_no;
	$url = 'https://www.hlr-lookups.com/api/?action=submitSyncLookupRequest&msisdn='.strval($ph_no).'&username=ahmad670&password=Randhawa670';

	//ECHO $url;
	//print_r($url);
	
	curl_setopt($cURLConnection, CURLOPT_URL, $url);
	curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($cURLConnection, CURLOPT_FAILONERROR, true);

	$carrier_response = curl_exec($cURLConnection);
	
	
	if (curl_errno($cURLConnection)) 
	{
    	$error_msg = curl_error($cURLConnection);
	}
	
	curl_close($cURLConnection);
	
	//print_r($carrier_response);
	if (!isset($error_msg)) 
	{	
		$jsonArrayResponse = json_decode($carrier_response);

		if($jsonArrayResponse->{"success"} == true)
		{
			//echo $jsonArrayResponse->{"results"}[0]->{"isvalid"};
			if($jsonArrayResponse->{"results"}[0]->{"isvalid"}=="Yes")
			{
				if($jsonArrayResponse->{"results"}[0]->{"isported"}=="Yes")
				{
					$carrier = $jsonArrayResponse->{"results"}[0]->{"portednetworkname"};
				}
				else
				{
					$carrier = $jsonArrayResponse->{"results"}[0]->{"originalnetworkname"};
				}

				if ($carrier!='')
				{
					if ($carrier == 'Telenor Pakistan') {
						$carrier = 'TELENOR';
					} else if ($carrier == 'Jazz (Mobilink)') {
						$carrier = 'MOBILINK';
					} else if ($carrier == 'ZONG (CMPak Limited)') {
						$carrier = 'ZONG';
					} else if ($carrier == 'Ufone (Pak Telecom Mobile Ltd)') {
						$carrier = 'UFONE';
					} else if ($carrier == 'Jazz (Warid Telecom)') {
						$carrier = 'WARID';
					}
				}
			}
			else
			{
				$carrier = 'error_number_not_valid';
			}
		}
		else
		{
			//error in getting response reeust made but not successful
			$carrier = "error_in_curl_request";
		}
	}
	else
	{
		//error in getting response
		$carrier = "error_in_curl_request";
	}

	return $carrier;
}

?>