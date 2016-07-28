<?php
$toCheck = array("image_file", "filter");

foreach($toCheck as $element) {
	if (!isset($action[$element]) || empty($action[$element])) {
		$error = genError("upload_camera_image", "missingparam", $element);
	}
}

if (!isset($error)) {
	$image = new UserPicture(0);
	$parameters = array(
		"owner_id" => $currentUser->getId(),
		"upload_source" => "file",
		"filter_used" => $action['filter'],
		"source" => $source);
	$return = $image->hydrate($parameters);
	if ($return !== true) {
		$error = $return;
	}
}

if (!isset($error)) {
	$imageManager = new UserPictureManager($db);
	$image->setId($imageManager->add($image));
}

if (!isset($error)) {
	if ($image->addFilter($action['filter'])) {
		$image->setId($imageManager->add($image));
	}
}