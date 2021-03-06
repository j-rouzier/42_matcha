<?php
$toCheck = array("id_picture", "content");

foreach($toCheck as $element) {
	if (!isset($action[$element]) || empty($action[$element])) {
		$error = genError("comment", "missingfield", $element);
	}
}

if (!isset($error)) {
	if (isUserLogged()) {
		$pm = new UserPictureManager($db);
		$commentedPic = $pm->get($action['id_picture']);
		if (is_object($commentedPic)) {
			$parameters = array(
				"id_user" => $currentUser->getId(),
				"id_picture" => $commentedPic->getId(),
				"content" => $action['content']);
			$comment = new Comment(0);
			$state = $comment->hydrate($parameters);
			if ($state === true) {
				$commentManager = new CommentManager($db);
				$commentManager->add($comment);
				$imageLink = Utilities::getAddress() . "picture.php?pic=" . $commentedPic->getId();
				$mm = new MemberManager($db);
				$pic_owner = $mm->get("id", $commentedPic->getOwner_id());
				$mail = 
				ucfirst($currentUser->getNickname()) . " a poste un nouveau commentaire sur votre photo : \"" . $comment->getContent() . "\"\nCliquez ici pour le voir : $imageLink";
				Utilities::sendMail($pic_owner->getEmail(),
					"Votre photo a recu un nouveau commentaire",
					$mail);
				echo $comment->getContent();
			} else {
				$error = $state;
			}
		}
	} else {
		$error = genError("comment", "notlogged", "login");
	}
}
