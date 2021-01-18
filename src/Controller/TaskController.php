<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller that works with tasks
 *
 * Class TaskController
 * @package App\Controller
 */
class TaskController extends AbstractController
{
    private TaskRepository $taskRepository;

    /**
     * TaskController constructor.
     * @param TaskRepository $taskRepository
     */
    public function __construct(TaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    /**
     * @Route("/tasks", name="app_user_tasks")
     */
    public function index(Request $request): Response
    {
        $page = $request->get('page') ?? 1;

        $query = $this->getTaskRepository()->createQueryBuilder('t')
            ->where('t.user = :userid')
            ->orderBy('t.id', 'DESC')
            ->setParameter('userid', $this->getUser()->getId())
            ->getQuery();

        $pageSize = '10';

        $paginator = new Paginator($query);
        $totalItems = count($paginator);
        $pagesCount = ceil($totalItems / $pageSize);

        $paginator
            ->getQuery()
            ->setFirstResult($pageSize * ($page-1))
            ->setMaxResults($pageSize);


        return $this->render('task/index.html.twig', [
            'tasks' => $paginator,
            'page_count' => $pagesCount,
            'total' => $totalItems,
            'page' => $page
        ]);
    }

    /**
     * @Route("/tasks/new", name="app_user_tasks_new")
     */
    public function new(Request $request): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $form->getData();

            $task = $form->getData();
            $task->setUser($this->getUser());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($task);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_tasks');
        }

        return $this->render('task/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @return TaskRepository
     */
    public function getTaskRepository(): TaskRepository
    {
        return $this->taskRepository;
    }
}
