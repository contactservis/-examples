<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
	return;

"VARIABLE_ALIASES" => array( 
      "list" => array(),
      "section" => array(
                        "IBLOCK_ID" => "BID",
                        "SECTION_ID" => "ID"
                        ),
      "element" => array(
      "SECTION_ID" => "SID",
      "ELEMENT_ID" => "ID"
      ),
)

?>