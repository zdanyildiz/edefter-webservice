<?php
class Image
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function getImages($limit,$currentPage): array
    {
        $currentPage = $currentPage<=0 ? 1 : $currentPage;
        $start = ($currentPage-1)*$limit;

        $imageQuery = "
            SELECT
                resimid as imageID, 
                resimklasorad as imageFolderName, 
                resimad as imageName, 
                resim as imagePath, 
                ren as imageWidth, 
                rboy as imageHeight
            FROM 
                resim 
                    INNER JOIN resimklasor 
                        ON resimklasor.resimklasorid = resim.resimklasorid
            LIMIT $start,$limit
        ";

        $imageResult = $this->db->select($imageQuery);

        if ($imageResult) {

            return $imageResult;
        }
        return [];
    }

    public function getProductImages($limit,$currentPage): array
    {
        $currentPage = $currentPage<=0 ? 1 : $currentPage;
        $start = ($currentPage-1)*$limit;

        $imageQuery = "
            SELECT
                resimid as imageID,
                resimklasorad as imageFolderName,
                resimad as imageName,
                resim as imagePath,
                ren as imageWidth,
                rboy as imageHeight
            FROM 
                resim 
                    INNER JOIN resimklasor 
                        ON resimklasor.resimklasorid = resim.resimklasorid
            Where resimklasor.resimklasorid=2
            LIMIT $start,$limit
        ";

        $imageResult = $this->db->select($imageQuery);

        if ($imageResult) {

            return $imageResult;
        }
        return [];
    }

    public function getImagesByFolder($folderID,$folderName): array
    {
        $folderID = (int)$folderID ?? 0;
        $folderName = $folderName ?? '';

        //where koşullarını kontrol edelim
        if ($folderID != 0 && !empty($folderName)) {
            if ($folderID>0){
                $where = "WHERE resim.resimklasorid = $folderID";
            }
            elseif (!empty($folderName)){
                $where = "WHERE resimklasor.resimklasorad = '$folderName'";
            }
            else {
                return [
                    'status' => 'error',
                    'message' => 'Geçersiz klasör bilgisi'
                ];
            }
        }
        else {
            return [
                'status' => 'error',
                'message' => 'Klasör bilgisi eksik'
            ];
        }
        $imageQuery = "
            SELECT
                resimid as imageID,
                resimklasorad as imageFolderName,
                resimad as imageName,
                resim as imagePath,
                ren as imageWidth,
                rboy as imageHeight
            FROM 
                resim 
                    INNER JOIN resimklasor 
                        ON resimklasor.resimklasorid = resim.resimklasorid
            WHERE resim.resimklasorid = $folderID
        ";

        $imageResult = $this->db->select($imageQuery);

        if ($imageResult) {
            return $imageResult;
        }
        return [];
    }

    public function getImagesBySearch($searchText): array
    {
        $searchText = $searchText ?? '';

        if (!empty($searchText)) {

            $searchText = "%$searchText%";

            $imageQuery = "
                SELECT
                    resimid as imageID,
                    resimklasorad as imageFolderName,
                    resimad as imageName,
                    resim as imagePath,
                    ren as imageWidth,
                    rboy as imageHeight
                FROM 
                    resim 
                        INNER JOIN resimklasor 
                            ON resimklasor.resimklasorid = resim.resimklasorid
                WHERE resim.resimad LIKE :searchText
                LIMIT 50
            ";

            $imageResult = $this->db->select($imageQuery, ['searchText' => $searchText]);

            if ($imageResult) {

                return $imageResult;
            }
        }
        return [];
    }

    public function getImageCount(): int
    {
        $imageCountQuery = "
            SELECT
                COUNT(*) as imageCount
            FROM 
                resim 
                    INNER JOIN resimklasor 
                        ON resimklasor.resimklasorid = resim.resimklasorid
        ";

        $imageCountResult = $this->db->select($imageCountQuery);

        if ($imageCountResult) {
            return $imageCountResult[0]['imageCount'];
        }
        return 0;
    }

    public function getImageFolders(): array
    {
        $imageFolderQuery = "
            SELECT
                resimklasorid as imageFolderID,
                resimklasorad as imageFolderName
            FROM 
                resimklasor
        ";

        $imageFolderResult = $this->db->select($imageFolderQuery);

        if ($imageFolderResult) {
            return $imageFolderResult;
        }
        return [];
    }

    public function getImageFolderIDByFolderName($folderName)
    {
        $folderName = $folderName ?? '';

        if (!empty($folderName)) {

            $imageFolderQuery = "
                SELECT
                    resimklasorid
                FROM 
                    resimklasor
                WHERE resimklasorad = :folderName
            ";

            $imageFolderResult = $this->db->select($imageFolderQuery, ['folderName' => $folderName]);

            if ($imageFolderResult) {
                return $imageFolderResult[0]['resimklasorid'];
            }
        }
        return 6;
    }

    public function getImageByID($imageID): array
    {
        $imageID = (int)$imageID ?? 0;

        $imageQuery = "
            SELECT
                resimid as imageID,
                resimklasorad as imageFolderName,
                resimad as imageName,
                resim as imagePath,
                ren as imageWidth,
                rboy as imageHeight
            FROM 
                resim 
                    INNER JOIN resimklasor 
                        ON resimklasor.resimklasorid = resim.resimklasorid
            WHERE resimid = :imageID
        ";

        return $this->db->select($imageQuery, ['imageID' => $imageID]);
    }

    function fetchImageContent($url)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3); // Zaman aşımı süresi saniye cinsinden

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

        $data = curl_exec($ch);

        if (curl_errno($ch)) {
            return [
                'status' => 'error',
                'message' => 'Resim URL\'den indirilemedi: ' . curl_error($ch)
            ];
        }

        curl_close($ch);

        if ($data === false) {
            return [
                'status' => 'error',
                'message' => 'Resim indirilemedi'
            ];
        }

        return [
            'status' => 'success',
            'data' => $data
        ];
    }

}
