<?php

	namespace mklive\dev\user\orm;

	use Bitrix\Main\Entity\DataManager;

	use Bitrix\Main\ORM\Fields;


	/**
	 * ORM для таблицы `b_uts_user` пользовательские свойства.
	 */
	class PropertyUserTable extends DataManager {

		public static function getTableName(){
			return 'b_uts_user';
		}

		public static function getMap(){

			return array(

				new Fields\IntegerField('USER_ID', [

					'primary' => true,
					'is_unique' => true,
					'column_name' => 'VALUE_ID'

				]),

			);

		}

	}
