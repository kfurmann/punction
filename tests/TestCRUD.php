<?php
use Punction\Entities\Patient;
use Hospitalplugin\DB\DoctrineBootstrap;
use Punction\Entities\PatientZZ;
use Punction\Entities\PatientPED;
use Punction\Entities\PatientBuilder;
use Punction\Entities\PatientCRUD;
use Hospitalplugin\utils\Utils;
use Hospitalplugin\utils\PersonGenerator;
use Hospitalplugin\Entities\WardCRUD;

class TestCRUD extends PHPUnit_Framework_TestCase
{

    public static $ADMIN_ID = 1;

    /**
     *
     * @var $db DoctrineBootstrap
     */
    public $entityManager;

    function setUp()
    {
        $this->entityManager = DoctrineBootstrap::getEntityManager();
        if (! $this->entityManager->isOpen()) {
            $this->entityManager = $this->entityManager->create($this->entityManager->getConnection(), $this->entityManager->getConfiguration());
        }
    }

    function testWardCRUD()
    {
    	 return;
    	$adminsWard = WardCRUD::getWardForUser(TestCRUD::$ADMIN_ID);
        $this->assertTrue($adminsWard->getId() > 0, "admin ward id > 0");
        $this->assertTrue(strpos($adminsWard->getName(), "Oddzi") !== FALSE, "admin ward name Oddzi");
        $this->assertTrue(strlen($adminsWard->getInfomedica()) > 0, "admin ward infomedica length > 0");
        $this->assertTrue($adminsWard->getPododdzial() == FALSE, "poddodzial false");
        $this->assertTrue(strlen($adminsWard->getTypOddzialu()) > 0, "typ oddzialu length > 0");
        $this->assertTrue(strlen($adminsWard->toString()) > 0, "admin ward to string > 0");
    }

    function testPatientCRUD1()
    {
        // getPatient
        $patient1 = PatientCRUD::getPatient(0, 'ZZ');
        $this->assertTrue($patient1->getId() == 0);
        $this->assertTrue($patient1 instanceof PatientZZ);
    }

    function testPatientCRUD2()
    {
        // getPatients
        $patients = PatientCRUD::getPatients();
        $this->assertTrue($patients == array());
    }

    function testPatientCRUD3()
    {
        foreach (array(
        		1 => 'ZZ'
//             1645 => 'PED',
//             1648 => 'ZZ',
//             1665 => 'ZZ',
//             1662 => 'PSY',
//             1654 => 'DIA'
        ) as $oddzid => $typ) {
            foreach (array(
                0,
                1,
                2,
                3
            ) as $kategoriaPacjenta) {
                // setPatientsCategories
                $patient1 = PatientCRUD::getPatient(0, $typ);
                $patient2 = TestCRUD::getRandomPatient($typ);
                $patient2->setDataKategoryzacji(new \DateTime("now"));
                $patient2->setOddzialId($oddzid);
                $patient2->setKategoriaPacjenta(1);
                $patient2->setNumerHistorii(rand(100, 999));
                $patient2 = Utils::cast($patient1, $patient2);
                $patient2->setAktywnoscFizyczna(1);
                $this->entityManager->persist($patient2);
                $this->entityManager->flush();
                $this->assertTrue($patient2->getAktywnoscFizyczna() == 1);
                $patient2->setAktywnoscFizyczna(2);
                $patient2->setKategoriaPacjenta($kategoriaPacjenta);
                // use setPC
                PatientCRUD::setPatientCategories($patient2, $typ);
                // load from DB
                $patient3 = PatientCRUD::getPatient($patient2->getId(), $typ);
                // check
                $this->assertTrue($patient3->getAktywnoscFizyczna() == 2);
                $this->assertTrue($patient3->getTyp() == $typ);
            }
        }
    }
    
    private static function getRandomPatient($typ) {
        $class = 'Punction\Entities\Patient' . $typ;
        $patient = new $class;
        $person = PersonGenerator::getRandomPerson();
        $patient->setName(explode('|',$person)[0]);
        $patient->setPesel(explode('|',$person)[1]);
        return $patient;
    }
}
