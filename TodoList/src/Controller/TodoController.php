<?php
namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TodoController extends AbstractController
{
    /**
     * Liste des tâches
     *
     * @Route("/todos", name="todo_list")
     */
    public function todoList(Request $request,TaskRepository $taskRepository)
    {
        $todos = $taskRepository->findAll();
        $task = new Task();

        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($task);
            $entityManager->flush();

            return $this->redirectToRoute('todo_list');
        }

        return $this->render('todo/list.html.twig', [
            'todos' => $todos,
            'form' =>$form->createView(),

        ]);
        }
    
    /**
     * Affichage d'une tâche
     *
     * @Route("/todo/{id}", name="todo_show", requirements={"id" = "\d+"})
     */
    public function todoShow($id)
    {
        $todo = TodoModel::find($id);
        return $this->render('todo/single.html.twig', [
            'todo' => $todo,
        ]);
    }
    /**
     * Changement de statut
     *
     * @Route("/todo/{id}/{status}", name="todo_set_status", requirements={"id" = "\d+"})
     */
    public function todoSetStatus($id, $status)
    {
    }
    /**
     * Ajout d'une tâche
     *
     * @Route("/todo/add", name="todo_add")
     *
     * La route est définie en POST parce qu'on veut ajouter une ressource sur le serveur
     */
    public function todoAdd(Request $request, TaskRepository $taskRepository)
    {
      
    }
}
