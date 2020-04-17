<?php

class Test
{
    public function Action($paramOne, $paramTwo, $paramThree, $check = [])
    {
        return [$paramOne, $paramTwo, $paramThree, $check];
    }

    public function emptyAction() {
        return [];
    }
}