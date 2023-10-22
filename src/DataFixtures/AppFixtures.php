<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Image;
use App\Entity\Product;
use Cocur\Slugify\Slugify;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }
    
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $faker = Factory::create('fr_FR');
        $slugify = new Slugify();

        $users =[];
        $genres = ['male','femelle'];

        for($u=1;$u<=3;$u++)
        {
            $user = new User();
            $genre = $faker->randomElement($genres);
            $picture = 'https://picsum.photos/seed/picsum/500/500';

            $hash = $this->passwordHasher->hashPassword($user,'password');

            $user->setFirstName($faker->firstName($genres))
                 ->setLastName($faker->lastName())
                 ->setEmail($faker->email())
                 ->setIntroduction($faker->sentence())
                 ->setDescription('<p>'.join('</p><p>',$faker->paragraphs(3)).'</p>')
                 ->setPassword($hash)
                 ->setPicture('');

            $manager->persist($user);
            $users[] = $user;
        }

        for($i=1; $i<=10;$i++)
        {
            $product = new Product();
            $name = $faker->name();
            $coverImage = 'https://fscl01.fonpit.de/userfiles/7687254/image/NextPit_Best_Camera_Phones_2023.jpg';
            $description = '<p>'.join('</p><p>',$faker->paragraphs(5)).'</p>';
            $type = $faker->sentence();
            $marque = $faker->lastName();

            $user = $users[rand(0,count($users)-1)];

            $product->setName($name)
            //    ->setSlug($slug) => plus nécéssaire car on a fait un slugify dans Ad.php avec la fonction qu'on a appelé iniatializeSlug()
               ->setDescription($description)
               ->setPrice(rand(40,200))
               ->setType($type)
               ->setMarque($marque)
               ->setCoverImage($coverImage)
               ->setAuthor($user);

               for($g=1;$g<=rand(2,3);$g++)
               {   
                $image = new Image();
                $image->setUrl('https://picsum.photos/id/'.$g.'/900')
                      ->setCaption($faker->sentence())
                      ->setProduct($product);

                $manager->persist($image);
               }
               


            $manager->persist($product);
        }

        $manager->flush();
    }
}
