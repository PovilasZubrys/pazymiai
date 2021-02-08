<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Student;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Entity\Grade;
use App\Entity\Lecture;

class StudentController extends AbstractController
{
    #[Route('/student', name: 'student_index', methods: ['GET'])]
    public function index(Request $r): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $student = $this->getDoctrine()
        ->getRepository(Student::class)
        ->findBy([],['surname'=>'asc']);

        return $this->render('student/index.html.twig', [
            'success' => $r->getSession()->getFlashBag()->get('success', []),
            'student' => $student
        ]);
    }

    #[Route('/student/create', name: 'student_create', methods: ['GET'])]
    public function create(Request $r): Response
    {
        $student_name = $r->getSession()->getFlashBag()->get('student_name', []);
        $student_surname = $r->getSession()->getFlashBag()->get('student_surname', []);
        $student_email = $r->getSession()->getFlashBag()->get('student_email', []);
        $student_phone = $r->getSession()->getFlashBag()->get('student_phone', []);

        return $this->render('student/create.html.twig', [
            'student_name' => $student_name,
            'student_surname' => $student_surname,
            'student_email' => $student_email,
            'student_phone' => $student_phone,
        ]);
    }

    #[Route('/student/store', name: 'student_store', methods: ['POST'])]
    public function store(Request $r, ValidatorInterface $validator): Response
    {
        $submittedToken = $r->request->get('token');

        if (!$this->isCsrfTokenValid('', $submittedToken)) $r->getSession()->getFlashBag()->add('errors', 'Invalid token.');

        if (!$this->isCsrfTokenValid('', $submittedToken)) return $this->redirectToRoute('student_create');


        $student = new Student;

        $student->
        setName($r->request->get('student_name'))->
        setSurname($r->request->get('student_surname'))->
        setEmail($r->request->get('student_email'))->
        setPhone($r->request->get('student_phone'));
        
        $errors = $validator->validate($student);

        if (count($errors) > 0) {

            foreach($errors as $error) {
                $r->getSession()->getFlashBag()->add('errors', $error->getMessage());

            }
            $r->getSession()->getFlashBag()->add('student_name', $r->request->get('student_name'));
            $r->getSession()->getFlashBag()->add('student_surname', $r->request->get('student_surname'));
            $r->getSession()->getFlashBag()->add('student_email', $r->request->get('student_email'));
            $r->getSession()->getFlashBag()->add('student_phone', $r->request->get('student_phone'));

            return $this->redirectToRoute('student_create');
        }

        $r->getSession()->getFlashBag()->add('success', 'Student has been succesfully added.');

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($student);
        $entityManager->flush();

        return $this->redirectToRoute('student_index');
    }

    #[Route('/student/edit/{id}', name: 'student_edit', methods: ['GET'])]
    public function edit(int $id, Request $r): Response
    {
        $student = $this->getDoctrine()
        ->getRepository(Student::class)
        ->find($id);

        $student_name = $r->getSession()->getFlashBag()->get('student_name', []);
        $student_surname = $r->getSession()->getFlashBag()->get('student_surname', []);
        $student_email = $r->getSession()->getFlashBag()->get('student_email', []);
        $student_phone = $r->getSession()->getFlashBag()->get('student_phone', []);
        
        return $this->render('student/edit.html.twig', [
            'errors' => $r->getSession()->getFlashBag()->get('errors', []),
            'student' => $student,
            'student_name' => $student_name[0] ?? '',
            'student_surname' => $student_surname[0] ?? '',
            'student_email' => $student_email[0] ?? '',
            'student_phone' => $student_phone[0] ?? '',
        ]);
    }

    #[Route('/student/update/{id}', name: 'student_update', methods: ['POST'])]
    public function update(Request $r, $id, ValidatorInterface $validator): Response
    {
        $submittedToken = $r->request->get('token');

        if (!$this->isCsrfTokenValid('', $submittedToken)) $r->getSession()->getFlashBag()->add('errors', 'Invalid token.');

        if (!$this->isCsrfTokenValid('', $submittedToken)) return $this->redirectToRoute('student_edit', ['id'=>$student->getId()]);

        $student = $this->getDoctrine()
        ->getRepository(Student::class)
        ->find($id);

        $student->
        setName($r->request->get('student_name'))->
        setSurname($r->request->get('student_surname'))->
        setEmail($r->request->get('student_email'))->
        setPhone($r->request->get('student_phone'));

        $student_name = $r->getSession()->getFlashBag()->get('student_name', []);
        $student_surname = $r->getSession()->getFlashBag()->get('student_surname', []);
        $student_email = $r->getSession()->getFlashBag()->get('student_email', []);
        $student_phone = $r->getSession()->getFlashBag()->get('student_phone', []);

        $errors = $validator->validate($student);

        if (count($errors) > 0) {

            foreach($errors as $error) {
                $r->getSession()->getFlashBag()->add('errors', $error->getMessage());

            }
            $r->getSession()->getFlashBag()->add('student_name', $r->request->get('student_name'));
            $r->getSession()->getFlashBag()->add('student_surname', $r->request->get('student_surname'));
            $r->getSession()->getFlashBag()->add('student_email', $r->request->get('student_email'));
            $r->getSession()->getFlashBag()->add('student_phone', $r->request->get('student_phone'));

            return $this->redirectToRoute('student_edit');
        }

        $r->getSession()->getFlashBag()->add('success', 'Student has been succesfully modified.');
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($student);
        $entityManager->flush();
        
        return $this->redirectToRoute('student_index');
    }

    #[Route('/student/delete/{id}', name: 'student_delete', methods: ['POST'])]
    public function delete(Request $r, $id): Response
    {
        $student = $this->getDoctrine()
        ->getRepository(Student::class)
        ->find($id);
        
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($student);
        $entityManager->flush();
        
        return $this->redirectToRoute('student_index');
    }

    #[Route('/student/grades/{id}', name: 'student_grades', methods: ['GET'])]
    public function grades(Request $r, $id): Response
    {
        
        $grade = $this->getDoctrine()
        ->getRepository(Grade::class)
        ->findAll();

        $student = $this->getDoctrine()
        ->getRepository(Student::class)
        ->findAll();

        $lecture = $this->getDoctrine()
        ->getRepository(Lecture::class)
        ->findAll();

        return $this->render('student/grade.html.twig', [
            'student' => $student,
            'lecture' => $lecture,
            'grade' => $grade,
            'id' => $id,
        ]);
    }

}