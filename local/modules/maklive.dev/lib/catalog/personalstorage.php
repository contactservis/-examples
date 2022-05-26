<?php

namespace Maklive\Dev\Catalog;
use Bitrix\Main\GroupTable;
\Bitrix\Main\Loader::includeModule('catalog');
\Bitrix\Main\Loader::IncludeModule('iblock');

/**
 * Personalstorage класс для работы с каталогом в личном кабинете
 */
class Personalstorage {
    
        
    /**
     * isDistr - метод возвращает true если пользователь дистрибьютор
     *
     * @return void
     */
    public static function isDistr()
	{
		$arOptGroup = GroupTable::getList(["filter" => ['STRING_ID' => 'Distributor']])->fetch();
		return in_array( $arOptGroup['ID'], \Bitrix\Main\Engine\CurrentUser::get()->getUserGroups() );
	}

    /**
     * isSubDistr - метод возвращает true если пользователь СубДистрибьютор
     *
     * @return void
     */
    public static function isSubDistr()
	{
		$arOptGroup = GroupTable::getList(["filter" => ['STRING_ID' => 'SubDistributor']])->fetch();
		return in_array($arOptGroup['ID'], \Bitrix\Main\Engine\CurrentUser::get()->getUserGroups());
	}
    
    /**
     * getProductStorageID - получить товары со склада по ID
     *
     * @param  mixed $ID_PRODUCT - ID продукта
     * @param  mixed $ID_STORAGE - ID склада
     * @return void
     */
    public function getProductStorageID($ID_PRODUCT = 0, $ID_STORAGE = 0){
        
        $rsStoreProduct = \Bitrix\Catalog\StoreProductTable::getList(array(
            'filter' => array(
                'STORE_ID'  => $ID_STORAGE, 
                'PRODUCT_ID'=> $ID_PRODUCT
            )
        ))->fetch();
        
        if( !empty($rsStoreProduct) ){
            $result = $rsStoreProduct;
        }else{
            $result = [
                'ERROR'         => true,
                'ERROR_TYPE'    => 'NULL',
                'ERROR_DESC'    => 'Не найдено записей ищщ'
            ];
        }
        
        return $result;
    }
    
    /**
     * getProducstStorageID - получить остатки по массиву ID товаров со склада ID
     *
     * @param  mixed $arID_PRODUCT
     * @param  mixed $ID_STORAGE
     * @return void
     */
    public function getProducstStorageID( $ID_STORAGE = 0){
        $rsStoreProduct = \Bitrix\Catalog\StoreProductTable::getList(array(
            'filter' => array(
                'STORE_ID'  => $ID_STORAGE
            )
        ));
        
        if( !empty($rsStoreProduct) ){
            while ( $arStoreProduct = $rsStoreProduct->fetch() ) {
                $result[$arStoreProduct['ID']] = $arStoreProduct;
            }            
        }else{
            $result = [
                'ERROR'         => true,
                'ERROR_TYPE'    => 'NULL',
                'ERROR_DESC'    => 'Не найдено записей ищщ'
            ];
        }
        
        return $result;
    }
      
    /**
     * addProductStorageID - метод добавлет на склад 
     *
     * @param  mixed $PRODUCT_ID
     * @param  mixed $STORE_ID
     * @param  mixed $AMOUNT
     * @return void
     */
    public function addProductStorageID($PRODUCT_ID, $STORE_ID, $AMOUNT ){
        $arFields = Array(
            "PRODUCT_ID" => $PRODUCT_ID,
            "STORE_ID" => $STORE_ID,
            "AMOUNT" => $AMOUNT,
        );
        
        $ID = \CCatalogStoreProduct::Add($arFields);
        return $ID;
    }
    
    /**
     * upProductStorageID - метод обновляет количество товара на определенном складе
     *
     * @param  mixed $PRODUCT_ID
     * @param  mixed $STORE_ID
     * @param  mixed $AMOUNT
     * @return void
     */
    public function upProductStorageID($PRODUCT_ID, $STORE_ID, $AMOUNT ){

        $rsStoreProduct = \Bitrix\Catalog\StoreProductTable::getList(array(
            'filter' => array(
                'STORE_ID'  => $ID_STORAGE, 
                'PRODUCT_ID'=> $ID_PRODUCT
            )
        ))->fetch();

        $arFields = Array(
            "PRODUCT_ID" => $PRODUCT_ID,
            "STORE_ID" => $STORE_ID,
            "AMOUNT" => $AMOUNT,
        );

        if( !empty( $rsStoreProduct['ID'] ) ){
            $ID = CCatalogStoreProduct::Update($rsStoreProduct['ID'], $arFields);
            return $ID;
        }else {
            return $this->addProductStorageID($PRODUCT_ID, $STORE_ID, $AMOUNT );
        }
    }
}