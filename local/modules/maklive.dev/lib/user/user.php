<?php

	namespace mklive\dev\user;

	/**
	 * Класс для работы с пользователями. Содержит пользовательские методы (доступные только для этого класса).
	 */
	class User extends UserAbstract {

	    public function setSecureID($secureID) {

	        $this->setPersonalPager($secureID);

        }

        public function getSecureID() {

	        return $this->getPersonalPager();

        }

	}
