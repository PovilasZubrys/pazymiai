<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Lecture;

class LectureController extends AbstractController
{
    #[Route('/lecture', name: 'lecture_index', methods: ['GET'])]
    public function index(Request $r): Response
    {
        $lecture = $this->getDoctrine()
        ->getRepository(Lecture::class)
        ->findAll();

        return $this->render('lecture/index.html.twig', [
            'controller_name' => 'LectureController',
            'lecture' => $lecture
        ]);
    }

    #[Route('/create', name: 'lecture_create', methods: ['GET'])]
    public function create(Request $r): Response
    {
        $lecture_name = $r->getSession()->getFlashBag()->get('lecture_name', []);
        $lecture_description = $r->getSession()->getFlashBag()->get('lecture_description', []);

        return $this->render('lecture/index.html.twig', [
            'lecture_name' => $lecture_name,
            'lecture_description' => $lecture_description,
        ]);
    }
}
