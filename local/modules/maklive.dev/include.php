<?
global $DB;
IncludeModuleLangFile(__FILE__);

$MODULE_ID = "maklive.dev";

$absolute_path = __DIR__;
$sitePAth = $_SERVER["DOCUMENT_ROOT"];

$componentPath = str_replace($sitePAth, "", $absolute_path);

define("NANO_PATH", $componentPath);


$arClasses = array();


CModule::AddAutoloadClasses(
	$MODULE_ID,
	$arClasses
);
