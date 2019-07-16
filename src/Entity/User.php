<?php


namespace App\Entity;


/**
 * @Entity
 */
class User
{
    /**
     * @Column(type=primary)
     * @var int
     */
    protected $id;

    /**
     * @Column(type=string)
     * @var string
     */
    protected $name;
    /**
     * @Column(type=string)
     * @var string
     */
    protected $password;

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
