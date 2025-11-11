<?php
class Appointment
{
    private $conn;
    private $table = "appointment";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // ✅ Generate Appointment ID (e.g. 2025-01-0000001)
    private function generateAppointmentID($APPT_DATE)
    {
        // Extract year and month from the given date (used only for formatting)
        $year  = date('Y', strtotime($APPT_DATE));
        $month = date('m', strtotime($APPT_DATE));

        // Get the total count of all appointments (no reset per month/year)
        $sql = "SELECT COUNT(*) AS count FROM {$this->table}";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'] + 1;

        // Format to 7 digits (with leading zeros)
        $sequence = str_pad($count, 7, "0", STR_PAD_LEFT);

        // Return appointment ID using the given date (random date supported)
        return "$year-$month-$sequence";
    }

    // ✅ Create new appointment
    public function create($PAT_ID, $DOC_ID, $SERV_ID, $STAT_ID, $APPT_DATE, $APPT_TIME)
    {
        $APPT_ID = $this->generateAppointmentID($APPT_DATE);

        $sql = "INSERT INTO {$this->table} 
                (APPT_ID, PAT_ID, DOC_ID, SERV_ID, STAT_ID, APPT_DATE, APPT_TIME,  APPT_CREATED_AT)
                VALUES (:APPT_ID, :PAT_ID, :DOC_ID, :SERV_ID, :STAT_ID, :APPT_DATE, :APPT_TIME,  NOW())";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ":APPT_ID" => $APPT_ID,
            ":PAT_ID" => $PAT_ID,
            ":DOC_ID" => $DOC_ID,
            ":SERV_ID" => $SERV_ID,
            ":STAT_ID" => $STAT_ID,
            ":APPT_DATE" => $APPT_DATE,
            ":APPT_TIME" => $APPT_TIME,
        ]);

        return $APPT_ID; // Return generated appointment ID for display
    }

    // ✅ Search appointment by ID
    public function findByID($APPT_ID)
    {
        $sql = "SELECT a.*, 
                       p.PAT_FNAME, p.PAT_LNAME,
                       d.DOC_FNAME, d.DOC_LNAME,
                       s.SERV_NAME, st.STAT_NAME
                FROM {$this->table} a
                JOIN patient p ON a.PAT_ID = p.PAT_ID
                JOIN doctor d ON a.DOC_ID = d.DOC_ID
                JOIN service s ON a.SERV_ID = s.SERV_ID
                JOIN status st ON a.STAT_ID = st.STAT_ID
                WHERE a.APPT_ID = :APPT_ID";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([":APPT_ID" => $APPT_ID]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    // ✅ Update appointment details
    public function update($APPT_ID, $DOC_ID, $SERV_ID, $APPT_DATE, $APPT_TIME)
    {
        $sql = "UPDATE {$this->table} 
                SET DOC_ID = :DOC_ID, 
                    SERV_ID = :SERV_ID,
                    APPT_DATE = :APPT_DATE,
                    APPT_TIME = :APPT_TIME,
                    APPT_UPDATED_AT = NOW()
                WHERE APPT_ID = :APPT_ID";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ":DOC_ID" => $DOC_ID,
            ":SERV_ID" => $SERV_ID,
            ":APPT_DATE" => $APPT_DATE,
            ":APPT_TIME" => $APPT_TIME,
            ":APPT_ID" => $APPT_ID
        ]);
    }

    // ✅ Cancel appointment (optional: set status to “Cancelled”)
    public function cancel($APPT_ID)
    {
        $sql = "UPDATE {$this->table} 
                SET STAT_ID = (SELECT STAT_ID FROM status WHERE STAT_NAME = 'Cancelled' LIMIT 1),
                    APPT_UPDATED_AT = NOW()
                WHERE APPT_ID = :APPT_ID";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([":APPT_ID" => $APPT_ID]);
    }

    // ✅ Update appointment status (Scheduled, Completed, Cancelled)
    public function updateStatus($APPT_ID, $STAT_ID)
    {
        $sql = "UPDATE {$this->table}
                SET STAT_ID = :STAT_ID,
                    APPT_UPDATED_AT = NOW()
                WHERE APPT_ID = :APPT_ID";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ":STAT_ID" => $STAT_ID,
            ":APPT_ID" => $APPT_ID
        ]);
    }

    // ✅ Fetch all appointments with related data
    public function getAllAppointments()
    {
        $sql = "SELECT a.*, 
                       p.pat_first_name AS pat_fname, p.pat_last_name AS pat_lname,
                       d.doc_first_name AS doc_fname, d.doc_last_name AS doc_lname,
                       s.serv_name, st.stat_name
                FROM {$this->table} a
                JOIN patient p ON a.pat_id = p.pat_id
                JOIN doctor d ON a.doc_id = d.doc_id
                JOIN service s ON a.serv_id = s.serv_id
                JOIN status st ON a.stat_id = st.stat_id
                ORDER BY a.appt_date, a.appt_time";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
