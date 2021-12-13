<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader,
    Bitrix\Iblock;

if (!Loader::includeModule("iblock")) {
    ShowError(GetMessage("SIMPLECOMP_EXAM2_IBLOCK_MODULE_NONE"));
    return;
}

if (!isset($arParams["CACHE_TIME"]))
    $arParams["CACHE_TIME"] = 36000000;

if (!isset($arParams["PRODUCTS_IBLOCK_ID"]))
    $arParams["PRODUCTS_IBLOCK_ID"] = 0;

if (!isset($arParams["NEWS_IBLOCK_ID"]))
    $arParams["NEWS_IBLOCK_ID"] = 0;

$cFilter = false;

if (isset($_REQUEST["F"])) {
    $cFilter = true;
}

global $USER;
if ($USER->IsAuthorized()) {
    $arButtons = CIBlock::GetPanelButtons($arParams["PRODUCTS_IBLOCK_ID"]);
    $this->AddIncludeAreaIcons(array(
        array(
            "ID" => "linklb",
            "TITLE" => "ИБ в админке",
            "URL" => $arButtons["submenu"]["element_list"]["ACTION_URL"],
            "IN_PARAMS_MENU" => true
        )
    ));
}

global $CACHE_MANAGER;

$arNavigation = CDBResult::GetNavParams();

if ($this->StartResultCache(false, array($cFilter, $arNavigation), "/servicesIblock")) {
    $CACHE_MANAGER->RegisterTag("iblock_id_3");
    $arNews = array();
    $arNewsId = array();

    $obNews = CIBlockElement::GetList(
        array(),
        array(
            "IBLOCK_ID" => $arParams["NEWS_IBLOCK_ID"],
            "ACTIVE" => "Y"
        ),
        false,
        array(
            "nPageSize" => $arParams["ELEMENT_PER_PAGE"],
            "bShowAll" => true
        ),
        array(
            "NAME",
            "ACTIVE_FROM",
            "ID"
        )
    );

    $arResult["NAV_STRING"] = $obNews->GetPageNavString(GetMessage("PAGE_TITLE"));

    while ($newsElement = $obNews->Fetch()) {
        $arNewsId[] = $newsElement["ID"];
        $arNews[$newsElement["ID"]] = $newsElement;
    }


    $arSections = array();
    $arSectionsID = array();


    $obSection = CIBlockSection::GetList(
        array(),
        array(
            "IBLOCK_ID" => $arParams["PRODUCTS_IBLOCK_ID"],
            "ACTIVE" => "Y",
        ),
        false,
        array(
            "NAME",
            "IBLOCK_ID",
            "ID",
            $arParams["PRODUCTS_IBLOCK_ID_PROPERTY"]
        ),
        false
    );
    while ($arSectionCatalog = $obSection->Fetch()) {
        $arSectionsID[] = $arSectionCatalog["ID"];
        $arSections[$arSectionCatalog["ID"]] = $arSectionCatalog;
    }


    $arFilterElements = array(
        "IBLOCK_ID" => $arParams["PRODUCTS_IBLOCK_ID"],
        "ACTIVE" => "Y",
        "SECTION_ID" => $arSectionsID
    );

    if ($cFilter) {
        $arFilterElements[] = array(
            array("<=PROPERTY_PRICE" => 1700, "PROPERTY_MATERIAL" => "Дерево, ткань"),
            array("<=PROPERTY_PRICE" => 1500, "PROPERTY_MATERIAL" => "Металл, пластик"),
            "LOGIC" => "OR"
        );
        $this->abortResultCache();
    }

    $obProduct = CIBlockElement::GetList(
        array(
            "NAME" => "asc",
            "SORT" => "asc"
        ),
        $arFilterElements,
        false,
        false,
        array(
            "NAME",
            "IBLOCK_SECTION_ID",
            "ID",
            "CODE",
            "IBLOCK_ID",
            "PROPERTY_ARTNUMBER",
            "PROPERTY_MATERIAL",
            "PROPERTY_PRICE",
        )
    );

    $arResult["PRODUCT_CNT"] = 0;

    while ($arProduct = $obProduct->Fetch()) {
        $arButtons = CIBlock::GetPanelButtons(
            $arParams["PRODUCTS_IBLOCK_ID"],
            $arProduct["ID"],
            0,
            array("SECTION_BUTTONS" => false, "SESSID" => false)
        );

        $arProduct["EDIT_LINK"] = $arButtons["edit"]["edit_element"]["ACTION_URL"];
        $arProduct["DELETE_LINK"] = $arButtons["edit"]["delete_element"]["ACTION_URL"];

        $arResult["ADD_LINK"] = $arButtons["edit"]["add_element"]["ACTION_URL"];
        $arResult["IBLOCK_ID"] = $arParams["PRODUCTS_IBLOCK_ID"];


        $arProduct["DETAIL_PAGE_URL"] = str_replace(
            array(
                "#SECTION_ID#",
                "#ELEMENT_CODE#"
            ),
            array(
                $arProduct["IBLOCK_SECTION_ID"],
                $arProduct["CODE"]
            ),
            $arParams["TEMPLATE_DETAIL_URL"]
        );


        $arResult["PRODUCT_CNT"]++;
        foreach ($arSections[$arProduct["IBLOCK_SECTION_ID"]][$arParams["PRODUCTS_IBLOCK_ID_PROPERTY"]] as $newsId) {
            if (isset($arNews[$newsId])) {
                $arNews[$newsId]["PRODUCTS"][] = $arProduct;
            }
        }
    }


    foreach ($arSections as $arSection) {
        foreach ($arSection[$arParams["PRODUCTS_IBLOCK_ID_PROPERTY"]] as $newId) {
            if (isset($arResult[$newId])) {
                $arResult[$newId]["SECTIONS"][] = $arSection["NAME"];
            }
        }
    }

    $arResult["NEWS"] = $arNews;
    // $this->SetResultCacheKeys(array("PRODUCT_CNT")) -- Проверка кеша без данной строки;

    $this->SetResultCacheKeys(array("PRODUCT_CNT"));
    $this->includeComponentTemplate();
} else {
    $this->abortResultCache();
}

$APPLICATION->SetTitle(GetMessage("COUNT_70") . $arResult["PRODUCT_CNT"]);
