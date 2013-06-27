<?php

$in = json_decode(file_get_contents('php://input'), false);

// JSON body could not be parsed, throw exception
if ($in === null) {
	throw new HTTPException(
		'There was a problem understanding the data sent to the server by the application.',
		409,
		array(
			'dev' => 'The JSON body sent to the server was unable to be parsed.',
			'internalCode' => 'REQ1000',
			'more' => ''
		)
	);
}

return $in;
