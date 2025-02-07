<?php

namespace App\entities;

class Tag
{
    private int $id;
    private string $tagName;

    public function __construct(int $id, string $tagName){
        $this->id = $id;
        $this->tagName = $tagName;
    }
    public function getId(): int{
        return $this->id;
    }
    public function getTagName(): string{
        return $this->tagName;
    }
    public function setTagName(string $tagName): void{
        $this->tagName = $tagName;
    }
}