<?php

	namespace mklive\dev\user;

	use mklive\dev\user\orm\CompanyTable;
    use mklive\dev\user\orm\DealerAdminTable;
    use mklive\dev\user\orm\DealerClientTable;
    use mklive\dev\user\orm\DealerManagerTable;
    use mklive\dev\user\orm\EO_User as VirtualUser;
    use mklive\dev\user\orm\PersonalTable;
    use mklive\dev\user\orm\UserTable as OrmlUser;

	use Bitrix\Main\ORM\Data\UpdateResult;
	use Bitrix\Main\ORM\Data\AddResult;

	use Bitrix\Main\Error;

	/**
	 * Абстрактный класс с заранее реализованными интерфейсными методами, который подключается к основному классу со своей логикой.
	 *
	 * Наследуется из виртуального класса с пользовательскими методами (через "__call") из ядра в 1С-Битрикс. Геттеры и сеттеры созданы с привязкой к пользовательским функциям, поэтому не видны. Чтобы просмотреть сформированные сеттеры и геттеры необходимо просмотреть поля описанные в ORM.
	 */
	abstract class UserAbstract extends VirtualUser {

	    protected static $type = "user";

	    protected $groups;

		/**
		 * Получаем объект пользователя на основе переданного ID.
		 *
		 * @param int $user_id
		 * @return type
		 */
		public static function load(int $user_id){
			return (new OrmlUser)->getByPrimary($user_id)->fetchObject();
		}

        /**
         */
        public static function getByLogin($login){

            return OrmlUser::getList([
                "filter" => [
                    "=LOGIN" => $login
                ]
            ])->fetchObject();

        }

		/**
		 * Метод добавляет пользователя. Переопределение метода ORM "add".
		 *
		 * @param array $data
		 */
		public static function __add(array $data){

			unset($data['__object']);

			// изменяем параметры пользователя
			$objUser = new \CUser;

			$isSuccess = $objUser->Add($data);

			// отдаём результат
			$result = new AddResult;

			if(!$isSuccess){ // если неуспешноx
				$result->addError(new Error($objUser->LAST_ERROR)); // прописываем ошибку из последней неуспешной операции
			} else { // если пользователь был создан, то отдаем его ID

				$result->setPrimary((int) $isSuccess); // получить можно через метод getPrimary() или getId()

				$result->setData(array('ID', $isSuccess)); // получить можно через метод getData()

			}

			return $result;

		}

		/**
		 * Аннотация для метода "__add".
		 *
		 * @param array $data
		 */
		public function add(){
			return $this->save();
		}

		/**
		 * Метод обновляет значения полей у пользователя. Переопределение метода ORM "update".
		 *
		 * @param array $primary
		 * @param array $data
		 */
		public static function __update($primary, array $data, $authAction = false){

			unset($data['__object']);

			// изменяем параметры пользователя
			$objUser = new \CUser;

			$isSuccess = $objUser->Update($primary['ID'], $data, $authAction);

			// отдаём результат
			$result = new UpdateResult;

			if(!$isSuccess){

				global $APPLICATION;

				$result->addError(new Error($APPLICATION->LAST_ERROR->msg)); // прописываем ошибку из последней неуспешной операции

			}

			return $result;

		}

		/**
		 * Метод удаляет пользователя. Переопределение метода ORM "delete".
		 *
		 * @param array $primary
		 */
		public static function __delete($primary){

			// отдаём результат
			$result = new UpdateResult;

			$user = new \CUser;

			// удаляем пользователя
			$isSuccess = $user->Delete($primary['ID']);

			if(!$isSuccess){

				global $APPLICATION;

				$result->addError(new Error($APPLICATION->LAST_ERROR->msg)); // прописываем ошибку из последней неуспешной операции

			}

			return $result;

		}

		/**
		 * Получаем объект коллекции.
		 */
		public static function fetchCollection($getListParams){

			self::sysSetNamespaceClass('User'); // устанавливаем текущий основной класс в качестве объекта модели

			return (new OrmlUser)->getList($getListParams)->fetchCollection();

		}


		/**
		 * Получаем объект коллекции групп или массив групп (если первый аргумент равен "false"). Переопределяем геттер битрикса на получение привязанных ID групп к пользователю (список ID).
		 */
		public function getGroups($object = true){

			// по умолчанию отдаём объект коллекции групп
			if($object){
				return parent::getGroups();
			} else { // если нам нужен массив, то формируем его

			    if (!is_array($this->groups)) {

			        $this->groups = (array) \CUser::GetUserGroup($this->getId());

                }

				return $this->groups;
			}

		}

		/**
		 * Устанавливаем группы пользователю.
		 */
		public function setGroups(array $groups){

			\CUser::SetUserGroup($this->getId(), $groups);

			return $this;

		}

		/**
		 * Метод получает случайный сгенерированный пароль.
		 *
		 * @return string
		 */
		public function getGeneratePassword($length = 8){

			// Символы, которые будут использоваться в пароле.
			$chars = 'qazxswedcvfrtgbnhyujmkiolp1234567890QAZXSWEDCVFRTGBNHYUJMKIOLP';

			// Определяем количество символов в $chars
			$size = StrLen($chars) - 1;

			// Определяем пустую переменную, в которую и будем записывать символы.
			$password = null;

			// Создаём пароль.
			for($i = 1; $i <= $length; $i++){
				$password .= $chars[rand(0, $size)];
			}

			return $password;

		}



	}
