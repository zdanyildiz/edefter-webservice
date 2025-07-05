<?php

/**
 * Table: formiletisim
 * Columns:
 * formid int AI PK
 * formcevapid int
 * tarih datetime
 * adsoyad varchar(100)
 * telefon varchar(100)
 * eposta varchar(100)
 * mesaj longtext
 * formbildirim tinyint(1)
 * formsil tinyint(1)
 */

/**
 * Table: newsletter
 * Columns:
 * id int AI PK
 * name varchar(255)
 * email varchar(255)
 * created_at timestamp
 * updated_at timestamp
 */
class AdminForm
{
    private AdminDatabase $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getContactForms($sort = 'tarih DESC', $limit = 10, $offset = 0) {
        $sql = "
            SELECT 
                * 
            FROM 
                formiletisim
            WHERE 
                formcevapid = 0 and formsil = 0
            ORDER BY 
                $sort
            LIMIT :limit OFFSET :offset
        ";

        return $this->db->select($sql, [
            'limit' => $limit,
            'offset' => $offset
        ]);
    }

    public function getContactFormById($id) {
        $sql = "
            SELECT
                *
            FROM
                formiletisim
            WHERE
                formid = :id
        ";

        $form = $this->db->select($sql, [
            'id' => $id
        ]);

        if (!empty($form)) {
            $form = $form[0];
            $form['responses'] = $this->getFormResponses($id);
        }

        return $form;
    }

    private function getFormResponses($formId) {
        $sql = "
            SELECT
                *
            FROM
                formiletisim
            WHERE
                formcevapid = :formId
        ";

        $responses = $this->db->select($sql, [
            'formId' => $formId
        ]);

        foreach ($responses as &$response) {
            $response['subResponses'] = $this->getFormResponses($response['formid']);
        }

        return $responses;
    }

    public function addFormResponse($formId, $responseData) {
        $responseDate = date('Y-m-d H:i:s');
        $name = $responseData['name'];
        $email = $responseData['email'];
        $message = $responseData['message'];
        $phone = $responseData['phone'];
        $formNotification = 1;
        $formDelete = 0;

        $sql = "
            INSERT 
                INTO formiletisim 
                    (formcevapid, tarih, adsoyad, telefon, eposta, mesaj, formbildirim, formsil) 
                VALUES 
                    (:formcevapid, :tarih, :adsoyad, :telefon, :eposta, :mesaj, :formbildirim, :formsil)
        ";
        $insertParams = [
            'formcevapid' => $formId,
            'tarih' => $responseDate,
            'adsoyad' => $name,
            'telefon' => $phone,
            'eposta' => $email,
            'mesaj' => $message,
            'formbildirim' => $formNotification,
            'formsil' => $formDelete
        ];

        return $this->db->insert($sql, $insertParams);
    }

    public function updateContactForm($formId, $formData) {
        $sql = "
            UPDATE formiletisim
            SET
                adsoyad = :adsoyad,
                telefon = :telefon,
                eposta = :eposta,
                mesaj = :mesaj,
                formbildirim = :formbildirim,
                formsil = :formsil
            WHERE
                formid = :formid
        ";

        $updateParams = [
            'adsoyad' => $formData['name'],
            'telefon' => $formData['phone'],
            'eposta' => $formData['email'],
            'mesaj' => $formData['message'],
            'formbildirim' => $formData['formNotification'],
            'formsil' => $formData['formDelete'],
            'formid' => $formId
        ];

        return $this->db->update($sql, $updateParams);
    }

    public function markAsRead($formId)
    {
        $sql = "
            UPDATE 
                formiletisim
            SET
                formbildirim = 0
            WHERE
                formid = :formId
        ";

        $params = ["formId" => $formId];

        return $this->db->update($sql, $params);
    }

    public function deleteContactForm($formId) {
        $sql = "
            UPDATE formiletisim
            SET
                formsil = 1
            WHERE
                formid = :formid
        ";

        $updateParams = [
            'formid' => $formId
        ];

        return $this->db->update($sql, $updateParams);
    }

    public function updateNewsletterForm($newsletterId, $data) {
        $sql = "
            UPDATE newsletter
            SET
                name = :name,
                email = :email
            WHERE
                id = :id
        ";

        $updateParams = [
            'name' => $data['name'],
            'email' => $data['email'],
            'id' => $newsletterId
        ];

        return $this->db->update($sql, $updateParams);
    }

    public function getNewsletterForms($sort = 'id DESC', $limit = 10, $offset = 0) {
        $sql = "
            SELECT
                *
            FROM
                newsletter
            ORDER BY
                $sort
            LIMIT :limit OFFSET :offset
        ";

        return $this->db->select($sql, [
            'limit' => $limit,
            'offset' => $offset
        ]);
    }

    public function addNewsletterForm($data)
    {
        $sql = "
            INSERT 
                INTO newsletter (name, email)
                VALUES (:name, :email)
        ";

        return $this->db->insert($sql, $data);
    }

    public function checkNewsletterForms($email)
    {
        $sql = "
            SELECT * FROM newsletter WHERE email = :email
        ";

        return $this->db->select($sql, [
            'email' => $email
        ]);

    }

    public function deleteNewsletterForm($email) {
        $sql = "
            DELETE FROM newsletter
            WHERE email = :email
        ";

        $deleteParams = [
            'email' => $email
        ];

        return $this->db->delete($sql, $deleteParams);
    }

    public function beginTransaction($funcName="")
    {
        $this->db->beginTransaction($funcName);
    }

    public function commit($funcName=""){
        $this->db->commit($funcName);
    }

    public function rollBack($funcName=""){
        $this->db->rollBack($funcName);
    }
}