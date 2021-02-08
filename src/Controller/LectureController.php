<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Entity\Lecture;

class LectureController extends AbstractController
{
    #[Route('/lecture', name: 'lecture_index', methods: ['GET'])]
    public function index(Request $r): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');


        $lecture = $this->getDoctrine()
        ->getRepository(Lecture::class)
        ->findBy([],['name'=>'asc']);

        return $this->render('lecture/index.html.twig', [
            'success' => $r->getSession()->getFlashBag()->get('success', []),
            'lecture' => $lecture
        ]);
    }

    #[Route('/lecture/create', name: 'lecture_create', methods: ['GET'])]
    public function create(Request $r): Response
    {
        $lecture_name = $r->getSession()->getFlashBag()->get('lecture_name', []);
        $lecture_description = $r->getSession()->getFlashBag()->get('lecture_description', []);
        
        return $this->render('lecture/create.html.twig', [
            'errors' => $r->getSession()->getFlashBag()->get('errors', []),
            'lecture_name' => $lecture_name,
            'lecture_description' => $lecture_description,
        ]);
    }

    #[Route('/lecture/store', name: 'lecture_store', methods: ['POST'])]
    public function store(Request $r, ValidatorInterface $validator): Response
    {
        $submittedToken = $r->request->get('token');

        if (!$this->isCsrfTokenValid('', $submittedToken)) $r->getSession()->getFlashBag()->add('errors', 'Invalid token.');

        if (!$this->isCsrfTokenValid('', $submittedToken)) return $this->redirectToRoute('lecture_create');

        $lecture = new Lecture;
        $lecture->
        setName($r->request->get('lecture_name'))->
        setDescription($r->request->get('lecture_description'));
    
        $errors = $validator->validate($lecture);

        if (count($errors) > 0) {

            foreach($errors as $error) {
                $r->getSession()->getFlashBag()->add('errors', $error->getMessage());

            }
            $r->getSession()->getFlashBag()->add('lecture_name', $r->request->get('lecture_name'));
            $r->getSession()->getFlashBag()->add('lecture_description', $r->request->get('lecture_description'));

            return $this->redirectToRoute('lecture_create');
        }

        $r->getSession()->getFlashBag()->add('success', 'Lecture has been succesfully added.');

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($lecture);
        $entityManager->flush();

        return $this->redirectToRoute('lecture_index');
    }

    #[Route('/lecture/edit/{id}', name: 'lecture_edit', methods: ['GET'])]
    public function edit(int $id, Request $r): Response
    {
        $lecture = $this->getDoctrine()
        ->getRepository(Lecture::class)
        ->find($id);

        $lecture_name = $r->getSession()->getFlashBag()->get('lecture_name', []);
        $lecture_description = $r->getSession()->getFlashBag()->get('lecture_description', []);
        
        return $this->render('lecture/edit.html.twig', [
            'errors' => $r->getSession()->getFlashBag()->get('errors', []),
            'lecture' => $lecture,
            'lecture_name' => $lecture_name[0] ?? '',
            'lecture_description' => $lecture_description[0] ?? '',
        ]);
    }

    #[Route('/lecture/update/{id}', name: 'lecture_update', methods: ['POST'])]
    public function update(Request $r, $id, ValidatorInterface $validator): Response
    {
        $submittedToken = $r->request->get('token');

        if (!$this->isCsrfTokenValid('', $submittedToken)) $r->getSession()->getFlashBag()->add('errors', 'Invalid token.');

        if (!$this->isCsrfTokenValid('', $submittedToken)) return $this->redirectToRoute('lecture_edit', ['id'=>$lecture->getId()]);

        $lecture = $this->getDoctrine()
        ->getRepository(Lecture::class)
        ->find($id);

        $lecture->
        setName($r->request->get('lecture_name'))->
        setDescription($r->request->get('lecture_description'));


        $errors = $validator->validate($lecture);

        if (count($errors) > 0) {

            foreach($errors as $error) {
                $r->getSession()->getFlashBag()->add('errors', $error->getMessage());

            }
            $r->getSession()->getFlashBag()->add('lecture_name', $r->request->get('lecture_name'));
            $r->getSession()->getFlashBag()->add('lecture_description', $r->request->get('lecture_description'));

            return $this->redirectToRoute('lecture_create');
        }

        $r->getSession()->getFlashBag()->add('success', 'Lecture has been succesfully modified.');
        
        $lecture_name = $r->getSession()->getFlashBag()->get('lecture_name', []);
        $lecture_description = $r->getSession()->getFlashBag()->get('lecture_description', []);
        
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($lecture);
        $entityManager->flush();
        
        return $this->redirectToRoute('lecture_index');
    }

    #[Route('/lecture/delete/{id}', name: 'lecture_delete', methods: ['POST'])]
    public function delete(Request $r, $id): Response
    {
        $lecture = $this->getDoctrine()
        ->getRepository(Lecture::class)
        ->find($id);
        
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($lecture);
        $entityManager->flush();
        
        return $this->redirectToRoute('lecture_index');
    }
}