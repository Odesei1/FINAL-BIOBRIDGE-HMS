<?php
require_once __DIR__ . "/../config/db.php";

class MedicalRecords {
    private $conn;
    private $table = "medical_record"; // Make sure this matches your actual DB table name

    public function __construct($db) {
        $this->conn = $db; // Receive the database connection
    }

    // CREATE new medical record
    public function add($MED_REC_DIAGNOSIS, $MED_REC_PRESCRIPTION, $MED_REC_VISIT_DATE, $APPT_ID) {
        $sql = "INSERT INTO {$this->table} (med_rec_diagnosis, med_rec_prescription, med_rec_visit_date, appt_id)
                VALUES (:MED_REC_DIAGNOSIS, :MED_REC_PRESCRIPTION, :MED_REC_VISIT_DATE, :APPT_ID)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ":MED_REC_DIAGNOSIS" => $MED_REC_DIAGNOSIS,
            ":MED_REC_PRESCRIPTION" => $MED_REC_PRESCRIPTION,
            ":MED_REC_VISIT_DATE" => $MED_REC_VISIT_DATE,
            ":APPT_ID" => $APPT_ID
        ]);
    }

    // READ all medical records with appointment details
public function all() {
    $sql = "SELECT
                m.med_rec_id,
                m.med_rec_diagnosis,
                m.med_rec_prescription,
                m.med_rec_visit_date,
                a.appt_id,
                a.appt_date
            FROM {$this->table} m
            LEFT JOIN appointment a ON m.appt_id = a.appt_id";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    // UPDATE existing medical record
    public function update($MED_REC_ID, $MED_REC_DIAGNOSIS, $MED_REC_PRESCRIPTION, $MED_REC_VISIT_DATE, $APPT_ID) {
        $sql = "UPDATE {$this->table}
                SET
                    med_rec_diagnosis = :MED_REC_DIAGNOSIS,
                    med_rec_prescription = :MED_REC_PRESCRIPTION,
                    med_rec_visit_date = :MED_REC_VISIT_DATE,
                    appt_id = :APPT_ID
                WHERE med_rec_id = :MED_REC_ID";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ":MED_REC_DIAGNOSIS" => $MED_REC_DIAGNOSIS,
            ":MED_REC_PRESCRIPTION" => $MED_REC_PRESCRIPTION,
            ":MED_REC_VISIT_DATE" => $MED_REC_VISIT_DATE,
            ":APPT_ID" => $APPT_ID,
            ":MED_REC_ID" => $MED_REC_ID
        ]);
    }

    // DELETE a medical record
    public function delete($MED_REC_ID) {
        $sql = "DELETE FROM {$this->table} WHERE med_rec_id = :MED_REC_ID";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([":MED_REC_ID" => $MED_REC_ID]);
    }
}
?>