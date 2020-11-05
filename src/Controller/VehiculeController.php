<?php

namespace App\Controller;

use App\Entity\Vehicule;
use App\Exception\ResourceValidationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validation;

class VehiculeController extends AbstractFOSRestController
{
    /**
     * @Route("/test", name="test")
     */
    public function index(): Response
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/VehiculeController.php',
        ]);
    }

    /**
     * @Rest\Get(
     *     path = "/vehicules/{id}",
     *     name = "app_vehicule_show",
     *     requirements = {"id"="\d+"}
     * )
     * @Rest\View
     */
    public function showVehicule(Vehicule $vehicule)
    {
        return $vehicule;
    }

    /**
     * @Rest\Get(
     *     path = "/vehicules",
     *     name = "app_vehicules_list",
     * )
     * @Rest\View
     */
    public function listVehicules()
    {
        $vehicules = $this->getDoctrine()->getRepository(Vehicule::class)->findAll();

        return $vehicules;
    }

    /**
     * @Rest\Post("/vehicule")
     * @Rest\View(StatusCode = 201)
     * @ParamConverter("vehicule", converter="fos_rest.request_body")
     * @throws ResourceValidationException
     */
    public function createVehicule(Vehicule $vehicule, ConstraintViolationList $violations)
    {
        if (count($violations)) {
            $message = 'The JSON sent contains invalid data. Here are the errors you need to correct: ';
            foreach ($violations as $violation) {
                $message .= sprintf("Field %s: %s ", $violation->getPropertyPath(), $violation->getMessage());
            }
            throw new ResourceValidationException($message);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($vehicule);
        $em->flush();

        return $vehicule;
    }

    /**
     * @Rest\View(StatusCode = 204)
     * @Rest\Delete(
     *     path = "/vehicule/{id}",
     *     name = "app_vehicule_delete",
     *     requirements = {"id"="\d+"}
     * )
     */
    public function deleteVehicule(Vehicule $vehicule)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($vehicule);
        $em->flush();

        return;
    }

    /**
     * @Rest\View(StatusCode = 200)
     * @Rest\Put(
     *     path = "/vehicule/{id}",
     *     name = "app_vehicule_update",
     *     requirements = {"id"="\d+"}
     * )
     * @ParamConverter("newVehicule", converter="fos_rest.request_body")
     * @throws ResourceValidationException
     */
    public function updateVehicule(Vehicule $vehicule, Vehicule $newVehicule, ConstraintViolationList $violations)
    {
        if (count($violations)) {
            $message = 'The JSON sent contains invalid data. Here are the errors you need to correct: ';
            foreach ($violations as $violation) {
                $message .= sprintf("Field %s: %s ", $violation->getPropertyPath(), $violation->getMessage());
            }

            throw new ResourceValidationException($message);
        }

        $vehicule->setNom($newVehicule->getNom());
        $vehicule->setCouleur($newVehicule->getCouleur());
        $vehicule->setCarburant($newVehicule->getCarburant());

        $this->getDoctrine()->getManager()->flush();

        return $vehicule;
    }

}
