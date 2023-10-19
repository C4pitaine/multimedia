<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Image;
use App\Entity\Product;
use Cocur\Slugify\Slugify;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $faker = Factory::create('fr_FR');
        $slugify = new Slugify();

        for($i=1; $i<=10;$i++)
        {
            $product = new Product();
            $name = $faker->name();
            $coverImage = 'https://fscl01.fonpit.de/userfiles/7687254/image/NextPit_Best_Camera_Phones_2023.jpg';
            $description = '<p>'.join('</p><p>',$faker->paragraphs(5)).'</p>';
            $type = $faker->sentence();
            $marque = $faker->lastName();

            $product->setName($name)
            //    ->setSlug($slug) => plus nécéssaire car on a fait un slugify dans Ad.php avec la fonction qu'on a appelé iniatializeSlug()
               ->setDescription($description)
               ->setPrice(rand(40,200))
               ->setType($type)
               ->setMarque($marque)
               ->setCoverImage($coverImage);

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
