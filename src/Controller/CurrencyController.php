<?php

namespace App\Controller;

use App\Entity\Currency;
use App\Form\CurrencyHandlerType;
use App\Form\CurrencyType;
use App\Handlers\CurrencyHandler;
use App\Model\Currency\Filter;
use App\Model\Currency\Graph;
use App\Repository\CurrencyRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/currency")
 */
class CurrencyController extends AbstractController
{
    private const PER_PAGE = 30;

    /**
     * @Route("/", name="currency_index", methods={"GET"})
     */
    public function index(Request $request, CurrencyRepository $currencyRepository): Response
    {
        $filter = new Filter\Filter();
        $form = $this->createForm(Filter\Form::class, $filter);
        $form->handleRequest($request);

        $pagination = $currencyRepository->all(
            $filter,
            $request->query->getInt('page', 1),
            self::PER_PAGE
//            $request->query->get('sort', 'c.dateofadded'),
//            $request->query->get('direction', 'desc')
        );

        return $this->render('currency/index.html.twig', [
            'pagination' => $pagination,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/cron", name="currency_cron")
     */
    public function cron(Request $request, CurrencyRepository $currencyRepository): Response
    {
        $currencyHandler = new CurrencyHandler();
        $currencyHandler->setDateofadded(new \DateTime('now'));

        $entityManager = $this->getDoctrine()->getManager();
        $currencyHandler->getCbrData($currencyRepository, $entityManager);

        return new Response();
    }

    /**
     * @Route("/new", name="currency_new", methods={"GET","POST"})
     */
    public function new(Request $request, CurrencyRepository $currencyRepository): Response
    {
        $currencyHandler = new CurrencyHandler();
        $currencyHandler->setDateofadded(new \DateTime('now'));
        $form = $this->createForm(CurrencyHandlerType::class, $currencyHandler);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            if ($currencyHandler->getCbrData($currencyRepository, $entityManager)) {
                return $this->redirectToRoute('currency_index');
            } else {
                $this->addFlash('notice', 'Данные за эту дату есть.');
            }

        }

        return $this->render('currency/new.html.twig', [
            'currencyHandler' => $currencyHandler,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/graph", name="currency_graph", methods={"GET"})
     */
    public function graph(Request $request, CurrencyRepository $currencyRepository): Response
    {
        $values = [];
        $valute = '';
        $filter = new Graph\Filter();
        $form = $this->createForm(Graph\Form::class, $filter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $values = $currencyRepository->getGraph($filter->num_code, $filter->date_from, $filter->date_till);
            $choices = array_flip($form->get('num_code')->getConfig()->getOption('choices'));
            $valute = $choices[$form->get('num_code')->getData()];
        }

        return $this->render('currency/graph.html.twig', [
            'form' => $form->createView(),
            'values' => $values,
            'valute' => $valute,
            'filter' => $filter
        ]);
    }

    /**
     * @Route("/json", name="currency_json", methods={"GET"})
     */
    public function sendJson(Request $request, CurrencyRepository $currencyRepository): Response
    {
        $filter = new Graph\Filter();
        $filter->num_code = $request->get('num_code');
        $filter->date_from = $request->get('date_from');
        $filter->date_till = $request->get('date_till');

        $values = [];
        $arr = $currencyRepository->period($filter);
        if ($arr) {
            foreach ($arr as $currency) {
                $values[] = [
                    'date' => $currency->getDateofadded()->format('Y-m-d'),
                    'value' => $currency->getValue() * $currency->getNominal()
                ];
            }
        }

        $response = new Response(json_encode($values));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
