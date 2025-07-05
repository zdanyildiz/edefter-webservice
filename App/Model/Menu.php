<?php
/**
 * Table: menu
 * Columns:
 * menuid int AI PK
 * dilid int
 * menukategori tinyint UN // menünün sitedeki konumunu gösterir. 1: topmenu, 2: mainmenu, 3: footermenu
 * ustmenuid int // 0 ise ana menü değil ise submenüdür. menuid ile eşleşir.
 * menukatman tinyint UN // menünün kaçıncı katmanda olduğunu gösterir
 * menuad varchar(100)
 * menulink varchar(500)
 * menusira tinyint UN // menünün yatay sırasını gösterir <ul><li>1</li><li>2</li></ul>
 * altkategori tinyint(1) // 0 değilse bu bir kategori menüsüdür ve kategorinin altkategorileri seo bilgileriyle getirilir
 * menubenzersizid varchar(20) // panelden menüyü oluştururken atanan benzersiz id
 * orjbenzersizid varchar(20) // menü sayfa ya da kategori ise benzersizid burada tutulur
 */

/**
 * Örnek mainMenu:
 * Array ( [22] => Array ( [menuid] => 3836 [dilid] => 1 [menukategori] => 1 [ustmenuid] => 0 [menukatman] => 0 [menuad] => Hasır Bantlar [menulink] => /hasir-bantlar/3473m.html [menusira] => 1 [altkategori] => 1 [menubenzersizid] => LI2U9YFE2KKQNSOA4MUZ [orjbenzersizid] => YZPNHAE7R4V9JU8DTQBM ) [23] => Array ( [menuid] => 3837 [dilid] => 1 [menukategori] => 1 [ustmenuid] => 0 [menukatman] => 0 [menuad] => Bakla Bantlar [menulink] => /bakla-bantlar/3459m.html [menusira] => 2 [altkategori] => 1 [menubenzersizid] => SFLWBVCP3WWZ8EDM1PG6 [orjbenzersizid] => GZCDJWXM6248FRYPV9QS ) [24] => Array ( [menuid] => 3838 [dilid] => 1 [menukategori] => 1 [ustmenuid] => 0 [menukatman] => 0 [menuad] => Kasnaklar ve Dişliler [menulink] => /kasnaklar-ve-disliler/3470m.html [menusira] => 3 [altkategori] => 0 [menubenzersizid] => MJA812D4FP5UEEKTLMOH [orjbenzersizid] => Q39NRDJUT57BFZYA62HX ) [25] => Array ( [menuid] => 3839 [dilid] => 1 [menukategori] => 1 [ustmenuid] => 0 [menukatman] => 0 [menuad] => Pabuçlar ve Ayaklar [menulink] => /pabuclar-ve-ayaklar/3449m.html [menusira] => 4 [altkategori] => 1 [menubenzersizid] => BBAIZ7D1ZU7613KETOE2 [orjbenzersizid] => V47YFCQHNBZ2R8DGKWSJ ) [26] => Array ( [menuid] => 3840 [dilid] => 1 [menukategori] => 1 [ustmenuid] => 0 [menukatman] => 0 [menuad] => Modüler Konveyor Elemanlar [menulink] => /moduler-konveyor-elemanlar/3452m.html [menusira] => 5 [altkategori] => 1 [menubenzersizid] => FPKO76EVQFOEH4X7CZJK [orjbenzersizid] => SDVXPZGRF9U854MQY2N3 ) [27] => Array ( [menuid] => 3841 [dilid] => 1 [menukategori] => 1 [ustmenuid] => 0 [menukatman] => 0 [menuad] => Sürtünme Profilleri ve Zincir Kızakları [menulink] => /surtunme-profilleri-ve-zincir-kizaklari/3450m.html [menusira] => 6 [altkategori] => 1 [menubenzersizid] => JC3XK1LFPNKNHWDDOQB0 [orjbenzersizid] => RDHTKQ6AG827JXP4EMZY ) [28] => Array ( [menuid] => 3842 [dilid] => 1 [menukategori] => 1 [ustmenuid] => 0 [menukatman] => 0 [menuad] => Aliminyum Profilleri ve Elemanları [menulink] => /aliminyum-profilleri-ve-elemanlari/3451m.html [menusira] => 7 [altkategori] => 1 [menubenzersizid] => 1V1D4EY0LY6FZH7AN0XB [orjbenzersizid] => QJ8UYZSNXTB4V72MCEFP ) [29] => Array ( [menuid] => 3843 [dilid] => 1 [menukategori] => 1 [ustmenuid] => 0 [menukatman] => 0 [menuad] => Kolay Konveyör Tasarımı [menulink] => /konveyorler/3485m.html [menusira] => 8 [altkategori] => 1 [menubenzersizid] => OPBQQKS9O33SNSK9NUX7 [orjbenzersizid] => 9KV7QZDG6UC85TRBAJMH ) )
 */

class Menu {

    private $db;
    private $config;
    private $languageID;
    private $json;
    private $allMenu;
    public $topMenu;
    public $mainMenu;
    public $leftAsideMenu;
    public $rightAsideMenu;
    public $footerMenu;

    public $showMainMenu;
    public $showFooterMenu;

    public function __construct($db,$config,$json,$languageID) {
        $this->db = $db;
        $this->config = $config;
        $this->languageID = $languageID;
        $this->json = $json;
        $this->getMenuByLanguage();
        $this->separateMenuByCategory();
        $this->setShowMainMenu($this->showMainMenu());
        $this->setShowFooterMenu($this->showFooterMenu());
        $this->setShowTopMenu($this->showTopMenu());
    }

    public function __destruct()
    {
        unset($this->db);
    }

    public function getMenuByLanguage() {
        $languageID = $this->languageID;
        $menuData = $this->json->readJson(["Menu","menu_$languageID"]);
        if($menuData !== null) {
            $this->allMenu = $menuData;
        }
        else {
            $menuSql = "SELECT * FROM menu WHERE dilid = :lang";
            $result = $this->db->select($menuSql,["lang" => $languageID]);
            if (empty($result)) {
                Log::write("Menu bulunamadı","warning");
                $this->allMenu = [];
                return;
            }
            $menu = $result;

            $this->json->createJson(["Menu","menu_$languageID"],$menu);
            $this->allMenu = $menu;
        }
    }

    public function separateMenuByCategory() {
        $menu = $this->allMenu;
        //print_r($menu);exit();
        //$allMenu içindeki menukategoriye göre ayırır
        //0 ise top,1 ise main,2 ise sol aside,3 ise sağ aside,4 ise footer
        $topMenu = array_filter($menu, function($menu) {
            return $menu['menukategori'] == 0;
        });
        $this->topMenu = $topMenu;

        $mainMenu = array_filter($menu, function($menu) {
            return $menu['menukategori'] == 1;
        });
        $this->mainMenu = $mainMenu;

        $leftAsideMenu = array_filter($menu, function($menu) {
            return $menu['menukategori'] == 2;
        });
        $this->leftAsideMenu = $leftAsideMenu;

        $rightAsideMenu = array_filter($menu, function($menu) {
            return $menu['menukategori'] == 3;
        });
        $this->rightAsideMenu = $rightAsideMenu;

        $footerMenu = array_filter($menu, function($menu) {
            return $menu['menukategori'] == 4;
        });
        $this->footerMenu = $footerMenu;
    }

    private function getSubmenuItems($menuId, $menuItems) {
        //print_r($menuItems);exit();
        $subMenuItems = array_filter($menuItems, function($item) use ($menuId) {
            return $item['ustmenuid'] == $menuId;
        });
        //print_r($subMenuItems);exit();
        // Her alt menü öğesi için alt menüleri de oluşturalım.
        foreach ($subMenuItems as &$subMenuItem) {
            $subMenuItem['altmenu'] = $this->getSubmenuItems($subMenuItem['menuid'], $menuItems);
        }

        return $subMenuItems;
    }

    private function getMenuItemHtml($menuItem) {
        $menuClass = (isset($menuItem['altmenu']) && count($menuItem['altmenu'])>0) ? 'hasDropdown' : 'singleItem';
        $subMenuClass = (isset($menuItem['altmenu']) && count($menuItem['altmenu'])>0 && count($menuItem['altmenu'])<=6) ? 's-menu' : 'xl-menu';

        $html = '
            <li class="menu-item '.$subMenuClass.'">
        ';

        if (isset($menuItem['altmenu']) && count($menuItem['altmenu']) > 0) {
            $html .= '<input type="checkbox" class="sub-menu-item-checkbox" id="'.$menuItem['menubenzersizid'].'">'."\n";
            $html .= '<input type="checkbox" class="sub-menu-item-checkboxClose" id="'.$menuItem['menubenzersizid'].'Close">';
        }

        $html .= '
            <label for="'.$menuItem['menubenzersizid'].'Close" class="crossLabel">
                    <div class="bar1"></div>
                    <div class="bar2"></div>
            </label>
        ';
        $linkTarget = (str_starts_with($menuItem['menulink'], 'http')) ? ' target="_blank"' : '';
        $html .= '
            <a href="' . $menuItem['menulink'] . '" class="'.$menuClass.'" data-id="'.$menuItem['menubenzersizid'].'" ' . $linkTarget . '>' . $menuItem['menuad'] . '</a>
        ';


        if (isset($menuItem['altmenu']) && count($menuItem['altmenu']) > 0) {

            $html .= '
                <label class="sub-menu-item-label '.$menuClass.' fake" for="'.$menuItem['menubenzersizid'].'Close">' . $menuItem['menuad'] . '</label>
            ';
            $html .= '
                <label class="sub-menu-item-label '.$menuClass.'" for="'.$menuItem['menubenzersizid'].'">' . $menuItem['menuad'] . '</label>
            ';

            $html .= '
                <ul class="sub-menu '.$subMenuClass.'">
            ';

            $html .= '
                <li class="sub-menu-parent">
                    <a href="' . $menuItem['menulink'] . '" ' . $linkTarget . '>' . $menuItem['menuad'] . '</a>
                </li>
            ';
            foreach ($menuItem['altmenu'] as $subMenuItem) {
                $linkTarget = (str_starts_with($subMenuItem['menulink'], 'http')) ? ' target="_blank"' : '';
                $html .= '
                    <li>
                        <a href="' . $subMenuItem['menulink'] . '" ' . $linkTarget . '><img src="'.imgRoot.'bos.jpg">' . $subMenuItem['menuad'] . '</a>
                    </li>';
            }
            $html .= '
                </ul>
            ';
        }

        $html .= '
            </li>
        ';

        return $html;
    }

    private function getFooterMenuItemHtml($menuItem) {
        $menuClass = (isset($menuItem['altmenu']) && count($menuItem['altmenu'])>0) ? 'hasDropdown' : 'singleItem';
        $subMenuClass = (isset($menuItem['altmenu']) && count($menuItem['altmenu'])>0 && count($menuItem['altmenu'])<=6) ? 's-menu' : 'xl-menu';

        $html = '<li class="menu-item '.$subMenuClass.'">';

        /*if (isset($menuItem['altmenu']) && count($menuItem['altmenu']) > 0) {
            $html .= '<input type="checkbox" class="sub-menu-item-checkbox" id="'.$menuItem['menubenzersizid'].'">';
            $html .= '<input type="checkbox" class="sub-menu-item-checkboxClose" id="'.$menuItem['menubenzersizid'].'Close">';
        }*/

        /*$html .= '<label for="'.$menuItem['menubenzersizid'].'Close" class="crossLabel">
                    <div class="bar1"></div>
                    <div class="bar2"></div>
                </label>';*/
        $linkTarget = (str_starts_with($menuItem['menulink'], 'http')) ? ' target="_blank"' : '';
        $html .= '<a href="' . $menuItem['menulink'] . '" class="'.$menuClass.'" data-id="'.$menuItem['menubenzersizid'].'" ' . $linkTarget . '>' . $menuItem['menuad'] . '</a>';


        if (isset($menuItem['altmenu']) && count($menuItem['altmenu']) > 0) {
            /*$html .= '<label class="sub-menu-item-label '.$menuClass.' fake" for="'.$menuItem['menubenzersizid'].'Close">' . $menuItem['menuad'] . '</label>';
            $html .= '<label class="sub-menu-item-label '.$menuClass.'" for="'.$menuItem['menubenzersizid'].'">' . $menuItem['menuad'] . '</label>';*/

            $html .= '<ul class="sub-menu '.$subMenuClass.'">';

            /*$html .= '<li class="sub-menu-parent"><a href="' . $menuItem['menulink'] . '">' . $menuItem['menuad'] . '</a></li>';*/
            foreach ($menuItem['altmenu'] as $subMenuItem) {
                $linkTarget = (str_starts_with($subMenuItem['menulink'], 'http')) ? ' target="_blank"' : '';
                $html .= '<li><a href="' . $subMenuItem['menulink'] . '" ' . $linkTarget . '><img src="'.imgRoot.'bos.jpg">' . $subMenuItem['menuad'] . '</a></li>';
            }
            $html .= '</ul>';
        }

        $html .= '</li>';

        return $html;
    }

    private function getSubmenusByCategories($menuId,$categoryUniqID) {
        //die("$menuId,$categoryUniqID");
        $this->config->includeClass("Category");
        $category = new Category($this->db,$this->json);
        $categoryID = $category->getCategoryIdByUniqId($categoryUniqID);
        //print_r($categoryID);exit();
        $subCategories = $category->getSubcategories($categoryID);
        //print_r($subCategories);exit();
        $menuSubCategories = [];
        if (!empty($subCategories)) {
            foreach ($subCategories as $subCategory) {
                $subCategoryID=$subCategory['kategoriid'];
                //die("$subCategoryID");
                $subCategoryDetails= $category->getCategoryByIdOrUniqId($subCategoryID,"");
                //print_r($subCategoryDetails);exit();
                if (empty($subCategoryDetails)) {
                    return $menuSubCategories;
                }
                $subCategoryDetails = $subCategoryDetails[0];
                $menuSubCategories[] = [
                    "menuid"=>"categoryID-".$subCategoryDetails['kategoriid'],
                    "dilid"=>$subCategoryDetails['dilid'],
                    "menukategori"=>99,
                    "ustmenuid"=>$menuId,
                    "menuad" => $subCategoryDetails['kategoriad'],
                    "menulink" => $subCategoryDetails['link'],
                    "menusira" => 99,
                    "altkategori" => 0,
                    "menubenzersizid" => $subCategoryDetails['benzersizid'],
                    "orjbenzersizid" => $subCategoryDetails['benzersizid']
                ];
            }
        }
        return $menuSubCategories;
    }
    public function showMainMenu() {
        $menuItems = $this->mainMenu; // Veritabanından menüleri alalım.
        // Ana menü öğelerini bulalım.
        $mainMenuItems = array_filter($menuItems, function($item) {
            return $item['ustmenuid'] == 0;
        });
        //print_r($mainMenuItems);exit();

        // Her ana menü öğesi için alt menüleri oluşturalım.
        foreach ($mainMenuItems as &$menuItem) {
            //print_r($menuItem);exit();
            $menuItem['altmenu'] = $this->getSubmenuItems($menuItem['menuid'], $menuItems);
        }
        //print_r($menuItem['altmenu']);exit();

        // Menü HTML'sini oluşturalım.
        $menuHtml = '<ul class="main-menu">';
        foreach ($mainMenuItems as &$menuItem) {
            if ($menuItem['altkategori'] == 1) {
                //echo $menuItem['menuid']." - ".$menuItem['orjbenzersizid'];exit();
                $subMenuItemCategories = $this->getSubmenusByCategories($menuItem['menuid'],$menuItem['orjbenzersizid']);
                //print_r($subMenuItemCategories);exit();
                if (!empty($subMenuItemCategories)) {
                    $menuItem['altmenu'] = $this->getSubmenuItems($menuItem['menuid'], $subMenuItemCategories);
                }
            }
            $menuHtml .= $this->getMenuItemHtml($menuItem);
        }
        $menuHtml .= '</ul>';

        return $menuHtml;
    }
    public function showFooterMenu(){
        $menuItems = $this->footerMenu; // Veritabanından menüleri alalım.
        // Ana menü öğelerini bulalım.
        $mainMenuItems = array_filter($menuItems, function($item) {
            return $item['ustmenuid'] == 0;
        });
        //print_r($mainMenuItems);exit();

        // Her ana menü öğesi için alt menüleri oluşturalım.
        foreach ($mainMenuItems as &$menuItem) {
            //print_r($menuItem);exit();
            $menuItem['altmenu'] = $this->getSubmenuItems($menuItem['menuid'], $menuItems);
        }
        //print_r($menuItem['altmenu']);exit();

        // Menü HTML'sini oluşturalım.
        $menuHtml = '<ul class="footer-menu">';
        foreach ($mainMenuItems as &$menuItem) {
            if ($menuItem['altkategori'] == 1) {
                //echo $menuItem['menuid']." - ".$menuItem['orjbenzersizid'];exit();
                $subMenuItemCategories = $this->getSubmenusByCategories($menuItem['menuid'],$menuItem['orjbenzersizid']);
                //print_r($subMenuItemCategories);exit();
                if (!empty($subMenuItemCategories)) {
                    $menuItem['altmenu'] = $this->getSubmenuItems($menuItem['menuid'], $subMenuItemCategories);
                }
            }
            $menuHtml .= $this->getFooterMenuItemHtml($menuItem);
        }
        $menuHtml .= '</ul>';

        return $menuHtml;
    }
    public function getShowTopMenu() {
        return $this->topMenu;
    }
    public function showTopMenu() {
        $menuItems = $this->topMenu; // Veritabanından menüleri alalım
        //print_r($menuItems);exit();
        $menuHtml = '<div class="top-menu">';
        foreach ($menuItems as $menuItem) {
            $menuHtml .= $this->getMenuItemHtml($menuItem);
        }
        $menuHtml .= '</div>';
        return $menuHtml;
    }
    public function setShowTopMenu($topMenu) {
        $this->topMenu = $topMenu;
    }
    public function getShowMainMenu() {
        return $this->showMainMenu;
    }
    public function setShowMainMenu($showMainMenu) {
        $this->showMainMenu = $showMainMenu;
    }
    public function getShowFooterMenu() {
        return $this->showFooterMenu;
    }
    public function setShowFooterMenu($showFooterMenu) {
        $this->showFooterMenu = $showFooterMenu;
    }
}