<?php

class Form
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function addContactForm($formData)
    {

        $formDate = date('Y-m-d H:i:s');
        $name = $formData['name'];
        $email = $formData['email'];
        $message = $formData['message'];
        $phone = $formData['phone'];
        $formAnswerID = 0;
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
            'formcevapid' => $formAnswerID,
            'tarih' => $formDate,
            'adsoyad' => $name,
            'telefon' => $phone,
            'eposta' => $email,
            'mesaj' => $message,
            'formbildirim' => $formNotification,
            'formsil' => $formDelete
        ];

        return $this->db->insert($sql, $insertParams);

    }

    public function addNewsletter($data)
    {
        $sql = "
            INSERT 
                INTO newsletter (name, email)
                VALUES (:name, :email)
        ";

        return $this->db->insert($sql, $data);
    }

    public function checkNewsletter($email)
    {
        $sql = "
            SELECT * FROM newsletter WHERE email = :email
        ";

        return $this->db->select($sql, [
            'email' => $email
        ]);

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

    public function addAppointmentForm($formData)
    {
        $formDate = date('Y-m-d H:i:s');
        $name = $formData['name'];
        $email = $formData['email'];
        $phone = $formData['phone'];
        $appointmentDate = $formData['appointmentDate'];
        $appointmentTime = $formData['appointmentTime'];
        $message = $formData['message'];
        
        // Randevu mesajını formatla
        $appointmentMessage = "RANDEVU TALEBİ\n";
        $appointmentMessage .= "Randevu Tarihi: " . $appointmentDate . "\n";
        $appointmentMessage .= "Randevu Saati: " . $appointmentTime . "\n";

        $appointmentMessage .= "Mesaj: " . $message;

        $formAnswerID = 0;
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
            'formcevapid' => $formAnswerID,
            'tarih' => $formDate,
            'adsoyad' => $name,
            'telefon' => $phone,
            'eposta' => $email,
            'mesaj' => $appointmentMessage,
            'formbildirim' => $formNotification,
            'formsil' => $formDelete
        ];

        return $this->db->insert($sql, $insertParams);
    }

}