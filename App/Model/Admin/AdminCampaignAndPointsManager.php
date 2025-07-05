<?php

class AdminCampaignAndPointsManager
{
    private AdminDatabase $db;

    public function __construct(AdminDatabase $db)
    {
        $this->db = $db;
    }

    // Kampanya işlemleri

    public function addCampaign($data)
    {
        $query = "
            INSERT INTO kampanyalar 
            SET 
                ad = :campaignName,
                aciklama = :campaignDescription,
                baslangic_tarihi = :campaignStartDate,
                bitis_tarihi = :campaignEndDate,
                tur = :campaignType,
                oncelik = :campaignPriority
        ";

        $params = [
            ':campaignName' => $data['campaignName'],
            ':campaignDescription' => $data['campaignDescription'],
            ':campaignStartDate' => $data['campaignStartDate'],
            ':campaignEndDate' => $data['campaignEndDate'],
            ':campaignType' => $data['campaignType'],
            ':campaignPriority' => $data['campaignPriority']
        ];

        return $this->db->insert($query, $params);
    }

    public function updateCampaign($data)
    {
        $query = "
            UPDATE kampanyalar 
            SET 
                ad = :campaignName,
                aciklama = :campaignDescription,
                baslangic_tarihi = :campaignStartDate,
                bitis_tarihi = :campaignEndDate,
                tur = :campaignType,
                oncelik = :campaignPriority
            WHERE 
                id = :campaignID
        ";

        $params = [
            ':campaignID' => $data['campaignID'],
            ':campaignName' => $data['campaignName'],
            ':campaignDescription' => $data['campaignDescription'],
            ':campaignStartDate' => $data['campaignStartDate'],
            ':campaignEndDate' => $data['campaignEndDate'],
            ':campaignType' => $data['campaignType'],
            ':campaignPriority' => $data['campaignPriority']
        ];

        return $this->db->update($query, $params);
    }
    public function getCampaigns()
    {
        $query = "
            SELECT 
                id as campaignID,
                ad as campaignName,
                aciklama as campaignDescription,
                baslangic_tarihi as campaignStartDate,
                bitis_tarihi as campaignEndDate,
                tur as campaignType,
                oncelik as campaignPriority
            FROM 
                kampanyalar 
        ";
        return $this->db->select($query);
    }

    public function getCampaign($campaignID)
    {
        $sql = "
            SELECT 
                id as campaignID,
                ad as campaignName,
                aciklama as campaignDescription,
                baslangic_tarihi as campaignStartDate,
                bitis_tarihi as campaignEndDate,
                tur as campaignType,
                oncelik as campaignPriority
            FROM 
                kampanyalar WHERE id = :id
        ";

        $params = [':id' => $campaignID];

        return $this->db->select($sql, $params);
    }

    public function deleteCampaign($id)
    {
        $query = "DELETE FROM kampanyalar WHERE id = :id";
        $params = [':id' => $id];
        return $this->db->delete($query, $params);
    }

    // Miktar indirimi işlemleri

    public function getQuantityDiscount($campaignID)
    {
        $query = "
            SELECT 
                id as quantityDiscountID,
                kampanya_id as campaignID,
                miktar_sinir as quantityLimit,
                indirim_orani as discountRate
            FROM 
                kampanya_miktar_indirim 
            WHERE 
                kampanya_id = :campaignID
        ";
        $params = [':campaignID' => $campaignID];
        return $this->db->select($query, $params);
    }

    public function addQuantityDiscount($data)
    {
        $query = "
            INSERT INTO kampanya_miktar_indirim 
            SET 
                kampanya_id = :campaignID,
                miktar_sinir = :quantityLimit,
                indirim_orani = :discountRate
        ";

        $params = [
            ':campaignID' => $data['campaignID'],
            ':quantityLimit' => $data['quantityLimit'],
            ':discountRate' => $data['discountRate']
        ];

        return $this->db->insert($query, $params);
    }

    public function deleteQuantityDiscount($campaignID){
        $query = "DELETE FROM kampanya_miktar_indirim WHERE kampanya_id = :campaignID";
        $params = [':campaignID' => $campaignID];
        return $this->db->delete($query, $params);
    }

    // Kampanya ürünleri işlemleri

    public function addCampaignProduct($data)
    {
        $query = "
            INSERT INTO kampanya_urunleri 
            SET 
                kampanya_id = :kampanyaId,
                urun_id = :urunId,
                kategori_id = :kategoriId,
                marka_id = :markaId,
                tedarikci_id = :tedarikciId,
                haric_urun_id = :haricUrunId
        ";

        $params = [
            ':kampanyaId' => $data['kampanya_id'],
            ':urunId' => $data['urun_id'] ?? null,
            ':kategoriId' => $data['kategori_id'] ?? null,
            ':markaId' => $data['marka_id'] ?? null,
            ':tedarikciId' => $data['tedarikci_id'] ?? null,
            ':haricUrunId' => $data['haric_urun_id'] ?? null
        ];

        return $this->db->insert($query, $params);
    }

    // Puan işlemleri

    public function getMusteriPuan($musteriId)
    {
        $query = "
            SELECT puan_bakiyesi
            FROM uye_puanlari
            WHERE uye_id = :musteriId
        ";
        $params = [':musteriId' => $musteriId];
        return $this->db->select($query, $params);
    }

    public function updateMusteriPuan($musteriId, $yeniPuanBakiyesi)
    {
        $query = "
            UPDATE uye_puanlari
            SET 
                puan_bakiyesi = :puanBakiyesi,
                son_guncelleme = NOW()
            WHERE 
                uye_id = :musteriId
        ";

        $params = [
            ':musteriId' => $musteriId,
            ':puanBakiyesi' => $yeniPuanBakiyesi
        ];

        return $this->db->update($query, $params);
    }

    public function addPuanIslemi($data)
    {
        $query = "
            INSERT INTO puan_islemleri 
            SET 
                uye_id = :musteriId,
                islem_tipi = :islemTipi,
                puan_miktari = :puanMiktari,
                islem_tarihi = NOW(),
                siparis_id = :siparisId
        ";

        $params = [
            ':musteriId' => $data['uye_id'],
            ':islemTipi' => $data['islem_tipi'],
            ':puanMiktari' => $data['puan_miktari'],
            ':siparisId' => $data['siparis_id'] ?? null
        ];

        return $this->db->insert($query, $params);
    }

    // Veritabanı işlem yönetimi

    public function beginTransaction()
    {
        $this->db->beginTransaction();
    }

    public function commit()
    {
        $this->db->commit();
    }

    public function rollback()
    {
        $this->db->rollback();
    }

    public function createCoupon($data)
    {
        $query = "
            INSERT INTO kuponlar 
            SET 
                kod = :kod,
                ad = :ad,
                aciklama = :aciklama,
                indirim_turu = :indirim_turu,
                indirim_degeri = :indirim_degeri,
                minimum_sepet_tutari = :minimum_sepet_tutari,
                baslangic_tarihi = :baslangic_tarihi,
                bitis_tarihi = :bitis_tarihi,
                kullanim_limiti = :kullanim_limiti,
                kalan_kullanim = :kullanim_limiti,
                ozel_musteri_id = :ozel_musteri_id,
                kategori_id = :kategori_id,
                urun_id = :urun_id
        ";

        $params = [
            ':kod' => $data['kod'],
            ':ad' => $data['ad'],
            ':aciklama' => $data['aciklama'],
            ':indirim_turu' => $data['indirim_turu'],
            ':indirim_degeri' => $data['indirim_degeri'],
            ':minimum_sepet_tutari' => $data['minimum_sepet_tutari'],
            ':baslangic_tarihi' => $data['baslangic_tarihi'],
            ':bitis_tarihi' => $data['bitis_tarihi'],
            ':kullanim_limiti' => $data['kullanim_limiti'],
            ':ozel_musteri_id' => $data['ozel_musteri_id'] ?? null,
            ':kategori_id' => $data['kategori_id'] ?? null,
            ':urun_id' => $data['urun_id'] ?? null
        ];

        return $this->db->insert($query, $params);
    }

    public function getCoupon($kod)
    {
        $query = "
            SELECT * 
            FROM kuponlar 
            WHERE kod = :kod AND durum = 1 AND bitis_tarihi > NOW()
        ";
        $params = [':kod' => $kod];
        return $this->db->selectOne($query, $params);
    }

    public function validateCoupon($kod, $musteriId, $sepetTutari, $urunler)
    {
        $kupon = $this->getCoupon($kod);

        if (!$kupon) {
            return false;
        }

        // Kupon kullanım limiti kontrolü
        if ($kupon['kalan_kullanim'] <= 0) {
            return false;
        }

        // Minimum sepet tutarı kontrolü
        if ($sepetTutari < $kupon['minimum_sepet_tutari']) {
            return false;
        }

        // Özel müşteri kontrolü
        if ($kupon['ozel_musteri_id'] && $kupon['ozel_musteri_id'] != $musteriId) {
            return false;
        }

        // Kategori veya ürün kontrolü
        if ($kupon['kategori_id'] || $kupon['urun_id']) {
            $uygunUrun = false;
            foreach ($urunler as $urun) {
                if (($kupon['kategori_id'] && $urun['kategori_id'] == $kupon['kategori_id']) ||
                    ($kupon['urun_id'] && $urun['id'] == $kupon['urun_id'])) {
                    $uygunUrun = true;
                    break;
                }
            }
            if (!$uygunUrun) {
                return false;
            }
        }

        return $kupon;
    }

    public function applyCoupon($kuponId)
    {
        $query = "
            UPDATE kuponlar 
            SET kalan_kullanim = kalan_kullanim - 1 
            WHERE id = :id AND kalan_kullanim > 0
        ";
        $params = [':id' => $kuponId];
        return $this->db->update($query, $params);
    }
}