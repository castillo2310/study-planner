<?php


namespace StudyPlanner\Infrastructure\Controller;


use StudyPlanner\Application\Plan\Create\CreatePlanCommand;
use StudyPlanner\Application\Plan\Create\CreatePlanCommandHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CreatePDFPlanController
 * @package StudyPlanner\Infrastructure\Controller
 */
class CreatePDFPlanController extends AbstractController
{
    /**
     * @var CreatePlanCommandHandler
     */
    private CreatePlanCommandHandler $handler;

    /**
     * CreatePlanController constructor.
     * @param CreatePlanCommandHandler $handlerPDF
     */
    public function __construct(CreatePlanCommandHandler $handlerPDF)
    {
        $this->handler = $handlerPDF;
    }

    /**
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function __invoke(Request $request)
    {
        try {
            $startDate = new \DateTime($request->get('startDate'));
            $endDate = new \DateTime($request->get('endDate'));

            $command = new CreatePlanCommand(
                $startDate,
                $endDate,
                $request->get('dailyStudyHours'),
                $request->get('allowedWeekDays'),
                $request->get('chapters')
            );

            $pdf = $this->handler->handle($command);

            return new Response(
                $pdf,
                Response::HTTP_OK,
                [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="doc.pdf"',
                    'Content-Length' => strlen($pdf)
                ]
            );
        } catch (\Throwable $exception) {
            return $this->json(['error' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
