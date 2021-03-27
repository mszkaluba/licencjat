<?php

namespace App\Controller;

use App\Entity\Oferta;
use App\Entity\Przetarg;
use App\Entity\User;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PracaController extends AbstractController
{
    /**
     * @Route("/", name="index")
     * @Method({"POST", "GET"})
     */
    public function index()
    {
        return $this->render('Praca/index.html.twig');
    }

    /**
     * @Route("/glowna", name="glowna")
     * @Method({"POST", "GET"})
     */
    public function glowna()
    {
        $przetargi = $this->getDoctrine()->getRepository(Przetarg::class)->findAll();

        if (!$przetargi) {
            print "<script type='text/javascript'>alert('Nie znaleziono przetargów!');</script>";
        }
        return $this->render('Praca/przetargi.html.twig', ['przetargi' => $przetargi]);
    }

    /**
     * @Route("/mojePrzetargi/{id}", name="mojePrzetargi")
     * @Method({"POST", "GET"})
     */
    public function mojePrzetargi($id)
    {
        $przetargi = $this->getDoctrine()->getRepository(Przetarg::class)->findBy(array('id' => $id));

        if (!$przetargi) {
            print "<script type='text/javascript'>alert('Nie wystawiłeś żadnego przetargu!');</script>";
        }
        return $this->render('Praca/moje_przetargi.html.twig', ['przetargi' => $przetargi]);
    }

    /**
     * @Route("/nowyPrzetarg", name="nowy")
     * @Method({"GET","POST"})
     */
    public function nowy()
    {
        return $this->render('Praca/nowy_przetarg.html.twig');
    }

    /**
     * @Route("/podsumowanie", name="podsumowanie")
     * @Method({"GET","POST"})
     */
    public function podsumowanie()
    {
        $entityMenager = $this->getDoctrine()->getManager();
        $id = $_POST['id'];
        $user = $entityMenager->find(User::class, $id);
        $nazwa = $_POST['nazwa'];
        $wystawca = $_POST['wystawca'];
        $dataWystwaienia = $_POST['dataWystwaienia'];
        $dataZakonczenia = $_POST['dataZakonczenia'];

        $przetarg = new Przetarg;
        $przetarg->setNazwa($nazwa);
        $przetarg->setWystawcaNazwa($wystawca);
        $przetarg->setDataRozpoczecia(new \DateTime($dataWystwaienia));
        $przetarg->setDataZakonczenia(new \DateTime($dataZakonczenia));
        $przetarg->setWystawca($user);

        $entityMenager->persist($przetarg);
        $entityMenager->flush();

        return $this->render('Praca/nowy_przearg_podsumowanie.html.twig', ['przetarg' => $przetarg]);
    }

    /**
     * @Route("/mojeOferty/{id}", name="mojeOferty")
     * @Method({"POST", "GET"})
     */
    public function mojeOferty($id)
    {
        $oferty = $this->getDoctrine()->getRepository(Oferta::class)->findBy(array('id' => $id));

        if (!$oferty) {
            print "<script type='text/javascript'>alert('Nie wysłałeś żdanej oferty!');</script>";
        }
        return $this->render('Praca/moje_oferty.html.twig', ['oferty' => $oferty]);
    }

    /**
     * @Route("/nowaOferta/{id}", name="nowaOferta")
     * @Method({"GET","POST"})
     */
    public function nowaOferta($id)
    {
        return $this->render('Praca/nowa_oferta.html.twig', ['id' => $id]);
    }

    /**
     * @Route("/podsumowanieOferty", name="podsumowanieOferty")
     * @Method({"GET","POST"})
     */
    public function podsumowanieOferty()
    {
        $entityMenager = $this->getDoctrine()->getManager();
        $id = $_POST['id'];
        $przetargId = $_POST['przetarg'];
        $user = $entityMenager->find(User::class, $id);
        $przetarg = $entityMenager->find(Przetarg::class, $przetargId);
        $nazwiskoNazwa = $_POST['nazwiskoNazwa'];
        $cena = $_POST['cena'];
        $dataRealizacji = $_POST['dataRealizacji'];
        $okresGwarancji = $_POST['okresGwarancji'];
        $doswiadczenie = $_POST['doswiadczenie'];
        $ilosc = $_POST['ilosc'];

        $oferta = new Oferta();
        $oferta->setIdOsobyfirmy($user);
        $oferta->setIdPrzetargu($przetarg);
        $oferta->setNazwsiskolubnazwa($nazwiskoNazwa);
        $oferta->setCena($cena);
        $oferta->setTerminRealizacji(new \DateTime($dataRealizacji));
        $oferta->setOkresGwarancji(new \DateTime($okresGwarancji));
        $oferta->setDoswiadczenie($doswiadczenie);
        $oferta->setIloscPodobnychProjektow($ilosc);

        $entityMenager->persist($oferta);
        $entityMenager->flush();

        return $this->render('Praca/podumowanie_oferty.html.twig', ['oferta' => $oferta]);
    }
}