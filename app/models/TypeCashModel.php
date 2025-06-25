<?php
class TypeCashModel
{
    private $db;

    public function __construct()
    {
        $this->db = getDbConnection();
    }

    public function all($keyword = '')
    {
        $sql = "SELECT * FROM type_cash
                WHERE deleted_at IS NULL
                ORDER BY updated_at DESC";

        if ($keyword !== '') {
            $sql .= " AND (name LIKE ? OR description LIKE ?)";
            $stmt = $this->db->prepare($sql);
            $like = '%' . $keyword . '%';
            $stmt->bind_param("ss", $like, $like);
        } else {
            $stmt = $this->db->prepare($sql);
        }

        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function find($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM type_cash WHERE id = ? AND deleted_at IS NULL");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function create($userId, $name, $description = null)
    {
        $stmt = $this->db->prepare("INSERT INTO type_cash (id_user, name, description) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $userId, $name, $description);
        return $stmt->execute() ? $this->db->insert_id : false;
    }

    public function update($id, $name, $description = null)
    {
        $stmt = $this->db->prepare("UPDATE type_cash SET name = ?, description = ? WHERE id = ?");
        $stmt->bind_param("ssi", $name, $description, $id);
        return $stmt->execute();
    }

    public function delete($id)
    {
        // Cek apakah kategori memiliki id_user null (tidak bisa dihapus)
        $data = $this->find($id);
        if ($data && $data['id_user'] === null) {
            return false;
        }

        $stmt = $this->db->prepare("UPDATE type_cash SET deleted_at = NOW() WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
