<?php
/**
 *CREATE TABLE videos (
 * video_id INT AUTO_INCREMENT PRIMARY KEY,
 * created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 * updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 * video_name VARCHAR(100),
 * video_file VARCHAR(255),
 * video_extension VARCHAR(4),
 * video_size VARCHAR(12),
 * video_width INT,
 * video_height INT,
 * unique_id CHAR(20),
 * video_iframe TEXT,
 * description TEXT,
 * );
 */

/**
 * video_id: Video'nun benzersiz ID'si.
 * created_at: Video'nun oluşturulma tarihi. Bu alan otomatik olarak doldurulur.
 * updated_at: Video'nun son güncelleme tarihi. Bu alan otomatik olarak güncellenir.
 * video_name: Video'nun adı.
 * video_file: Video dosyasının adı.
 * video_extension: Video dosyasının uzantısı.
 * video_size: Video dosyasının boyutu.
 * video_width: Video'nun genişliği.
 * video_height: Video'nun yüksekliği.
 * unique_id: Video'nun benzersiz ID'si.
 * video_iframe: Video'nun iframe kodu. Bu alan, video bir iframe ise doldurulur.
 * description: Video'nun açıklaması
 */
class AdminVideo {
    private AdminDatabase $db;

    public function __construct(AdminDatabase $db) {
        $this->db = $db;
    }

    public function getVideos() {
        $sql = "SELECT * FROM videos WHERE is_deleted=0";
        $result = $this->db->select($sql);

        if (!$result) {
            return [];
        }

        return $result;
    }

    public function getVideoById(int $videoId) {
        $sql = "SELECT * FROM videos WHERE video_id = :videoId";
        return $this->db->select($sql, ['videoId' => $videoId]);
    }

    public function addVideo(array $data) {
        $sql = "
            INSERT INTO videos 
                (video_name, video_file, video_extension, video_size, video_width, video_height, unique_id, video_iframe, description, is_deleted) 
            VALUES (:video_name, :video_file, :video_extension, :video_size, :video_width, :video_height, :unique_id, :video_iframe, :description, :is_deleted)";

        $result = $this->db->insert($sql, $data);

        if (!$result) {
            return false;
        }

        return $result;
    }

    public function updateVideo(array $data) {
        $sql = "
            UPDATE videos 
            SET 
                video_name = :video_name, 
                video_file = :video_file, 
                video_extension = :video_extension, 
                video_size = :video_size, 
                video_width = :video_width, 
                video_height = :video_height,
                video_iframe = :video_iframe, 
                description = :description 
            WHERE 
                video_id = :video_id";

        $result = $this->db->update($sql, $data);

        if (!$result) {
            return false;
        }

        return true;
    }

    public function deleteVideo(int $videoId) {
        $sql = "UPDATE videos SET is_deleted = 1 WHERE video_id = :video_id";
        $result = $this->db->update($sql, ['video_id' => $videoId]);

        if (!$result) {
            return false;
        }

        return true;
    }

    public function uploadVideo(string $newVideoName, $file, string $folderName) {

        $uploadPath = PUBL.$folderName."/";
        // Check if directory exists, if not, create it
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        $targetFile = $uploadPath . $newVideoName;
        $videoExtension = pathinfo($file["name"], PATHINFO_EXTENSION);
        $targetFile = $targetFile ."." . $videoExtension;

        if(file_exists($targetFile)){
            $newVideoName = $newVideoName . "_" . rand(10000, 99999);
            $targetFile = $uploadPath . $newVideoName . "." . $videoExtension;
        }

        if (move_uploaded_file($file["tmp_name"], $targetFile)) {
            $videoInf = getimagesize($targetFile);
            $videoWidth = 1280;
            $videoHeight = 720;
            $videoSize = $file["size"];
            $videoFile = $folderName."/".$newVideoName . "." . $videoExtension;;

            return [
                'status' => 'success',
                'videoData' => [
                    'video_file' => $videoFile,
                    'video_extension' => $videoExtension,
                    'video_size' => $videoSize,
                    'video_width' => $videoWidth,
                    'video_height' => $videoHeight
                ]
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Video upload failed'
            ];
        }
    }

    public function searchVideo($searchText){

        $searchText = "%$searchText%";
        $sql = "
            SELECT 
                *
            FROM 
                videos 
            WHERE 
               video_name LIKE :searchText
            GROUP BY 
                video_id
            ORDER BY 
                video_name ASC
        ";
        return $this->db->select($sql, ['searchText' => $searchText]);
    }

    public function beginTransaction() {
        $this->db->beginTransaction();
    }

    public function commit() {
        $this->db->commit();
    }

    public function rollback() {
        $this->db->rollback();
    }
}