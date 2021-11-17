<?
IncludeModuleLangFile(__FILE__);

// файл /bitrix/php_interface/init.php
// регистрируем обработчик
AddEventHandler("iblock", "OnBeforeIBlockElementUpdate", array("Ex2", "Ex2_50"));
AddEventHandler("main", "OnEpilog", array("Ex2", "Ex2_93"));
AddEventHandler("main", "OnBeforeEventAdd", array("Ex2", "ex2_51"));
AddEventHandler("main", "OnBuildGlobalMenu", array("Ex2", "ex2_95"));
AddEventHandler("main", "OnBeforeProlog", array("Ex2", "ex2_94"));



class Ex2
{
    // создаем обработчик события "OnBeforeIBlockElementUpdate"
    function Ex2_50(&$arFields)
    {
        if ($arFields["IBLOCK_ID"] == ID_BLOCK_CATALOG) {
            if ($arFields["ACTIVE"] == "N") {
                $arSelect = array(
                    "ID",
                    "IBLOCK_ID",
                    "NAME",
                    "SHOW_COUNTER"
                );
                $arFilter = array(
                    "IBLOCK_ID" => ID_BLOCK_CATALOG,
                    "ID" => $arFields["ID"]
                );
                $res = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
                $arItems = $res->Fetch();

                if ($arItems["SHOW_COUNTER"] > MAX_COUNT) {
                    global $APPLICATION;
                    $sText = GetMessage("EX2_50_text", array("#COUNT#" => $arItems["SHOW_COUNTER"]));
                    $APPLICATION->throwException($sText);
                    return false;
                }
            }
        }
    }

    function Ex2_93()
    {
        if (defined("ERROR_404") && ERROR_404 == "Y") {
            global $APPLICATION;
            $APPLICATION->RestartBuffer();
            include $_SERVER["DOCUMENT_ROOT"] . SITE_TEMPLATE_PATH . "/header.php";
            include $_SERVER["DOCUMENT_ROOT"] . "/404.php";
            include $_SERVER["DOCUMENT_ROOT"] . SITE_TEMPLATE_PATH . "/footer.php";

            CEventLog::Add(array(
                "SEVERITY" => "SECURITY",
                "AUDIT_TYPE_ID" => "ERROR 404",
                "MODULE_ID" => "main",
                "ITEM_ID" => 123,
                "DESCRIPTION" => $APPLICATION->GetCurPage(),
            ));
        }
    }

    function ex2_51(&$event, &$lid, &$arFields)
    {
        if ($event == "FEEDBACK_FORM") {

            global $USER;
            if ($USER->isAuthorized()) {

                $arFields["AUTHOR2"] = GetMessage("EX2_S1_AUTH_USER", array(
                    "#ID#" => $USER->GetId(),
                    "#LOGIN#" => $USER->GetLogin(),
                    "#NAME#" => $USER->GetFullName(),
                    "#NAME_FORM#" => $arFields["AUTHOR"]
                ));
            } else {
                $arFields["AUTHOR2"] = GetMessage("EX2_S1_NO_AUTH_USER", array(
                    "#NAME_FORM#" => $arFields["AUTHOR"]
                ));
            }
            CEventLog::Add(array(
                "SEVERITY" => "SECURITY",
                "AUDIT_TYPE_ID" => "FORMA",
                "MODULE_ID" => "main",
                "ITEM_ID" => $event,
                "DESCRIPTION" => "CeventLog" . print_r($arFields["AUTHOR2"]),
            ));
        }
    }
    function ex2_95(&$aGlobalMenu, &$aModuleMenu)
    {
        $isAdmin = false;
        $isManager = false;

        global $USER;
        $userGroup = CUSER::GetUserGroupList($USER->GetID());
        $contentGroupID = CGroup::GetList(
            $by = "c_sort",
            $order = "asc",
            array("STRING_ID" => "content_editor")
        )->Fetch()["ID"];
        while ($group = $userGroup->Fetch()) {
            if ($group["GROUP_ID"] == 1) {
                $isAdmin = true;
            }
            if ($group["GROUP_ID"] == $contentGroupID) {
                $isManager = true;
            }
        }


        if (!$isAdmin && $isManager) {

            foreach ($aModuleMenu as $key => $item) {

                if ($item["items_id"] == "menu_iblock_/news") {
                    $aModuleMenu = [$item];

                    foreach ($item["items"] as $childItem) {

                        if ($childItem["items_id"] == "menu_iblock_/news/1") {
                            $aModuleMenu[0]["items"] = [$childItem];
                            break;
                        }
                    }
                    break;
                }
            }
            $aGlobalMenu = ["global_menu_content" => $aGlobalMenu["global_menu_content"]];
        }
    }
    function ex2_94()
    {
        global $APPLICATION;
        $curPage = $APPLICATION->GetCurDir();

        if (\Bitrix\Main\Loader::includeModule("iblock")) {
            $arSelect = array(
                "IBLOCK_ID",
                "ID",
                "PROPERTY_title",
                "PROPERTY_description"
            );
            $arFilter = array(
                "IBLOCK_ID" => IBLOCK_META,
                "NAME" => $curPage
            );

            $ob = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
            if ($arRes = $ob->Fetch()) {
                print_r($arRes);
                $APPLICATION->SetPageProperty('title', $arRes["PROPERTY_TITLE_VALUE"]);
                $APPLICATION->SetPageProperty('description', $arRes["PROPERTY_DESCRIPTION_VALUE"]);
            }
        }
    }
}
