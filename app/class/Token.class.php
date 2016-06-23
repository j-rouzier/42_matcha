<?php
Class Token {
	private $_id;
	private $_user_id;
	private $_token;
	private $_time_created;
	private $_usefor;
	private $_isUsed;

	use commomMembers;

	public function getId() { return $this->_id; }
	public function getUser_id() { return $this->_user_id; }
	public function getToken() { return $this->_token; }
	public function getTime_created() { return $this->_time_created; }
	public function getUsefor() { return $this->_usefor; }
	public function getIsUsed() { return $this->_isUsed; }

	public function setUser_id($id) {
		if (is_numeric($id)) {
			$this->_user_id = $id;
			return true;
		}
		return genError("token", "invalid", "userid");
	}
	
	public function setToken($token) {
		$this->_token = $token;
		return true;
	}

	public function setTime_created($time) {
		if (!Utilities::isDigits($register_time) || $register_time <= 0) {
			return genError("token", "invalid", "time_created");
		}
		$this->_register_time = $register_time;
		return true;
	}

	public function setUsefor($usefor) {
		if (strlen($usefor) > 20 || !ctype_alpha($usefor)) {
			return genError("token", "invalid", "usefor");
		}
		$this->_usefor = $usefor;
		return true;
	}

	public function setIsUsed($isused) {
		if ($isused) {
			$this->_isUsed = true;
		} else {
			$this->_isUsed = false;
		}
		return true;
	}
}