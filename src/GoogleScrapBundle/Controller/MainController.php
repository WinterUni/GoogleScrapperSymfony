<?php namespace GoogleScrapBundle\Controller;

use GoogleScrapBundle\Entity\ScrapResult;
use GoogleScrapBundle\Form\ScrapResultFormType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class MainController extends Controller
{
    /**
     * @Route("/", name="index_page")
     */
    public function indexAction(Request $request)
    {
        $scrapResult = new ScrapResult();
        $form = $this->createForm(ScrapResultFormType::class, $scrapResult);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var ScrapResult $scrapResult */
            $scrapResult = $form->getData();

            $googleScrapService = $this->get('google_scrap.google_scrap');
            $googleScrapService->setDomainName($scrapResult->getDomainName());
            $googleScrapService->setKeyWord($scrapResult->getKeyWord());
            $domainPosition = $googleScrapService->getDomainPositionByKeyword();

            if (!$domainPosition['status']) {
                $this->addFlash('success', 'Ни один из прокси серверов не отвечает. Попробуйте сделать запрос позже');
            }

            if ($domainPosition['position'] && $domainPosition['status']) {
                $scrapResult->setQueryStatus(true);
                $scrapResult->setPosition($domainPosition['position']);
                $this->addFlash('success', 'Домен "' . $scrapResult->getDomainName() . '" по ключевому слову ' .
                    $scrapResult->getKeyWord() . ' находится на ' . $domainPosition['position'] . ' позиции в Google.');
            }

            if (!$domainPosition['position'] && $domainPosition['status']) {
                $scrapResult->setQueryStatus(true);
                $this->addFlash('success', 'Среди 100 результатов выдачи Google, домен "' . $scrapResult->getDomainName() . '" по ключевому слову ' .
                    $scrapResult->getKeyWord() . ' не найден.');
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($scrapResult);
            $em->flush();

            return $this->redirectToRoute('index_page');
        }

        return $this->render('GoogleScrapBundle:Web:index.html.twig', [
            'scrapResultForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/history/{page}", name="history_page")
     */
    public function historyAction($page = 1)
    {
        $scrapResultsRepo = $this->getDoctrine()->getRepository('GoogleScrapBundle:ScrapResult');

        $paginatorLimit = 10;
        $scrapResults = $scrapResultsRepo->getPaginatedScrapResults($page, $paginatorLimit);

        $totalResultsReturned = $scrapResults->getIterator()->count();
        $totalResultsAmount = $scrapResults->count();

        if (!$totalResultsReturned) {
            throw $this->createNotFoundException('Запрашиваемая страница на найдена на сервере');
        }

        $pagesAmount = ceil($totalResultsAmount / $paginatorLimit);

        return $this->render('@GoogleScrap/Web/history.index.html', [
            'scrapResults' => $scrapResults,
            'pagesAmount' => $pagesAmount,
            'currentPage' => $page,
        ]);
    }
}