<?php

class BalanceTransfer {
    private $pdo;
    private $fromSim;
    private $toSim;
    private $amount;
    private $comment;

    public function __construct(PDO $pdo, SimCard $fromSim, SimCard $toSim, $amount, $comment) {
        $this->pdo = $pdo;
        $this->fromSim = $fromSim;
        $this->toSim = $toSim;
        $this->amount = $amount;
        $this->comment = $comment;
    }

    public function execute() {
        try {
            $this->pdo->beginTransaction();

            $this->fromSim->updateBalance($this->amount, $this->comment, true);

            $this->toSim->updateBalance($this->amount, $this->comment, false);

            $this->pdo->commit();

            echo "Перенос баланса выполнен успешно.";
        } catch (Exception $e) {
            $this->pdo->rollBack();
            die("Ошибка: " . $e->getMessage());
        }
    }
}
