<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Grade;
use App\Entity\Student;
use App\Entity\Lecture;

class GradeController extends AbstractController
{
    #[Route('/grade', name: 'grade_index')]
    public function index(Request $r): Response
    {   
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $student = $this->getDoctrine()
        ->getRepository(Student::class)
        ->findAll();

        $lecture = $this->getDoctrine()
        ->getRepository(Lecture::class)
        ->findAll();

        $student_name = $r->getSession()->getFlashBag()->get('student_name', []);
        $student_surname = $r->getSession()->getFlashBag()->get('student_surname', []);

        $lecture_name = $r->getSession()->getFlashBag()->get('lecture_name', []);

        return $this->render('grade/index.html.twig', [
            'student' => $student,
            'lecture' => $lecture,
            'student_name' => $student_name,
            'student_surname' => $student_surname,
            'lecture_name' => $lecture_name,
        ]);
    }

    #[Route('/grade/store', name: 'grade_store', methods: ['POST'])]
    public function store(Request $r): Response
    {
        $student = $this->getDoctrine()
        ->getRepository(Student::class)
        ->find($r->request->get('grade_student_id'));
        
        $lecture = $this->getDoctrine()
        ->getRepository(Lecture::class)
        ->find($r->request->get('grade_lecture_id'));

        $grade = new Grade;
        
        $grade->
        setLecture($lecture)->
        setStudent($student)->
        setGrade($r->request->get('grade_grade'));

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($grade);
        $entityManager->flush();

        return $this->redirectToRoute('grade_index');
    }
}
     
