<?php

include_once("app/init.app.php");

$pageTitle = "Recherche Matcha";
$pageStylesheets = array ("main.css", "header.css", "index.css");

$ageMin = 0;
$ageMax = 0;
$popMin = 0;
$popMax = 0;
$localisation = "";
$tags = "";
$tagsList = "";
$locMax = 0;
$sexe = "both";
$sexuality = "both";
$sortMethod = "popularity";
$sortOrder = "asc";
$localisationLatLong = NULL;

$mm = new MemberManager($db);
$tm = new TagManager($db);

if (isset($_SESSION['recherche_parameters']['sortOrder']))
	$sortOrder = $_SESSION['recherche_parameters']['sortOrder'];

if (isset($_SESSION['recherche_parameters']['ageMin']))
	$ageMin = $_SESSION['recherche_parameters']['ageMin'];

if (isset($_SESSION['recherche_parameters']['ageMax']))
	$ageMax = $_SESSION['recherche_parameters']['ageMax'];

if (isset($_SESSION['recherche_parameters']['popMin']))
	$popMin = $_SESSION['recherche_parameters']['popMin'];

if (isset($_SESSION['recherche_parameters']['popMax']))
	$popMax = $_SESSION['recherche_parameters']['popMax'];

if (isset($_SESSION['recherche_parameters']['locMax']))
	$locMax = $_SESSION['recherche_parameters']['locMax'];

if (isset($_SESSION['recherche_parameters']['localisation']))
	$localisation = $_SESSION['recherche_parameters']['localisation'];

if (isset($_SESSION['recherche_parameters']['sexuality']))
	$sexe = $_SESSION['recherche_parameters']['sexuality'];

if (isset($_SESSION['recherche_parameters']['sexe']))
	$sexe = $_SESSION['recherche_parameters']['sexe'];

if (isset($_SESSION['recherche_parameters']['sortMethod'])) {
	$sortMethod = $_SESSION['recherche_parameters']['sortMethod'];
}

if (isset($_POST['ageMin'])) {
	if ($_POST['ageMin'] > 0 && ($_POST['ageMin'] <= $_POST['ageMax'] || $_POST['ageMax'] == 0))
		$ageMin = intval($_POST['ageMin']);
	else
		$ageMin = 0;
	$_SESSION['recherche_parameters']['ageMin'] = $ageMin;
}

if (isset($_POST['ageMax'])) {
	if ($_POST['ageMax'] > 0 && ($_POST['ageMax'] >= $ageMin || $ageMin == 0))
		$ageMax = intval($_POST['ageMax']);
	else
		$ageMax = 0;
	$_SESSION['recherche_parameters']['ageMax'] = $ageMax;
}

if (isset($_POST['popMin'])) {
	if ($_POST['popMin'] > 0 && ($_POST['popMin'] <= $_POST['popMax'] || $_POST['popMax'] == 0))
		$popMin = intval($_POST['popMin']);
	else
		$popMin = 0;
	$_SESSION['recherche_parameters']['popMin'] = $popMin;
}

if (isset($_POST['popMax'])) {
	if ($_POST['popMax'] > 0 && ($_POST['popMax'] >= $popMin || $popMin == 0))
		$popMax = intval($_POST['popMax']);
	else
		$popMax = 0;
	$_SESSION['recherche_parameters']['popMax'] = $popMax;
}

if (isset($_POST['locMax']) && isset($_POST['localisation'])) {
	if ($_POST['locMax'] >= 0) {
		$locMax = intval($_POST['locMax']);
		$_SESSION['recherche_parameters']['locMax'] = $locMax;
	}

	if (($gresult = Utilities::getLongLatFromString($_POST['localisation'])) != false) {
		$localisation = Utilities::getLocationInString($gresult['lat'], $gresult['long']);
		$localisationLatLong = $gresult;
	} else {
		$localisation = "";
	}
	$_SESSION['recherche_parameters']['localisation'] = $localisation;
}

if (isset($_POST['tags'])) {
	$tagsToCheck = explode(",", $_POST['tags']);
	$tagsList = array();

	foreach($tagsToCheck as $tagData) {
		$tagData = trim($tagData);
		if (!in_array($tagData, $tagsList) && $tm->ifExist($tagData)) {
			$tags .= ($tags == "") ? $tagData : (", " . $tagData);
			$tagsList[] = $tagData;
		}
	}
}

if (isset($_POST['sexe'])) {
	switch($_POST['sexe']) {
		case "male":
			$sexe = "male";
			$_SESSION['recherche_parameters']['sexe'] = $sexe;
			break;

		case "female":
			$sexe = "female";
			$_SESSION['recherche_parameters']['sexe'] = $sexe;
			break;

		case "both":
			$sexe = "both";
			$_SESSION['recherche_parameters']['sexe'] = $sexe;
			break;
		break;
	}
}

if (isset($_POST['sexuality'])) {
	switch($_POST['sexuality']) {
		case "male":
			$sexuality = "male";
			$_SESSION['recherche_parameters']['sexuality'] = $sexuality;
			break;

		case "female":
			$sexuality = "female";
			$_SESSION['recherche_parameters']['sexuality'] = $sexuality;
			break;

		case "both":
			$sexuality = "both";
			$_SESSION['recherche_parameters']['sexuality'] = $sexuality;
			break;
		break;
	}
}

if (isset($_POST['sortMethod'])) {
	switch($_POST['sortMethod']) {
		case "age":
			$sortMethod = "age";
			$_SESSION['recherche_parameters']['sortMethod'] = $sortMethod;
			break;

		case "popularity":
			$sortMethod = "popularity";
			$_SESSION['recherche_parameters']['sortMethod'] = $sortMethod;
			break;

		case "localisation":
			$sortMethod = "localisation";
			$_SESSION['recherche_parameters']['sortMethod'] = $sortMethod;
			break;

		case "tags":
			$sortMethod = "tags";
			$_SESSION['recherche_parameters']['sortMethod'] = $sortMethod;
			break;
		break;
	}
}

if (isset($_POST["sortOrder"])) {
	if ($_POST["sortOrder"] === "asc") {
		$_SESSION['recherche_parameters']['sortOrder'] = "asc";
		$sortOrder = $_SESSION['recherche_parameters']['sortOrder'];
	} else if ($_POST["sortOrder"] === "desc"){
		$_SESSION['recherche_parameters']['sortOrder'] = "desc";
		$sortOrder = $_SESSION['recherche_parameters']['sortOrder'];
	}
}

function ifUsersHaveTags($userId, $tagsList) {
	global $db;
	$tm = new TagManager($db);
	$tagsFromUser = $tm->getAllTagsFromMemberId($userId, "content_array");

	$nbOfTags = 0;
	foreach($tagsFromUser as $tag) {
		$tagContent = $tm->getTagContentFromId($tag->getId());
		if (in_array($tagContent, $tagsList))
			$nbOfTags++;
	}
	return $nbOfTags;
}

function search_users($ageMin, $ageMax, $popMin, $popMax, $locMax, $localisationLatLong, $tags, $sexe, $sexuality, $sortMethod, $sortOrder) {
	global $db;
	$mm = new MemberManager($db);
	$tm = new TagManager($db);
	$totalUsers = $mm->getAllExistingUsers();
	$finalListOfUsers = array();

	foreach ($totalUsers as $user) {
		$member = new Member(0);
		$result = $member->hydrate($user);

		if (
			($ageMin == 0 || $member->getAge() >= $ageMin) &&
			($ageMax == 0 || $member->getAge() <= $ageMax) &&
			($popMin == 0 || $member->getPopularity() >= $popMin) &&
			($popMax == 0 || $member->getPopularity() <= $popMax) &&
			($sexe == 0 || $member->getSexe() === $sexe) &&
			($sexuality == 0 || $member->getSexuality() === $sexuality) &&
			($locMax == 0 || $localisationLatLong == NULL ||
				(Utilities::distanceBetweenTwoPoints($member->getLocationLat(), $member->getLocationLong(),
					$localisationLatLong['lat'], $localisationLatLong['long'])) <= $locMax) &&
			(empty($tags) || ifUsersHaveTags($member->getId(), $tags) > 0)
		) {
			$finalListOfUsers[] = $member;
		}
	}

	if ($sortMethod == "age" && $sortOrder == "asc") {
		usort($finalListOfUsers, function($a, $b) {
		    return $a->getAge() <=> $b->getAge();
		});
	} else if ($sortMethod == "age" && $sortOrder == "desc"){
		usort($finalListOfUsers, function($a, $b) {
		    return $b->getAge() <=> $a->getAge();
		});
	} else if ($sortMethod == "popularity" && $sortOrder == "asc"){
		usort($finalListOfUsers, function($a, $b) {
		    return $a->getPopularity() <=> $b->getPopularity();
		});
	} else if ($sortMethod == "popularity" && $sortOrder == "desc"){
		usort($finalListOfUsers, function($a, $b) {
		    return $b->getPopularity() <=> $a->getPopularity();
		});
	} else if ($sortMethod == "tags" && $sortOrder == "asc" && !empty($tagsList)){
		usort($finalListOfUsers, function($a, $b) {
		    return ifUsersHaveTags($a->getId(), $tagsList) <=> ifUsersHaveTags($b->getId(), $tagsList);
		});
	} else if ($sortMethod == "tags" && $sortOrder == "desc" && !empty($tagsList)){
		usort($finalListOfUsers, function($a, $b) {
		    return ifUsersHaveTags($b->getId(), $tagsList) <=> ifUsersHaveTags($a->getId(), $tagsList);
		});
	} else if ($sortMethod == "localisation" && $sortOrder == "desc" && !empty($localisationLatLong)) {
		usort($finalListOfUsers, function($a, $b) {
			global $localisationLatLong;
		    return Utilities::distanceBetweenTwoPoints($a->getLocationLat(), $a->getLocationLong(), $localisationLatLong['lat'], $localisationLatLong['long']) <=> Utilities::distanceBetweenTwoPoints($b->getLocationLat(), $b->getLocationLong(), $localisationLatLong['lat'], $localisationLatLong['long']);
		});
	} else if ($sortMethod == "localisation" && $sortOrder == "asc" && !empty($localisationLatLong)) {
		usort($finalListOfUsers, function($a, $b) {
			global $localisationLatLong;
		    return Utilities::distanceBetweenTwoPoints($b->getLocationLat(), $b->getLocationLong(), $localisationLatLong['lat'], $localisationLatLong['long']) <=> Utilities::distanceBetweenTwoPoints($a->getLocationLat(), $a->getLocationLong(), $localisationLatLong['lat'], $localisationLatLong['long']);
		});
	}

	return ($finalListOfUsers);
}

$users = search_users($ageMin, $ageMax, $popMin, $popMax, $locMax, $localisationLatLong, $tagsList, $sexe, $sexuality, $sortMethod, $sortOrder);