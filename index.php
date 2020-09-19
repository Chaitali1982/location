<?php

	ini_set('display_errors', 'On');
	error_reporting(E_ALL);

	include('AbstractGeocoder.php');
	include('Geocoder.php');

	$geocoder = new \OpenCage\Geocoder\Geocoder('dd23949b2baa4598a19d75070f1be9ae');

	$result = $geocoder->geocode('51.952659, 7.632473', ['language' => 'fr']);

	if (in_array($result['status']['code'], [401,402,403,429])) {

		$handle = curl_init('https://geocoder.ls.hereapi.com/6.2/geocode.json?searchtext=' . urlencode($_REQUEST['q']) . '&gen=9&language=' . $_REQUEST['lang'] . '&locationattributes=tz&locationattributes=tz&apiKey=3a-30Zv1XS6W1oOiLxhsIfSudk2mDak6bfVQmOrPvjA');

        curl_setopt($handle, CURLOPT_HTTPHEADER, array('Content-Type: text/plain; charset=UTF-8'));
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        $json_result = curl_exec($handle);

		$searchResult = [];
		$searchResult['results'] = [];

		$temp = [];

		$r = json_decode($json_result, true);

		foreach ($r['Response']['View'][0]['Result'] as $result) {

			$temp['source'] = 'here';
			$temp['formatted'] = $result['Location']['Address']['Label'];
			$temp['geometry']['lat'] = $result['Location']['DisplayPosition']['Latitude'];
			$temp['geometry']['lng'] = $result['Location']['DisplayPosition']['Longitude'];
			$temp['countryCode'] = getCountryCode($result['Location']['Address']['Country']);
			$temp['timezone'] = $result['Location']['AdminInfo']['TimeZone']['id'];

			array_push($searchResult['results'], $temp);

		}

	} else {

		$searchResult = [];
		$searchResult['results'] = [];

		$temp = [];

		

		foreach ($result['results'] as $entry) {

			$temp['documentation'] = 'https://opencagedata.com/api';
			$temp['licence']['name'] = 'see attribution guide';
			$temp['licence']['url'] =  'https://opencagedata.com/credits';
			$temp['rate']['limit'] = '2500';
			$temp['rate'] ['remaining']= '2498';
			$temp['rate'] ['reset'] = '1596240000';
			
            $temp['annotations']['DMS'] = $entry['annotations']['DMS'];
			$temp['annotations']['MGRS'] = $entry['annotations']['MGRS'];
			$temp['annotations']['Maidenhead'] = $entry['annotations']['Maidenhead'];
			$temp['annotations']['Mercator'] = $entry['annotations']['Mercator'];
			$temp['annotations']['OSM'] = $entry['annotations']['OSM'];
			$temp['annotations']['UN_M49'] = $entry['annotations']['UN_M49'];
			$temp['annotations']['UN_M49']['regions'] = $entry['annotations']['UN_M49']['regions'];
	        $temp['annotations']['callingcode'] = '49';
			$temp['annotations']['currency'] = $entry['annotations']['currency'];
			$temp['annotations']['flag'] = 'DE';
			$temp['annotations']['geohash'] = 'u1jrt9ty1t8rg3r5wttm';
			$temp['annotations']['qibla'] = '128.55';
            $temp['annotations']['roadinfo'] = $entry['annotations']['roadinfo'];
			$temp['annotations']['sun'] = $entry['annotations']['sun'];
			$temp['annotations']['timezone'] = $entry['annotations']['timezone'];
			$temp['annotations']['what3words']['words'] = $entry['annotations']['what3words']['words'];

			$temp['bounds'] ['northeast']= $entry['bounds'] ['northeast'];
			$temp['bounds'] ['southwest']= $entry['bounds'] ['southwest'];

			$temp['components']=$entry['components'];
			
            $temp['formated'] = 'Friedrich-Ebert-Straße 7, 48153 Münster, Germany';
			$temp['confidence'] = '10';$temp['geometry']['lat'] = $entry['geometry']['lat'];
			$temp['geometry']['lng'] = $entry['geometry']['lng'];
			$temp['countryCode'] = strtoupper($entry['components']['country_code']);
			$temp['timezone'] = $entry['annotations']['timezone']['name'];
            $temp['status'] ['message'] = 'ok';
			$temp['status'] ['code'] = '200';
			$temp['stay_informed'] ['blog'] = 'https://blog.opencagegetdata.com';
			$temp['stay_informed'] ['twitter'] = 'https://twitter.com/OpenCage';
            $temp['thanks'] = 'For using an OpenCage API';
			$temp['timestamp'] ['created_http'] = 'Fri, 31 Jul 2020 09:50:14 GMT';
			$temp['timestamp'] ['created_unix'] = '1596189014';
            $temp['total_results'] = '1';
			
			array_push($searchResult['results'], $temp);

		}
	}

header('Content-Type: application/json; charset=UTF-8');
echo json_encode($searchResult, JSON_UNESCAPED_UNICODE);


?>
