<?php

namespace App\Controller;

use App\Entity\Oferta;
use App\Entity\Przetarg;
use App\Entity\User;
use Ds\Map;
use ContainerSDI9S8v\getAppAuthenticatorService;
use phpDocumentor\Reflection\Types\Array_;
use PhpParser\Node\Expr\List_;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
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
            ->add('nazwa', TextareaType::class, array('label' => 'Nazwa i opis',
                'attr' => array('class' => 'form-control')))
            ->add('wystawcaNazwa', TextType::class, array('label' => 'Wystawca przetargu', 'attr' => array('class' => 'form-control')))
            ->add('dataRozpoczecia', DateType::class, array('widget' => 'single_text',
                'attr' => array('class' => 'input-group input-daterange')))
            ->add('dataZakonczenia', DateType::class, array('widget' => 'single_text',
                'attr' => array('class' => 'input-group input-daterange')))
            ->add('zapisz', SubmitType::class, array('label' => 'Zapisz przetarg',
                'attr' => array('class' => 'btn btn-outline-primary', 'style' => 'margin-top:8px')))
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $uzytkownik = $entityManager->find(User::class, $id);
            $przetarg->setWystawca($uzytkownik);
            $entityManager->persist($przetarg);
            $entityManager->flush();

            $przetargi = $this->getDoctrine()->getRepository(Przetarg::class)->findBy(array('wystawca' => $id));
            return $this->render('Praca/moje_przetargi.html.twig', ['przetargi' => $przetargi]);
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
            ->add('nazwsiskolubnazwa', TextType::class, array('label' => 'Nazwisko lub nazwa firmy',
                'attr' => array('class' => 'form-control')))
            ->add('cena', NumberType::class, array('attr' => array('class' => 'form-control')))
            ->add('terminRealizacji', NumberType::class, array('label' => 'Termin realizacji w miesiącach:',
                'attr' => array('class' => 'form-control')))
            ->add('okresGwarancji', NumberType::class, array('label' => 'Okres gwarancji w miesiącach:',
                'attr' => array('class' => 'form-control')))
            ->add('doswiadczenie', TextareaType::class, array('attr' => array('class' => 'form-control')))
            ->add('iloscPodobnychProjektow', NumberType::class, array('attr' => array('class' => 'form-control')))
            ->add('zapisz', SubmitType::class, array('label' => 'Wyślij ofertę',
                'attr' => array('class' => 'btn btn-outline-primary', 'style' => 'margin-top:8px')))
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

            $oferty = $this->getDoctrine()->getRepository(Oferta::class)->findBy(array('idOsobyfirmy' => $oferta->getIdOsobyfirmy()));
            return $this->render('Praca/moje_oferty.html.twig', ['oferty' => $oferty]);
        }

        return $this->render('Praca/nowa_oferta.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/edytujPrzetarg/{id}", name="edytujPrzetarg")
     * @Method ({"GET", "POST"})
     */
    public function edytujPrzetarg(Request $request, $id)
    {
        $przetarg = new Przetarg();
        $przetarg = $this->getDoctrine()->getRepository(Przetarg::class)->find($id);

        $form = $this->createFormBuilder($przetarg)
            ->add('nazwa', TextareaType::class, array('label' => 'Nazwa i opis',
                'attr' => array('class' => 'form-control')))
            ->add('dataRozpoczecia', DateType::class, array('widget' => 'single_text',
                'attr' => array('class' => 'input-group input-daterange')))
            ->add('dataZakonczenia', DateType::class, array('widget' => 'single_text',
                'attr' => array('class' => 'input-group input-daterange')))
            ->add('zapisz', SubmitType::class, array('label' => 'Zapisz przetarg',
                'attr' => array('class' => 'btn btn-outline-primary', 'style' => 'margin-top:8px')))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            $przetargi = $this->getDoctrine()->getRepository(Przetarg::class)->findBy(array('wystawca' => $przetarg->getWystawca()));
            return $this->render('Praca/moje_przetargi.html.twig', ['przetargi' => $przetargi]);
        }
        return $this->render('Praca/edytujPrzetarg.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/edytujOferte/{id}", name="edytujOferte")
     * @Method ({"GET", "POST"})
     */
    public function edytujOferte(Request $request, $id)
    {
        $oferta = new Oferta();
        $oferta = $this->getDoctrine()->getRepository(Oferta::class)->find($id);

        $form = $this->createFormBuilder($oferta)
            ->add('nazwsiskolubnazwa', TextType::class, array('label' => 'Nazwisko lub nazwa firmy',
                'attr' => array('class' => 'form-control')))
            ->add('cena', NumberType::class, array('attr' => array('class' => 'form-control')))
            ->add('terminRealizacji', NumberType::class, array('label' => 'Termin realizacji w miesiącach:',
                'attr' => array('class' => 'form-control')))
            ->add('okresGwarancji', NumberType::class, array('label' => 'Okres gwarancji w miesiącach:',
                'attr' => array('class' => 'form-control')))
            ->add('doswiadczenie', TextareaType::class, array('attr' => array('class' => 'form-control')))
            ->add('iloscPodobnychProjektow', NumberType::class, array('attr' => array('class' => 'form-control')))
            ->add('zapisz', SubmitType::class, array('label' => 'Zapisz ofertę',
                'attr' => array('class' => 'btn btn-outline-primary', 'style' => 'margin-top:8px')))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            $oferty = $this->getDoctrine()->getRepository(Oferta::class)->findBy(array('idOsobyfirmy' => $oferta->getIdOsobyfirmy()));
            return $this->render('Praca/moje_oferty.html.twig', ['oferty' => $oferty]);
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
        return $this->render('Praca/ofertyPrzetargu.html.twig', ['oferty' => $oferty, 'idPrzetargu' => $id]);
    }

    /**
     * @Route("/SAWWariant1/{id}", name="SAW1Form")
     * @Method({"POST", "GET"})
     */
    public function SAW1Form($id) {
        $oferty = $this->getDoctrine()->getRepository(Oferta::class)->findBy(array('idPrzetargu' => $id));
        return $this->render('Praca/SAWWariant1.html.twig', ['oferty' => $oferty, 'idPrzetargu' => $id]);
    }

    /**
     * @Route("/SAW1/{id}", name="SAW1")
     * @Method({"POST", "GET"})
     */
    public function metodaSAW($id)
    {
        $oferty = $this->getOferty($id);
        $oceny = array();
        $ileOfert = 0;
        foreach ($oferty as $oferta) {
            $wartosciDlaOferty = array();
            array_push($wartosciDlaOferty, $oferta->getId());
            $cena = $_POST['c' . $oferta->getId()];
            array_push($wartosciDlaOferty, $cena);
            $terminRealizacji = $_POST['t' . $oferta->getId()];
            array_push($wartosciDlaOferty, $terminRealizacji);
            $okresGwarancji = $_POST['o' . $oferta->getId()];
            array_push($wartosciDlaOferty, $okresGwarancji);
            $doswiadczenie = $_POST['d' . $oferta->getId()];
            array_push($wartosciDlaOferty, $doswiadczenie);
            $iloscPodobnychProjektow = $_POST['i' . $oferta->getId()];
            array_push($wartosciDlaOferty, $iloscPodobnychProjektow);

            $ileOfert++;
            array_push($oceny, $wartosciDlaOferty);
        }
        $maxOcenaCeny = $oceny[0][1];
        $maxOcenaTerminu = $oceny[0][2];
        $maxOcenaGwarancji = $oceny[0][3];
        $maxOcenaDoswiadczenia = $oceny[0][4];
        $maxOcenaIlosci = $oceny[0][5];
        for ($i = 1; $i < $ileOfert; $i++) {
            if ($oceny[$i][1] > $maxOcenaCeny)
                $maxOcenaCeny = $oceny[$i][1];
            if ($oceny[$i][2] > $maxOcenaTerminu)
                $maxOcenaTerminu = $oceny[$i][2];
            if ($oceny[$i][3] > $maxOcenaGwarancji)
                $maxOcenaGwarancji = $oceny[$i][3];
            if ($oceny[$i][4] > $maxOcenaDoswiadczenia)
                $maxOcenaDoswiadczenia = $oceny[$i][4];
            if ($oceny[$i][5] > $maxOcenaIlosci)
                $maxOcenaIlosci = $oceny[$i][5];
        }

        $normalizacja = array();
        for ($i = 0; $i < $ileOfert; $i++) {
            $wartosciZnormalizowaneDlaOferty = array();
            array_push($wartosciZnormalizowaneDlaOferty, $oceny[$i][0]);
            $norCeny = $oceny[$i][1] / $maxOcenaCeny;
            array_push($wartosciZnormalizowaneDlaOferty, $norCeny);
            $norTerminu = $oceny[$i][2] / $maxOcenaTerminu;
            array_push($wartosciZnormalizowaneDlaOferty, $norTerminu);
            $norGwarancji = $oceny[$i][3] / $maxOcenaGwarancji;
            array_push($wartosciZnormalizowaneDlaOferty, $norGwarancji);
            $norDoswiadczenia = $oceny[$i][4] / $maxOcenaDoswiadczenia;
            array_push($wartosciZnormalizowaneDlaOferty, $norDoswiadczenia);
            $norIlosci = $oceny[$i][5] / $maxOcenaIlosci;
            array_push($wartosciZnormalizowaneDlaOferty, $norIlosci);

            array_push($normalizacja, $wartosciZnormalizowaneDlaOferty);
        }

        $wyniki = array();
        for ($i = 0; $i < $ileOfert; $i++) {
            $wynikOferty = array();
            array_push($wynikOferty, $normalizacja[$i][0]);
            $sumaOcenZnormalizowanych = 0;
            for ($j = 1; $j <= 5; $j++) {
                $sumaOcenZnormalizowanych += $normalizacja[$i][$j];
                array_push($wynikOferty, $sumaOcenZnormalizowanych);
            }
            array_push($wyniki, $wynikOferty);
        }

        $najLepszaOcena = $wyniki[0][1];
        $najLepszaOfertaId = $wyniki[0][0];
        for ($i = 1; $i < $ileOfert; $i++) {
            if ($najLepszaOcena < $wyniki[$i][1]) {
                $najLepszaOcena = $wyniki[$i][1];
                $najLepszaOfertaId = $wyniki[$i][0];
            }
        }
        $najLepszaOferta = $this->getDoctrine()->getRepository(Oferta::class)->find($najLepszaOfertaId);

        return $this->render('Praca/najlepszaOferta.html.twig', ['najLepszaOferta' => $najLepszaOferta, "oferty" => $oferty, 'wariant' => "waraintu SAW-1"]);
    }

    /**
     * @Route("/SAWWariant2/{id}", name="SAW2Form")
     * @Method({"POST", "GET"})
     */
    public function SAW2Form($id) {
        $oferty = $this->getDoctrine()->getRepository(Oferta::class)->findBy(array('idPrzetargu' => $id));
        return $this->render('Praca/SAWWariant2.html.twig', ['oferty' => $oferty, 'idPrzetargu' => $id]);
    }

    /**
     * @Route("/SAW2/{id}", name="SAW2")
     * @Method({"POST", "GET"})
     */
    public function metodaSAW2($id)
    {
        $oferty = $this->getOferty($id);
        $oceny = array();
        $ileOfert = 0;
        foreach ($oferty as $oferta) {
            $wartosciDlaOferty = array();
            array_push($wartosciDlaOferty, $oferta->getId());
            array_push($wartosciDlaOferty, $oferta->getCena());
            array_push($wartosciDlaOferty, $oferta->getTerminRealizacji());
            array_push($wartosciDlaOferty, $oferta->getOkresGwarancji());
            $doswiadczenie = $_POST['d' . $oferta->getId()];
            array_push($wartosciDlaOferty, $doswiadczenie);
            array_push($wartosciDlaOferty, $oferta->getIloscPodobnychProjektow());

            $ileOfert++;
            array_push($oceny, $wartosciDlaOferty);
        }
        $minCena = $oceny[0][1];
        $minTermin = $oceny[0][2];
        $maxGwarancji = $oceny[0][3];
        $maxDoswiadczenia = $oceny[0][4];
        $maxIloscs = $oceny[0][5];
        for ($i = 1; $i < $ileOfert; $i++) {
            if ($oceny[$i][1] < $minCena)
                $minCena = $oceny[$i][1];
            if ($oceny[$i][2] < $minTermin)
                $minTermin = $oceny[$i][2];
            if ($oceny[$i][3] > $maxGwarancji)
                $maxGwarancji = $oceny[$i][3];
            if ($oceny[$i][4] > $maxDoswiadczenia)
                $maxDoswiadczenia = $oceny[$i][4];
            if ($oceny[$i][5] > $maxIloscs)
                $maxIloscs = $oceny[$i][5];
        }

        $normalizacja = array();
        for ($i = 0; $i < $ileOfert; $i++) {
            $wartosciZnormalizowaneDlaOferty = array();
            array_push($wartosciZnormalizowaneDlaOferty, $oceny[$i][0]);
            $norCeny = $minCena / $oceny[$i][1];
            array_push($wartosciZnormalizowaneDlaOferty, $norCeny);
            $norTerminu = $minTermin / $oceny[$i][2];
            array_push($wartosciZnormalizowaneDlaOferty, $norTerminu);
            $norGwarancji = $oceny[$i][3] / $maxGwarancji;
            array_push($wartosciZnormalizowaneDlaOferty, $norGwarancji);
            $norDoswiadczenia = $oceny[$i][4] / $maxDoswiadczenia;
            array_push($wartosciZnormalizowaneDlaOferty, $norDoswiadczenia);
            $norIlosci = $oceny[$i][5] / $maxIloscs;
            array_push($wartosciZnormalizowaneDlaOferty, $norIlosci);

            array_push($normalizacja, $wartosciZnormalizowaneDlaOferty);
        }

        $wyniki = array();
        for ($i = 0; $i < $ileOfert; $i++) {
            $wynikOferty = array();
            array_push($wynikOferty, $normalizacja[$i][0]);
            $sumaOcenZnormalizowanych = 0;
            for ($j = 1; $j <= 5; $j++) {
                $sumaOcenZnormalizowanych += $normalizacja[$i][$j];
                array_push($wynikOferty, $sumaOcenZnormalizowanych);
            }
            array_push($wyniki, $wynikOferty);
        }

        $najLepszaOcena = $wyniki[0][1];
        $najLepszaOfertaId = $wyniki[0][0];
        for ($i = 1; $i < $ileOfert; $i++) {
            if ($najLepszaOcena < $wyniki[$i][1]) {
                $najLepszaOcena = $wyniki[$i][1];
                $najLepszaOfertaId = $wyniki[$i][0];
            }
        }
        $najLepszaOferta = $this->getDoctrine()->getRepository(Oferta::class)->find($najLepszaOfertaId);

        return $this->render('Praca/najlepszaOferta.html.twig', ['najLepszaOferta' => $najLepszaOferta, "oferty" => $oferty, 'wariant' => "waraintu SAW-2"]);
    }

    /**
     * @Route("/Shanon/{id}", name="ShanonForm")
     * @Method({"POST", "GET"})
     */
    public function ShanonForm($id) {
        $oferty = $this->getDoctrine()->getRepository(Oferta::class)->findBy(array('idPrzetargu' => $id));
        return $this->render('Praca/shanon.html.twig', ['oferty' => $oferty, 'idPrzetargu' => $id]);
    }

    /**
     * @Route("/ShanonOferta/{id}", name="Shanon")
     * @Method({"POST", "GET"})
     */
    public function shanon($id) {
        $oferty = $this->getOferty($id);
        $Y = array();
        $ileOfert = 0;
        foreach ($oferty as $oferta) {
            $wartosciDlaOferty = array();
            array_push($wartosciDlaOferty, $oferta->getId());
            array_push($wartosciDlaOferty, 1/$oferta->getCena());
            array_push($wartosciDlaOferty, 1/$oferta->getTerminRealizacji());
            array_push($wartosciDlaOferty, $oferta->getOkresGwarancji());
            $doswiadczenie = $_POST['d' . $oferta->getId()];
            array_push($wartosciDlaOferty, $doswiadczenie);
            array_push($wartosciDlaOferty, $oferta->getIloscPodobnychProjektow());

            $ileOfert++;
            array_push($Y, $wartosciDlaOferty);
        }
        $sumaCen = 0;
        $sumaTerminow = 0;
        $sumaGwarancji = 0;
        $sumaDoswiadczenia = 0;
        $sumaIlosciProjektow = 0;

        for ($i = 0; $i < $ileOfert; $i++) {
            $sumaCen += $Y[$i][1];
            $sumaTerminow += $Y[$i][2];
            $sumaGwarancji += $Y[$i][3];
            $sumaDoswiadczenia += $Y[$i][4];
            $sumaIlosciProjektow += $Y[$i][5];
        }

        $Z = array();
        for ($i = 0; $i < $ileOfert; $i++) {
            $normalizacja = array();
            array_push($normalizacja, $Y[$i][0]);
            array_push($normalizacja, $Y[$i][1]/$sumaCen);
            array_push($normalizacja, $Y[$i][2]/$sumaTerminow);
            array_push($normalizacja, $Y[$i][3]/$sumaGwarancji);
            array_push($normalizacja, $Y[$i][4]/$sumaDoswiadczenia);
            array_push($normalizacja, $Y[$i][5]/$sumaIlosciProjektow);

            array_push($Z, $normalizacja);
        }

        $sumaCenZn = 0;
        $sumaTerminowZn = 0;
        $sumaGwarancjiZn = 0;
        $sumaDoswiadczeniaZn = 0;
        $sumaIlosciProjektowZn = 0;
        for ($i = 0; $i < $ileOfert; $i++) {
            $sumaCenZn += $Z[$i][1] * log($Z[$i][1]);
            $sumaTerminowZn += $Z[$i][2] * log($Z[$i][2]);
            $sumaGwarancjiZn += $Z[$i][3] * log($Z[$i][3]);
            $sumaDoswiadczeniaZn += $Z[$i][4] * log($Z[$i][4]);
            $sumaIlosciProjektowZn += $Z[$i][5] * log($Z[$i][5]);
        }

        $E = array();
        array_push($E, -1 / log($ileOfert) * $sumaCenZn);
        array_push($E, -1 / log($ileOfert) * $sumaTerminowZn);
        array_push($E, -1 / log($ileOfert) * $sumaGwarancjiZn);
        array_push($E, -1 / log($ileOfert) * $sumaDoswiadczeniaZn);
        array_push($E, -1 / log($ileOfert) * $sumaIlosciProjektowZn);

        $D = array();
        array_push($D, 1 - $E[0]);
        array_push($D, 1 - $E[1]);
        array_push($D, 1 - $E[2]);
        array_push($D, 1 - $E[3]);
        array_push($D, 1 - $E[4]);

        $sumaPoziomuZmiennosci = 0;
        for ($i = 0; $i < 5; $i++) {
            $sumaPoziomuZmiennosci += $D[$i];
        }

        $W = array();
        array_push($W, $D[0]/$sumaPoziomuZmiennosci);
        array_push($W, $D[1]/$sumaPoziomuZmiennosci);
        array_push($W, $D[2]/$sumaPoziomuZmiennosci);
        array_push($W, $D[3]/$sumaPoziomuZmiennosci);
        array_push($W, $D[4]/$sumaPoziomuZmiennosci);

        $wyniki = array();
        for ($i = 0; $i < $ileOfert; $i++) {
            $rekord = array();
            array_push($rekord, $Z[$i][0]);
            array_push($rekord, $Z[$i][1] * $W[0]);
            array_push($rekord, $Z[$i][2] * $W[1]);
            array_push($rekord, $Z[$i][3] * $W[2]);
            array_push($rekord, $Z[$i][4] * $W[3]);
            array_push($rekord, $Z[$i][5] * $W[4]);

            array_push($wyniki, $rekord);
        }

        $sumyWynikow = array();
        for ($i = 0; $i < $ileOfert; $i++) {
            $wynikOferty = array();
            array_push($wynikOferty, $wyniki[$i][0]);
            $sumaOcen = 0;
            for ($j = 1; $j <= 5; $j++) {
                $sumaOcen += $wyniki[$i][$j];
                array_push($wynikOferty, $sumaOcen);
            }
            array_push($sumyWynikow, $wynikOferty);
        }

        $najLepszaOfertaId = $sumyWynikow[0][0];
        $najLepszaOcena = $sumyWynikow[0][1];
        for ($i = 1; $i < $ileOfert; $i++) {
            if ($najLepszaOcena < $sumyWynikow[$i][1]) {
                $najLepszaOcena = $sumyWynikow[$i][1];
                $najLepszaOfertaId = $sumyWynikow[$i][0];
            }
        }

        $najLepszaOferta = $this->getDoctrine()->getRepository(Oferta::class)->find($najLepszaOfertaId);
        return $this->render('Praca/najlepszaOferta.html.twig', ['najLepszaOferta' => $najLepszaOferta, "oferty" => $oferty, 'wariant' => "waraintu SAW-SH"]);
    }

    private function getOferty($id) {
        $oferty = $this->getDoctrine()->getRepository(Oferta::class)->findBy(array('idPrzetargu' => $id));
        return $oferty;
    }
}