<?php
namespace App\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
/**
 * @ORM\Entity(repositoryClass="App\Repository\PersonRepository")
 * @Assert\Callback({"App\Entity\Person", "validate"})
 */
class Person
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Sex", inversedBy="people")
     * @ORM\JoinColumn(nullable=false)
     */
    private $sex;
    /**
     * @ORM\Column(type="integer")
     */
    private $age;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Person", inversedBy="fathersChildren")
     */
    private $father;
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Person", mappedBy="father")
     */
    private $fathersChildren;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Person", inversedBy="mothersChildren")
     */
    private $mother;
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Person", mappedBy="mother")
     */
    private $mothersChildren;
    public function __construct()
    {
        $this->fathersChildren = new ArrayCollection();
        $this->mothersChildren = new ArrayCollection();
    }
    public function getId(): ?int
    {
        return $this->id;
    }
    public function getName(): ?string
    {
        return $this->name;
    }
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }
    public function getSex(): ?Sex
    {
        return $this->sex;
    }
    public function setSex(?Sex $sex): self
    {
        $this->sex = $sex;
        return $this;
    }
    public function getAge(): ?int
    {
        return $this->age;
    }
    public function setAge(int $age): self
    {
        $this->age = $age;
        return $this;
    }
    public function getFather(): ?self
    {
        return $this->father;
    }
    public function setFather(?self $father): self
    {
        $this->father = $father;
        return $this;
    }
    /**
     * @return Collection|self[]
     */
    public function getFathersChildren(): Collection
    {
        return $this->fathersChildren;
    }
    public function addFathersChildren(self $fathersChildren): self
    {
        if (!$this->fathersChildren->contains($fathersChildren)) {
            $this->fathersChildren[] = $fathersChildren;
            $fathersChildren->setFather($this);
        }
        return $this;
    }
    public function removeFathersChildren(self $fathersChildren): self
    {
        if ($this->fathersChildren->contains($fathersChildren)) {
            $this->fathersChildren->removeElement($fathersChildren);
            // set the owning side to null (unless already changed)
            if ($fathersChildren->getFather() === $this) {
                $fathersChildren->setFather(null);
            }
        }
        return $this;
    }
    public function getMother(): ?self
    {
        return $this->mother;
    }
    public function setMother(?self $mother): self
    {
        $this->mother = $mother;
        return $this;
    }
    /**
     * @return Collection|self[]
     */
    public function getMothersChildren(): Collection
    {
        return $this->mothersChildren;
    }
    public function addMothersChild(self $mothersChild): self
    {
        if (!$this->mothersChildren->contains($mothersChild)) {
            $this->mothersChildren[] = $mothersChild;
            $mothersChild->setMother($this);
        }
        return $this;
    }
    public function removeMothersChild(self $mothersChild): self
    {
        if ($this->mothersChildren->contains($mothersChild)) {
            $this->mothersChildren->removeElement($mothersChild);
            // set the owning side to null (unless already changed)
            if ($mothersChild->getMother() === $this) {
                $mothersChild->setMother(null);
            }
        }
        return $this;
    }
    public function validate(Person $person, ExecutionContextInterface $context)
    {
        if ($person->getFather() && ($person->getId() === $person->getFather()->getId())) {
            $context->addViolation('A person cannot be his own father');
        }
        if ($person->getMother() && ($person->getId() === $person->getMother()->getId())) {
            $context->addViolation('A person cannot be his own mother');
        }
        if ($person->getFather() && $person->getFather()->getSex() && ($person->getFather()->getSex()->getName() !== 'Male')) {
            $context->addViolation('Father must be a male');
        }
        if ($person->getMother() && $person->getMother()->getSex() && ($person->getMother()->getSex()->getName() !== 'Female')) {
            $context->addViolation('Mother must be a female');
        }
    }
}