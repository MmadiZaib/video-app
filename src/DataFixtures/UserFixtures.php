<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private UserPasswordEncoderInterface $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        foreach ($this->userData() as [$name, $lastName, $email, $password, $apiKey, $roles]) {
            $user = new User();
            $user->setName($name);
            $user->setLastName($lastName);
            $user->setEmail($email);
            $user->setPassword($this->encoder->encodePassword($user, $password));
            $user->setRoles($roles);
            $user->setVimeoApiKey($apiKey);

            $manager->persist($user);
        }

        $manager->flush();
    }

    private function userData(): array
    {
        return [
          ['John', 'Doe', 'john@app.test', 'test', 'ce2e688482e64dea7da4bec0039fe8e5', ['ROLE_ADMIN']],
          ['user', 'Doe', 'user@app.test', 'test', null, ['ROLE_USER']],
          ['admin', 'Doe', 'admin@app.test', 'test', null, ['ROLE_ADMIN']],
          ['sub', 'sub', 'sub@app.test', 'test', null, ['ROLE_USER']],
        ];
    }
}
