<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Entity\Job;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class EmployeeController extends AbstractController
{
    public function __construct()
    {
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer($classMetadataFactory)];

        $this->serializer = new Serializer($normalizers, $encoders);
    }

    /**
     * @Route("/employees", name="employees")
     */
    public function index()
    {
        $employees = $this->getDoctrine()->getRepository(Employee::class)->findAll();
        $data = $this->serializer->normalize($employees, null, ['groups' => 'all_employees']);
        $jsonContent = $this->serializer->serialize($data, 'json');

        $response = new Response($jsonContent);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
    * @Route("/employees/{id}", name="employees_show", methods={"GET"}, requirements={"employee"="\d+"})
    */
    public function show(Employee $employee): Response
    {
        $employee = $this->getDoctrine()->getRepository(Employee::class)->find($employee);

        $data = $this->serializer->normalize($employee, null, ['groups' => 'all_employees']);

        $jsonContent = $this->serializer->serialize($data, 'json');

        $response = new Response($jsonContent);
        $response->headers->set('Content-Type', 'application/json');

        return $response;

    }


    /**
     * @Route("/employees/{id}/edit", name="employee_patch", methods={"POST"} , requirements={"id":"\d+"})
     */
    public function update(Request $request, Employee $employee)
    {

        if (!empty($request->request->get('firstname'))) {
            $employee->setFirstname($request->request->get('firstname'));
        }

        if (!empty($request->request->get('lastname'))) {
            $employee->setLastname($request->request->get('lastname'));
        }

        if (!empty($request->request->get('job_id'))) {
            $employee->setJob($this->getDoctrine()->getRepository(Job::class)->find($request->request->get('job_id')));
        }

        if (!empty($request->request->get('employement_date'))) {
            $date = new \DateTime($request->request->get('employement_date'));
            $employee->setEmployementDate($date);
        }

        $manager = $this->getDoctrine()->getManager();
        $manager->flush();

        return new Response(null, 202);
    }

    /**
     * @Route("/employee/{id}", name="job_delete", methods={"DELETE"},requirements={"id":"\d+"})
     */
    public function delete(Request $request, Employee $employee)
    {

        $manager = $this->getDoctrine()->getManager();
        $manager->remove($employee);

        $manager->flush();

        return new Response(null, 200);
    }
}
