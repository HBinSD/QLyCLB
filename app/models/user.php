<?php
class User {
    private string $username;
    private string $role;
    private int $status;
    private string $fullname;
    private string $email;
    private ?string $phone;
    private ?string $idNumber;

    public function __construct($username, $role, $status = 1, $fullname = '', $email = '', $phone = null, $idNumber = null) {
        $this->username = $username;
        $this->role     = $role;
        $this->status   = $status;
        $this->fullname = $fullname;
        $this->email    = $email;
        $this->phone    = $phone;
        $this->idNumber = $idNumber;
    }

    // Getters
    public function getUsername() { return $this->username; }
    public function getRole()     { return $this->role; }
    public function getStatus()   { return $this->status; }
    public function getFullname() { return $this->fullname; }
    public function getEmail()    { return $this->email; }
    public function getPhone()    { return $this->phone; }
    public function getIdNumber() { return $this->idNumber; }
}
?>