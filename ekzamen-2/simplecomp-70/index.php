<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Простой компонент-ex2-70");
?><?$APPLICATION->IncludeComponent(
	"exam2:simplecomp.exam.70",
	"",
	Array(
		"CACHE_TIME" => "36000000",
		"CACHE_TYPE" => "A",
		"NEWS_IBLOCK_ID" => "1",
		"PRODUCTS_IBLOCK_ID" => "2",
		"PRODUCTS_IBLOCK_ID_PROPERTY" => "UF_NEWS_LINK"
	)
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>