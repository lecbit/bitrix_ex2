<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Простой компонент-ex2-70");
?><?$APPLICATION->IncludeComponent(
	"exam2:simplecomp.exam.70", 
	".default", 
	array(
		"CACHE_TIME" => "36000000",
		"CACHE_TYPE" => "A",
		"NEWS_IBLOCK_ID" => "1",
		"PRODUCTS_IBLOCK_ID" => "2",
		"PRODUCTS_IBLOCK_ID_PROPERTY" => "UF_NEWS_LINK",
		"COMPONENT_TEMPLATE" => ".default",
		"TEMPLATE_DETAIL_URL" => "catalog_exam/#SECTION_ID#/#ELEMENT_CODE#",
		"ELEMENT_PER_PAGE" => "2"
	),
	false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>