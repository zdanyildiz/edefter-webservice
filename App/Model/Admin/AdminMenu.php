<?php
/**
 * menu
 * menuid
 * dilid
 * menukategori
 * ustmenuid
 * menukatman
 * menuad
 * menulink
 * menusira
 * altkategori
 * menubenzersizid
 * orjbenzersizid
 */

class AdminMenu
{

    private AdminDatabase $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function updateMenuLinkByLink($data){

        $sql = "UPDATE menu SET menulink = :newMenulink WHERE menulink = :oldMenulink";
        $result = $this->db->update($sql, $data);

        if ($result>0) {

            return [
                "status" => "success",
                "message" => "Menü link güncellendi"
            ];
        }
        elseif ($result==0) {

            return [
                "status" => "success",
                "message" => "Menü link güncel"
            ];
        }
        else {
            return [
                "status" => "error",
                "message" => "Menü link güncellenemedi"
            ];
        }
    }

    public function updateMenuLinkByMenuOrijinalUniqID($uniqID, $newMenuLink)
    {
        $sql = "UPDATE menu SET menulink = :newMenulink WHERE orjbenzersizid = :orjbenzersizid";
        $result = $this->db->update($sql, [
            "newMenulink" => $newMenuLink,
            "orjbenzersizid" => $uniqID
        ]);

        if ($result>0) {
            return [
                "status" => "success",
                "message" => "Menü link güncellendi"
            ];
        }
        elseif ($result==0) {

            return [
                "status" => "success",
                "message" => "Menü link güncel"
            ];
        }
        else {
            return [
                "status" => "error",
                "message" => "Menü link güncellenemedi"
            ];
        }
    }

    public function deleteMenu($languageID,$menuLocation)
    {
        $sql = "
            DELETE FROM menu
            WHERE dilid = :languageID
            AND menukategori = :menuLocation
        ";

        return $this->db->delete($sql, [
            "languageID" => $languageID,
            "menuLocation" => $menuLocation
        ]);
    }

    public function getMenuByLocation($menuData)
    {
        $languageID = $menuData["languageID"];
        $menuLocation = $menuData["menuLocation"];
        $menuArea = $menuData["menuArea"];

        $sql = "
            SELECT * FROM menu
            WHERE dilid = :languageID
            AND menukategori = :menuLocation
            AND menusira = :menuArea
            AND ustmenuid=0
            ORDER BY menuid
        ";

        $result = $this->db->select($sql, [
            "languageID" => $languageID,
            "menuLocation" => $menuLocation,
            "menuArea" => $menuArea
        ]);

        if(!$result){
            return false;
        }

        return $result[0];

    }

    public function getMenuByLanguage($languageID)
    {
        $sql = "
            SELECT * FROM menu
            WHERE dilid = :languageID
            ORDER BY menuid
        ";

        $result = $this->db->select($sql, [
            "languageID" => $languageID
        ]);

        return $result;
    }

    public function getMenuByLanguageAndLocation($languageID, $menuLocation)
    {
        $sql = "
            SELECT * FROM menu
            WHERE dilid = :languageID
            AND menukategori = :menuLocation
            ORDER BY menuid
        ";

        $result = $this->db->select($sql, [
            "languageID" => $languageID,
            "menuLocation" => $menuLocation
        ]);

        return $result;
    }

    public function saveMenu($menu){
        //print_r($menu["contentUniqID"]);exit;
        $sql = "
            INSERT INTO menu
            SET
            dilid = :languageID,
            menukategori = :menuLocation,
            ustmenuid = :menuParent,
            menukatman = :menuLayer,
            menuad = :menuName,
            menulink = :menuLink,
            menusira = :menuArea,
            altkategori = :getSubCategory,
            menubenzersizid = :contentUniqID,
            orjbenzersizid = :contentOrjUniqID,
            menuType = :menuType
        ";

        $result = $this->db->insert($sql, [
            "languageID" => $menu["languageID"],
            "menuLocation" => $menu["menuLocation"],
            "menuParent" => $menu["menuParent"],
            "menuLayer" => $menu["menuLayer"],
            "menuName" => $menu["menuName"],
            "menuLink" => $menu["menuLink"],
            "menuArea" => $menu["menuArea"],
            "getSubCategory" => $menu["getSubCategory"],
            "contentUniqID" => $menu["contentUniqID"],
            "contentOrjUniqID" => $menu["contentOrjUniqID"],
            "menuType" => $menu["menuType"]
        ]);

        return $result;
    }

    public function checkMenuByLanguage($languageID)
    {
        $sql = "
            SELECT dilid FROM menu
            WHERE dilid = :languageID
            LIMIT 1
        ";

        $result = $this->db->select($sql, [
            "languageID" => $languageID
        ]);

        return $result;
    }

    public function beginTransaction($funcName = "")
    {
        $this->db->beginTransaction($funcName);
    }

    public function commit($funcName = "")
    {
        $this->db->commit($funcName);
    }

    public function rollback($funcName = "")
    {
        $this->db->rollback($funcName);
    }
}
