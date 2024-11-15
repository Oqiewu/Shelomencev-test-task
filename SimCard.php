<?php

class SimCard {
    private $pdo;
    private $iccid;
    private $balance;

    public function __construct(PDO $pdo, $simId) {
        $this->pdo = $pdo;
        $this->iccid = $this->fetchIccid($simId);
        $this->balance = $this->fetchBalance();
    }

    private function fetchIccid($simId) {
        $stmt = $this->pdo->prepare("SELECT iccid FROM sim WHERE RIGHT(iccid, 6) = :simid");
        $stmt->execute(['simid' => $simId]);
        $iccid = $stmt->fetchColumn();

        if (!$iccid) {
            throw new Exception("Сим-карта с SIMID $simId не найдена.");
        }

        return $iccid;
    }

    private function fetchBalance() {
        $stmt = $this->pdo->prepare("SELECT balance FROM sim WHERE iccid = :iccid");
        $stmt->execute(['iccid' => $this->iccid]);
        return $stmt->fetchColumn();
    }

    public function getIccid() {
        return $this->iccid;
    }

    public function getBalance() {
        return $this->balance;
    }

    public function updateBalance($amount, $comment, $isDebit = true) {
        if ($isDebit && $this->balance < $amount) {
            throw new Exception("Недостаточно средств на сим-карте с ICCID " . $this->iccid);
        }

        $table = $isDebit ? 'sim_balance_away' : 'sim_balance_come';
        $stmt = $this->pdo->prepare("INSERT INTO $table (iccid, amount, comment) VALUES (:iccid, :amount, :comment)");
        $stmt->execute(['iccid' => $this->iccid, 'amount' => $amount, 'comment' => $comment]);

        $this->balance += $isDebit ? -$amount : $amount;
    }
}
