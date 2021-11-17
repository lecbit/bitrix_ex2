<?
IncludeModuleLangFile(__FILE__);

// файл /bitrix/php_interface/init.php
// регистрируем обработчик
AddEventHandler("iblock", "OnBeforeIBlockElementUpdate", array("Ex2", "Ex2_50"));
AddEventHandler("main", "OnEpilog", array("Ex2", "Ex2_93"));


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
}
