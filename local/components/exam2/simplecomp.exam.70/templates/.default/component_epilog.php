<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if (isset($arResult["MIN_PRICE"]) && isset($arResult["MAX_PRICE"])) {
    $intoTemplates = "<div style=\"color:red; margin: 34px 15px 35px 15px\">#TEXT#</div>";
    $sText = "Минимальная цена: " . $arResult["MIN_PRICE"] . "</br>" . "Максимальная цена: " . $arResult["MAX_PRICE"];
    $finalTest = str_replace("#TEXT#", $sText, $intoTemplates);
    $APPLICATION->AddViewContent("prices", $finalTest);
}
