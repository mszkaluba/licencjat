<?php

namespace App\Controller;

use App\Entity\Oferta;
use App\Entity\Przetarg;
use App\Entity\User;
use ContainerSDI9S8v\getAppAuthenticatorService;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Validator\Constraints\DateTime;

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
        $przetargi = $this->getDoctrine()->getRepository(Przetarg::class)->findBy(array('wystawca' => $id));

        if (!$przetargi) {
            print "<script type='text/javascript'>alert('Nie wystawiłeś żadnego przetargu!');</script>";
        }
        return $this->render('Praca/moje_przetargi.html.twig', ['przetargi' => $przetargi]);
    }

    /**
     * @Route("/nowyPrzetarg{id}", name="nowy")
     * @Method({"GET","POST"})
     */
    public function nowy(Request $request, $id)
    {
        $przetarg = new Przetarg();

        $form = $this->createFormBuilder($przetarg)
            ->add('nazwa', TextType::class, array('label' => 'Nazwa i opis'))
            ->add('wystawcaNazwa', TextType::class)
            ->add('dataRozpoczecia', DateType::class, array('widget' => 'choice'))
            ->add('dataZakonczenia', DateType::class, array('widget' => 'choice'))
            ->add('zapisz', SubmitType::class, array('label' => 'Zapisz przetarg'))
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $uzytkownik = $entityManager->find(User::class, $id);
            $przetarg->setWystawca($uzytkownik);
            $entityManager->persist($przetarg);
            $entityManager->flush();

            return $this->render('Praca/nowy_przearg_podsumowanie.html.twig', array('przetarg' => $przetarg));
        }

        return $this->render('Praca/nowy_przetarg.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/mojeOferty/{id}", name="mojeOferty")
     * @Method({"POST", "GET"})
     */
    public function mojeOferty($id)
    {
        $oferty = $this->getDoctrine()->getRepository(Oferta::class)->findBy(array('idOsobyfirmy' => $id));

        if (!$oferty) {
            print "<script type='text/javascript'>alert('Nie wysłałeś żdanej oferty!');</script>";
        }
        return $this->render('Praca/moje_oferty.html.twig', ['oferty' => $oferty]);
    }

    /**
     * @Route("/nowaOferta/{id}", name="nowaOferta")
     * @Method({"GET","POST"})
     */
    public function nowaOferta(Request $request, $id)
    {
        $oferta = new Oferta();

        $form = $this->createFormBuilder($oferta)
            ->add('nazwsiskolubnazwa', TextType::class, array('label' => 'Nazwisko lub nazwa firmy'))
            ->add('cena', NumberType::class)
            ->add('terminRealizacji', NumberType::class, array('label' => 'Termin realizacji w miesiącach:'))
            ->add('okresGwarancji', NumberType::class, array('label' => 'Okres gwarancji w miesiącach:'))
            ->add('doswiadczenie', TextType::class)
            ->add('iloscPodobnychProjektow', NumberType::class)
            ->add('zapisz', SubmitType::class, array('label' => 'Wyślij ofertę'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $przetarg = $entityManager->find(Przetarg::class, $id);
            $user = $this->getUser();
            $oferta->setIdPrzetargu($przetarg);
            $oferta->setIdOsobyfirmy($user);

            $entityManager->persist($oferta);
            $entityManager->flush();

            return $this->render('Praca/podumowanie_oferty.html.twig', array('oferta' => $oferta));
        }

        return $this->render('Praca/nowa_oferta.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/edytujPrzetarg/{id}", name="edytujPrzetarg")
     *@Method ({"GET", "POST"})
     */
    public function edytujPrzetarg(Request $request, $id) {
        $przetarg = new Przetarg();
        $przetarg = $this->getDoctrine()->getRepository(Przetarg::class)->find($id);

        $form = $this->createFormBuilder($przetarg)
            ->add('nazwa', TextType::class, array('label' => 'Nazwa i opis'))
            ->add('dataRozpoczecia', DateType::class, array('widget' => 'choice'))
            ->add('dataZakonczenia', DateType::class, array('widget' => 'choice'))
            ->add('zapisz', SubmitType::class, array('label' => 'Zapisz przetarg'))
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            return $this->render('Praca/nowy_przearg_podsumowanie.html.twig', array('przetarg' => $przetarg));
        }
        return $this->render('Praca/edytujPrzetarg.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/edytujOferte/{id}", name="edytujOferte")
     *@Method ({"GET", "POST"})
     */
    public function edytujOferte(Request $request, $id) {
        $oferta = new Oferta();
        $oferta = $this->getDoctrine()->getRepository(Oferta::class)->find($id);

        $form = $this->createFormBuilder($oferta)
            ->add('nazwsiskolubnazwa', TextType::class, array('label' => 'Nazwisko lub nazwa firmy'))
            ->add('cena', NumberType::class)
            ->add('terminRealizacji', NumberType::class, array('label' => 'Termin realizacji w miesiącach:'))
            ->add('okresGwarancji', NumberType::class, array('label' => 'Okres gwarancji w miesiącach:'))
            ->add('doswiadczenie', TextType::class)
            ->add('iloscPodobnychProjektow', NumberType::class)
            ->add('zapisz', SubmitType::class, array('label' => 'Zapisz ofertę'))
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            return $this->render('Praca/podumowanie_oferty.html.twig', array('oferta' => $oferta));
        }
        return $this->render('Praca/edytujOferte.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/ofertyDlaPrzetargu/{id}", name="ofertyPrzetargu")
     * @Method({"POST", "GET"})
     */
    public function ofertyDlaPrzetargu($id)
    {
        $oferty = $this->getDoctrine()->getRepository(Oferta::class)->findBy(array('idPrzetargu' => $id));

        if (!$oferty) {
            print "<script type='text/javascript'>alert('Nie złożono jeszcze żadnej oferty!');</script>";
        }
        return $this->render('Praca/ofertyPrzetargu.html.twig', ['oferty' => $oferty]);
    }
}