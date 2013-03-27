# Migrate from KissMetrics to Customer.io

Limetree, like a lot of companies out there, rely on KissMetrics to get insights on their users. We track lots of events and try to make sense out of them.

With all this data there is still on flaw. We can easily segment our customers and create amazing charts, but we cannot reach them. KissMetrics does not help here; their data is fairly close and there is no API available.

I want to email my customers that have used my service for a few weeks and then stopped. Or how about those that only send pictures, but no videos. I want to tell them that videos is an amazing part of our product.

## How to migrate to Customer.io

We are not ditching KissMetrics but Customer.io relies on it's own event system so I needed a way to import my old metrics. I used the export service from KissMetrics which will dump hundreds of JSON files to a S3 bucket of your choice. It will take some hours on the first time but after that they will keep the bucket updated every few hours.

### Step 1: Export the KissMetrics data

This step is simple and KissMetrics has a simple guide: [link for kissmetrics export]
Follow this steps and come back for the next step.

### Step 2: Export your users

Before you start sending the events to Customer.io you need to create the customers - which is the same as users. Export your userbase to a CSV file. A simple dump from your database.

The required fields are: user id, email, name and creation date. You can add other properties you might find useful - probabily those you were already using on KissMetrics: type of plan or country. 

````
23123, "John Doe", "johndoe@dispostable.com", "2012-11-23", "Portugal"
...
````

### Step 3: Import your users

Feed each user to __create_customer(id, email, name, createdAt, attributes)__

This process will take a few minutes to hours depending on your userbase.

````
require("kissmetrics-to-customerio.php");

$csvFile = file_get_contents('users-dump.csv');

foreach (explode("\n", $csvFile) as $row)
{
	$data		= str_getcsv($row);
	$id			= $data[0];
	$name		= $data[1];
	$email		= $data[2];
	$createdAt	= strtotime($data[3]);
	$country	= $data[4];
	
	$attributes = array('country' => $country);
	
	try {
		create_customer($id, $email, $name, $createdAt, $attributes);
	} catch (Exception $ex) {
		echo $ex->getMessage();
	}
}
````

### Step 4: Collect your events

After you imported all your users the S3 bucket should be filled with the juicy content of all your KissMetrics events. The number of files can be overwhelming. They export hundreds of small files each containing several one-liners with JSON; we will need to compact it into one to facilitate the import process. Example:

```
{"platform":"iphone","_n":"login","_p":"12312","_t":1352317082}
{"platform":"web","_n":"login","_p":"2221321","_t":1352316831}
{"platform":"web","_n":"login","_p":"112123","_t":1352317100}
```

First sync the S3 bucket with a local directory:

```
s3cmd sync s3://kissmetrics-export-bucket km-export
```

Compact all the JSON files into one:

```
cat km-export/*.json > km-data.json
```


### Step 5: Import your events

Feed each event to __track_event(userId, name, timestamp, attributes)__

This process will take a few hours depending on the volume of events.

````
require("kissmetrics-to-customerio.php");

$eventsFile = file_get_contents('km-data.json');

foreach (explode("\n", $eventsFile) as $row)
{
	$data		= json_decode($row, true);

	$userId      = $data['_p'];
	$name        = $data['_n'];
	$timestamp   = $data['_t'];
	
	unset($data['_p']);
	unset($data['_n']);
	unset($data['_t']);
	
	// After unsetting _p, _n and _t, the remaining are event properties
	$attributes = $data;
	
	try {
		track_event($userId, $name, $timestamp, $attributes);
	} catch (Exception $ex) {
		echo $ex->getMessage();
	}
}
````

### Step 6: Wait

On this step you can grab a coffee, watch a movie, do some more coding or simply sleep.
It's gonna take a few hours but I garantee you that will save you days or even weeks of work.
The data your already have on KissMetrics is worth a lot.

### Step 7: Extra tip

If you have a huge amount of data - a few months or even years - you can accelerate this process by importing only the last 30 days, for example. There are some use cases where you might need data from the Past but this timeframe worked for me.

Just add this piece of code:


````

$eventsSince = strtotime('-30 days');

foreach (explode("\n", $eventsFile) as $row)
{
	$data		= json_decode($row, true);

	$userId      = $data['_p'];
	$name        = $data['_n'];
	$timestamp   = $data['_t'];

	if ($timestamp < $eventsSince) continue;

	…
}
````

### Conclusion

This guide may seem big but you can do this process in a few hours. If you have any doubt just tweet or send me an email: diogo at knokio dot com.