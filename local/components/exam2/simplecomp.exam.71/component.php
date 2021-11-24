<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader,
    Bitrix\Iblock;

if (!Loader::includeModule("iblock")) {
    ShowError(GetMessage("SIMPLECOMP_EXAM2_IBLOCK_MODULE_NONE"));
    return;
}

if (empty($arParams["CACHE_TIME"])) {
    $arParams["CACHE_TIME"] = 36000000;
}

if(empty($arParams["PRODUCTS_IBLOCK_ID"])){
    $arParams["PRODUCTS_IBLOCK_ID"] = 0;
}

if(empty($arParams["CLASSIF_IBLOCK_ID"])){
    $arParams["CLASSIF_IBLOCK_ID"] = 0;
}

$arParams["PROPERTY_CODE"] = trim($arParams["PROPERTY_CODE"]);

global $USER;

if ($this->startResultCache(false, array($USER->GetGroups()))) {
    $arClassif = array();
    $arClassifId = array();
    $arResult["COUNT"] = 0;
    // Получаем список классификаторов
    $arSelectElements = array(
        "ID",
        "IBLOCK_ID",
        "NAME",
    );
    $arFilterElements = array(
        "IBLOCK_ID" => $arParams["CLASSIF_IBLOCK_ID"],
        "CHECK_PERMISSIONS" => $arParams["CACHE_GROUPS"],
        "ACTIVE" => "Y"
    );

    $rsElements = CIBlockElement::GetList(array(), $arFilterElements, false, false, $arSelectElements);

    while ($arElement = $rsElements->GetNext()) {
        $arClassif[$arElement["ID"]] = $arElement;
        $arClassifId[] = $arElement["ID"];
    }
    $arResult["COUNT"] = count($arClassifId);

    // Получаем список элементов с привязками к классификатору
    $arSelectElementsCatalog = array(
        "ID",
        "IBLOCK_ID",
        "IBLOCK_SECTION_ID",
        "NAME",
        "DETAIL_PAGE_URL",
    );
    $arFilterElementsCatalog = array(
        "IBLOCK_ID" => $arParams["PRODUCTS_IBLOCK_ID"],
        "CHECK_PERMISSIONS" => $arParams["CACHE_GROUPS"],
        "PROPERTY_" . $arParams["PROPERTY_CODE"] => $arClassifId,
        "ACTIVE" => "Y"
    );

    $rsElements = CIBlockElement::GetList(array(), $arFilterElementsCatalog, false, false, $arSelectElementsCatalog);

    while ($rsEl = $rsElements->GetNextElement()) {
        $arField = $rsEl->GetFields();
        $arField["PROPERTY"] = $rsEl->GetProperties();

        foreach ($arField["PROPERTY"]["FIRMA"]["VALUE"] as $value) {
            $arClassif[$value]["ELEMENTS"][$arField["ID"]] = $arField;
        }
    }
    $arResult["CLASSIF"] = $arClassif;
    $this->SetResultCacheKeys(array("COUNT"));
    $this->includeComponentTemplate();
} else {
    $this->abortResultCache();
}
$APPLICATION->SetTitle(GetMessage("COUNT_71") . $arResult["COUNT"]);