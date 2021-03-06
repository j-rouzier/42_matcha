<?php

require_once("app/class/Tag.class.php");

class TagManager {
	private $_db;

	public function __construct($db) {
		$this->_db = $db;
	}

	public function add(Tag $tag) {
		$q = $this->_db->prepare('
			INSERT INTO tags(content)
			VALUES(:content)');
		$q->bindValue(':content', $tag->getContent(), PDO::PARAM_STR);

		$q->execute();

		$id = $this->_db->lastInsertId();
		return ($id);
	}

	public function get( $field, $value ) {
		$fieldCorrectValues = array("id", "content");
		if (!in_array($field, $fieldCorrectValues)) {
			throw new Exception("Invalid field");
		}
		$statement = ('SELECT * FROM tags WHERE ' . $field . ' = :value');
		$q = $this->_db->prepare($statement);
		$q->bindValue(':value', $value, PDO::PARAM_STR);
		$q->execute();
		$donnees = $q->fetch();

		if ($q->rowCount() > 0) {
			$tag = new Tag($donnees['id']);
			$tag->hydrate($donnees);

			return ($tag);
		} else {
			return false;
		}
	}

	public function exist($field, $value) {
		$fieldCorrectValues = array("id", "content");
		if (!in_array($field, $fieldCorrectValues)) {
			throw new Exception("Invalid field");
		}

		$statement = 'SELECT COUNT(*) FROM tags WHERE ' . $field . ' = :value';
		$q = $this->_db->prepare($value);
		$q->bindValue(':value', $value, PDO::PARAM_STR);
		$q->execute();

		$result = $q->fetch();
		if ($result[0] > 0) {
			return true;
		} else {
			return false;
		}
	}

	public function ifExist($content) {
		$q = $this->_db->prepare('SELECT COUNT(*) FROM tags WHERE content = :content');
		$q->bindValue(':content', $content, PDO::PARAM_STR);
		$q->execute();

		$result = $q->fetch();
		
		return (($result[0] > 0) ? true : false);
	}

	public function getFromId($id) {
		return $this->get('id', $id);
	}

	public function getAllExistingTags() {
		$q = $this->_db->prepare('
			SELECT * FROM tags');
		$q->execute();

		$tags = $q->fetchAll();

		$result = [];
		foreach ($tags as $tag) {
			$newTag = new Tag(0);
			$newTag->hydrate($tag);
			$result[] = $newTag;
		}
		return ($result);
	}

	public function getTagContentFromId($id) {
		$statement = ('SELECT content FROM tags WHERE id = :id');
		$q = $this->_db->prepare($statement);
		$q->bindValue(':id', $id, PDO::PARAM_INT);
		$q->execute();
		$donnees = $q->fetch();
		return $donnees['content'];
	}

	public function getAllTagsFromMemberId($id) {
		$q = $this->_db->prepare('SELECT * FROM tags_users WHERE id_user = :id_user');
		$q->bindValue(':id_user', $id, PDO::PARAM_INT);
		$q->execute();

		$tagsFromId = $q->fetchAll();
		$tagsToFetch = "";
		foreach($tagsFromId as $tag) {
			$tagsToFetch .= $tag['id_tag'] . ",";
		}
		$tagsToFetch = rtrim($tagsToFetch, ',');

		$statement = ('SELECT * FROM tags WHERE id IN (' . $tagsToFetch . ')');
		$q = $this->_db->prepare($statement);
		$q->execute();

		$tags = $q->fetchAll();

		$result = [];
		foreach ($tags as $tag) {
			$newTag = new Tag(0);
			$newTag->hydrate($tag);
			$result[] = $newTag;
		}

		return ($result);
	}

	public function deleteTagLink($idUser, $idTag) {
		$q = $this->_db->prepare('DELETE FROM tags_users WHERE id_user = :id_user and id_tag = :id_tag');
		$q->bindValue(':id_user', $idUser, PDO::PARAM_INT);
		$q->bindValue(':id_tag', $idTag, PDO::PARAM_INT);
		$q->execute();
	}

	public function addLink($idUser, $idTag) {
		$q = $this->_db->prepare('SELECT count(*) FROM tags_users WHERE id_user = :id_user and id_tag = :id_tag');
		$q->bindValue(':id_user', $idUser, PDO::PARAM_INT);
		$q->bindValue(':id_tag', $idTag, PDO::PARAM_INT);
		$q->execute();

		$result = $q->fetch();
		if ($result[0] > 0) {
			return genError("taglink", "alreadyexist", "addlink");
		}

		$q = $this->_db->prepare('INSERT INTO tags_users(id_user, id_tag) VALUES(:id_user, :id_tag)');
		$q->bindValue(':id_user', $idUser, PDO::PARAM_INT);
		$q->bindValue(':id_tag', $idTag, PDO::PARAM_INT);
		$q->execute();
		return true;
	}
}