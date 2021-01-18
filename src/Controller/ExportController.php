<?php

namespace App\Controller;

use App\Repository\TaskRepository;
use DateTime;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class for exporting reports
 *
 * Class ExportController
 * @package App\Controller
 */
class ExportController extends AbstractController
{
    private TaskRepository $taskRepository;

    /**
     * ExportController constructor.
     * @param TaskRepository $taskRepository
     */
    public function __construct(TaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    /**
     * @Route("/export", name="export")
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function index(Request $request): Response
    {
        $rawBody = $request->getContent();
        $post = json_decode($rawBody, JSON_OBJECT_AS_ARRAY);

        $tasks = $this->getTaskRepository()->findByDate(
            DateTime::createFromFormat("m/d/Y", $post['datepicker1']),
            DateTime::createFromFormat("m/d/Y", $post['datepicker2'])
        );

        $generatedFileLink = $this->getLink($tasks, $post['format']);

        return JsonResponse::create([
            'body' => [
                'link' => '<a href="'. $generatedFileLink . '">Download file</a>'
            ]
        ]);
    }

    /**
     * @return TaskRepository
     */
    public function getTaskRepository(): TaskRepository
    {
        return $this->taskRepository;
    }

    /**
     * Gives a ready link to download a report
     *
     * @param $tasks
     * @return string
     * @throws Exception
     */
    private function getLink($tasks, $format)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $i = 1;
        foreach ($tasks as $task) {
            $sheet->setCellValue('A'.$i, $task->getId());
            $sheet->setCellValue('B'.$i, $task->getTitle());
            $sheet->setCellValue('C'.$i, $task->getComment());
            $sheet->setCellValue('D'.$i, $task->getDate());
            $sheet->setCellValue('E'.$i, $task->getTimespent());
            $i++;
        }

        switch ($format) {
            case 'csv':
                $writer = new Csv($spreadsheet);
                break;
            case 'xlsx':
                $writer = new Xlsx($spreadsheet);
                break;
            case 'pdf':
                $writer = new Mpdf($spreadsheet);
                break;
            default:
                throw new Exception('Wrong file format: ' . $format);
        }

        $abstractLink = '/documents/tasks_' . time() . '.' . $format;
        $link = __DIR__ . '../../../public' . $abstractLink;
        $writer->save($link);

        return $abstractLink;
    }
}
