<?
AddEventHandler("main", "OnEpilog", "error_page");
function error_page()
{
   $page_404 = "/404.php";
   global $APPLICATION;
   if (strpos($APPLICATION->GetCurPage(), $page_404) === false && defined("ERROR_404") && ERROR_404 == "Y") {
      $APPLICATION->RestartBuffer();
      CHTTP::SetStatus("404 Not Found");
      include($_SERVER["DOCUMENT_ROOT"] . $page_404);
      die();
   }
}
