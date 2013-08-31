<?php
$dump = serialize($_FILES);
error_log($dump);

header('Access-Control-Allow-Origin: http://mmoscreens.web.jellykit.com');
header('Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description');
header('Access-Control-Allow-Origin: http://mmoscreens.web.jellykit.com');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json');

sleep(2);

$data = array(
	'files' => array(
		array('name' => 'filename'),
	),
);

exit(json_encode($data));
?>
<pre>

<?php
#print_r($_REQUEST);
print_r($_GET);
#print_r($_POST);
print_r($_FILES);
?>

</pre>
