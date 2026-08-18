<?php

class User
{
    private $conn;
    private $table = "users";

    public function __construct($db)
    {
        $this->conn = $db;
    }


    // =========================
    // TÌM USER THEO EMAIL
    // =========================

    public function findByEmail($email)
    {
        $sql = "SELECT *
                FROM {$this->table}
                WHERE email = :email
                LIMIT 1";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(":email", $email);

        $stmt->execute();

        return $stmt->fetch();
    }


    // =========================
    // TÌM USER THEO ID
    // =========================

    public function findById($id)
    {
        $sql = "SELECT *
                FROM {$this->table}
                WHERE id = :id
                LIMIT 1";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(":id", $id);

        $stmt->execute();

        return $stmt->fetch();
    }


    // =========================
    // KIỂM TRA PASSWORD
    // =========================

    public function verifyPassword($password, $hashedPassword)
    {
        return password_verify($password, $hashedPassword);
    }


    // =========================
    // TẠO USER
    // =========================

    public function create($data)
    {
        $sql = "INSERT INTO {$this->table}
                (
                    full_name,
                    msv,
                    class_name,
                    faculty,
                    email,
                    phone,
                    password,
                    role,
                    status
                )
                VALUES
                (
                    :full_name,
                    :msv,
                    :class_name,
                    :faculty,
                    :email,
                    :phone,
                    :password,
                    'member',
                    'active'
                )";

        $stmt = $this->conn->prepare($sql);

        $passwordHash = password_hash(
            $data['password'],
            PASSWORD_DEFAULT
        );

        $stmt->bindParam(
            ":full_name",
            $data['full_name']
        );

        $stmt->bindParam(
            ":msv",
            $data['msv']
        );

        $stmt->bindParam(
            ":class_name",
            $data['class_name']
        );

        $stmt->bindParam(
            ":faculty",
            $data['faculty']
        );

        $stmt->bindParam(
            ":email",
            $data['email']
        );

        $stmt->bindParam(
            ":phone",
            $data['phone']
        );

        $stmt->bindParam(
            ":password",
            $passwordHash
        );

        return $stmt->execute();
    }
}