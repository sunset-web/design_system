<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var CBitrixComponent $this */
/** @var array $arParams */
/** @var array $arResult */
/** @var string $componentPath */
/** @var string $componentName */
/** @var string $componentTemplate */
/** @global CDatabase $DB */
/** @global CUser $USER */
/** @global CMain $APPLICATION */

use Bitrix\Main\Context,
	Bitrix\Main\Type\DateTime,
	Bitrix\Main\Loader,
	Bitrix\Iblock;

if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 36000000;

$arParams["IBLOCK_TYPE"] = trim($arParams["IBLOCK_TYPE"]);
if($arParams["IBLOCK_TYPE"] == '')
	$arParams["IBLOCK_TYPE"] = "news";

$arParams["ELEMENT_ID"] = intval($arParams["~ELEMENT_ID"]);
if($arParams["ELEMENT_ID"] > 0 && $arParams["ELEMENT_ID"]."" != $arParams["~ELEMENT_ID"])
{
	if (Loader::includeModule("iblock"))
	{
		Iblock\Component\Tools::process404(
			trim($arParams["MESSAGE_404"]) ?: GetMessage("T_NEWS_DETAIL_NF")
			,true
			,$arParams["SET_STATUS_404"] === "Y"
			,$arParams["SHOW_404"] === "Y"
			,$arParams["FILE_404"]
		);
	}
	return;
}

$arParams["IBLOCK_URL"]=trim($arParams["IBLOCK_URL"]);

$arParams["USE_PERMISSIONS"] = $arParams["USE_PERMISSIONS"]=="Y";
if(!is_array($arParams["GROUP_PERMISSIONS"]))
	$arParams["GROUP_PERMISSIONS"] = array(1);

$bUSER_HAVE_ACCESS = !$arParams["USE_PERMISSIONS"];
if($arParams["USE_PERMISSIONS"] && isset($USER) && is_object($USER))
{
	$arUserGroupArray = $USER->GetUserGroupArray();
	foreach($arParams["GROUP_PERMISSIONS"] as $PERM)
	{
		if(in_array($PERM, $arUserGroupArray))
		{
			$bUSER_HAVE_ACCESS = true;
			break;
		}
	}
}

if(!$bUSER_HAVE_ACCESS)
{
	ShowError(GetMessage("T_NEWS_DETAIL_PERM_DEN"));
	return 0;
}

if($arParams["SHOW_WORKFLOW"] || $this->startResultCache(false, array(($arParams["CACHE_GROUPS"]==="N"? false: $USER->GetGroups()),$bUSER_HAVE_ACCESS, $arNavigation, $pagerParameters)))
{

	if(!Loader::includeModule("iblock"))
	{
		$this->abortResultCache();
		ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
		return;
	}
	if($arParams["ELEMENT_ID"] <= 0)
		$arParams["ELEMENT_ID"] = CIBlockFindTools::GetElementID(
			$arParams["ELEMENT_ID"],
			$arParams["~ELEMENT_CODE"],
			$arParams["STRICT_SECTION_CHECK"]? $arParams["SECTION_ID"]: false,
			$arParams["STRICT_SECTION_CHECK"]? $arParams["~SECTION_CODE"]: false,
			$arFilter
		);

	$arSelect = array_merge($arParams["FIELD_CODE"], array(
		"ID",
		"NAME",
		"IBLOCK_ID",
		"IBLOCK_SECTION_ID",
	));

	$arFilter["ID"] = $arParams["ELEMENT_ID"];
	$rsElement = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
	if($obElement = $rsElement->GetNextElement())
	{
		$arResult = $obElement->GetFields();
		$ipropValues = new Iblock\InheritedProperty\ElementValues($arResult["IBLOCK_ID"], $arResult["ID"]);

		$arResult["FIELDS"] = array();
		foreach($arParams["FIELD_CODE"] as $code)
			if(array_key_exists($code, $arResult))
				$arResult["FIELDS"][$code] = $arResult[$code];

		$arResult["IBLOCK"] = GetIBlock($arResult["IBLOCK_ID"], $arResult["IBLOCK_TYPE"]);

		$resultCacheKeys = array(
			"ID",
			"IBLOCK_ID",
			"NAV_CACHED_DATA",
			"NAME",
			"IBLOCK_SECTION_ID",
			"IBLOCK",
			"LIST_PAGE_URL", "~LIST_PAGE_URL",
			"SECTION_URL",
			"CANONICAL_PAGE_URL",
			"SECTION",
			"IPROPERTY_VALUES",
			"TIMESTAMP_X",
		);
		$this->setResultCacheKeys($resultCacheKeys);

		$files = CIBlockElement::GetProperty($arResult['IBLOCK_ID'], $arResult['ID'], "sort", "asc", array("CODE" => $arParams['PROPERTY_CODE']));
			while ($ob = $files->GetNext()){
				$VALUES[] = $ob['VALUE'];
			}
		foreach ($VALUES as $fileID) {
			$link = CFile::GetPath($fileID);
			$file = CFile::GetByID($fileID);
			$file=$file->Fetch();
			$size = CFile::FormatSize($file['FILE_SIZE'],0);
			$fullName = $file['ORIGINAL_NAME'];
			$type = strstr($file['FILE_NAME'], '.');
			$name = str_replace($type, "", $file['ORIGINAL_NAME']);
			$type = str_replace(".", "", $type);
			$iconFile = "/local/templates/upfly/img/icons.files/" . $type . ".svg";

			$arFile[$fileID] = array(
				'FULL_NAME' => $fullName,
				'NAME' => $name,
				'TYPE' => $name,
				'LINK' => $link,
				'SIZE' => $size,
				'ICON' => $iconFile,
			);
		}
		if($arParams['VARIABLES_TO_DO'] == 0)$arResult['FILES_PROPERTIES'] = $arFile;
		if($arParams['VARIABLES_TO_DO'] == 1)$arResult['FILES_PROPERTIES'] = reset($arFile);


		if(CModule::IncludeModule('iblock')){
			$res = CIBlockElement::GetList(
				Array(), 
				Array("IBLOCK_TYPE"=>$arResult['IBLOCK_TYPE'], "IBLOCK_ID"=>$arResult['IBLOCK_ID'] , "ID"=>$arResult['ID']), 
			);

			if ($ob = $res->GetNextElement()){; // переходим к след элементу, если такой есть
				$arProps = $ob->GetProperties(); // свойства элемента
			}
			$arResult['ALL_PROPERTIES'] = $arProps;
		}
		$this->includeComponentTemplate();
	}
	else
	{
		$this->abortResultCache();
		Iblock\Component\Tools::process404(
			trim($arParams["MESSAGE_404"]) ?: GetMessage("T_NEWS_DETAIL_NF")
			,true
			,$arParams["SET_STATUS_404"] === "Y"
			,$arParams["SHOW_404"] === "Y"
			,$arParams["FILE_404"]
		);
	}
}

if(isset($arResult["ID"])){return $arResult["ID"];}else{return 0;}