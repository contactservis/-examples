<?php

	namespace mklive\dev\controller;

	use Bitrix\Main\Context;

	/**
	 * Класс "Controller".
	 *
	 * Устанавливает необходимые параметры и запускает контроллеры.
	 */
	class Controller extends \CBitrixComponent {

		public function __construct(\CBitrixComponent $component = null){

			parent::__construct($component);

			$this->request = Context::getCurrent()->getRequest();

		}

		public $page = '';

		public $request;

		public function executeComponent() {

			if(strlen($this->arParams['controller']) <= 0){
				return false;
			}

			$methodName = $this->arParams['controller'].'Controller';

			if(!method_exists($this, $methodName)){
				throw new \Exception('CONTROLLER_NOT_FOUND');
			}

			$this->$methodName();

		}

	}
