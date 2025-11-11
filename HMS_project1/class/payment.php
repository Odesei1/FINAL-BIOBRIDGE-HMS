<?php
require_once "../config/db.php";
class Payment{
    private $conn;
    private $table = "payment";

public function __construct($db){
      $this->conn = $db;
}
public function add($pymt_amount_paid, $pymt_date, $pymt_meth_id, $pymt_stat_id, $appt_id){
    $sql = "INSERT INTO {$this->table} (pymt_amount_paid, pymt_date, pymt_meth_id, pymt_stat_id, appt_id)
        VALUES (:pymt_amount_paid, :pymt_date, :pymt_meth_id, :pymt_stat_id, :appt_id)";
    $stmt =$this->conn->prepare($sql);
    return $stmt->execute([":pymt_amount_paid" => $pymt_amount_paid,
     ":pymt_date" => $pymt_date,
      ":pymt_meth_id" => $pymt_meth_id,
       ":pymt_stat_id" => $pymt_stat_id,
        ":appt_id" => $appt_id]);
}
public function all(){
    $sql = "SELECT p.pymt_id, p.pymt_amount_paid, p.pymt_date, pm.pymt_meth_name, ps.pymt_stat_name, a.appt_id
    FROM {$this->table} p
    LEFT JOIN payment_method pm ON p.pymt_meth_id = pm.pymt_meth_id
    LEFT JOIN payment_status ps ON p.pymt_stat_id = ps.pymt_stat_id
    LEFT JOIN appointment a ON p.appt_id = a.appt_id
    ORDER BY p.pymt_id DESC";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
    public function update($pymt_id, $pymt_amount_paid, $pymt_date, $pymt_meth_id, $pymt_stat_id, $appt_id) {
        $sql = "UPDATE {$this->table}
                SET pymt_amount_paid = :pymt_amount_paid,
                    pymt_date = :pymt_date,
                    pymt_meth_id = :pymt_meth_id,
                    pymt_stat_id = :pymt_stat_id,
                    appt_id = :appt_id
                WHERE pymt_id = :pymt_id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ":pymt_amount_paid" => $pymt_amount_paid,
            ":pymt_date" => $pymt_date,
            ":pymt_meth_id" => $pymt_meth_id,
            ":pymt_stat_id" => $pymt_stat_id,
            ":appt_id" => $appt_id,
            ":pymt_id" => $pymt_id
        ]);
    }

    // ✅ Delete Payment Record
    public function delete($pymt_id) {
        $sql = "DELETE FROM {$this->table} WHERE pymt_id = :pymt_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":pymt_id", $pymt_id);
        return $stmt->execute();
    }
}
?>