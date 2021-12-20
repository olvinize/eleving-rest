<?php

namespace App\DataFixtures;

use App\Entity\Courier;
use App\Entity\Product;
use App\Entity\Seller;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $sellers = ['Apple', 'Saudi Aramco', 'Microsoft', 'Amazon', 'Alphabet', 'Facebook', 'Tencent', 'Tesla', 'Alibaba	Chin', 'Berkshire'];
        $streets = ['Aalto Place', 'Abaco Path', 'Abana Path', 'Abasco Court', 'Abbeville Loop', 'Abbey Way', 'Abdella Way', 'Abdella Way',
            'Abel Place', 'Aber Lane', 'SE 84TH Abercorn Court Marion', 'Abercrombie Way', 'Aberdeen Run', 'Abernethy Place', 'Abner Street',
            'Abney Avenue VOF', 'Abordale Lane', 'Acorn Court', 'Acosta Court', 'Adair Lane', 'Adams Lane', 'Adamsville Avenue VOF',
            'Addison Avenue', 'Adeline Way', 'Adelphi Avenue', 'Adler Court', 'Adriana Way', 'Adrienne Way', 'Adrienne Way', 'Afton Avenue',
            'Agnew Terrace', 'Agua Way Lake', 'Aiken Avenue', 'Ainsworth Circle', 'Aintree Lane', 'Akin Way VOSO', 'Alameda Avenue',
            'Alandari Lane', 'Alandari Lane', 'Alaura Avenue VOSO', 'SE 86TH Albany Avenue Marion', 'Albara Place', 'Albatross Avenue VOSO',
            'SE 82ND Albemarle Avenue Marion', 'Albion Way', 'Alcade Place', 'Alcaraz Place'];
        $goods = ['Fish', 'Footwear (sandals)', 'Gold', 'Oil (palm)', 'Rubber', 'Tin', 'Tobacco', 'Bricks', 'Carpets',
            'Cotton', 'Coffee', 'Fish', 'Gold', 'Khat/Miraa (stimulant plant)', 'Rice', 'Sand', 'Sisal', 'Sugarcane',
            'Tea', 'Tobacco', 'Cotton', 'Tobacco', 'Potatoes', 'Tobacco', 'Cattle', 'Diamonds', 'Rubber', 'Mica',
            'Sapphires', 'Stones', 'Vanilla', 'Tea', 'Tobacco', 'Electronics', 'Garments', 'Oil (palm)', 'Rubber Gloves',
            'Cotton', 'Gold', 'Rice', 'Cattle', 'Goats', 'Beans (green beans)', 'Cattle', 'Chile Peppers', 'Coffee',
            'Cucumbers', 'Eggplants', 'Garments', 'Leather Goods', 'Melons', 'Onions', 'Poppies', 'Pornography',
            'Sugarcane', 'Tobacco', 'Tomatoes', 'Coal', 'Fluorspar (mineral)', 'Gold', 'Tobacco', 'Bricks', 'Carpets',
            'Embellished Textiles', 'Stones', 'Bananas', 'Coffee', 'Gold', 'Gravel (crushed stones)', 'Shellfish',
            'Stones (pumice)', 'Tobacco', 'Cattle', 'Gold', 'Gypsum (mineral)', 'Salt', 'Trona (mineral)', 'Cocoa'];
        $couriers = ['James', 'Mary', 'Robert', 'Patricia', 'John', 'Jennifer', 'Michael', 'Linda', 'William', 'Elizabeth',
            'David', 'Barbara', 'Richard', 'Susan', 'Joseph', 'Jessica', 'Thomas', 'Sarah'];

        $dbSellers = [];
        foreach ($sellers as $i => $name) {
            $seller = new Seller();
            $seller->setName($name);
            $seller->setAddress($streets[$i]);
            $dbSellers[] = $seller;
            $manager->persist($seller);
        }
        $manager->flush();
        foreach ($goods as $good) {
            $product = new Product();
            $product->setName($good);
            $product->setPrice(mt_rand(10, 100));
            $product->setSeller($dbSellers[mt_rand(0, count($dbSellers)-1)]);
            $manager->persist($product);
        }

        foreach ($couriers as $name) {
            $courier = new Courier();
            $courier->setName($name);
            $manager->persist($courier);
        }
        $manager->flush();
    }
}
