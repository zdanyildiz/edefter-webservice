<?php


class AdminBanners
{
    private $db;

    public function __construct(AdminDatabase $db)
    {
        $this->db = $db;
    }

    // Banner Çeşitlerini Getir
    public function getBannerTypes()
    {
        return $this->db->select('banner_types');
    }

    // Banner Gruplarını Getir
    public function getBannerGroups()
    {
        return $this->db->select('banner_groups');
    }

    // Banner Gruplarına Göre Bannerları Getir
    public function getBannersByGroup($groupId)
    {
        return $this->db->select('banners',  ['group_id' => $groupId, 'active' => 1]);
    }

    // Banner Ekle
    public function addBanner($data)
    {
        return $this->db->insert('banners', $data);
    }

    // Banner Güncelle
    public function updateBanner($id, $data)
    {
        return $this->db->update('banners', $data);
    }

    // Banner Sil
    public function deleteBanner($id)
    {
        return $this->db->delete('banners', ['id' => $id]);
    }

    // Banner Stillerini Getir
    public function getBannerStyles()
    {
        return $this->db->select('banner_styles');
    }

    // Banner Stili Ekle
    public function addBannerStyle($data)
    {
        return $this->db->insert('banner_styles', $data);
    }

    // Banner Stili Güncelle
    public function updateBannerStyle($id, $data)
    {
        return $this->db->update('banner_styles', $data);
    }

    // Banner Stili Sil
    public function deleteBannerStyle($id)
    {
        return $this->db->delete('banner_styles', ['id' => $id]);
    }

    // Banner Grupları Ekle
    public function addBannerGroup($data)
    {
        return $this->db->insert('banner_groups', $data);
    }

    // Banner Gruplarını Güncelle
    public function updateBannerGroup($id, $data)
    {
        return $this->db->update('banner_groups', $data);
    }

    // Banner Gruplarını Sil
    public function deleteBannerGroup($id)
    {
        // Grup ile ilişkili bannerları da silelim
        $this->db->delete('banners', ['group_id' => $id]);
        return $this->db->delete('banner_groups', ['id' => $id]);
    }

    // Banner Görüntüleme Kurallarını Getir
    public function getDisplayRules($groupId)
    {
        return $this->db->select('banner_display_rules');
    }

    // Görüntüleme Kuralı Ekle
    public function addDisplayRule($data)
    {
        return $this->db->insert('banner_display_rules', $data);
    }

    // Görüntüleme Kuralını Güncelle
    public function updateDisplayRule($id, $data)
    {
        return $this->db->update('banner_display_rules', $data);
    }

    // Görüntüleme Kuralını Sil
    public function deleteDisplayRule($id)
    {
        return $this->db->delete('banner_display_rules', ['id' => $id]);
    }
}


