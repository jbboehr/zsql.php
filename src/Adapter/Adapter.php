<?php

namespace zsql\Adapter;

interface Adapter {
    public function getAffectedRows();
    public function getInsertId();
    public function getQueryCount();

    public function query($query);

    public function select();
    public function insert();
    public function update();
    public function delete();
}
