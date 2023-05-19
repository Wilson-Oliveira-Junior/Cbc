<?php

class Recurso {
    private $id;
    private $recurso;
    private $saldoDisponivel;

    public function __construct($id, $recurso, $saldoDisponivel) {
        $this->id = $id;
        $this->recurso = $recurso;
        $this->saldoDisponivel = $saldoDisponivel;
    }

    // Métodos getter e setter para os atributos da classe

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getRecurso() {
        return $this->recurso;
    }

    public function setRecurso($recurso) {
        $this->recurso = $recurso;
    }

    public function getSaldoDisponivel() {
        return $this->saldoDisponivel;
    }

    public function setSaldoDisponivel($saldoDisponivel) {
        $this->saldoDisponivel = $saldoDisponivel;
    }
}

?>
