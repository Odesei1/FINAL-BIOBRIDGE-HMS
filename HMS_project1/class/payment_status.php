<?php
require_once "../config/db.php";
class payment_status{
    private $conn;
    private $table = "payment_status";

public function __construct($db){
    $this->conn = $db;
}
public function add($PYMT_STAT_NAME){
    $sql = "INSERT INTO {$this->table} (pymt_stat_name, pymt_stat_created_at)
    VALUES (:PYMT_STAT_NAME, NOW())";
    $stmt = $this->conn->prepare($sql);
    return $stmt->execute([":PYMT_STAT_NAME" => $PYMT_STAT_NAME,]);
}
public function all(){
    $sql = "SELECT * FROM {$this->table} ORDER BY pymt_stat_id ASC";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    public function edit($PYMT_STAT_ID) {
        $sql = "SELECT * FROM {$this->table} WHERE pymt_stat_id = :PYMT_STAT_ID";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([":PYMT_STAT_ID" => $PYMT_STAT_ID]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($PYMT_STAT_ID, $PYMT_STAT_NAME) {
        $sql = "UPDATE {$this->table}
                SET pymt_stat_name = :PYMT_STAT_NAME,
                    pymt_stat_updated_at = NOW()
                WHERE pymt_stat_id = :PYMT_STAT_ID";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ":PYMT_STAT_NAME" => $PYMT_STAT_NAME,
            ":PYMT_STAT_ID" => $PYMT_STAT_ID
        ]);
    }

public function delete($PYMT_STAT_ID){
    $sql = "DELETE FROM {$this->table} WHERE pymt_stat_id = :PYMT_STAT_ID";
    $stmt = $this->conn->prepare($sql);
    return $stmt->execute([":PYMT_STAT_ID" => $PYMT_STAT_ID]);
}
}
?>