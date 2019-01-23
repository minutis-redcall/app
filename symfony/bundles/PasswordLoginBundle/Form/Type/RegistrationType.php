<?php

namespace Bundles\PasswordLoginBundle\Form\Type;

use Bundles\PasswordLoginBundle\Base\BaseType;
use Bundles\PasswordLoginBundle\Entity\Captcha;
use Bundles\PasswordLoginBundle\Entity\User;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class RegistrationType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', Type\EmailType::class, [
                'label'       => 'password_login.register.email',
                'required'    => true,
                'constraints' => [
                    new Constraints\Email(),
                    new Constraints\Regex('/^[a-zA-Z0-9\_\-\.\@]+$/'),
                    new Constraints\Length(['min' => 8]),
                    new Constraints\Callback([
                        'callback' => function ($object, ExecutionContextInterface $context, $payload) {
                            if ($this->getManager(User::class)->findOneByUsername($object)) {
                                $context
                                    ->buildViolation($this->trans('password_login.register.already_exists'))
                                    ->atPath('username')
                                    ->addViolation();
                            }
                        },
                    ]),
                ],
            ])
            ->add('password', Type\RepeatedType::class, [
                'type'            => Type\PasswordType::class,
                'invalid_message' => $this->trans('password_login.register.password_should_match'),
                'required'        => true,
                'first_options'   => [
                    'label'       => 'password_login.register.password',
                    'constraints' => new Constraints\Length([
                        'min' => 8,
                        'max' => BCryptPasswordEncoder::MAX_PASSWORD_LENGTH,
                    ]),
                ],
                'second_options'  => ['label' => 'password_login.register.repeat_password'],
            ]);

        $ip = $this->get('request_stack')->getMasterRequest()->getClientIp();
        if (!$this->getManager(Captcha::class)->isAllowed($ip)) {
//            $builder
//                ->add('recaptcha', EWZRecaptchaType::class, [
//                    'label'       => 'password_login.register.captcha',
//                    'constraints' => [
//                        new RecaptchaTrue(),
//                    ],
//                    'mapped'      => false,
//                ]);

            $builder->add('fake_recaptcha', Type\CheckboxType::class, [
                'label' => "password_login.fake_recaptcha",
                'constraints' => [
                    new Constraints\IsTrue()
                ],
                'mapped'      => false,
                'required' => false,
            ]);

        }

        $builder->add('submit', Type\SubmitType::class, [
            'label' => 'password_login.register.register',
        ]);
    }
}
