<?php
class File
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function getFileById($fileId){
        $sql = "
            SELECT
                dosyaid as fileID, 
                dosyaad as fileName,
                dosya as filePath,
                dosyaboyut as fileSize,
                dosyauzanti as fileExtension,
                resimklasorad as fileFolderName
            FROM 
                dosya
                    INNER JOIN resimklasor ON resimklasor.resimklasorid = dosya.dosyaklasorid
            WHERE 
                dosyaid = :fileId
        ";
        return $this->db->select($sql, ['fileId' => $fileId]);
    }
    public function getFiles($limit,$currentPage): array
    {
        $currentPage = $currentPage<=0 ? 1 : $currentPage;
        $start = ($currentPage-1)*$limit;

        $fileQuery = "
            SELECT
                dosyaid as fileID, 
                dosyaad as fileName,
                dosya as filePath,
                dosyaboyut as fileSize,
                dosyauzanti as fileExtension,
                resimklasorad as fileFolderName
            FROM 
                dosya 
                INNER JOIN resimklasor 
                    ON resimklasor.resimklasorid = dosya.dosyaklasorid
            LIMIT $start,$limit
        ";

        return $this->db->select($fileQuery);

    }

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
}