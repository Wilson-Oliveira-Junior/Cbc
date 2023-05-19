<?php

class Clube {
    private $id;
    private $clube;
    private $saldoDisponivel;

    public function __construct($id, $clube, $saldoDisponivel) {
        $this->id = $id;
        $this->clube = $clube;
        $this->saldoDisponivel = $saldoDisponivel;
    }

    // Métodos getter e setter para os atributos da classe

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getClube() {
        return $this->clube;
    }

    public function setClube($clube) {
        $this->clube = $clube;
    }

    public function getSaldoDisponivel() {
        return $this->saldoDisponivel;
    }

    public function setSaldoDisponivel($saldoDisponivel) {
        $this->saldoDisponivel = $saldoDisponivel;
    }
}

?>
