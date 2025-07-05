<?php
class Video {
    private Database $db;

    public function __construct(Database $db) {
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

}