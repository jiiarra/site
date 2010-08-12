<?php
class Oibs_Controller_Plugin_Toplist_Cities extends Oibs_Controller_Plugin_TopList {
	
	private		$_usersWithCity = array();
	private		$_name = "cities";
	
	
	public function __construct() {
		parent::__construct();
	}
	
	public function autoSet() {
		foreach($this->_topLists as $name => $info) {
			$this->setTop($name);
		}
		$this->addDescriptions();
		$this->addTitleLinks();
		$this->addTitles();
		
		return $this;
	}

	public function fetchUsersWithCity() {
		$this->_usersWithCity = $this->_userProfileModel->getUsersWithCity($this->_userList);
		return $this;
	}
	
	public function setTop($choice) {
		try { $this->_initializeTop($choice); }
		catch (Exception $e) { echo "Exception: ".$e->getMessage(); }
	
		try {
			if(!empty($this->_usersWithCity)) {
				$temp = $this->_getChoiceValue($choice,array_keys($this->_usersWithCity));
				$this->_topListIds[$choice]['users'] = $this->_intersectMergeArray($temp,array_values($this->_usersWithCity));
				$this->_makeToCityTops($choice);
				
			}
			else {
				$error = "Cities not fetched.";
				throw new Exception($error);
			}
		}
		 catch (Exception $e) {
			echo "Exception: ".$e->getMessage();
		}
		
	}
	
	private function _makeToCityTops($choice) {
		if(isset($this->_topListIds[$choice])) {
			$final = array();
			foreach($this->_topListIds[$choice]['users'] as $data) {
				$city = mb_convert_case($data['city'], MB_CASE_TITLE, "UTF-8");
				if(!isset($final[$city])) {
					$final[$city]['name'] = $city;
					$final[$city]['value'] = $data['value'];
				}
				else {
					$final[$city]['value'] += $data['value'];
				}
			}
			
			$this->_topList[$choice][$this->_name] = $final;
			$this->_valueSort($this->_name,$choice);
			$this->_cutToLimit($this->_name,$choice);
			$this->_topList[$choice][$this->_name] = array_values($this->_topList[$choice][$this->_name]);
			
		}
		else {
			$this->_topList[$choice][$this->_name] = array("No users");
		}
		$this->_topList[$choice]['name'] = $choice; 
		return;
	}
}