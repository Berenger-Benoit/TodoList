<?php
namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class TodoController extends AbstractController
{
    /**
     * Liste des tâches
     *
     * @Route("/todos", name="todo_list")
     */
    public function todoList(Request $request, TaskRepository $taskRepository)
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
            'form' => $form->createView(),

        ]);
    }

    /**
     * Affichage d'une tâche
     *
     * @Route("/todo/{id}", name="todo_show", requirements={"id" = "\d+"})
     */
    public function todoShow($id, TaskRepository $taskRepository)
    {
        $todo = $taskRepository->find($id);
  
        if($todo->getStatus() == false){
            $status = '';
        }
        if($todo->getStatus() == true){
            $status = 'done';
        }

        return $this->render('todo/single.html.twig', [
            'todo' => $todo,
            'status' => $status,
        ]);
    }
   /**
     * Changement de statut
     *
     * @Route(
     * "/todo/{id}/{status}",
     *  name="todo_set_status",
     *  requirements={
     *          "id" = "\d+",
     *          "status" = "undone|done"
     *          },
     *           methods={"GET", "POST"},
     * 
     *  )
     * 
     */
    public function todoSetStatus($id, $status, Task $task,TaskRepository $taskRepository)
    {
       $entityManager = $this->getDoctrine()->getManager();
       $task = $taskRepository->find($id);

       if($task->setStatus( !$task->getStatus())) {
        $entityManager->flush();
        $this->addFlash(
                 'info',
                 '<strong>Super !</strong> La tâche a bien été marquée comme '.$status.' !'
             );
        
       }
         else {
            $this->addFlash(
                'danger',
                '<strong>Attention !</strong> La tâche n\'as pas été modifiée car elle n\'existe pas !'
            );
        }

        // On redirige vers la liste des tâches
        return $this->redirectToRoute('todo_list');
    }

    /**
     * Ajout d'une tâche
     *
     * @Route("/todo/add", name="todo_add", methods={"POST"})
     *
     * La route est définie en POST parce qu'on veut ajouter une ressource sur le serveur
     */
    public function todoAdd(Request $request, TaskRepository $taskRepository)
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
       
        $form->handleRequest($request);
        
  
        if ($form->isSubmitted() && $form->isValid()) {
          
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($task);
            $entityManager->flush();

            $this->addflash(
                'success',
                '<strong>Super !</strong> La tâche «'. $task .'» a bien été ajoutée !'
            );
            return $this->redirectToRoute('todo_list', [
                   'form' => $form->createView(),
            ]);
          
        }
     
     
    }

    /**
     * Suppression d'une tâche
     *
     * @Route("/todo/delete/{id}", name="todo_delete", requirements={"id" = "\d+"})
     */
    public function deleteTask(Request $request, Task $task)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($task);
        $entityManager->flush();
        $this->addflash(
            'danger',
             'La tâche «'. $task .'» a bien été supprimée !'
        );
        return $this->redirectToRoute('todo_list');
    }
}
