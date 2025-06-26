<?php

class TypeFeeModel
{
    private $db;

    public function __construct()
    {
        $this->db = getDbConnection();
    }

    public function all()
    {
        $userId = $_SESSION['user_id'];
        $stmt = $this->db->prepare("
            SELECT * FROM type_fee 
            WHERE id_user = ? AND deleted_at IS NULL
            ORDER BY name ASC
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function find($id)
    {
        $userId = $_SESSION['user_id'];
        $stmt = $this->db->prepare("
            SELECT * FROM type_fee 
            WHERE id = ? AND id_user = ? AND deleted_at IS NULL
        ");
        $stmt->bind_param("ii", $id, $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getNominal($id)
    {
        $userId = $_SESSION['user_id'];
        $stmt = $this->db->prepare("
            SELECT nominal FROM type_fee 
            WHERE id = ? AND id_user = ? AND deleted_at IS NULL
        ");
        $stmt->bind_param("ii", $id, $userId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['nominal'] ?? 0;
    }

    public function create($id_user, $name, $nominal, $duration, $description)
    {
        $stmt = $this->db->prepare("
            INSERT INTO type_fee 
            (id_user, name, nominal, duration, description) 
            VALUES (?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "isiss",
            $id_user,
            $name,
            $nominal,
            $duration,
            $description
        );

        return $stmt->execute() ? $this->db->insert_id : false;
    }

    public function update($id, $name, $nominal, $duration, $description)
    {
        $stmt = $this->db->prepare("
            UPDATE type_fee SET
                name = ?,
                nominal = ?,
                duration = ?,
                description = ?,
                updated_at = NOW()
            WHERE id = ? AND id_user = ?
        ");

        $stmt->bind_param(
            "sissii",
            $name,
            $nominal,
            $duration,
            $description,
            $id,
            $_SESSION['user_id']
        );

        return $stmt->execute();
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("UPDATE type_fee SET deleted_at = NOW() WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
