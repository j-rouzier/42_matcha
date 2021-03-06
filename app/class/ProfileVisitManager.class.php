<?php

class ProfileVisitManager {
	protected $_db;

	public function __construct($db) {
		$this->_db = $db;
	}

	private function create(ProfileVisit $profile) {
		$q = $this->_db->prepare('
			INSERT INTO user_visits(id, idUser, idProfileVisited, time)
			VALUES(:id, :idUser, :idProfileVisited, :time)');
		$q->bindValue(':id', $profile->getId(), PDO::PARAM_INT);
		$q->bindValue(':idUser', $profile->getIdUser(), PDO::PARAM_INT);
		$q->bindValue(':idProfileVisited', $profile->getIdProfileVisited(), PDO::PARAM_INT);
		$q->bindValue(':time', time(), PDO::PARAM_INT);

		$q->execute();

		$profile->setId($this->_db->lastInsertId());
		return ($profile->getId());
	}

	public function addVisit($fromUser, $toUser) {
		if (!$this->ifProfileIsVisitedByUser($toUser, $fromUser)) {
			$visit = [];
			$visit["IdUser"] = $fromUser;
			$visit["IdProfileVisited"] = $toUser;

			$visitObject = new ProfileVisit(0);
			$visitObject->hydrate($visit);
			$this->create($visitObject);
		} else {
			$visitObject = $this->getWithoutId($fromUser, $toUser);
			
			$visitObject->setTime(time());
			$this->update($visitObject);
		}
	}

	public function update(ProfileVisit $profile) {
		$q = $this->_db->prepare('
			UPDATE user_visits
			SET idUser = :idUser, id = :id, idProfileVisited = :idProfileVisited, time = :time
			WHERE id = :id');
		$q->bindValue(':id', $profile->getId(), PDO::PARAM_INT);
		$q->bindValue(':idUser', $profile->getIdUser(), PDO::PARAM_INT);
		$q->bindValue(':idProfileVisited', $profile->getIdProfileVisited(), PDO::PARAM_INT);
		$q->bindValue(':time', $profile->getTime(), PDO::PARAM_INT);

		$q->execute();
	}

	public function get( $id ) {
		$q = $this->_db->prepare('SELECT * FROM user_visits WHERE id = :id');
		$q->bindValue(':id', $id, PDO::PARAM_INT);
		$q->execute();

		$donnees = $q->fetch();

		if ($q->rowCount() > 0) {
			$profile = new ProfileVisit($donnees['id']);
			$profile->hydrate($donnees);
			return ($profile);
		} else {
			return false;
		}
	}

	public function getWithoutId($fromUser, $toUser) {
		$query = "SELECT * FROM user_visits WHERE idProfileVisited = :idProfileVisited AND idUser = :idUser";
		$q = $this->_db->prepare($query);
		$q->bindValue(':idProfileVisited', $toUser, PDO::PARAM_INT);
		$q->bindValue(':idUser', $fromUser, PDO::PARAM_INT);
		$q->execute();

		$data = $q->fetch();
		$profile = new ProfileVisit(0);
		$profile->hydrate($data);

		return ($profile);
	}

	public function getListOfUserVisits($user) {
		$query = "SELECT * FROM user_visits WHERE idProfileVisited = :idProfileVisited ORDER BY time DESC";
		$q = $this->_db->prepare($query);
		$q->bindValue(':idProfileVisited', $user, PDO::PARAM_INT);
		$q->execute();

		$result = array();
		while($data = $q->fetch()) {
			$profile = new ProfileLike(0);
			$profile->hydrate($data);
			$result[] = $profile;
		}
		return ($result);
	}

	public function getNumberOfVisits($user) {
		$query = "SELECT COUNT(*) FROM user_visits WHERE idProfileVisited = :idProfileVisited";
		$q = $this->_db->prepare($query);
		$q->bindValue(':idProfileVisited', $user, PDO::PARAM_INT);
		$q->execute();

		$result = $q->fetch();
		return ($result[0]);
	}

	public function ifProfileIsVisitedByUser($profileVisited, $byUser) {
		$query = "SELECT COUNT(*) FROM user_visits WHERE idProfileVisited = :idProfileVisited AND idUser = :idUser";
		$q = $this->_db->prepare($query);
		$q->bindValue(':idProfileVisited', $profileVisited, PDO::PARAM_INT);
		$q->bindValue(':idUser', $byUser, PDO::PARAM_INT);
		$q->execute();

		$result = $q->fetch();
		return ($result[0]);
	}

	public function delete( $id ) {		
		$q = $this->_db->prepare('DELETE FROM user_visits WHERE id = :id');
		$q->bindValue(':id', $id, PDO::PARAM_INT);
		$q->execute();

		return true;
	}
	public function deleteWithoutId($profileVisited, $byUser) {		
		$q = $this->_db->prepare('DELETE FROM user_visits WHERE idProfileVisited = :idProfileVisited AND idUser = :idUser');
		$q->bindValue(':idProfileVisited', $profileVisited, PDO::PARAM_INT);
		$q->bindValue(':idUser', $byUser, PDO::PARAM_INT);
		$q->execute();

		return true;
	}
}