<?php

namespace App\Entity;

use App\Repository\BadgeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity(repositoryClass=BadgeRepository::class)
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 */
class Badge
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=64, unique=true, nullable=true)
     */
    private $externalId;

    /**
     * @ORM\Column(type="string", length=64)
     * @Assert\NotBlank
     * @Assert\Length(max="64")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max="255")
     */
    private $description;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Range(min="-1000", max="1000")
     */
    private $priority = 0;

    /**
     * @ORM\Column(type="boolean")
     */
    private $visibility = false;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="badges")
     */
    private $category;

    /**
     * @ORM\ManyToMany(targetEntity=Structure::class, inversedBy="visibleBadges")
     * @ORM\JoinTable(
     *  name="badge_visibility",
     *  joinColumns={
     *      @ORM\JoinColumn(name="badge_id", referencedColumnName="id")
     *  },
     *  inverseJoinColumns={
     *      @ORM\JoinColumn(name="structure_id", referencedColumnName="id")
     *  }
     * )
     */
    private $isVisibleFor;

    /**
     * @ORM\Column(type="boolean")
     */
    private $restricted = false;

    /**
     * @ORM\ManyToMany(targetEntity=Structure::class, inversedBy="customBadges")
     * @ORM\JoinTable(
     *  name="badge_restriction",
     *  joinColumns={
     *      @ORM\JoinColumn(name="badge_id", referencedColumnName="id")
     *  },
     *  inverseJoinColumns={
     *      @ORM\JoinColumn(name="structure_id", referencedColumnName="id")
     *  }
     * )
     */
    private $isRestrictedTo;

    /**
     * @ORM\ManyToMany(targetEntity=Volunteer::class, inversedBy="badges")
     */
    private $volunteers;

    /**
     * @ORM\ManyToOne(targetEntity=Badge::class, inversedBy="children", cascade="all")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity=Badge::class, mappedBy="parent", cascade="all")
     */
    private $children;

    /**
     * @ORM\ManyToOne(targetEntity=Badge::class, inversedBy="synonyms", cascade="all")
     */
    private $synonym;

    /**
     * @ORM\OneToMany(targetEntity=Badge::class, mappedBy="synonym", cascade="all")
     */
    private $synonyms;

    public function __construct()
    {
        $this->isVisibleFor   = new ArrayCollection();
        $this->isRestrictedTo = new ArrayCollection();
        $this->volunteers     = new ArrayCollection();
        $this->children       = new ArrayCollection();
        $this->synonyms       = new ArrayCollection();
    }

    public function getId() : ?int
    {
        return $this->id;
    }

    public function getExternalId() : ?string
    {
        return $this->externalId;
    }

    public function setExternalId(string $externalId) : self
    {
        $this->externalId = $externalId;

        return $this;
    }

    public function isExternal() : bool
    {
        return null !== $this->externalId;
    }

    public function getName() : ?string
    {
        return $this->name;
    }

    public function setName(string $name) : self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription() : ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description) : self
    {
        $this->description = $description;

        return $this;
    }

    public function getPriority() : ?int
    {
        return $this->priority;
    }

    public function setPriority(?int $priority) : self
    {
        $this->priority = $priority;

        return $this;
    }

    public function getVisibility() : ?bool
    {
        return $this->visibility;
    }

    public function setVisibility(bool $visibility) : self
    {
        $this->visibility = $visibility;

        return $this;
    }

    public function isVisible() : bool
    {
        return $this->visibility;
    }

    public function getCategory() : ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category) : self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection|Structure[]
     */
    public function getIsVisibleFor() : Collection
    {
        return $this->isVisibleFor;
    }

    public function addIsVisibleFor(Structure $isVisibleFor) : self
    {
        if (!$this->isVisibleFor->contains($isVisibleFor)) {
            $this->isVisibleFor[] = $isVisibleFor;
        }

        return $this;
    }

    public function removeIsVisibleFor(Structure $isVisibleFor) : self
    {
        if ($this->isVisibleFor->contains($isVisibleFor)) {
            $this->isVisibleFor->removeElement($isVisibleFor);
        }

        return $this;
    }

    public function isRestricted() : bool
    {
        return $this->restricted;
    }

    public function setRestricted(bool $restricted) : Badge
    {
        $this->restricted = $restricted;

        return $this;
    }

    /**
     * @return Collection|Structure[]
     */
    public function getIsRestrictedTo() : Collection
    {
        return $this->isRestrictedTo;
    }

    public function addIsRestrictedTo(Structure $isRestrictedTo) : self
    {
        if (!$this->isRestrictedTo->contains($isRestrictedTo)) {
            $this->isRestrictedTo[] = $isRestrictedTo;
        }

        return $this;
    }

    public function removeIsRestrictedTo(Structure $isRestrictedTo) : self
    {
        if ($this->isRestrictedTo->contains($isRestrictedTo)) {
            $this->isRestrictedTo->removeElement($isRestrictedTo);
        }

        return $this;
    }

    /**
     * @return Collection|Volunteer[]
     */
    public function getVolunteers() : Collection
    {
        return $this->volunteers;
    }

    public function addVolunteer(Volunteer $volunteer) : self
    {
        if (!$this->volunteers->contains($volunteer)) {
            $this->volunteers[] = $volunteer;
        }

        return $this;
    }

    public function removeVolunteer(Volunteer $volunteer) : self
    {
        if ($this->volunteers->contains($volunteer)) {
            $this->volunteers->removeElement($volunteer);
        }

        return $this;
    }

    public function getSynonym() : ?self
    {
        return $this->synonym;
    }

    public function setSynonym(?self $synonym) : self
    {
        $this->synonym = $synonym;

        return $this;
    }

    public function getParent() : ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent) : self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getChildren() : Collection
    {
        return $this->children;
    }

    public function addChild(self $child) : self
    {
        if (!$this->children->contains($child)) {
            $this->children[] = $child;
            $child->setParent($this);
        }

        return $this;
    }

    public function removeChild(self $child) : self
    {
        if ($this->children->contains($child)) {
            $this->children->removeElement($child);
            // set the owning side to null (unless already changed)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

        return $this;
    }

    public function getFullName() : string
    {
        if ($this->description) {
            return sprintf('%s (%s)', $this->name, $this->description);
        }

        return $this->name;
    }

    /**
     * @return array
     */
    public function toSearchResults() : array
    {
        return [
            'id'   => (string) $this->getId(),
            'name' => $this->getFullName(),
        ];
    }

    public function __toString()
    {
        return $this->getFullName();
    }

    /**
     * @return Collection|self[]
     */
    public function getSynonyms() : Collection
    {
        return $this->synonyms;
    }

    public function addSynonym(self $synonym) : self
    {
        if (!$this->synonyms->contains($synonym)) {
            $this->synonyms[] = $synonym;
            $synonym->setSynonym($this);
        }

        return $this;
    }

    public function removeSynonym(self $synonym) : self
    {
        if ($this->synonyms->contains($synonym)) {
            $this->synonyms->removeElement($synonym);
            // set the owning side to null (unless already changed)
            if ($synonym->getSynonym() === $this) {
                $synonym->setSynonym(null);
            }
        }

        return $this;
    }

    public function canBeRemoved() : bool
    {
        return null === $this->externalId;
    }

    public function getCoveringBadges(int $stop = null) : array
    {
        $parents = [];
        $ref     = $this->getParent();
        while ($ref && (null === $stop || $ref->getId() !== $stop)) {
            array_unshift($parents, $ref);
            $ref = $ref->getParent();
        }

        return $parents;
    }

    public function getCoveredBadges() : array
    {
        $children = [];

        foreach ($this->getChildren() as $child) {
            $children = array_merge($children, [$child], $child->getCoveredBadges());
        }

        return $children;
    }

    public function isUsable() : bool
    {
        if ($this->isRestricted()) {
            return false;
        }

        if ($this->getSynonyms()) {
            return false;
        }

        return true;
    }

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        if ($this->getParent()) {
            // Infinite loop because parent was bound to one of his children
            if ($this->isParentLooping()) {
                $context
                    ->buildViolation('form.badge.errors.parent.loop', [
                        '%hierarchy%' => implode(' -> ', $this->getCoveringBadges($this->id)),
                    ])
                    ->atPath('parent')
                    ->addViolation();
            }

            // A parent cannot be a synonym
            if ($this->getParent()->getSynonym()) {
                $context
                    ->buildViolation('form.badge.errors.parent.synonym', [
                        '%name%' => $this->getParent()->getSynonym()->getName(),
                    ])
                    ->atPath('parent')
                    ->addViolation();
            }
        }

        foreach ($this->synonyms as $synonym) {
            /** @var Badge $synonym */

            // A badge cannot be marked as synonym if it is enabled
            if ($synonym->isVisible()) {
                $context
                    ->buildViolation('form.badge.errors.synonym.visible', [
                        '%name%' => $synonym->getName(),
                    ])
                    ->atPath('synonyms')
                    ->addViolation();
            }

            // A badge cannot be marked as synonym if it has synonyms himself
            if ($synonym->getSynonyms()->count()) {
                $context
                    ->buildViolation('form.badge.errors.synonym.has_synonyms', [
                        '%name%' => $synonym->getName(),
                    ])
                    ->atPath('synonyms')
                    ->addViolation();
            }

            // cannot have a synonym having parents
            if ($synonym->getParent()) {
                $context
                    ->buildViolation('form.badge.errors.synonym.has_parent', [
                        '%name%' => $synonym->getName(),
                    ])
                    ->atPath('synonyms')
                    ->addViolation();
            }
        }
    }

    private function isParentLooping() : bool
    {
        $ref = $this->getParent();
        while ($ref) {

            if ($ref->id === $this->id) {
                return true;
            }
            $ref = $ref->getParent();
        }

        return false;
    }
}
