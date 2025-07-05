<?php

class AdminDynamicFormBuilder
{
    private AdminDatabase $db;

    public function __construct($db)
    {
        $this->db = $db;
        $this->checkAndCreateTables();
    }

    private function checkAndCreateTables()
    {
        // Table creation queries
        $queries = [
            "CREATE TABLE IF NOT EXISTS Forms (
                form_id INT AUTO_INCREMENT PRIMARY KEY,
                form_name VARCHAR(255) NOT NULL,
                form_description TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME ON UPDATE CURRENT_TIMESTAMP,
                active BOOLEAN DEFAULT TRUE
            ) ENGINE=InnoDB;",

            "CREATE TABLE IF NOT EXISTS Form_Fields (
                field_id INT AUTO_INCREMENT PRIMARY KEY,
                form_id INT,
                field_name VARCHAR(255) NOT NULL,
                field_label VARCHAR(255) NOT NULL,
                field_type ENUM('text', 'textarea', 'select', 'radio', 'checkbox', 'email', 'date') NOT NULL,
                required BOOLEAN DEFAULT FALSE,
                options JSON,
                FOREIGN KEY (form_id) REFERENCES Forms(form_id) ON DELETE CASCADE
            ) ENGINE=InnoDB;",

            "CREATE TABLE IF NOT EXISTS Form_Entries (
                entry_id INT AUTO_INCREMENT PRIMARY KEY,
                form_id INT,
                submitted_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (form_id) REFERENCES Forms(form_id) ON DELETE CASCADE
            ) ENGINE=InnoDB;",

            "CREATE TABLE IF NOT EXISTS Form_Entries_Data (
                data_id INT AUTO_INCREMENT PRIMARY KEY,
                entry_id INT,
                field_id INT,
                field_value TEXT,
                FOREIGN KEY (entry_id) REFERENCES Form_Entries(entry_id) ON DELETE CASCADE,
                FOREIGN KEY (field_id) REFERENCES Form_Fields(field_id) ON DELETE CASCADE
            ) ENGINE=InnoDB;"
        ];

        // Execute each query using createTable() method
        foreach ($queries as $query) {
            if (!$this->db->createTable($query)) {
                //echo "Error creating table: " . $query;
                Log::adminWrite("Error creating table: " . $query, "error");
            }
        }
    }

    public function createForm($formName, $formDescription = null)
    {
        $query = "INSERT INTO Forms (form_name, form_description) VALUES (?, ?)";
        $params = [$formName, $formDescription];
        return $this->db->insert($query, $params);
    }

    public function updateForm($formId, $formName, $formDescription = null)
    {
        $query = "UPDATE Forms SET form_name = ?, form_description = ? WHERE form_id = ?";
        $params = [$formName, $formDescription, $formId];
        return $this->db->update($query, $params) > 0;
    }

    public function deleteForm($formId)
    {
        $query = "DELETE FROM Forms WHERE form_id = ?";
        $params = [$formId];
        return $this->db->delete($query, $params) > 0;
    }

    public function createFormField($formId, $fieldName, $fieldLabel, $fieldType, $required = false, $options = null)
    {
        $query = "INSERT INTO Form_Fields (form_id, field_name, field_label, field_type, required, options) VALUES (?, ?, ?, ?, ?, ?)";
        $params = [$formId, $fieldName, $fieldLabel, $fieldType, $required, $options];
        return $this->db->insert($query, $params);
    }

    public function updateFormField($fieldId, $fieldName, $fieldLabel, $fieldType, $required = false, $options = null)
    {
        $query = "UPDATE Form_Fields SET field_name = ?, field_label = ?, field_type = ?, required = ?, options = ? WHERE field_id = ?";
        $params = [$fieldName, $fieldLabel, $fieldType, $required, $options, $fieldId];
        return $this->db->update($query, $params) > 0;
    }

    public function deleteFormField($fieldId)
    {
        $query = "DELETE FROM Form_Fields WHERE field_id = ?";
        $params = [$fieldId];
        return $this->db->delete($query, $params) > 0;
    }
}

// Usage example:
// Assuming you have a PDO connection instance $db
// $formBuilder = new AdminDynamicFormBuilder($db);
