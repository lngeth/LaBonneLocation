<?php

namespace App\DataFixtures;

use App\Entity\Billing;
use App\Entity\Car;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $manager->flush();
        $this->load_users($manager);
        $this->load_cars($manager);
        $this->load_billings($manager);
    }

    public function load_users($manager)
    {
        $tab_users=[
            ["name" => "Laurent", "email" => "laurent.dupont@gmail.fr", "password" => '$2y$13$rCaU/fYTcnUkaV6t7EM7zuBCSNMdohH0gFHAsDLR.t33ZvQwiptXu', "role" => "Admin"],
            ["name" => "Dupont", "email" => "savory.dupont@gmail.fr", "password" => '$2y$13$qkfINxEXUqLif/gchyKZIOoOID3Ynn76fkVXV9ueTE4kkw7FHyNhi', "role" => "client"],
            ["name" => "Leroy", "email" => "le_roy999@yahoo.fr", "password" => '$2y$13$NQgALg42R9OdrV.QIz8.5eb.UUpZK649W2cQiq7bZRzow1FHJqjnK', "role" => "loueur"],
            ["name" => "Moreau", "email" => "m.martin@hotmail.com", "password" => '$2y$13$dzDjfWQHsyBNUKwPpL6wmOJPzakeQ02gMp65gbrbSyykd4/Md1bGm', "role" => "client"]
        ];
        foreach ($tab_users as $user) {
            $new_user = new User();
            $new_user->setName($user['name']);
            $new_user->setEmail($user['email']);
            $new_user->setPassword($user['password']);
            $new_user->setRole($user['role']);
            $manager->persist($new_user);
        }
        $manager->flush();
    }

    public function load_cars($manager)
    {
        $tab_cars=[
            ["type" => "Peugeot 206", "carac" => ["motor" => "hybride", "vitesse" => "automatique", "nbSeat" => "5"], "amount" => 100.0, "rent" => "disponible", "image" => "peugeot_206.jpg", "id_owner" => "2", "quantity" => 1],
            ["type" => "Peugeot 207", "carac" => ["motor" => "diesel", "vitesse" => "mecanique", "nbSeat" => "2"], "amount" => 150.0, "rent" => "disponible", "image" => "peugeot_207.jpg", "id_owner" => "2", "quantity" => 1],
            ["type" => "Citroen C3", "carac" => ["motor" => "essence", "vitesse" => "mecanique", "nbSeat" => "5"], "amount" => 90.0, "rent" => "indisponible", "image" => "citroen_c3.jpg", "id_owner" => "2", "quantity" => 1]
        ];
        foreach ($tab_cars as $car) {
            $new_car = new Car();
            $new_car->setType($car['type']);
            $new_car->setDatasheet($car['carac']);
            $new_car->setAmount($car['amount']);
            $new_car->setRent($car['rent']);
            $new_car->setImage($car['image']);
            $owner = $manager->getRepository(User::class)->find($car['id_owner']);
            $new_car->setIdOwner($owner);
            $new_car->setQuantity($car['quantity']);
            $manager->persist($new_car);
        }
        $manager->flush();
    }

    public function load_billings($manager)
    {
        $tab_billings=[
            ["idUser" => "3", "idCar" => "3", "startDate" => "2021-10-01", "endDate" => "2021-10-02", "price" => 200.0, "paid" => true, "returned" => false]
        ];
        foreach ($tab_billings as $billing) {
            $new_billing = new Billing();
            $user = $manager->getRepository(User::class)->find($billing['idUser']);
            $new_billing->setIdUser($user);
            $car = $manager->getRepository(Car::class)->find($billing['idCar']);
            $new_billing->setIdCar($car);
            $startDate = \DateTime::createFromFormat('Y-m-d', $billing['startDate']);
            $new_billing->setStartDate($startDate);
            $new_billing->setPrice($billing['price']);
            $new_billing->setPaid($billing['paid']);
            $new_billing->setReturned($billing['returned']);
            $manager->persist($new_billing);
        }
        $manager->flush();
    }
}
