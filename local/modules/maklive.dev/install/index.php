<?php

	use Bitrix\Main\Localization\Loc;

	Loc::loadMessages(__FILE__);

	class maklive_dev extends CModule {

		var $MODULE_ID = 'maklive.dev';

		var $MODULE_VERSION;
		var $MODULE_VERSION_DATE;

		var $MODULE_NAME;
		var $MODULE_DESCRIPTION;

		/**
		 * Конструктор класса.
		 */
		function maklive_dev(){

			$arModuleVersion = array();

			$path = str_replace("\\", "/", __FILE__);
			$path = substr($path, 0, strlen($path) - strlen('/index.php'));

			include($path.'/version.php');

			if(is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion)){

				$this->MODULE_VERSION = $arModuleVersion['VERSION'];

				$this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];

			}

			$this->MODULE_NAME = Loc::getMessage('NANODEV_MODULE_NAME');
			$this->MODULE_DESCRIPTION = Loc::getMessage('NANODEV_MODULE_DESCRIPTION');

			$this->PARTNER_URI  = 'http://www.maklive.ru';
			$this->PARTNER_NAME = 'maklive';

		}

		/**
		 * Установка файлов.
		 *
		 * @param type $arParams
		 * @return boolean
		 */
		function InstallFiles($arParams = array()){


			return true;

		}

		/**
		 * Удаление файлов.
		 *
		 * @return boolean
		 */
		function UnInstallFiles(){

			return true;

		}

		function DoInstall(){

			RegisterModule($this->MODULE_ID);

		}

		function DoUninstall(){

			UnRegisterModule($this->MODULE_ID);

		}

	}
