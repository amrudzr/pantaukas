<?php

class PaymentCashModel
{
    private $db;

    public function __construct()
    {
        $this->db = getDbConnection();
    }

    public function all($keyword = '')
    {
        $userId = $_SESSION['user_id'];

        $sql = "SELECT pc.*, tc.name AS category_name 
                FROM payment_cash pc 
                LEFT JOIN type_cash tc ON pc.id_type_cash = tc.id 
                WHERE pc.id_user = ? AND pc.deleted_at IS NULL
                ORDER BY pc.updated_at DESC";

        if ($keyword !== '') {
            $sql .= " AND (pc.title LIKE ? OR tc.name LIKE ?)";
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
        $stmt = $this->db->prepare("SELECT * FROM payment_cash WHERE id = ? AND id_user = ? AND deleted_at IS NULL");
        $stmt->bind_param("ii", $id, $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function create($data)
    {
        $stmt = $this->db->prepare("INSERT INTO payment_cash 
            (id_type_cash, id_user, title, payment_date, nominal, type, attachment, notes) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->bind_param(
            "iississs",
            $data['id_type_cash'],
            $data['id_user'],
            $data['title'],
            $data['payment_date'],
            $data['nominal'],
            $data['type'],
            $data['attachment'],
            $data['notes']
        );

        return $stmt->execute() ? $this->db->insert_id : false;
    }

    public function update($id, $data)
    {
        $stmt = $this->db->prepare("
            UPDATE payment_cash SET
                id_type_cash = ?, 
                title = ?, 
                payment_date = ?, 
                nominal = ?, 
                type = ?, 
                attachment = ?, 
                notes = ?, 
                updated_at = NOW()
            WHERE id = ? AND id_user = ?
        ");

        $stmt->bind_param(
            "ississsii",
            $data['id_type_cash'],
            $data['title'],
            $data['payment_date'],
            $data['nominal'],
            $data['type'],
            $data['attachment'],
            $data['notes'],
            $id,
            $_SESSION['user_id']
        );

        return $stmt->execute();
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("UPDATE payment_cash SET deleted_at = NOW() WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function sumByTypeAndDate($type, $month, $year)
    {
        $userId = $_SESSION['user_id'];
        $stmt = $this->db->prepare("
            SELECT SUM(nominal) as total 
            FROM payment_cash 
            WHERE id_user = ? 
                AND type = ? 
                AND deleted_at IS NULL
                AND MONTH(payment_date) = ? 
                AND YEAR(payment_date) = ?
        ");
        $stmt->bind_param("isii", $userId, $type, $month, $year);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total'] ?? 0;
    }

    public function sumByCategoryAndDate($categoryName, $month, $year)
    {
        $userId = $_SESSION['user_id'];
        $startDate = "$year-$month-01";
        $endDate = date("Y-m-t", strtotime($startDate));

        // Asumsi kita mencari berdasarkan nama kategori (tc.name) bukan kolom category
        $stmt = $this->db->prepare("
            SELECT SUM(pc.nominal) as total 
            FROM payment_cash pc
            LEFT JOIN type_cash tc ON pc.id_type_cash = tc.id
            WHERE pc.id_user = ?
                AND pc.type = 'in'
                AND tc.name LIKE ?
                AND pc.payment_date BETWEEN ? AND ?
                AND pc.deleted_at IS NULL
        ");
        $categorySearch = '%' . $categoryName . '%';
        $stmt->bind_param("isss", $userId, $categorySearch, $startDate, $endDate);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return (int) ($result['total'] ?? 0);
    }

    public function getRecentTransactions($limit = 5)
    {
        $userId = $_SESSION['user_id'];
        $stmt = $this->db->prepare("
            SELECT pc.*, tc.name as category_name 
            FROM payment_cash pc
            LEFT JOIN type_cash tc ON pc.id_type_cash = tc.id
            WHERE pc.id_user = ? AND pc.deleted_at IS NULL
            ORDER BY pc.payment_date DESC, pc.created_at DESC
            LIMIT ?
        ");
        $stmt->bind_param("ii", $userId, $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
