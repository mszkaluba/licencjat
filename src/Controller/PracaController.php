<?php
        namespace App\Controller;

        use App\Entity\Przetarg;
        use Symfony\Component\Routing\Annotation\Route;
        use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
        use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

        class PracaController extends AbstractController {
            /**
             * @Route("/", name="index")
             * @Method({"POST", "GET"})
             */
            public function index() {
                return $this->render('Praca/index.html.twig');
            }

            /**
             * @Route("/glowna", name="glowna")
             * @Method({"POST", "GET"})
             */
            public function glowna() {
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
            public function mojePrzetargi($id) {
                $przetargi = $this->getDoctrine()->getRepository(Przetarg::class)->findBy(array('wystawca' => $id));

                if (!$przetargi) {
                    print "<script type='text/javascript'>alert('Nie wystawiłeś żadnego przetargu!');</script>";
                }
                return $this->render('Praca/moje_przetargi.html.twig', ['przetargi' => $przetargi]);
            }

            /**
             * @Route("/nowyPrzetarg", name="nowy")
             * @Method({"GET","POST"})
             */
            public function nowy() {
                return $this->render('Praca/nowy_przetarg.html.twig');
            }

            /**
             * @Route("/podsumowanie", name="podsumowanie")
             * @Method({"GET","POST"})
             */
            public function podsumowanie() {
                $entityMenager = $this->getDoctrine()->getManager();
                $id = $_POST['id'];
                $nazwa = $_POST['nazwa'];
                $wystawca = $_POST['wystawca'];
                $dataWystwaienia = $_POST['dataWystwaienia'];
                $dataZakonczenia = $_POST['dataZakonczenia'];

                $przetarg = new Przetarg;
                $przetarg->setNazwa($nazwa);
                $przetarg->setWystawcaNazwa($wystawca);
                $przetarg->setDataRozpoczecia($dataWystwaienia);
                $przetarg->setDataZakonczenia($dataZakonczenia);
                $przetarg->setWystawca($id);

                $entityMenager->persist($przetarg);
                $entityMenager->flush();

                return $this->render('Praca/nowy_przearg_podsumowanie.html.twig', ['przetarg' => $przetarg]);
            }
        }