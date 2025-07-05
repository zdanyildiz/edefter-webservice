<?php
class BannerTypeModel {
    private Database $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllTypes() {
        $query = "SELECT * FROM banner_types";
        return $this->db->select($query);
    }

    public function getTypeById($id) {
        $query = "SELECT * FROM banner_types WHERE id = :id";
        return $this->db->select($query, ['id' => $id]);
    }
}

class BannerLayoutModel {
    private Database $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllLayouts() {
        $query = "SELECT * FROM banner_layouts";
        return $this->db->select($query);
    }

    public function getLayoutById($id) {
        $query = "SELECT * FROM banner_layouts WHERE id = :id";
        return $this->db->select($query, ['id' => $id]);
    }

    public function getLayoutsByTypeId($type_id) {
        $query = "SELECT * FROM banner_layouts WHERE type_id = :type_id";
        return $this->db->select($query, ['type_id' => $type_id]);
    }
}

class BannerGroupModel {
    private Database  $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllGroups() {
        $query = "SELECT * FROM banner_groups";
        return $this->db->select($query);
    }

    public function getGroupById($id) {
        $query = "SELECT * FROM banner_groups WHERE id = :id";
        return $this->db->select($query, ['id' => $id]);
    }
}

class BannerStyleModel {
    private Database $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllStyles() {
        $query = "SELECT * FROM banner_styles";
        return $this->db->select($query);
    }

    public function getStyleById($id) {
        $query = "SELECT * FROM banner_styles WHERE id = :id";
        return $this->db->select($query, ['id' => $id]);
    }
}

class BannerModel {
    private Database $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllBanners() {
        $query = "SELECT * FROM banners";
        return $this->db->select($query);
    }

    public function getBannerById($id) {
        $query = "SELECT * FROM banners WHERE id = :id";
        return $this->db->select($query, ['id' => $id]);
    }

    public function getBannersByGroupID($bannerGroupID)
    {
        $query = "SELECT * FROM banners WHERE group_id = :group_id";
        return $this->db->select($query, ['group_id' => $bannerGroupID]);
    }
}

class BannerDisplayRulesModel {
    private Database $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllDisplayRules() {
        $query = "SELECT * FROM banner_display_rules";
        return $this->db->select($query);
    }

    public function getDisplayRuleById($id) {
        $query = "SELECT * FROM banner_display_rules WHERE id = :id";
        return $this->db->select($query, ['id' => $id]);
    }

    public function getDisplayRulesByLanguageCode($languageCode) {
        $query = "SELECT * FROM banner_display_rules WHERE language_code = :languageCode Group by group_id";
        return $this->db->select($query, ['languageCode' => $languageCode]);
    }

    public function getDisplayRuleByLanguageId($languageCode,$bannerTypeID) {
        $query = "SELECT * FROM banner_display_rules WHERE language_code = :languageCode and type_id = :bannerTypeID Group by group_id";
        return $this->db->select($query, ['languageCode' => $languageCode, 'bannerTypeID' => $bannerTypeID]);
    }

    public function getDisplayRuleByGroupId($group_id) {
        $query = "SELECT * FROM banner_display_rules WHERE group_id = :group_id";
        return $this->db->select($query, ['group_id' => $group_id]);
    }
}

