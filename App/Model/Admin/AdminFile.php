<?php
class AdminFile
{
    private AdminDatabase $db;

    public function __construct(AdminDatabase $db)
    {
        $this->db = $db;
    }

    public function getFiles($limit,$currentPage): array
    {
        $currentPage = $currentPage<=0 ? 1 : $currentPage;
        $start = ($currentPage-1)*$limit;

        $fileQuery = "
            SELECT
                dosyaid, dosyaad, dosya, dosyaboyut, dosyauzanti,resimklasorad
            FROM 
                dosya 
                INNER JOIN resimklasor 
                            ON resimklasor.resimklasorid = dosya.dosyaklasorid
            LIMIT $start,$limit
        ";

        $fileResult = $this->db->select($fileQuery);

        if ($fileResult) {
            //sütun isimlerini ingilizceye çevirelim
            $fileResult = array_map(function($file) {
                return [
                    'fileID' => $file['dosyaid'],
                    'fileName' => $file['dosyaad'],
                    'fileFolderName' => $file['resimklasorad'],
                    'filePath' => $file['dosya'],
                    'fileSize' => $file['dosyaboyut'],
                    'fileExtension' => $file['dosyauzanti']
                ];
            }, $fileResult);

            return $fileResult;
        }
        return [];
    }

    //getFilesBySearch
    public function getFilesBySearch($searchText): array
    {
        $fileQuery = "
            SELECT
                dosyaid, dosyaad, dosya, dosyaboyut, dosyauzanti
            FROM 
                dosya 
            WHERE dosyaad LIKE :searchText
        ";

        $fileResult = $this->db->select($fileQuery, ['searchText' => "%$searchText%"]);

        if ($fileResult) {
            //sütun isimlerini ingilizceye çevirelim
            $fileResult = array_map(function($file) {
                return [
                    'fileID' => $file['dosyaid'],
                    'fileName' => $file['dosyaad'],
                    'filePath' => $file['dosya'],
                    'fileSize' => $file['dosyaboyut'],
                    'fileExtension' => $file['dosyauzanti']
                ];
            }, $fileResult);

            return $fileResult;
        }
        return [];
    }

    public function addFile($fileData)
    {
        $fileQuery = "
            INSERT INTO dosya
                (dosyaekletarih, dosyaad, dosya, dosyaboyut, dosyauzanti, benzersizid)
            VALUES
                (:fileAddDate, :fileName, :filePath, :fileSize, :fileExtension, :fileUniqID)
        ";

        $params = [
            'fileAddDate' => $fileData['fileAddDate'],
            'fileName' => $fileData['fileName'],
            'filePath' => $fileData['filePath'],
            'fileSize' => $fileData['fileSize'],
            'fileExtension' => $fileData['fileExtension'],
            'fileUniqID' => $fileData['fileUniqID']
        ];

        $this->db->beginTransaction();

        $fileResult = $this->db->insert($fileQuery, $params);

        if ($fileResult) {
            $this->db->commit();
            return $fileResult;
        }
        $this->db->rollBack();
        return false;

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
        return [];
    }
    public function uploadFile($fileName,$newFileName,$file,$folderName){

        $targetDir = FILE; //"/Public/File/"

        $fileFileType = strtolower(pathinfo($file["name"],PATHINFO_EXTENSION));

        $folderName = $folderName ?? 'Page';

        $folderID = $this->getImageFolderIDByFolderName($folderName);

        $targetDir = $targetDir.$folderName."/"; //"/Public/File/Product"

        $targetFile = $targetDir . $newFileName . '.' . $fileFileType; //"/Public/File/Product/1.xls"

        $filePath = $newFileName . '.' . $fileFileType;

        if (file_exists($targetFile)) {
            $newFileName = $newFileName . uniqid();
            $targetFile = $targetDir . $newFileName . '.' . $fileFileType;

            $filePath = $newFileName . '.' . $fileFileType;
        }



        //dosya boyutunu alalım
        $fileSize = $file["size"];

        if ($fileSize <= 0) {
            return [
                'status' => 'error',
                'message' => 'Dosya boyutu çok büyük'
            ];
        }

        $acceptedFiles = ['cvs', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'zip', 'rar', 'excel', 'odf', 'odp', 'xml'];

        if (!in_array($fileFileType, $acceptedFiles)) {
            return [
                'status' => 'error',
                'message' => 'Dosya türü desteklenmiyor'
            ];
        }

        //10MB büyük ise kabul etmeyelim
        if ($fileSize > 10*1024*1024) {
            return [
                'status' => 'error',
                'message' => 'Dosya boyutu çok büyük'
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
                'filePath' => $filePath,
                'fileFolderID' => $folderID,
                'fileFolderName' => $folderName,
                'fileName' => $fileName,
                'fileExtension' => $fileFileType,
                'fileSize' => $fileSize
            ];
        }
        else {
            return [
                'status' => 'error',
                'message' => 'Dosya yüklenirken hata oluştu'
            ];
        }
    }
}