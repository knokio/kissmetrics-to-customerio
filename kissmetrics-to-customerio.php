<?php

	error_reporting(-1);

	date_default_timezone_set('Europe/Lisbon');

	function create_customer($customer_id, $email, $created_at, $attributes)
	{
		$customerio_url = 'https://app.customer.io/api/v1/customers/' . $customer_id;

		$data = array_merge(array("email" => $email, "created_at" => $created_at), $attributes);

		$session = curl_init();

		curl_setopt($session, CURLOPT_URL, $customerio_url);
		curl_setopt($session, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($session, CURLOPT_HTTPGET, 1);
		curl_setopt($session, CURLOPT_HEADER, false);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($session, CURLOPT_CUSTOMREQUEST, 'PUT');
		curl_setopt($session, CURLOPT_VERBOSE, 0);
		curl_setopt($session, CURLOPT_POSTFIELDS,http_build_query($data));
		curl_setopt($session, CURLOPT_USERPWD, SITE_ID . ":2" . API_KEY);
		curl_setopt($session, CURLOPT_SSL_VERIFYPEER,false);

		$response = curl_exec($session);

		if (curl_getinfo($session, CURLINFO_HTTP_CODE) != 200)
		{
			throw new Exception($response);
		}

		curl_close($session);
	}

	function delete_customer($customer_id)
	{
		$session = curl_init();
		$customerio_url = 'https://app.customer.io/api/v1/customers/'.$customer_id;  

		curl_setopt($session, CURLOPT_URL, $customerio_url);
		curl_setopt($session, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($session, CURLOPT_HTTPGET, 1);
		curl_setopt($session, CURLOPT_HEADER, false);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($session, CURLOPT_CUSTOMREQUEST, 'DELETE');
		curl_setopt($session, CURLOPT_VERBOSE, 0);

		curl_setopt($session,CURLOPT_USERPWD, SITE_ID . ":" . API_KEY);

		if(ereg("^(https)",$request)) curl_setopt($session,CURLOPT_SSL_VERIFYPEER,false);

		$response = curl_exec($session); 

		if (curl_getinfo($session, CURLINFO_HTTP_CODE) != 200)
		{
			throw new Exception($response);
		}

		curl_close($session);
	}

	function track_event($customer_id, $name, $timestamp, $attributes)
	{
		$customerio_url = 'https://app.customer.io/api/v1/customers/'.$customer_id.'/events';  

		$data = array('name' => $name, 'timestamp' => $timestamp, 'data' => $attributes);
	 
		$session = curl_init();

		curl_setopt($session, CURLOPT_URL, $customerio_url);
		curl_setopt($session, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($session, CURLOPT_HTTPGET, 1);
		curl_setopt($session, CURLOPT_HEADER, false);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($session, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($session, CURLOPT_VERBOSE, 0);
		curl_setopt($session, CURLOPT_POSTFIELDS,http_build_query($data));

		curl_setopt($session,CURLOPT_USERPWD, SITE_ID . ":" . API_KEY);
		curl_setopt($session,CURLOPT_SSL_VERIFYPEER,false);

		$response = curl_exec($session);

		if (curl_getinfo($session, CURLINFO_HTTP_CODE) != 200)
		{
			throw new Exception($response);
		}

		curl_close($session);
	}
