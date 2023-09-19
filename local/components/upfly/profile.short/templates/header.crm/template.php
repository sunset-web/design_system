<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
?>

<a href="<?=$arParams["USER_PROFILE_LINK"];?>" class="menu-top__user">
    <div class="menu-top__user-name">
        <?=$arResult["USER"]["LAST_NAME"].' '.$arResult["USER"]["NAME"];?>
    </div>
    <div class="menu-top__user-img">
        <?if($arResult["USER"]["PERSONAL_PHOTO"]) {?>
            <img src="<?=getResizePhoto($arResult["USER"]["PERSONAL_PHOTO"], 500, 500);?>" alt="personal photo">
        <?} else {?>
            <img src="<?=$arResult["USER"]["PERSONAL_GENDER"] == 'M' ? '/upload/images/areas/crm/male_avatar.png' : '/upload/images/areas/crm/female_avatar.png';?>" alt="personal photo">
        <?}?>      
    </div>
</a>

