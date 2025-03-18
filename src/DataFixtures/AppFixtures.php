<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use Faker\Factory;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    public function __construct(public UserPasswordHasherInterface $encoder) {}

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $categoryTitle = ['HTML', 'CSS', 'Javascript', 'PHP', 'Vue', 'React', 'Symfony'];
        $categoryArray = [];
        $userArray = [];


        // Create 20 users
        for ($i = 0; $i < 20; $i++) {

            $user = new User();
            $hash = $this->encoder->hashPassword($user, 'password');
            $user->setUsername($faker->userName())
                ->setEmail($faker->email())
                ->setPassword($hash)
                ->setCreatedAt($faker->dateTimeBetween('-6 months', 'now'))
            ;

            array_push($userArray, $user);
            $manager->persist($user);
        }

        // Create 7 categories
        for ($j = 0; $j < 7; $j++) {

            $category = new Category();
            $category->setTitle($categoryTitle[$j])
                ->setDescription($faker->paragraph())
            ;

            array_push($categoryArray, $category);
            $manager->persist($category);
        }

        // Create posts and comments
        for ($k = 0; $k < 50; $k++) {

            $post = new Post();
            $post->setAuthor($user)
                ->setCategory($categoryArray[mt_rand(0, 6)])
                ->setTitle($faker->sentence())
                ->setContent($faker->text(500))
                ->setCreatedAt($faker->dateTimeBetween('-6 months', 'now'))
            ;

            $manager->persist($post);

            for ($l = 0; $l < mt_rand(3, 10); $l++) {

                $comment = new Comment();
                $comment->setAuthor($userArray[mt_rand(0, 19)])
                    ->setPost($post)
                    ->setContent($faker->paragraph(5, true))
                    ->setCreatedAt($faker->dateTimeBetween('-6 months', 'now'))
                ;

                $manager->persist($comment);
            }
        }

        $manager->flush();
    }
}
