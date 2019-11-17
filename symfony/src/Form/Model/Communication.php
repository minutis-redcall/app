<?php

namespace App\Form\Model;

use App\Entity\Message;
use App\Validator\Constraints as CustomAssert;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class Communication
{
    /**
     * @var string
     *
     * @Assert\Length(max=255, groups={"label_edition"})
     */
    public $label;

    /**
     * @var string
     *
     * @Assert\Choice(choices = {
     *     \App\Entity\Communication::TYPE_SMS,
     *     \App\Entity\Communication::TYPE_EMAIL
     * })
     */
    public $type = \App\Entity\Communication::TYPE_SMS;

    /**
     * @var Collection
     *
     * @Assert\Count(min="1", minMessage="form.campaign.errors.volunteers.min")
     */
    public $volunteers;

    /**
     * @var string
     *
     * @Assert\Length(max=80)
     */
    public $subject;

    /**
     * @var string
     *
     * @Assert\NotNull(message="form.campaign.errors.message.empty")
     * @Assert\Length(min=Message::MIN_LENGTH)
     */
    public $message;

    /**
     * @var array
     *
     * @Assert\Count(max=9)
     */
    public $answers;

    /**
     * @var boolean
     */
    public $geoLocation;

    /**
     * @var boolean
     */
    public $multipleAnswer;

    /**
     * @var string|null
     *
     * @Assert\Length(min=1, max=1)
     * @Assert\Regex(pattern="/^[A-Z]$/u")
     * @CustomAssert\UnusedPrefix()
     */
    public $prefix;

    /**
     * @param ExecutionContextInterface $context
     * @param                           $payload
     *
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        if ($this->type === \App\Entity\Communication::TYPE_EMAIL) {
            if ($this->geoLocation) {
                $context->buildViolation('form.communication.errors.email_geolocation')
                        ->atPath('geoLocation')
                        ->addViolation();
            }

            if (mb_strlen($this->message) > Message::MAX_LENGTH_EMAIL) {
                $context->buildViolation('form.communication.errors.too_large_email')
                        ->atPath('message')
                        ->addViolation();
            }
        }

        if ($this->type == \App\Entity\Communication::TYPE_SMS) {
            if (mb_strlen($this->message) > Message::MAX_LENGTH_SMS) {
                $context->buildViolation('form.communication.errors.too_large_sms')
                        ->atPath('message')
                        ->addViolation();
            }
        }
    }
}