<?php
/**
 * @var Menu $allMenu
 */
?>
<nav id="mainMenu" class="nav-container">
    <input type="checkbox" id="mobileMainmenuViewer" role="button">
    <label for="mobileMainmenuViewer" class="mobileMainmenuViewerLabel">
        <div></div><div></div><div></div>
    </label>
    <?=$allMenu->getShowMainMenu()?>
</nav>