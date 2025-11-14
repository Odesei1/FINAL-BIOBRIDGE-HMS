<?php
class Service {
    private $conn;
    private $table = "service";

    public function __construct($db){
        $this->conn = $db;
    }

    // ✅ Add new service
    public function add($serv_name, $serv_description, $serv_price){
        $sql = "INSERT INTO {$this->table} (serv_name, serv_description, serv_price, serv_created_at)
                VALUES (:serv_name, :serv_description, :serv_price, NOW())";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ":serv_name" => $serv_name,
            ":serv_description" => $serv_description,
            ":serv_price" => $serv_price
        ]);
    }

    // ✅ View all services
    public function all(){
        $sql = "SELECT * FROM {$this->table} ORDER BY serv_name ASC";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ✅ View appointments by service
    public function getAppointmentsByService($serv_id){
        $sql = "SELECT a.appt_id, a.appt_date, a.appt_time, 
                       p.pat_first_name, p.pat_last_name, 
                       d.doc_first_name, d.doc_last_name, 
                       s.serv_name, st.stat_name
                FROM appointment a
                JOIN patient p ON a.pat_id = p.pat_id
                JOIN doctor d ON a.doc_id = d.doc_id
                JOIN service s ON a.serv_id = s.serv_id
                JOIN status st ON a.stat_id = st.stat_id
                WHERE s.serv_id = :serv_id
                ORDER BY a.appt_date DESC, a.appt_time DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([":serv_id" => $serv_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ✅ Update service
    public function update($serv_id, $serv_name, $serv_description, $serv_price){
        $sql = "UPDATE {$this->table}
                SET serv_name = :serv_name,
                    serv_description = :serv_description,
                    serv_price = :serv_price,
                    serv_updated_at = NOW()
                WHERE serv_id = :serv_id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ":serv_name" => $serv_name,
            ":serv_description" => $serv_description,
            ":serv_price" => $serv_price,
            ":serv_id" => $serv_id
        ]);
    }

    // ✅ Delete service
    public function delete($serv_id){
        $sql = "DELETE FROM {$this->table} WHERE serv_id = :serv_id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ":serv_id" => $serv_id
        ]);
    }

    public function findID($serv_id){
        $sql = "SELECT * FROM {$this->table} WHERE serv_id = :serv_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([":serv_id" => $serv_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
