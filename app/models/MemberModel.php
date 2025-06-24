<?php
class MemberModel
{
    private $db;

    public function __construct()
    {
        $this->db = getDbConnection(); // Fungsi dari config database
    }

    public function all($keyword = '')
    {
        $userId = $_SESSION['user_id']; // pastikan sudah login
        $sql = "SELECT * FROM member WHERE id_user = ? AND deleted_at IS NULL";

        if ($keyword !== '') {
            $sql .= " AND (name LIKE ? OR address LIKE ?)";
            $stmt = $this->db->prepare($sql);
            $like = '%' . $keyword . '%';
            $stmt->bind_param("iss", $userId, $like, $like);
        } else {
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i", $userId);
        }

        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function find($id)
    {
        $userId = $_SESSION['user_id'];
        $stmt = $this->db->prepare("SELECT * FROM member WHERE id = ? AND id_user = ? AND deleted_at IS NULL");
        $stmt->bind_param("ii", $id, $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function create($userId, $name, $phone, $address, $status)
    {
        $userId = $_SESSION['user_id'];
        $stmt = $this->db->prepare("INSERT INTO member (id_user, name, phone, address, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $userId, $name, $phone, $address, $status);
        return $stmt->execute() ? $this->db->insert_id : false;
    }

    public function update($id, $name, $phone, $address, $status)
    {
        $stmt = $this->db->prepare("UPDATE member SET name = ?, phone = ?, address = ?, status = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $name, $phone, $address, $status, $id);
        return $stmt->execute();
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("UPDATE member SET deleted_at = NOW() WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
