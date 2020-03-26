<?php

namespace App\DataFixtures;

use App\Entity\Job;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class JobFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $tab = array(
            array('title' => 'Docteur'),
            array('title' => 'Mécanicien'),
            array('title' => 'Opticien'),
            array('title' => 'Producteur'),
            array('title' => 'Developpeur'),
        );
    
        foreach($tab as $row)
        {
        

        $job = new Job();
        $job->setTitle($row['title']);
    
        $manager->persist($job);
        }
    

        $manager->flush();
    }
}
