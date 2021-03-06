<?php

$error = false;
$json_output = array("output" => "ok");
if (isset($_POST['data']) && !empty($_POST['data'])) {
	$data = json_decode($_POST['data'], true);
} else {
	$error = "no_data";
}

if ($error === false) {
	if ($data['pageId'] == 'password') {
		$return = $currentUser->setPassword($data['password'], true);
	} else if ($data['pageId'] == "location") {
		$return = $currentUser->setLocationFromString($data['location']);
	} else {
		$return = $currentUser->hydrate($data);
	}
	if ($return !== true) {
		$error = $return;
	}
}

if ($error === false) {
	$memberManager = new MemberManager($db);
	$memberManager->update($currentUser);

	foreach ($data as $key => $value) {
		if ($key == "sexe") {
			$json_output[$key] = $currentUser->getSexeInString();
		} else if ($key == "sexual_orientation") {
			$json_output[$key] = $currentUser->getOrientationInString();
		} else if ($key == "location") {
			$json_output[$key] = $currentUser->getLocationInString();
		} else {
			$method = "get" . ucfirst($key);
			if (method_exists($currentUser, $method) && isset($value)) {
				$result = $currentUser->$method();
				if ($result != NULL) {
					$json_output[$key] = $result;
				}
			}
		}
	}
}

if ($error) {
	if (is_array($error)) {
		$json_output["err_msg"] = $error['msg'];
	} else {
		$json_output["err_msg"] = "Une erreur est survenue. Nous nous excusons de la gêne occasionnée";
	}
	$json_output["output"] = "error";
}

echo json_encode($json_output);