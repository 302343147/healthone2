<?php

namespace App\Controller;


use App\Entity\Activiteit;
use App\Form\ActiviteitType;
use App\Form\SoortActiviteitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

class MedewerkerController extends AbstractController
{
    /**
     * @Route("/admin/activiteiten", name="activiteitenoverzicht")
     */
    public function activiteitenOverzichtAction()
    {

        $activiteiten=$this->getDoctrine()
            ->getRepository('App:Activiteit')
            ->findAll();

        $soortactiviteiten=$this->getDoctrine()
            ->getRepository('App:Soortactiviteit')
            ->findAll();

        return $this->render('medewerker/activiteiten.html.twig', [
            'activiteiten'=>$activiteiten, 'soortactiviteiten' => $soortactiviteiten
        ]);
    }

    /**
     * @Route("/admin/soortactiviteiten", name="soortactiviteitenoverzicht")
     */
    public function soortActiviteitenOverzichtAction()
    {

        $activiteiten=$this->getDoctrine()
            ->getRepository('App:Activiteit')
            ->findAll();

        $soortactiviteiten=$this->getDoctrine()
            ->getRepository('App:Soortactiviteit')
            ->findAll();

        return $this->render('medewerker/soortactiviteiten.html.twig', [
            'soortactiviteiten'=>$soortactiviteiten, 'activiteiten'=>$activiteiten
        ]);
    }


    /**
     * @Route("/admin/details/{id}", name="details")
     */
    public function detailsAction($id)
    {
        $activiteiten=$this->getDoctrine()
            ->getRepository('App:Activiteit')
            ->findAll();
        $activiteit=$this->getDoctrine()
            ->getRepository('App:Activiteit')
            ->find($id);

        $deelnemers=$this->getDoctrine()
            ->getRepository('App:User')
            ->getDeelnemers($id);


        return $this->render('medewerker/details.html.twig', [
            'activiteit'=>$activiteit,
            'deelnemers'=>$deelnemers,
            'aantal'=>count($activiteiten)
        ]);
    }

    /**
     * @Route("/admin/beheer", name="beheer")
     */
    public function beheerAction()
    {
        $activiteiten=$this->getDoctrine()
            ->getRepository('App:Activiteit')
            ->findAll();

        return $this->render('medewerker/beheer.html.twig', [
            'activiteiten'=>$activiteiten
        ]);
    }

    /**
     * @Route("/admin/add", name="add")
     */
    public function addAction(Request $request)
    {
        // create a user and a contact
        $a=new Activiteit();

        $form = $this->createForm(ActiviteitType::class, $a);
        $form->add('save', SubmitType::class, array('label'=>"voeg toe"));
        //$form->add('reset', ResetType::class, array('label'=>"reset"));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($a);
            $em->flush();

            $this->addFlash(
                'notice',
                'activiteit toegevoegd!'
            );
            return $this->redirectToRoute('beheer');
        }
        $activiteiten=$this->getDoctrine()
            ->getRepository('App:Activiteit')
            ->findAll();
        return $this->render('medewerker/nieuwSA.html.twig',array('form'=>$form->createView(),'naam'=>'toevoegen','aantal'=>count($activiteiten)
            ));
    }

    /**
     * @Route("/admin/update/{id}", name="update")
     */
    public function updateAction($id,Request $request)
    {
        $a=$this->getDoctrine()
            ->getRepository('App:Activiteit')
            ->find($id);

        $form = $this->createForm(ActiviteitType::class, $a);
        $form->add('save', SubmitType::class, array('label'=>"aanpassen"));

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            // tells Doctrine you want to (eventually) save the contact (no queries yet)
            $em->persist($a);


            // actually executes the queries (i.e. the INSERT query)
            $em->flush();
            $this->addFlash(
                'notice',
                'activiteit aangepast!'
            );
            return $this->redirectToRoute('beheer');
        }

        $activiteiten=$this->getDoctrine()
            ->getRepository('App:Activiteit')
            ->findAll();

        return $this->render('medewerker/nieuwSA.html.twig',array('form'=>$form->createView(),'naam'=>'aanpassen','aantal'=>count($activiteiten)));
    }


    /**
     * @Route("/admin/updatesoort/{id}", name="updatesoort")
     */
    public function updateSoortAction($id,Request $request)
    {
        $a=$this->getDoctrine()
            ->getRepository('App:Soortactiviteit')
            ->find($id);

        $form = $this->createForm(SoortActiviteitType::class, $a);
        $form->add('save', SubmitType::class, array('label'=>"aanpassen"));

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            // tells Doctrine you want to (eventually) save the contact (no queries yet)
            $em->persist($a);


            // actually executes the queries (i.e. the INSERT query)
            $em->flush();
            $this->addFlash(
                'notice',
                'soort activiteit aangepast!'
            );
            return $this->redirectToRoute('soortactiviteitenoverzicht');
        }

        $activiteiten=$this->getDoctrine()
            ->getRepository('App:Soortactiviteit')
            ->findAll();

        return $this->render('medewerker/nieuwSA.html.twig',array('form'=>$form->createView(),'naam'=>'aanpassen','aantal'=>count($activiteiten)));
    }


    /**
     * @Route("/admin/delete/{id}", name="delete")
     */
    public function deleteAction($id)
    {
        $em=$this->getDoctrine()->getManager();
        $a= $this->getDoctrine()
            ->getRepository('App:Activiteit')->find($id);
        $em->remove($a);
        $em->flush();

        $this->addFlash(
            'notice',
            'activiteit verwijderd!'
        );
        return $this->redirectToRoute('beheer');

    }

    /**
     * @Route("/admin/deletesoort/{id}", name="deletesoort")
     */
    public function deleteSoortAction($id)
    {
        $em=$this->getDoctrine()->getManager();
        $a= $this->getDoctrine()
            ->getRepository('App:Soortactiviteit')->find($id);
        $em->remove($a);
        $em->flush();

        $this->addFlash(
            'notice',
            'Soort activiteit verwijderd!'
        );
        return $this->redirectToRoute('soortactiviteitenoverzicht');

    }
}
