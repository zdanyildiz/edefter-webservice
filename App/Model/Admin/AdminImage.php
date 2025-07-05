<?php
class AdminImage
{
    private AdminDatabase $db;

    public function __construct(AdminDatabase $db)
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

    public function addImage($imageData)
    {
        $imageUniqID = $imageData['imageUniqID'];
        $imageFolderID = $imageData['imageFolderID'];
        $imageName = $imageData['imageName'];
        $imagePath = $imageData['imagePath'];
        $imageWidth = $imageData['imageWidth'];
        $imageHeight = $imageData['imageHeight'];

        $imageInsertQuery = "
            INSERT INTO resim
                (benzersizid, resimklasorid, resimad, resim, ren, rboy)
            VALUES
                (:imageUniqID, :imageFolderID, :imageName, :imagePath, :imageWidth, :imageHeight)
        ";

        $params = [
            'imageUniqID' => $imageUniqID,
            'imageFolderID' => $imageFolderID,
            'imageName' => $imageName,
            'imagePath' => $imagePath,
            'imageWidth' => $imageWidth,
            'imageHeight' => $imageHeight
        ];


        $result = $this->db->insert($imageInsertQuery, $params);
        if ($result) {
            return $result;
        }
        else {
            return false;
        }
    }

    public function uploadImage($imageName, $newImageName, $file, $folderName){

        $targetDir = IMG; //"/Public/Image/"

        $imageFileType = strtolower(pathinfo($file["name"],PATHINFO_EXTENSION));

        $folderName = $folderName ?? 'Page';

        $folderID = $this->getImageFolderIDByFolderName($folderName);

        $targetDir = $targetDir.$folderName."/"; //"/Public/Image/Product"
        //klasör yoksa oluşturalım
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $targetFile = $targetDir . $newImageName . '.' . $imageFileType; //"/Public/Image/Product/1.jpg"

        $imagePath = $newImageName . '.' . $imageFileType;
        if (file_exists($targetFile)) {
            $newImageName = $newImageName . uniqid();
            $targetFile = $targetDir . $newImageName . '.' . $imageFileType;

            $imagePath = $newImageName . '.' . $imageFileType;
        }



        $check = getimagesize($file["tmp_name"]);
        if($check !== false) {

            $imageWidth = $check[0];
            $imageHeight = $check[1];


            if ($file["size"] > 5242880) {
                return [
                    'status' => 'error',
                    'message' => 'Dosya boyutu çok büyük'
                ];
            }

            if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                && $imageFileType != "webp") {
                return [
                    'status' => 'error',
                    'message' => 'Sadece JPG, JPEG, PNG ve WEBP dosyaları yüklenebilir'
                ];
            }

            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            if (move_uploaded_file($file["tmp_name"], $targetFile)) {

                //yükleme kontrol edelim
                if (!file_exists($targetFile)) {
                    return [
                        'status' => 'error',
                        'message' => 'Dosya yüklenirken hata oluştu'
                    ];
                }
                return [
                    'status' => 'success',
                    'message' => 'Dosya yüklendi',
                    'imagePath' => $imagePath,
                    'imageFolderID' => $folderID,
                    'imageFolderName' => $folderName,
                    'imageName' => $imageName,
                    'imageWidth' => $imageWidth,
                    'imageHeight' => $imageHeight
                ];
            }
            else {
                return [
                    'status' => 'error',
                    'message' => 'Dosya yüklenirken hata oluştu'
                ];
            }
        }
        else {
            return [
                'status' => 'error',
                'message' => 'Dosya bir resim değil'
            ];
        }
    }

    public function uploadAdminImage($imageName, $newImageName, $file, $folderName){

        $targetDir = $_SERVER['DOCUMENT_ROOT']."/_y/m/r/"; //"/Public/Image/"

        $imageFileType = strtolower(pathinfo($file["name"],PATHINFO_EXTENSION));

        $folderName = $folderName ?? 'Admin';

        $folderID = 0;

        $targetDir = $targetDir.$folderName."/"; //"/Public/Image/Product"
        //klasör yoksa oluşturalım
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $targetFile = $targetDir . $newImageName . '.' . $imageFileType; //"/Public/Image/Product/1.jpg"

        $imagePath = $newImageName . '.' . $imageFileType;
        if (file_exists($targetFile)) {
            $newImageName = $newImageName . uniqid();
            $targetFile = $targetDir . $newImageName . '.' . $imageFileType;

            $imagePath = $newImageName . '.' . $imageFileType;
        }



        $check = getimagesize($file["tmp_name"]);
        if($check !== false) {

            $imageWidth = $check[0];
            $imageHeight = $check[1];


            if ($file["size"] > 3145728) {
                return [
                    'status' => 'error',
                    'message' => 'Dosya boyutu çok büyük'
                ];
            }

            if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                && $imageFileType != "webp") {
                return [
                    'status' => 'error',
                    'message' => 'Sadece JPG, JPEG, PNG ve WEBP dosyaları yüklenebilir'
                ];
            }

            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            if (move_uploaded_file($file["tmp_name"], $targetFile)) {

                //yükleme kontrol edelim
                if (!file_exists($targetFile)) {
                    return [
                        'status' => 'error',
                        'message' => 'Dosya yüklenirken hata oluştu'
                    ];
                }
                return [
                    'status' => 'success',
                    'message' => 'Dosya yüklendi',
                    'imagePath' => $imagePath,
                    'imageFolderID' => $folderID,
                    'imageFolderName' => $folderName,
                    'imageName' => $imageName,
                    'imageWidth' => $imageWidth,
                    'imageHeight' => $imageHeight
                ];
            }
            else {
                return [
                    'status' => 'error',
                    'message' => 'Dosya yüklenirken hata oluştu'
                ];
            }
        }
        else {
            return [
                'status' => 'error',
                'message' => 'Dosya bir resim değil'
            ];
        }
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

    //benzersizid'ye göre varsa güncelleyecek yoksa ekleyecek

    public function updateImage($imageData)
    {
        $imageUniqID = $imageData['imageUniqID'];
        $imageFolderID = $imageData['imageFolderID'];
        $imageName = $imageData['imageName'];
        $imagePath = $imageData['imagePath'];
        $imageWidth = $imageData['imageWidth'];
        $imageHeight = $imageData['imageHeight'];

        $imageInsertQuery = "
            INSERT INTO resim
                (benzersizid, resimklasorid, resimad, resim, ren, rboy)
            VALUES
                (:imageUniqID, :imageFolderID, :imageName, :imagePath, :imageWidth, :imageHeight)
            ON DUPLICATE KEY UPDATE
                resimklasorid = :imageFolderIDUpdate,
                resimad = :imageNameUpdate,
                resim = :imagePathUpdate,
                ren = :imageWidthUpdate,
                rboy = :imageHeightUpdate
        ";

        $params = [
            'imageUniqID' => $imageUniqID,
            'imageFolderID' => $imageFolderID,
            'imageName' => $imageName,
            'imagePath' => $imagePath,
            'imageWidth' => $imageWidth,
            'imageHeight' => $imageHeight,
            'imageFolderIDUpdate' => $imageFolderID,
            'imageNameUpdate' => $imageName,
            'imagePathUpdate' => $imagePath,
            'imageWidthUpdate' => $imageWidth,
            'imageHeightUpdate' => $imageHeight
        ];

        $this->db->beginTransaction();
        $result = $this->db->insert($imageInsertQuery, $params);
        if ($result) {
            $this->db->commit();
            return $result;
        }
        else {
            $this->db->rollBack();
            return false;
        }
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

    function saveImageFromUrl($imageUrl, $productName, $folderName = 'Product')
    {
        global $helper;

        // Resim adını işleyin
        $imageName = $productName;
        $newImageName = $helper->toLowercase($imageName);
        $newImageName = $helper->trToEn($newImageName);
        $newImageName = $helper->cleanString($newImageName);

        // Resmi URL'den indirin
        $imageContentResult = $this->fetchImageContent($imageUrl);

        // fetchImageContent sonucu kontrol et
        if ($imageContentResult['status'] === 'error') {
            return [
                'status' => 'error',
                'message' => $imageContentResult['message']
            ];
        }

        // fetchImageContent başarılı, data alalım
        $imageContent = $imageContentResult['data'];

        // Resim bilgilerini alın
        $imageInfo = getimagesizefromstring($imageContent);
        if ($imageInfo === false) {
            return [
                'status' => 'error',
                'message' => 'Geçerli bir resim değil'
            ];
        }

        // Resim uzantısını belirleyin
        $mimeType = $imageInfo['mime'];
        $extension = '';
        switch ($mimeType) {
            case 'image/jpeg':
                $extension = 'jpg';
                break;
            case 'image/png':
                $extension = 'png';
                break;
            case 'image/webp':
                $extension = 'webp';
                break;
            default:
                return [
                    'status' => 'error',
                    'message' => 'Desteklenmeyen resim formatı: ' . $mimeType
                ];
        }

        // Dosya boyutunu kontrol edin
        $fileSize = strlen($imageContent);
        if ($fileSize > 3145728) { // 3MB
            return [
                'status' => 'error',
                'message' => 'Dosya boyutu çok büyük'
            ];
        }

        // Hedef dizini belirleyin
        $targetDir = IMG; // Örneğin: "/Public/Image/"
        $folderName = $folderName ?? 'Page';
        $folderID = $this->getImageFolderIDByFolderName($folderName);
        $targetDir = $targetDir . $folderName . '/'; // Örneğin: "/Public/Image/Product/"

        // Klasör yoksa oluşturun
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        // Dosya yolunu oluşturun
        $targetFile = $targetDir . $newImageName . '.' . $extension; // Örneğin: "/Public/Image/Product/urun-adi.jpg"

        // Aynı isimde dosya varsa isim değiştirin
        if (file_exists($targetFile)) {
            $newImageName = $newImageName . uniqid();
            $targetFile = $targetDir . $newImageName . '.' . $extension;
        }

        // Resmi kaydedin
        $saveResult = file_put_contents($targetFile, $imageContent);
        if ($saveResult === false) {
            return [
                'status' => 'error',
                'message' => 'Resim kaydedilirken hata oluştu'
            ];
        }

        // Resim boyutlarını alın
        $imageWidth = $imageInfo[0];
        $imageHeight = $imageInfo[1];

        // Başarılı sonucu döndürün
        return [
            'status' => 'success',
            'message' => 'Resim başarıyla kaydedildi',
            'imagePath' => $newImageName . '.' . $extension,
            'imageFolderID' => $folderID,
            'imageFolderName' => $folderName,
            'imageName' => $imageName,
            'imageWidth' => $imageWidth,
            'imageHeight' => $imageHeight
        ];
    }

    //transaction işlemleri
    public function beginTransaction(){
        $this->db->beginTransaction();
    }

    public function commit(){
        $this->db->commit();
    }

    public function rollback(){
        $this->db->rollback();
    }

}
